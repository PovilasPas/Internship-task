<?php

declare(strict_types=1);

namespace App\Processor;

use App\Hyphenator\HyphenatorInterface;

class LineProcessor
{
    private HyphenatorInterface $hyphenator;

    public function __construct(HyphenatorInterface $hyphenator)
    {
        $this->hyphenator = $hyphenator;
    }

    public function process(array $rawLines): array
    {
        $pattern = '/[a-zA-Z]+/';
        $processedLines = [];
        foreach ($rawLines as  $idx => $rawLine) {
            $processedLine = '';
            $matches = [];
            preg_match_all($pattern, $rawLine, $matches, PREG_OFFSET_CAPTURE);
            for ($i = 0; $i < count($matches[0]); $i++) {
                $word = $matches[0][$i][0];
                $start = $matches[0][$i][1];
                $end = $start + strlen($word);
                $hyphenated = $this->hyphenator->hyphenate($word);
                if ($i + 1 < count($matches[0])) {
                    $next = $matches[0][$i + 1][1];
                    $processedLine .= $hyphenated . substr($rawLine, $end, $next - $end);
                } else {
                    $processedLine .= $hyphenated . substr($rawLine, $end);
                }
            }
            $processedLines[] = $processedLine;
            echo $idx . PHP_EOL;
        }
        return $processedLines;
    }
}