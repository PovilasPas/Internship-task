<?php

namespace App;

use App\hyphenators\IHyphenator;

class TaskUtils
{
    public static function HyphenateFile(string $path, IHyphenator $hyphenator): void
    {
        $pattern = "/[a-zA-Z]+/";
        $data = IOUtils::readFile($path);
        foreach ($data as $line) {
            $processedLine = "";
            $matches = [];
            preg_match_all($pattern, $line, $matches, PREG_OFFSET_CAPTURE);
            for ($i = 0; $i < count($matches[0]); $i++) {
                $word = $matches[0][$i][0];
                $startPos  = $matches[0][$i][1];
                $endPos = $startPos + strlen($word);
                $hyphenated = $hyphenator->hyphenate($word);
                if ($i + 1 < count($matches[0])) {
                    $nextPos = $matches[0][$i + 1][1];
                    $processedLine .= $hyphenated . substr($line, $endPos, $nextPos - $endPos);
                } else {
                    $processedLine .= $hyphenated . substr($line, $endPos);
                }
            }
            echo $processedLine . PHP_EOL;
        }
    }
}