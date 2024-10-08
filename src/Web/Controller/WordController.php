<?php

declare(strict_types=1);

namespace App\Web\Controller;

use App\Mapper\WordMapper;
use App\Model\Word;
use App\Repository\WordRepository;
use App\Web\Request\Request;
use App\Web\Response\JsonResponse;
use App\Web\Response\Response;

class WordController
{
    public function __construct(
        private readonly WordRepository $repo
    ) {

    }

    public function list(): Response {
        $words = $this->repo->getWords();
        $mapper = new WordMapper();
        $serialized = array_map(fn (Word $item): ?array => $mapper->serialize($item), $words);

        return new JsonResponse(
            [],
            $serialized
        );
    }

    public function create(Request $request): Response {
        $mapper = new WordMapper();
        $deserialized = $mapper->deserialize($request->getData());
        $this->repo->insertWord($deserialized);
        $id = (int) $this->repo->getLastInsertedId();
        $inserted = $this->repo->getWord($id);
        $serialized = $mapper->serialize($inserted);
        return new JsonResponse(
            [],
            $serialized,
            201
        );
    }

    public function update(int $id, Request $request): Response {
        $word = $this->repo->getWord($id);
        if ($word === null) {
            return new JsonResponse(
                [],
                ['message' => 'Not found.'],
                404
            );
        }
        $mapper = new WordMapper();
        $deserialized = $mapper->deserialize($request->getData());
        $this->repo->updateWord($id, $deserialized);
        $updated = $this->repo->getWord($id);
        $serialized = $mapper->serialize($updated);
        return new JsonResponse(
            [],
            $serialized
        );
    }

    public function delete(int $id): Response {
        $word = $this->repo->getWord($id);
        if ($word === null) {
            return new JsonResponse(
                [],
                ['message' => 'Not found.'],
                404
            );
        }
        $this->repo->deleteWord($id);
        return new JsonResponse(
            [],
            [],
            204
        );
    }
}
