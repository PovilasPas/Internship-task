<?php

use src\hyphenators\RegexHyphenator;
use src\IOUtils;

const RULE_FILE = "data.txt";

spl_autoload_register( function ($class ) {
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $filePath = $path . ".php";
    if(file_exists($filePath)) {
        include $filePath;
    }
});

$rules = IOUtils::readFile(RULE_FILE);
$word = IOUtils::readFromConsole("Enter a word to be hyphenated: ");

$hyphenator = new RegexHyphenator($rules);
$start = microtime(true);
$hyphenated = $hyphenator->hyphenate($word);
$elapsed = round((microtime(true) - $start), 6);

IOUtils::writeToConsole($hyphenated);
IOUtils::writeToConsole("Hyphenation took: {$elapsed}s");