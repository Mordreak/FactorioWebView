<?php
/**
 * Created by PhpStorm.
 * User: omicron
 * Date: 10/05/17
 * Time: 14:41
 */

namespace FWV\ManagerBundle\Helper;


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
        $lines = explode("\n", $logFile->getContents());
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'Operating system:') !== false)
                $logs['system'] = trim(substr($line, strpos($line, 'Operating system:') + strlen('Operating system:')));
            if (strpos($line, 'System info:') !== false)
                $logs['config'] = trim(substr($line, strpos($line, 'System info:') + strlen('System info:')));
        }
        return $logs;
    }
}