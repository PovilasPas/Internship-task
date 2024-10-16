<?php

declare(strict_types=1);

namespace Test\Repository;

use App\Database\QueryBuilder;
use App\Model\PatternMatch;
use App\Repository\MatchRepository;
use PHPUnit\Framework\TestCase;

class MatchRepositoryTest extends TestCase
{
    public function testInsertZeroMatches(): void
    {
        $matches = [];
        $builder = $this->createMock(QueryBuilder::class);
        $connection = $this->createMock(\PDO::class);

        $builder->expects($this->never())->method('insert');
        $connection->expects($this->never())->method('prepare');

        $matchRepository = new MatchRepository($connection, $builder);

        $matchRepository->insertMatches($matches);
    }

    public function testInsertMultipleMatches(): void
    {
        $matches = [
            new PatternMatch(0, 0),
            new PatternMatch(0, 1),
            new PatternMatch(0, 2),
            new PatternMatch(0, 3),
        ];
        $builder = $this->createMock(QueryBuilder::class);
        $connection = $this->createMock(\PDO::class);
        $statement = $this->createMock(\PDOStatement::class);

        $insertArguments = ['matches', ['rule_id', 'word_id'], 4];
        $builder->expects($this->once())->method('insert')->with(...$insertArguments)->willReturnSelf();
        $builder->expects($this->once())->method('get')->willReturn('');

        $connection->expects($this->once())->method('prepare')->with('')->willReturn($statement);

        $executeArguments = [[0, 0, 1, 0, 2, 0, 3, 0]];
        $statement->expects($this->once())->method('execute')->with(...$executeArguments);

        $matchRepository = new MatchRepository($connection, $builder);

        $matchRepository->insertMatches($matches);
    }
}
