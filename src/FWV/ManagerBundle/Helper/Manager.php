<?php
/**
 * Created by PhpStorm.
 * User: omicron
 * Date: 29/04/17
 * Time: 15:51
 */

namespace FWV\ManagerBundle\Helper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Manager
{
    public function startServer($saveName = null, $logger)
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');

        if (empty($saveName)) {
            $saveName = $this->getLastUsedSave()['name'];
            if (!$this->saveExists($saveName))
                throw new \InvalidArgumentException('No saves to load, please create a new save.');
        }

        if (!$this->saveExists($saveName))
            throw new \InvalidArgumentException('The save does not exist');

        $process = new Process('./bin/x64/factorio --start-server ' . $saveName . ' > ./logs/' . $saveName . '.log &', '../var/factorio', null, null, 10, array());
        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            if ($this->isServerRunning())
                return $process->getOutput();
            else {
                $logger->error('timedOut: ' . $process->getOutput());
                throw new \Exception('Impossible to start the server, please contact the author.');
            }
        }

        if ($this->isServerRunning())
            return $process->getOutput();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->isServerRunning()) {
            $logger->error('Server not running: ' . './bin/x64/factorio --start-server ' . $saveName . ' > logs/' . $saveName . '.log &');
            throw new \Exception('Impossible to start the server, please contact the author.');
        }

        return $process->getOutput();
    }

    public function stopServer()
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');

        $process = new Process('pkill -1 factorio', null, null, null, 10, array());
        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            if ($this->isServerRunning()) {
                $this->forceStop();
                if ($this->isServerRunning())
                    throw new \Exception('Impossible to stop the server');
            }
            else
                return $process->getOutput();
        }

        sleep(2);

        if ($this->isServerRunning())
            $this->forceStop();

        if ($this->isServerRunning())
            throw new \Exception('Impossible to stop the server');

        return $process->getOutput();
    }

    public function isServerRunning()
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');

        $process = new Process('pidof factorio', null, null, null, 3, array());
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            return false;
        }

        if ($process->getOutput() === '1' || $process->getOutput() === 1)
            return false;

        return $process->getOutput();
    }

    public function forceStop()
    {
        $process = new Process('pkill -9 factorio', null, null, null, 3, array());
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function saveExists($saveName) {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');

        $finder = new Finder();
        $finder->files()->in('../var/factorio/saves');

        foreach ($finder as $file) {
            if ($file->getRelativePathname() === $saveName . '.zip')
                return true;
        }
        return false;
    }

    public function restartServer($logger)
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');
        $this->stopServer();
        return $this->startServer(null, $logger);
    }

    public function getSaves()
    {
        if (!$this->_isGameInstalled())
            return array();
        try {
            sleep(1);
            $finder = new Finder();
            $finder->files()->in('../var/factorio/saves')->name('*.zip')->sortByAccessedTime();
            $files = array();
            $i = 0;
            foreach ($finder as $file) {
                $files[$i]['name'] = substr($file->getRelativePathname(), 0, -4);
                $files[$i]['time'] = date('d-m-Y H:i:s', fileatime($file->getRealPath()));
                $i++;
            }
            return array_reverse($files);
        } catch (\InvalidArgumentException $e) {
            return array();
        }
    }

    public function getLastUsedSave()
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');
        $saves = $this->getSaves();
        if (count($saves)) {
            return $saves[0];
        }
        return null;
    }

    public function createGame($saveName)
    {
        if (!$this->_isGameInstalled())
            throw new \InvalidArgumentException('The game is not installed yet');

        if ($this->saveExists($saveName))
            throw new \Exception('A save with that name already exists');

        if ($this->isServerRunning())
            $this->stopServer();

        $saveName = str_replace(' ', '_', $saveName);

        $creatingProcess = new Process('./bin/x64/factorio --create ' . $saveName . ' ./saves/' . $saveName . '.zip', '../var/factorio', null, null, 10, array());
        $creatingProcess->run();

        if (!$creatingProcess->isSuccessful()) {
            throw new ProcessFailedException($creatingProcess);
        }

        $movingProcess = new Process('mv ' . $saveName . '.zip saves/' . $saveName . '.zip', '../var/factorio', null, null, 1, array());
        $movingProcess->run();

        if (!$movingProcess->isSuccessful()) {
            throw new ProcessFailedException($movingProcess);
        }

        if (!$this->saveExists($saveName)) {
            throw new \Exception('Could not create the save');
        }
    }

    protected function _isGameInstalled()
    {
        try {
            $finder = new Finder();
            $finder->files()->in('../var/factorio/');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function installGame()
    {
        if ($this->_isGameInstalled()) {
            if ($this->isServerRunning())
                $this->stopServer();

            $oldSavesProcess = new Process('mv -f factorio/saves saves', '../var', null, null, 2, array());
            $oldSavesProcess->run();

            if (!$oldSavesProcess->isSuccessful()) {
                throw new ProcessFailedException($oldSavesProcess);
            }

            $removeGameProcess = new Process('rm -rf factorio', '../var', null, null, 2, array());
            $removeGameProcess->run();

            if (!$removeGameProcess->isSuccessful()) {
                throw new ProcessFailedException($removeGameProcess);
            }
        }
        $untarProcess = new Process('tar Jxf factorio.tar.xz', '../var', null, null, 5, array());
        $untarProcess->run();

        if (!$untarProcess->isSuccessful()) {
            throw new ProcessFailedException($untarProcess);
        }

        $mkdirProcess = new Process('mkdir saves && mkdir logs', '../var/factorio', null, null, 1, array());
        $mkdirProcess->run();

        if (!$mkdirProcess->isSuccessful()) {
            throw new ProcessFailedException($mkdirProcess);
        }

        $backupSavesProcess = new Process('mv -f saves factorio/', '../var', null, null, 1, array());
        $backupSavesProcess->run();

        if (!$backupSavesProcess->isSuccessful()) {
            throw new ProcessFailedException($backupSavesProcess);
        }
    }
}