<?php

declare(strict_types=1);

namespace App\Dependency;

use App\Console\Executor\DatabaseHyphenationExecutor;
use App\Console\Executor\EmptyExecutor;
use App\Console\Executor\FileHyphenationExecutor;
use App\Console\Executor\HyphenateNotHyphenatedExecutor;
use App\Console\Executor\LoaderExecutor;
use App\Console\Hyphenator\ArrayHyphenator;
use App\Console\Hyphenator\HyphenationRule;
use App\Console\Loader\RuleFileLoader;
use App\Console\Loader\WordFileLoader;
use App\Console\Processor\DatabaseProcessor;
use App\Database\ConnectionManager;
use App\Database\QueryBuilder;
use App\Database\QueryWriterFactory;
use App\IOUtils;
use App\Logger\SimpleLogger;
use App\Model\Rule;
use App\Paths;
use App\Repository\MatchRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;
use App\Web\Router\Router;

final class DependencyLoader
{
    private static DependencyManager $manager;

    public static function load(): DependencyManager
    {
        if (isset(self::$manager)) {
            return self::$manager;
        }

        self::$manager = new DependencyManager();
        self::$manager->register('Database', fn(): \PDO => ConnectionManager::getConnection());
        self::$manager->register('Logger', fn(): SimpleLogger => new SimpleLogger(Paths::LOGS_DIR));
        self::$manager->register('QueryWriterFactory', fn(): QueryWriterFactory => new QueryWriterFactory());
        self::$manager->register('Router', fn(): Router => new Router());
        self::$manager->register('QueryBuilder', function (DependencyManager $manager): QueryBuilder {
            $factory = $manager->resolve('QueryWriterFactory');

            return new QueryBuilder($factory);
        });
        self::$manager->register('WordRepository', function (DependencyManager $manager): WordRepository {
            $connection = $manager->resolve('Database');
            $builder = $manager->resolve('QueryBuilder');

            return new WordRepository($connection, $builder);
        });
        self::$manager->register('RuleRepository', function (DependencyManager $manager): RuleRepository {
            $connection = $manager->resolve('Database');
            $builder = $manager->resolve('QueryBuilder');

            return new RuleRepository($connection, $builder);
        });
        self::$manager->register('MatchRepository', function (DependencyManager $manager): MatchRepository {
            $connection = $manager->resolve('Database');
            $builder = $manager->resolve('QueryBuilder');

            return new MatchRepository($connection, $builder);
        });
        self::$manager->register('DatabaseArrayHyphenator', function (DependencyManager $manager): ArrayHyphenator {
            $ruleRepository = $manager->resolve('RuleRepository');
            $rules = array_map(
                fn(Rule $item): HyphenationRule => new HyphenationRule($item->getRule()),
                $ruleRepository->getRules(),
            );

            return new ArrayHyphenator($rules);
        });
        self::$manager->register('FileArrayHyphenator', function (): ArrayHyphenator {
            $rules = array_map(
                fn(string $item): HyphenationRule => new HyphenationRule($item),
                IOUtils::readFile(Paths::RULE_FILE)
            );

            return new ArrayHyphenator($rules);
        });
        self::$manager->register('DatabaseProcessor', function (DependencyManager $manager): DatabaseProcessor {
            $wordRepository = $manager->resolve('WordRepository');
            $ruleRepository = $manager->resolve('RuleRepository');
            $matchRepository = $manager->resolve('MatchRepository');
            $connection = $manager->resolve('Database');
            $hyphenator = $manager->resolve('DatabaseArrayHyphenator');

            return new DatabaseProcessor(
                $wordRepository,
                $ruleRepository,
                $matchRepository,
                $connection,
                $hyphenator,
            );
        });
        self::$manager->register('WordFileLoader', function (DependencyManager $manager): WordFileLoader {
            $wordRepository = $manager->resolve('WordRepository');

            return new WordFileLoader($wordRepository);
        });
        self::$manager->register('RuleFileLoader', function (DependencyManager $manager): RuleFileLoader {
            $ruleRepository = $manager->resolve('RuleRepository');

            return new RuleFileLoader($ruleRepository);
        });
        self::$manager->register('WordLoaderExecutor', function (DependencyManager $manager): LoaderExecutor {
            $loader = $manager->resolve('WordFileLoader');

            return new LoaderExecutor($loader);
        });
        self::$manager->register('RuleLoaderExecutor', function (DependencyManager $manager): LoaderExecutor {
            $loader = $manager->resolve('RuleFileLoader');

            return new LoaderExecutor($loader);
        });
        self::$manager->register('EmptyExecutor', fn(): EmptyExecutor => new EmptyExecutor());
        self::$manager->register(
            'HyphenateNotHyphenatedExecutor',
            function (DependencyManager $manager): HyphenateNotHyphenatedExecutor {
                $wordRepository = $manager->resolve('WordRepository');
                $processor = $manager->resolve('DatabaseProcessor');

                return new HyphenateNotHyphenatedExecutor($wordRepository, $processor);
            },
        );
        self::$manager->register(
            'DatabaseHyphenationExecutor',
            function (DependencyManager $manager): DatabaseHyphenationExecutor {
                $wordRepository = $manager->resolve('WordRepository');
                $ruleRepository = $manager->resolve('RuleRepository');
                $processor = $manager->resolve('DatabaseProcessor');

                return new DatabaseHyphenationExecutor($wordRepository, $ruleRepository, $processor);
            },
        );
        self::$manager->register(
            'FileHyphenationExecutor',
            function (DependencyManager $manager): FileHyphenationExecutor {
                $hyphenator = $manager->resolve('FileArrayHyphenator');

                return new FileHyphenationExecutor($hyphenator);
            }
        );

        return self::$manager;
    }
}
