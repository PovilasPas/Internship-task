<?php

declare(strict_types=1);

require_once 'autoload.php';

use App\CLI\Menu;
use App\CLI\MenuAction;
use App\Executor\DBHyphenationExecutor;
use App\Executor\EmptyExecutor;
use App\Executor\FileHyphenationExecutor;
use App\Executor\LoaderExecutor;
use App\Loader\RuleFileLoader;
use App\Loader\WordFileLoader;
use App\Logger\SimpleLogger;

class Main
{
    private const string RULE_FILE = 'var/rules.txt';
    private const string DATA_FILE = 'var/data.txt';
    private const string RESULT_FILE = 'var/result.txt';
    private const string LOGS_DIR = 'logs';

    public static function main(): void
    {
        $host = getenv('HOST');
        $port = getenv('PORT');
        $database = getenv('DATABASE');
        $username = getenv('USERNAME');
        $password = getenv('PASSWORD');
        $logger = new SimpleLogger(self::LOGS_DIR);
        try {
            $conn = new PDO(
                "mysql:host=$host;port=$port;dbname=$database",
                $username,
                $password,
                [PDO::MYSQL_ATTR_LOCAL_INFILE => true]
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $wordFileLoader = new LoaderExecutor(new WordFileLoader($conn));
            $ruleFileLoader = new LoaderExecutor(new RuleFileLoader($conn));
            $skip = new EmptyExecutor();
            $importMenu = new Menu(
                [
                    new MenuAction('Import words from file.', $wordFileLoader),
                    new MenuAction('Import rules from file.', $ruleFileLoader),
                    new MenuAction('Skip.', $skip)
                ]
            );
            $importMenu->show();

            $word = trim(readline('Enter a word to be hyphenated: '));

            $dbSourceExecutor = new DBHyphenationExecutor($conn, $word);
            $fileSourceExecutor = new FileHyphenationExecutor(self::RULE_FILE, $word);
            $sourceMenu = new Menu(
              [
                  new MenuAction('Use database for hyphenation.', $dbSourceExecutor),
                  new MenuAction('Use rule file for hyphenation.', $fileSourceExecutor),
                  new MenuAction('Skip.', $skip)
              ]
            );
            $sourceMenu->show();
        } catch (PDOException $e) {
            $logger->error($e->getMessage());
        }
    }
}

Main::main();
