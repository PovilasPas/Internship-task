<?php

declare(strict_types=1);

namespace App;

class IOUtils
{
    public static function readFile(string $filePath): array
    {
        $file = new \SplFileObject($filePath);
        $lines = [];
        while (!$file->eof()) {
            $line = trim($file->fgets());
            $lines[] = $line;
        }
        unset($file);
        return $lines;
    }

    public static function writeFile(string $filePath, array $lines): void
    {
        $file = new \SplFileObject($filePath, 'w');
        foreach ($lines as $line) {
            $file->fwrite($line . PHP_EOL);
        }
    }

    public static function appendFile(string $filePath, array $lines): void
    {
        $file = new \SplFileObject($filePath, 'a');
        foreach ($lines as $line) {
            $file->fwrite($line . PHP_EOL);
        }
    }
}
