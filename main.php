<?php

require_once "autoload.php";

use App\hyphenators\RegexHyphenator;
use App\loggers\SimpleLogger;
use App\IOUtils;

const RULE_FILE = "data.txt";

$rules = IOUtils::readFile(RULE_FILE);
$word = readline("Enter a word to be hyphenated: ");

$logger = new SimpleLogger("logs");
$hyphenator = new RegexHyphenator($rules, $logger);

$logger->info("Word to be hyphenated: $word");

$start = microtime(true);
$hyphenated = $hyphenator->hyphenate($word);
$elapsed = round((microtime(true) - $start), 6);

$endMessage = "Hyphenation took: {$elapsed}s";

echo $hyphenated . PHP_EOL;
$logger->info($endMessage);
echo $endMessage . PHP_EOL;
