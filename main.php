<?php

require_once "autoload.php";

use src\hyphenators\RegexHyphenator;
use src\IOUtils;

const RULE_FILE = "data.txt";

$rules = IOUtils::readFile(RULE_FILE);
$word = IOUtils::readFromConsole("Enter a word to be hyphenated: ");

$hyphenator = new RegexHyphenator($rules);
$start = microtime(true);
$hyphenated = $hyphenator->hyphenate($word);
$elapsed = round((microtime(true) - $start), 6);

IOUtils::writeToConsole($hyphenated);
IOUtils::writeToConsole("Hyphenation took: {$elapsed}s");