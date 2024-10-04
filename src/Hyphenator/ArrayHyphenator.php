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

    public function hyphenate(string $word): string
    {
        $word = new HyphenationWord($word);
        foreach($this->rules as $rule) {
            if (str_starts_with($rule->getRule(), '.') && $rule->matchesStart($word)) {
                $iFrom = 0;
                $iTo = min($word->getLevelsLength(), $rule->getLevelsLength());
                $from = 0;
                $word->updateLevels($rule, $iFrom, $iTo, $from);
            } elseif (str_ends_with($rule->getRule(), '.') && $rule->matchesEnd($word)) {
                $from = $word->getWordLength() - $rule->getRuleLength();
                $iFrom = abs(min($from, 0));
                $iTo = $rule->getLevelsLength() - $iFrom;
                $word->updateLevels($rule, $iFrom, $iTo, $from);
            } else {
                $from = $rule->matchesMiddle($word);
                if ($from >= 0) {
                    $from -= 1;
                    $iFrom = abs(min($from, 0));
                    $iTo = min($word->getLevelsLength() - $from, $rule->getLevelsLength());
                    $word->updateLevels($rule, $iFrom, $iTo, $from);
                }
            }
        }

        return $word->getHyphenated();
    }
}
