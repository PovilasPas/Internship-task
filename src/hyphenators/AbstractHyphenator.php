<?php

namespace App\hyphenators;

abstract class AbstractHyphenator implements IHyphenator
{
    protected array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }
}
