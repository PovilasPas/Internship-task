<?php

require_once "autoload.php";

use App\cache\SimpleCache;
use App\hyphenators\RegexHyphenator;
use App\loggers\SimpleLogger;
use App\IOUtils;

class Main {
    private const string RULE_FILE = "data.txt";

    public static function main()
    {
        $cache = new SimpleCache();
        $logger = new SimpleLogger("logs");

        if ($cache->has("data")) {
            $rules = $cache->get("data");
        } else {
            $rules = IOUtils::readFile(self::RULE_FILE);
            $cache->set("data", $rules);
        }

        $hyphenator = new RegexHyphenator($rules, $logger);

        $word = readline("Enter a word to be hyphenated: ");
        $logger->info("Word to be hyphenated: $word");

        $start = microtime(true);
        $hyphenated = $hyphenator->hyphenate($word);
        $elapsed = round((microtime(true) - $start), 6);

        $endMessage = "Hyphenation took: {$elapsed}s";
        echo $hyphenated . PHP_EOL;
        $logger->info($endMessage);
        echo $endMessage . PHP_EOL;
    }
}

Main::main();