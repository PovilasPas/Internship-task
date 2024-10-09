<?php

declare(strict_types=1);

namespace App\Repository;

use App\IOUtils;
use App\Mapper\WordMapper;
use App\Model\Word;

class WordRepository implements RepositoryInterface
{
    public function __construct(
        private readonly \PDO $connection,
    ) {

    }

    public function getWords(): array
    {
        $query = 'SELECT * FROM words';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $words = array_map(fn (array $word): Word => new Word($word['word'], $word['id'], $word['hyphenated']), $data);

        return $words;
    }

    public function getWord(int $id): ?Word
    {
        $query = 'SELECT * FROM words WHERE id = ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        return $data !== false ? new Word($data['word'], $data['id'], $data['hyphenated']) : null;
    }

    public function insertWord(Word $word): void
    {
        $query = 'INSERT INTO words (word) VALUES (?)';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord()]);
    }

    public function updateWord(int $id, Word $word): void
    {
        $query = 'UPDATE words SET word = ?, hyphenated = ? WHERE id = ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord(), $word->getHyphenated(), $id]);
    }

    public function deleteWord(int $id): void
    {
        $query = 'DELETE FROM words WHERE id = ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
    }

    public function getWordByString(string $word): ?Word
    {
        $query = 'SELECT * FROM words WHERE word LIKE ?';
        $statement = $this->connection->prepare($query);
        $statement->execute([$word]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        return $data !== false ? new Word($data['word'], $data['id'], $data['hyphenated']) : null;
    }

    public function getLastInsertedId(): ?string
    {
        $id = $this->connection->lastInsertId('words');

        return $id !== false ? $id : null;
    }

    public function loadWordsFromFile(string $filePath): void
    {
        $words = IOUtils::ReadFile($filePath);
        $query = 'INSERT IGNORE INTO words (word) VALUES '
            . rtrim(str_repeat('(?), ', count($words)), ', ');
        $this->connection->prepare($query)->execute($words);
    }

    public function getWordsWithoutHyphenation(): array
    {
        $query = 'SELECT * FROM words WHERE hyphenated IS NULL';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $words = array_map(fn (array $word): Word => new Word($word['word'], $word['id'], $word['hyphenated']), $data);

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
