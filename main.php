<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\CLI\Menu;
use App\CLI\MenuAction;
use App\Executor\DBHyphenationExecutor;
use App\Executor\EmptyExecutor;
use App\Executor\FileHyphenationExecutor;
use App\Executor\HyphenateNotHyphenatedExecutor;
use App\Executor\LoaderExecutor;
use App\Loader\RuleFileLoader;
use App\Loader\WordFileLoader;
use App\Logger\SimpleLogger;
use App\Repository\WordRepository;

class Main
{
    private const string RULE_FILE = 'var/rules.txt';
    private const string LOGS_DIR = 'logs';

    public static function main(): void
    {
        $logger = new SimpleLogger(self::LOGS_DIR);
        try {
            $host = getenv('HOST');
            $port = getenv('PORT');
            $database = getenv('DATABASE');
            $username = getenv('USER');
            $password = getenv('PASSWORD');

            $connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$database",
                $username,
                $password,
                [PDO::MYSQL_ATTR_LOCAL_INFILE => true]
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $wordFileLoader = new LoaderExecutor(new WordFileLoader($connection));
            $ruleFileLoader = new LoaderExecutor(new RuleFileLoader($connection));
            $skip = new EmptyExecutor();
            $importMenu = new Menu(
                [
                    new MenuAction('Import words from file.', $wordFileLoader),
                    new MenuAction('Import rules from file.', $ruleFileLoader),
                    new MenuAction('Skip this step.', $skip)
                ]
            );
            $importMenu->show();

            $wordRepo = new WordRepository($connection);
            if($wordRepo->hasWordsWithoutHyphenation())
            {
                $hyphenateNotHyphenated = new HyphenateNotHyphenatedExecutor($connection);
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
                $dbSourceExecutor = new DBHyphenationExecutor($connection, $word);
                $fileSourceExecutor = new FileHyphenationExecutor(self::RULE_FILE, $word);
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
