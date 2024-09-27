<?php

declare(strict_types=1);

namespace App\Logger;

use App\IOUtils;

class SimpleLogger extends AbstractLogger
{
    private string $logDir;

    public function __construct(string $logDir)
    {
        $this->logDir = $logDir;
    }

    public function log(LogLevel $level, string|\Stringable $message, array $context = []): void
    {
        if (!file_exists($this->logDir) || !is_dir($this->logDir)) {
            mkdir($this->logDir);
        }
        $filename = "$this->logDir/log.txt";
        $date = new \DateTime();
        $type = strtoupper($level->value);
        $message = "[{$date->format('Y-m-d H:i:s')}][$type] $message";
        IOUtils::appendFile($filename, [$message]);
    }
}
