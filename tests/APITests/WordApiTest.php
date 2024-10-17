<?php

declare(strict_types=1);

namespace Test\APITests;

use App\Database\QueryBuilder;
use App\Database\QueryWriterFactory;
use App\Mapper\WordMapper;
use App\Model\Word;
use App\Repository\WordRepository;
use PHPUnit\Framework\Attributes\DataProvider;

class WordApiTest extends ApiTest
{
    public static function wordProvider(): array
    {
        return [
            'Multiple words in database' => [
                [
                    new Word('mistranslate'),
                    new Word('products'),
                    new Word('university'),
                    new Word('management'),
                ],
            ],
            'Single word in database' => [
                [
                    new Word('technology'),
                ],
            ],
            'No words in database' => [
                [],
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

        $url = self::$host . '/api/words';

        $actual = json_decode(file_get_contents($url), true);
        $this->assertSame(json_last_error(), JSON_ERROR_NONE);
        $this->assertSame($expected, $actual);
    }
}
