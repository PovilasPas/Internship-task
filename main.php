<?php

spl_autoload_register( function ($class ) {
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $filePath = $path . ".php";
    if(file_exists($filePath)) {
        include $filePath;
    }
});

use src\Hyphenator;
use src\Reader;
use src\Writer;

$rules = Reader::readFile("data.txt");

$word = Reader::readFromConsole("Enter a word to be hyphenated: ");

$hyphenator = new Hyphenator($rules);

$start = hrtime(true);
$hyphenated = $hyphenator->hyphenate($word);
$elapsedSecs = (hrtime(true) - $start)/1e+9;

Writer::writeToConsole($hyphenated);

Writer::writeToConsole("Hyphenation took: {$elapsedSecs}s");

