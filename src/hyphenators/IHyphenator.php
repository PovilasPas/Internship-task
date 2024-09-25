<?php

namespace App\hyphenators;

interface IHyphenator
{
    public function hyphenate(string $word) : string;
}
