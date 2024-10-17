<?php

declare(strict_types=1);

namespace App\Console\Hyphenator;


class ArrayHyphenator implements HyphenatorInterface
{
    /**
     * @param HyphenationRule[] $rules
     */
    public function __construct(
        private readonly array $rules,
    ) {

    }

    public function hyphenate(string $word): HyphenationResult
    {
        $word = new HyphenationWord($word);
        $patterns = [];
        foreach($this->rules as $rule) {
            $wasFound = false;
            $indexFrom = -1;
            $indexTo = -1;
            if (str_starts_with($rule->getRule(), '.') && $rule->matchesStart($word)) {
                $from = 0;
                $indexFrom = 0;
                $indexTo = min($word->getLevelsLength(), $rule->getLevelsLength());
                $wasFound = true;
            } elseif (str_ends_with($rule->getRule(), '.') && $rule->matchesEnd($word)) {
                $from = $word->getWordLength() - $rule->getRuleLength();
                $indexFrom = abs(min($from, 0));
                $indexTo = $rule->getLevelsLength() - $indexFrom;
                $wasFound = true;
            } else {
                $from = $rule->compareMiddle($word);
                if ($from >= 0) {
                    $from -= 1;
                    $indexFrom = abs(min($from, 0));
                    $indexTo = min($word->getLevelsLength() - $from, $rule->getLevelsLength());
                    $wasFound = true;
                }
            }

            if ($wasFound) {
                $word->updateLevels($rule, $indexFrom, $indexTo, $from);
                $patterns[] = $rule->getOriginal();
            }
        }

        $word = $word->getHyphenated();

        return new HyphenationResult($word, $patterns);
    }
}
