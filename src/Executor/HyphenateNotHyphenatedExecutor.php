<?php

declare(strict_types=1);

namespace App\Executor;

use App\Hyphenator\ArrayHyphenator;
use App\Model\Rule;
use App\Processor\DBProcessor;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class HyphenateNotHyphenatedExecutor implements ExecutorInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function execute(): void
    {
        $wordRepo = new WordRepository($this->connection);
        $ruleRepo = new RuleRepository($this->connection);

        $notHyphenated = $wordRepo->getWordsWithoutHyphenation();

        $rules = array_map(function (Rule $rule) {
            return $rule->getRule();
        }, $ruleRepo->getRules());

        $hyphenator = new ArrayHyphenator($rules);

        $processor = new DBProcessor($this->connection, $hyphenator);

        $processor->process($notHyphenated);
    }
}
