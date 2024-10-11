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
use App\Console\Processor\DBProcessor;
use App\DB\ConnectionManager;
use App\DB\QueryBuilder;
use App\DB\QueryWriterFactory;
use App\IOUtils;
use App\Model\Rule;
use App\Paths;
use App\Repository\MatchRepository;
use App\Repository\RuleRepository;
use App\Repository\WordRepository;

class DependencyLoader
{
    public static function loadDependencies(): DependencyManager
    {
        $manager = new DependencyManager();
        $manager->register('Database', fn(): \PDO => ConnectionManager::getConnection());
        $manager->register('QueryWriterFactory', fn(): QueryWriterFactory => new QueryWriterFactory());
        $manager->register('QueryBuilder', function (DependencyManager $manager): QueryBuilder {
            $factory = $manager->resolve('QueryWriterFactory');

            return new QueryBuilder($factory);
        });
        $manager->register('WordRepository', function (DependencyManager $manager): WordRepository {
            $connection = $manager->resolve('Connection');
            $builder = $manager->resolve('QueryBuilder');

            return new WordRepository($connection, $builder);
        });
        $manager->register('RuleRepository', function (DependencyManager $manager): RuleRepository {
           $connection = $manager->resolve('Connection');
           $builder = $manager->resolve('QueryBuilder');

           return new RuleRepository($connection, $builder);
        });
        $manager->register('MatchRepository', function (DependencyManager $manager): MatchRepository {
            $connection = $manager->resolve('Connection');
            $builder = $manager->resolve('QueryBuilder');

            return new MatchRepository($connection, $builder);
        });
        $manager->register('DatabaseArrayHyphenator', function (DependencyManager $manager): ArrayHyphenator {
            $ruleRepository = $manager->resolve('RuleRepository');
            $rules = array_map(
                fn (Rule $item): HyphenationRule => new HyphenationRule($item->getRule()),
                $ruleRepository->getRules(),
            );

            return new ArrayHyphenator($rules);
        });
        $manager->register('FileArrayHyphenator', function (): ArrayHyphenator {
            $rules = array_map(
                fn (string $item): HyphenationRule => new HyphenationRule($item),
                IOUtils::readFile(Paths::RULE_FILE)
            );

            return new ArrayHyphenator($rules);
        });
        $manager->register('DatabaseProcessor', function (DependencyManager $manager): DBProcessor {
            $wordRepository = $manager->resolve('WordRepository');
            $ruleRepository = $manager->resolve('RuleRepository');
            $matchRepository = $manager->resolve('MatchRepository');
            $connection = $manager->resolve('Database');
            $hyphenator = $manager->resolve('DatabaseArrayHyphenator');

            return new DBProcessor(
                $wordRepository,
                $ruleRepository,
                $matchRepository,
                $connection,
                $hyphenator,
            );
        });
        $manager->register('WordFileLoader', function (DependencyManager $manager): WordFileLoader {
            $wordRepository = $manager->resolve('WordRepository');

            return new WordFileLoader($wordRepository);
        });
        $manager->register('RuleFileLoader', function (DependencyManager $manager): RuleFileLoader {
            $ruleRepository = $manager->resolve('RuleRepository');

            return new RuleFileLoader($ruleRepository);
        });
        $manager->register('WordLoaderExecutor', function (DependencyManager $manager): LoaderExecutor {
            $loader = $manager->resolve('WordFileLoader');
            return new LoaderExecutor($loader);
        });
        $manager->register('RuleLoaderExecutor', function (DependencyManager $manager): LoaderExecutor {
            $loader = $manager->resolve('RuleFileLoader');
            return new LoaderExecutor($loader);
        });
        $manager->register('EmptyExecutor', fn (): EmptyExecutor => new EmptyExecutor());
        $manager->register(
            'HyphenateNotHyphenatedExecutor',
            function (DependencyManager $manager): HyphenateNotHyphenatedExecutor {
                $wordRepository = $manager->resolve('WordRepository');
                $processor = $manager->resolve('DatabaseProcessor');
                return new HyphenateNotHyphenatedExecutor($wordRepository, $processor);
            },
        );
        $manager->register(
            'DatabaseHyphenationExecutor',
            function (DependencyManager $manager): DatabaseHyphenationExecutor {
                $wordRepository = $manager->resolve('WordRepository');
                $ruleRepository = $manager->resolve('RuleRepository');
                $processor = $manager->resolve('DatabaseProcessor');
                return new DatabaseHyphenationExecutor($wordRepository, $ruleRepository, $processor);
            },
        );
        $manager->register(
            'FileHyphenationExecutor',
            function (DependencyManager $manager): FileHyphenationExecutor {
                $hyphenator = $manager->resolve('FileArrayHyphenator');
                return new FileHyphenationExecutor(Paths::RULE_FILE, $hyphenator);
            }
        );

        return $manager;
    }
}
