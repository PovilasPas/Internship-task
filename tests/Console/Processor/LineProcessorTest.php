<?php

declare(strict_types=1);

namespace Test\Console\Processor;

use App\Console\Hyphenator\HyphenationResult;
use App\Console\Hyphenator\HyphenatorInterface;
use App\Console\Processor\LineProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LineProcessorTest extends TestCase
{
    public static function lineProvider(): array
    {
        return [
            [
                [
                    'If once you start down the dark path, forever will it dominate',
                    'your destiny, consume you it will, as it did Obi-Wan\'s',
                    'apprentice.'
                ],
            ],
            [
                [
                    'Most wels catfish are mainly about 1.3-1.6 m (4 ft 3 in-5 ft 3',
                    'in) long; fish longer than 2 m (6 ft 7 in) are normally a rarity.'
                ],
            ],
            [
                [
                    'first',
                    'would',
                    'services',
                    'these',
                    'people',
                    'health',
                    'products'
                ],
            ]
        ];
    }

    #[DataProvider('lineProvider')]
    public function testLineProcessing(array $lines): void
    {
        $hyphenator = $this->createMock(HyphenatorInterface::class);
        $hyphenator     
            ->expects($this->any())
            ->method('hyphenate')
            ->willReturnCallback(
                fn (string $word): HyphenationResult => new HyphenationResult($word, [])
            );
        $processor = new LineProcessor($hyphenator);
        $expected = $lines;

        $actual = $processor->process($lines);

        $this->assertSame($expected, $actual);
    }
}
