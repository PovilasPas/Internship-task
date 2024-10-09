<?php

declare(strict_types=1);

namespace App;

use App\Model\Rule;

class IOUtils
{
    public static function readRuleFile(string $filePath): array
    {
        $file = new \SplFileObject($filePath);
        $rules = [];
        foreach ($file as $line) {
            $rules[] = new Rule(trim($line));
        }

        return $rules;
    }

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

    public static function printLinesToCli(array $lines): void
    {
        foreach ($lines as $line) {
            echo $line . PHP_EOL;
        }
    }
}
