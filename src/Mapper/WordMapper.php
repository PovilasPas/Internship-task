<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Model\ModelInterface;
use App\Model\Word;

class WordMapper implements MapperInterface
{
    public function serialize(ModelInterface $model): array
    {
        if (!$model instanceof Word) {
            throw new \InvalidArgumentException('Model must be instance of Word');
        }

        return [
            'id' => $model->getId(),
            'word' => $model->getWord(),
            'hyphenated' => $model->getHyphenated(),
        ];
    }

    public function deserialize(array $data): Word
    {
        $word = $data['word'];
        $id = $data['id'] ?? null;
        $hyphenated = $data['hyphenated'] ?? null;

        return new Word($word, $id, $hyphenated);
    }
}
