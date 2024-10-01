<?php

declare(strict_types=1);

namespace App\Executor;

use App\Hyphenator\ArrayHyphenator;

class DBSourceHyphenatorExecutor implements ExecutorInterface
{
    public function __construct(private readonly \PDO $connection, private readonly string $wordToHyphenate)
    {

    }

    public function execute(): void
    {
        $query = 'SELECT * FROM words WHERE word LIKE ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$this->wordToHyphenate]);
        $word = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($word !== false && $word['hyphenated'] !== null) {
           echo $word['hyphenated'] . PHP_EOL;

           return;
        }

        if ($word === false) {
            $query = 'INSERT INTO words (word) VALUES (?)';
            $this->connection->prepare($query)->execute([$this->wordToHyphenate]);
            $id = $this->connection->lastInsertId();
        } else {
            $id = $word['id'];
        }

        $query = 'SELECT rule FROM rules';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rules = $statement->fetchAll(\PDO::FETCH_COLUMN);

        $hyphenator = new ArrayHyphenator($rules);
        $hyphenated = $hyphenator->hyphenate($this->wordToHyphenate);

        $query = 'UPDATE words SET hyphenated = ? WHERE id = ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$hyphenated, $id]);

        echo $hyphenated->getWord() . PHP_EOL;
    }
}
