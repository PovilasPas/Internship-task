<?php

declare(strict_types=1);

namespace App\Console\Hyphenator;

interface HyphenatorInterface
{
    public function hyphenate(string $word): HyphenationResult;
}
