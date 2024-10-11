<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\Console\CLI\Menu;
use App\Console\CLI\MenuAction;
use App\Dependency\DependencyLoader;

class Main
{
    public static function main(): void
    {
        $manager = DependencyLoader::loadDependencies();
        $logger = $manager->resolve('Logger');
        try {
            $wordFileLoader = $manager->resolve('WordLoaderExecutor');
            $ruleFileLoader = $manager->resolve('RuleLoaderExecutor');
            $skip = $manager->resolve('EmptyExecutor');

            $importMenu = new Menu(
                [
                    new MenuAction('Import words from file.', $wordFileLoader),
                    new MenuAction('Import rules from file.', $ruleFileLoader),
                    new MenuAction('Skip this step.', $skip)
                ]
            );
            $importMenu->show();

            $wordRepository = $manager->resolve('WordRepository');

            if($wordRepository->hasWordsWithoutHyphenation())
            {
                $hyphenateNotHyphenated = $manager->resolve('HyphenateNotHyphenatedExecutor');

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
                $databaseSourceExecutor = $manager->resolve('DatabaseHyphenationExecutor');
                $databaseSourceExecutor->setWord($word);
                $fileSourceExecutor = $manager->resolve('FileHyphenationExecutor');
                $fileSourceExecutor->setWord($word);
                $sourceMenu = new Menu(
                    [
                        new MenuAction('Use database for hyphenation.', $databaseSourceExecutor),
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
