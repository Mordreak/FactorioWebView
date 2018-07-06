<?php
/**
 * Created by PhpStorm.
 * User: omicron
 * Date: 10/05/17
 * Time: 14:41
 */

namespace FWV\ManagerBundle\Helper;


use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Finder\Finder;

class Parser
{
    public function parseLog()
    {
        $logs = array();
        $finder = new Finder();
        $files = $finder->files()->in('../var/factorio/logs')->name('*.log')->sortByAccessedTime();
        foreach ($files as $file)
            $logFile = $file;
        if ($logFile)
            $lines = explode("\n", $logFile->getContents());
        else
            $lines = array();
        $key = 0;
        foreach ($lines as $line) {
            try {
                $line = trim($line);
                $time = $this->_getLineTime($line);
                if (!empty($time)) {
                    $logs[$key]['time'] = $time;
                    $logs[$key]['info'] = $this->_getLineInfo($line, $time);
                    $key++;
                } else {
                    continue;
                }
            } catch (ContextErrorException $e) {
                continue;
            }
        }
        return $logs;
    }

    protected function _getLineTime($line)
    {
        return explode(' ', $line)[0];
    }

    protected function _getLineInfo($line, $time)
    {
        return substr(strstr($line, $time), strlen($time));
    }
}