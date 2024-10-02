<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Word;

class WordRepository extends AbstractRepository
{
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
}
