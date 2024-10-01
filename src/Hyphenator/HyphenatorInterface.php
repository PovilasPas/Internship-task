<?php

declare(strict_types=1);

namespace App\Hyphenator;

interface HyphenatorInterface
{
    public function hyphenate(string $word): HyphenationResult;
}
