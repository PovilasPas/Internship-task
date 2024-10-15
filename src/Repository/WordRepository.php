<?php

declare(strict_types=1);

namespace App\Repository;

use App\IOUtils;
use App\DB\QueryBuilder;
use App\Model\Word;

class WordRepository implements RepositoryInterface
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly QueryBuilder $builder,
    ) {

    }

    public function getWords(): array
    {
        $query = $this->builder->select('words')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $words = array_map(fn (array $word): Word => new Word($word['word'], $word['id'], $word['hyphenated']), $data);

        return $words;
    }

    public function getWord(int $id): ?Word
    {
        $query = $this->builder->select('words')->where('id = ?')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        return $data !== false ? new Word($data['word'], $data['id'], $data['hyphenated']) : null;
    }

    public function insertWord(Word $word): void
    {
        $query = $this->builder->insert('words', ['word'])->get();
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord()]);
    }

    public function insertWords(array $words): void
    {
        if (empty($words)) {
            return;
        }
        $query = $this->builder->insert('words', ['word'], count($words))->get();
        $data = [];
        foreach ($words as $word) {
            $data[] = $word->getWord();
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($data);
    }

    public function updateWord(int $id, Word $word): void
    {
        $query = $this->builder->update('words', ['word', 'hyphenated'])->where('id = ?')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute([$word->getWord(), $word->getHyphenated(), $id]);
    }

    public function deleteWord(int $id): void
    {
        $query = $this->builder->delete('words')->where('id = ?')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute([$id]);
    }

    public function getWordByString(string $word): ?Word
    {
        $query = $this->builder->select('words')->where('word LIKE ?')->get();
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
        $query = $this->builder->insert('words', ['word'], count($words), true)->get();
        $this->connection->prepare($query)->execute($words);
    }

    public function getWordsWithoutHyphenation(): array
    {
        $query = $this->builder->select('words')->where('hyphenated IS NULL')->get();
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $words = array_map(fn (array $word): Word => new Word($word['word'], $word['id'], $word['hyphenated']), $data);

        return $words;
    }

    public function hasWordsWithoutHyphenation(): bool
    {
        $query = $this->builder
            ->select('words', ['COUNT(*) as `count`'])
            ->where('hyphenated IS NULL')
            ->get();
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        return $data['count'] > 0;
    }
}
