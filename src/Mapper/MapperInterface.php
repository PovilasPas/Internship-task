<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Model\ModelInterface;

interface MapperInterface
{
    public function serialize(ModelInterface $model): ?array;
    public function deserialize(array $data): ModelInterface;
}
