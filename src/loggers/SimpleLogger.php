<?php

namespace App\loggers;

use DateTime;
use http\Exception\InvalidArgumentException;
use SplFileObject;

class SimpleLogger extends AbstractLogger
{
    private string $logDir;

    public function __construct(string $logsDir)
    {
        $this->logDir = $logsDir;
    }

    public function log(LogLevel $level, string|\Stringable $message, array $context = []): void
    {
        if (!file_exists($this->logDir) || !is_dir($this->logDir)) {
            mkdir($this->logDir);
        }
        $filename = $this->logDir . "/" . $level->value . ".txt";
        $file = new SplFileObject($filename, "a");
        $date = new DateTime();
        $message = "[" . $date->format("Y-m-d H:i:s") . "] " . $message;
        $file->fwrite($message . PHP_EOL);
    }
}
