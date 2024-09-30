<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\Cache\SimpleCache;
use App\Hyphenator\ArrayHyphenator;
use App\Hyphenator\RegexHyphenator;
use App\Processor\LineProcessor;
use App\Logger\SimpleLogger;
use App\IOUtils;
use App\Timer;

class Main
{
    private const string RULE_FILE = 'var/rules.txt';
    private const string TO_HYPHENATE = 'var/data.txt';
    private const string RESULT_FILE = 'var/result.txt';
    private const string LOGS_DIR = "logs";

    public static function main(): void
    {
        $mc = new Memcached();
        $mc->addServer('127.0.0.1', 11211);
        $cache = new SimpleCache($mc);
        $key = 'data';
        if ($cache->has($key)) {
            $rules = $cache->get($key);
        } else {
            $rules = IOUtils::readFile(self::RULE_FILE);
            $cache->set($key, $rules);
        }

        $logger = new SimpleLogger(self::LOGS_DIR);
        $logger->info("Starting processing...");

        $rawLines = IOUtils::readFile(self::TO_HYPHENATE);

        $hyphenator = new ArrayHyphenator($rules);
        $processor = new LineProcessor($hyphenator);

        $timer = new Timer();
        $timer->startTimer();
        $processedLines = $processor->process($rawLines);
        $timer->stopTimer();
        echo $timer->getElapsed() . PHP_EOL;

//        $result = $hyphenator->hyphenate('contact');

        IOUtils::writeFile(self::RESULT_FILE, $processedLines);
    }
}

Main::main();
