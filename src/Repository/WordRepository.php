<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Word;

class WordRepository implements RepositoryInterface
{
    public function __construct(private readonly \PDO $connection)
    {

    }

    public function findByWord(string $word): ?Word
    {
        $query = 'SELECT * FROM words WHERE word LIKE ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        return $data !== false ? new Word($data['word'], $data['id'], $data['hyphenated']) : null;
    }

    public function insertWord(Word $word): void
    {
        $query = 'INSERT INTO words (word) VALUES (?)';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord()]);
    }

    public function getLastInsertedId(): ?string
    {
        $id = $this->connection->lastInsertId();
        return $id !== false ? $id : null;
    }

    public function updateWord(int $id, Word $word): void
    {
        $query = 'UPDATE words SET word = ?, hyphenated = ? WHERE id = ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord(), $word->getHyphenated(), $id]);
    }

    public function loadWordsFromFile(string $filePath): void
    {
        $query = 'LOAD DATA LOCAL INFILE ? IGNORE INTO TABLE words FIELDS TERMINATED BY \'\' (word)';
        $this->connection->prepare($query)->execute([$filePath]);
    }

    public function getWordsWithoutHyphenation(): array
    {
        $query = 'SELECT * FROM words WHERE hyphenated IS NULL';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $words = array_map(function (array $word) {
            return new Word($word['word'], $word['id'], $word['hyphenated']);
        }, $data);
        return $words;
    }

    public function hasWordsWithoutHyphenation(): bool
    {
        $query = 'SELECT COUNT(*) as `count` FROM words WHERE hyphenated IS NULL';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        return $data['count'] > 0;
    }
}
