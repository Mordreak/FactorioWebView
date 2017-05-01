<?php
/**
 * Created by PhpStorm.
 * User: omicron
 * Date: 29/04/17
 * Time: 15:51
 */

namespace FWV\ManagerBundle\Helper;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Manager
{
    public function startServer($saveName = null, $logger)
    {
        if (empty($saveName))
            $saveName = $this->getLastUsedSave()['name'];

        if (!$this->saveExists($saveName))
            throw new InvalidArgumentException('Le fichier de sauvegarde n\'existe pas.');

        $process = new Process('./bin/x64/factorio --start-server ' . $saveName . ' > ./logs/' . $saveName . '.log &', '/factorio', null, null, 3, array());
        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            if ($this->isServerRunning())
                return $process->getOutput();
            else {
                $logger->error('timedOut: ' . $process->getOutput());
                throw new \Exception('Impossible de démarrer le serveur. Contactez l\'Administrateur');
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
            throw new \Exception('Impossible de démarrer le serveur. Contactez l\'Administrateur');
        }

        return $process->getOutput();
    }

    public function stopServer()
    {
        $process = new Process('pkill -1 factorio', null, null, null, 3, array());
        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            if ($this->isServerRunning())
                throw new ProcessFailedException($process);
            else
                return $process->getOutput();
        }

        sleep(2);

        if ($this->isServerRunning())
            $this->forceStop();

        if ($this->isServerRunning())
            throw new ProcessFailedException($process);

        return $process->getOutput();
    }

    public function isServerRunning()
    {
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
        $finder = new Finder();
        $finder->files()->in('/factorio/saves');

        foreach ($finder as $file) {
            if ($file->getRelativePathname() === $saveName . '.zip')
                return true;
        }
        return false;
    }

    public function restartServer($logger)
    {
        $this->stopServer();
        return $this->startServer(null, $logger);
    }

    public function getSaves()
    {
        sleep(1);
        $finder = new Finder();
        $finder->files()->in('/factorio/saves')->name('*.zip')->sortByAccessedTime();
        $files = array();
        $i = 0;
        foreach ($finder as $file) {
            $files[$i]['name'] = substr($file->getRelativePathname(), 0, -4);
            $files[$i]['time'] = date('d-m-Y H:i:s', fileatime($file->getRealPath()));
            $i++;
        }
        return array_reverse($files);
    }

    public function getLastUsedSave()
    {
        $saves = $this->getSaves();
        if (count($saves)) {
            return $saves[0];
        }
        return null;
    }

    public function createGame($saveName)
    {
        if ($this->saveExists($saveName))
            throw new \Exception('Une partie avec ce nom existe déjà');

        if ($this->isServerRunning())
            $this->stopServer();

        $creatingProcess = new Process('./bin/x64/factorio --create ' . $saveName . ' ./saves/' . $saveName . '.zip', '/factorio', null, null, 4, array());
        $creatingProcess->run();

        if (!$creatingProcess->isSuccessful()) {
            throw new ProcessFailedException($creatingProcess);
        }

        $movingProcess = new Process('mv ' . $saveName . '.zip ./saves/' . $saveName . '.zip', '/factorio', null, null, 1, array());
        $movingProcess->run();

        if (!$movingProcess->isSuccessful()) {
            throw new ProcessFailedException($movingProcess);
        }

        if (!$this->saveExists($saveName)) {
            throw new \Exception('La partie n\'a pas pu être générée.');
        }
    }
}