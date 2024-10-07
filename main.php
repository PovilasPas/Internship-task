<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\Console\CLI\Menu;
use App\Console\CLI\MenuAction;
use App\Console\Executor\DBHyphenationExecutor;
use App\Console\Executor\EmptyExecutor;
use App\Console\Executor\FileHyphenationExecutor;
use App\Console\Executor\HyphenateNotHyphenatedExecutor;
use App\Console\Executor\LoaderExecutor;
use App\Console\Hyphenator\ArrayHyphenator;
use App\Console\Loader\RuleFileLoader;
use App\Console\Loader\WordFileLoader;
use App\Console\Processor\DBProcessor;
use App\DB\ConnectionManager;
use App\Logger\SimpleLogger;
use App\Repository\MatchRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class Main
{
    private const string RULE_FILE = 'var/rules.txt';
    private const string LOGS_DIR = 'logs';

    public static function main(): void
    {
        $logger = new SimpleLogger(self::LOGS_DIR);
        try {
            $connection = ConnectionManager::getConnection();
            $wordRepository = new WordRepository($connection);
            $ruleRepository = new RuleRepository($connection);
            $matchRepository = new MatchRepository($connection);

            $hyphenator = new ArrayHyphenator($ruleRepository->getRules());
            $processor = new DBProcessor(
                $wordRepository,
                $ruleRepository,
                $matchRepository,
                $connection,
                $hyphenator
            );

            $wordFileLoader = new LoaderExecutor(new WordFileLoader($wordRepository));
            $ruleFileLoader = new LoaderExecutor(new RuleFileLoader($ruleRepository));
            $skip = new EmptyExecutor();

            $importMenu = new Menu(
                [
                    new MenuAction('Import words from file.', $wordFileLoader),
                    new MenuAction('Import rules from file.', $ruleFileLoader),
                    new MenuAction('Skip this step.', $skip)
                ]
            );
            $importMenu->show();

            if($wordRepository->hasWordsWithoutHyphenation())
            {
                $hyphenateNotHyphenated = new HyphenateNotHyphenatedExecutor(
                    $wordRepository,
                    $processor
                );
                echo 'Do you want to hyphenate unhyphenated words in database?' . PHP_EOL;
                $choiceMenu = new Menu(
                    [
                        new MenuAction('Yes.', $hyphenateNotHyphenated),
                        new MenuAction('No.', $skip)
                    ]
                );
                $choiceMenu->show();
            }

            $word = trim(readline('Enter a word to be hyphenated (leave empty to skip this step): '));

            if(strlen($word) > 0) {
                $dbSourceExecutor = new DBHyphenationExecutor($wordRepository, $ruleRepository, $processor, $word);
                $fileSourceExecutor = new FileHyphenationExecutor(self::RULE_FILE, $hyphenator, $word);
                $sourceMenu = new Menu(
                    [
                        new MenuAction('Use database for hyphenation.', $dbSourceExecutor),
                        new MenuAction('Use rule file for hyphenation.', $fileSourceExecutor),
                    ]
                );
                $sourceMenu->show();
            }
        } catch (PDOException $e) {
            $logger->error($e->getMessage());
        }
    }
}

Main::main();
