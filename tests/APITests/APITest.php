<?php

declare(strict_types=1);

namespace Test\APITests;

use App\DB\ConnectionManager;
use App\DB\QueryBuilder;
use App\DB\QueryWriterFactory;
use App\Mapper\WordMapper;
use App\Model\Word;
use App\Repository\WordRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class APITest extends TestCase
{
    private static \PDO $connection;

    public static function setUpBeforeClass(): void
    {
        self::$connection = ConnectionManager::getConnection();
    }

    public function tearDown(): void
    {
        $query = 'SET FOREIGN_KEY_CHECKS=0;'
            . 'TRUNCATE TABLE words;'
            . 'SET FOREIGN_KEY_CHECKS=1;';
        self::$connection->exec($query);
    }

    public static function wordProvider(): array
    {
        return [
            [
                [
                    new Word('mistranslate'),
                    new Word('products'),
                    new Word('university'),
                    new Word('management'),
                ]
            ],
            [
                [
                    new Word('technology'),
                ]
            ],
            [
                []
            ]
        ];
    }

    #[DataProvider('wordProvider')]
    public function testListWordsApi(array $words): void
    {
        $factory = new QueryWriterFactory();
        $builder = new QueryBuilder($factory);
        $wordRepository = new WordRepository(self::$connection, $builder);
        $wordRepository->insertWords($words);

        $inserted = $wordRepository->getWords();
        $mapper = new WordMapper();
        $expected = array_map(fn (Word $item): array => $mapper->serialize($item), $inserted);

        $url = 'http://localhost:8000/api/words';

        $actual = json_decode(file_get_contents($url), true);
        $this->assertSame(json_last_error(), JSON_ERROR_NONE);
        $this->assertSame($expected, $actual);
    }
}
