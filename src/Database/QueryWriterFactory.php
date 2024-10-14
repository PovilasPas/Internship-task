<?php

declare(strict_types=1);

namespace App\Database;

class QueryWriterFactory
{
    public function createWriter(?QueryType $type): QueryWriterInterface
    {
        switch ($type) {
            case QueryType::SELECT:
                return new SelectQueryWriter();
            case QueryType::INSERT:
                return new InsertQueryWriter();
            case QueryType::UPDATE:
                return new UpdateQueryWriter();
            case QueryType::DELETE:
                return new DeleteQueryWriter();
            default:
                throw new \InvalidArgumentException('Unsupported query type: ' . $type->value);
        }
    }
}
