<?php

declare(strict_types=1);

namespace App\Hyphenator;

class ArrayHyphenator implements HyphenatorInterface
{
    private array $rules;

    public function __construct(array $rules)
    {
        foreach($rules as $rule) {
            $this->rules[] = new HyphenationRule($rule);
        }
    }

    public function hyphenate(string $word): HyphenationResult
    {
        $word = new HyphenationWord($word);
        $patterns = [];
        foreach($this->rules as $rule) {
            $wasFound = false;
            $iFrom = -1;
            $iTo = -1;
            $from = -1;
            if (str_starts_with($rule->getRule(), '.') && $rule->matchesStart($word)) {
                $iFrom = 0;
                $iTo = min($word->getLevelsLength(), $rule->getLevelsLength());
                $from = 0;
                $wasFound = true;
            } elseif (str_ends_with($rule->getRule(), '.') && $rule->matchesEnd($word)) {
                $from = $word->getWordLength() - $rule->getRuleLength();
                $iFrom = abs(min($from, 0));
                $iTo = $rule->getLevelsLength() - $iFrom;
                $wasFound = true;
            } else {
                $from = $rule->matchesMiddle($word);
                if ($from >= 0) {
                    $from -= 1;
                    $iFrom = abs(min($from, 0));
                    $iTo = min($word->getLevelsLength() - $from, $rule->getLevelsLength());
                    $wasFound = true;
                }
            }

            if ($wasFound) {
                $word->updateLevels($rule, $iFrom, $iTo, $from);
                $patterns[] = $rule->getOriginal();
            }
        }

        $word = $word->getHyphenated();

        return new HyphenationResult($word, $patterns);
    }
}
