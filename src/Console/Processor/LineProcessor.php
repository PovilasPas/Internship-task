<?php

declare(strict_types=1);

namespace App\Console\Processor;

use App\Console\Hyphenator\HyphenatorInterface;

class LineProcessor
{
    public function __construct(
        private readonly HyphenatorInterface $hyphenator
    ) {

    }

    public function process(array $rawLines): array
    {
        $pattern = '/[a-zA-Z]+/';
        $processedLines = [];
        foreach ($rawLines as $rawLine) {
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
                    $processedLine .= $hyphenated->getWord() . substr($rawLine, $end, $next - $end);
                } else {
                    $processedLine .= $hyphenated->getWord() . substr($rawLine, $end);
                }
            }
            $processedLines[] = $processedLine;
        }
        return $processedLines;
    }
}
