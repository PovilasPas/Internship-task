<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\Cache\SimpleCache;
use App\CLI\Menu;
use App\CLI\MenuAction;
use App\Executor\DBSourceHyphenatorExecutor;
use App\Executor\EmptyExecutor;
use App\Executor\FileSourceHyphenatorExecutor;
use App\Executor\LoaderExecutor;
use App\Hyphenator\ArrayHyphenator;
use App\Hyphenator\RegexHyphenator;
use App\Loader\RuleFileLoader;
use App\Loader\WordFileLoader;
use App\Processor\LineProcessor;
use App\Logger\SimpleLogger;
use App\IOUtils;
use App\Timer;

class Main
{
    private const string RULE_FILE = 'var/rules.txt';
    private const string DATA_FILE = 'var/data.txt';
    private const string RESULT_FILE = 'var/result.txt';
    private const string LOGS_DIR = "logs";

    public static function main(): void
    {
        $logger = new SimpleLogger(self::LOGS_DIR);
        try {
            $conn = new PDO(
                "mysql:host=127.0.0.1;port=3306;dbname=internship_task",
                "app",
                "twoday24",
                [PDO::MYSQL_ATTR_LOCAL_INFILE => true]
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $importMenu = new Menu(
                [
                    new MenuAction("Import words from file.", new LoaderExecutor(new WordFileLoader($conn))),
                    new MenuAction("Import rules from file.", new LoaderExecutor(new RuleFileLoader($conn))),
                    new MenuAction("Skip.", new EmptyExecutor())
                ]
            );
            $importMenu->getSelection()->execute();

            $wordToHyphenate = trim(readline("Enter a word to be hyphenated: "));

        } catch (PDOException $e) {
            $logger->error($e->getMessage());
        }
    }
}

Main::main();
