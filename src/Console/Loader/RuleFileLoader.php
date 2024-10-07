<?php

declare(strict_types=1);

namespace App\Console\Loader;

use App\Repository\RuleRepository;

class RuleFileLoader implements FileLoaderInterface
{
    public function __construct(
        private readonly RuleRepository $ruleRepository
    ) {

    }

    public function load(string $filePath): void
    {
        $this->ruleRepository->loadRulesFromFile($filePath);
    }
}
