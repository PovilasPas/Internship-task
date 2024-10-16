<?php

declare(strict_types=1);

namespace Test\DB;

use App\Database\JoinType;
use App\Database\OperatorType;
use App\Database\QueryBuilder;
use App\Database\QueryWriterFactory;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    private static QueryBuilder $builder;

    public static function setUpBeforeClass(): void
    {
        $factory = new QueryWriterFactory();
        self::$builder = new QueryBuilder($factory);
    }

    public function testSelect(): void
    {
        $table = 'words';
        $expected = 'SELECT * FROM words';

        $factory = new QueryWriterFactory();
        $builder = new QueryBuilder($factory);
        $actual = $builder->select($table)->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithCustomFields(): void
    {
        $table = 'words';
        $fields = ['id', 'word'];
        $expected = 'SELECT id, word FROM words';

        $actual = self::$builder->select($table, $fields)->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithSingleWhereCondition(): void
    {
        $table = 'words';
        $condition = 'word LIKE ?';
        $expected = 'SELECT * FROM words WHERE word LIKE ?';

        $actual = self::$builder->select($table)->where($condition)->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithMultipleWhereConditions(): void
    {
        $table = 'words';
        $condition1 = 'word LIKE ?';
        $condition2 = 'id BETWEEN ? AND ?';
        $condition3 = 'LENGTH(word) >= 10';
        $expected = 'SELECT * FROM words WHERE word LIKE ? AND id BETWEEN ? AND ? OR LENGTH(word) >= 10';

        $actual = self::$builder
            ->select($table)
            ->where($condition1)
            ->where($condition2, OperatorType::AND)
            ->where($condition3, OperatorType::OR)
            ->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithSingleJoin(): void
    {
        $table1 = 'words';
        $table2 = 'matches';
        $condition = 'words.id = matches.word_id';
        $expected = 'SELECT * FROM words LEFT JOIN matches ON words.id = matches.word_id';

        $actual = self::$builder
            ->select($table1)
            ->join($table2, $condition, JoinType::LEFT)
            ->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithMultipleJoins(): void
    {
        $table1 = 'words';
        $table2 = 'matches';
        $table3 = 'rules';
        $condition1 = 'words.id = matches.word_id';
        $condition2 = 'rules.id = matches.rule_id';
        $expected = 'SELECT * FROM words'
            . ' LEFT JOIN matches ON words.id = matches.word_id'
            . ' INNER JOIN rules ON rules.id = matches.rule_id';

        $actual = self::$builder
            ->select($table1)
            ->join($table2, $condition1, JoinType::LEFT)
            ->join($table3, $condition2)
            ->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithJoinAndWhereCondition(): void
    {
        $table1 = 'words';
        $table2 = 'matches';
        $joinCondition = 'words.id = matches.word_id';
        $whereCondition = 'LENGTH(words.word) >= 10';
        $expected = 'SELECT * FROM words '
            . 'INNER JOIN matches ON words.id = matches.word_id '
            . 'WHERE LENGTH(words.word) >= 10';

        $actual = self::$builder
            ->select($table1)
            ->join($table2, $joinCondition)
            ->where($whereCondition)
            ->get();

        $this->assertSame($expected, $actual);
    }

    public function testSelectWithJoinAndWhereConditions(): void
    {
        $table1 = 'words';
        $table2 = 'matches';
        $table3 = 'rules';
        $joinCondition1 = 'words.id = matches.word_id';
        $joinCondition2 = 'rules.id = matches.rule_id';
        $whereCondition1 = 'words.word LIKE ?';
        $whereCondition2 = 'words.id BETWEEN ? AND ?';
        $whereCondition3 = 'LENGTH(words.word) >= 10';
        $expected = 'SELECT * FROM words '
            . 'LEFT JOIN matches ON words.id = matches.word_id '
            . 'INNER JOIN rules ON rules.id = matches.rule_id '
            . 'WHERE words.word LIKE ? '
            . 'AND words.id BETWEEN ? AND ? '
            . 'OR LENGTH(words.word) >= 10';

        $actual = self::$builder
            ->select($table1)
            ->join($table2, $joinCondition1, JoinType::LEFT)
            ->join($table3, $joinCondition2)
            ->where($whereCondition1)
            ->where($whereCondition2, OperatorType::AND)
            ->where($whereCondition3, OperatorType::OR)
            ->get();

        $this->assertEquals($expected, $actual);
    }

    public function testInsert(): void
    {
        $table = 'words';
        $fields = ['word'];
        $expected = 'INSERT INTO words (word) VALUES (?)';

        $actual = self::$builder->insert($table, $fields)->get();

        $this->assertSame($expected, $actual);
    }

    public function testInsertIgnore(): void
    {
        $table = 'words';
        $fields = ['word'];
        $expected = 'INSERT IGNORE INTO words (word) VALUES (?)';

        $actual = self::$builder->insert($table, $fields, 1, true)->get();

        $this->assertSame($expected, $actual);
    }

    public function testInsertMultiple(): void
    {
        $table = 'words';
        $fields = ['word'];
        $rows = 10;
        $expected = 'INSERT INTO words (word) VALUES (?), (?), (?), (?), (?), (?), (?), (?), (?), (?)';

        $actual = self::$builder->insert($table, $fields, $rows)->get();

        $this->assertSame($expected, $actual);
    }

    public function testUpdate(): void
    {
        $table = 'words';
        $fields = ['word', 'hyphenated'];
        $expected = 'UPDATE words SET word = ?, hyphenated = ?';

        $actual = self::$builder->update($table, $fields)->get();

        $this->assertSame($expected, $actual);
    }

    public function testUpdateWithWhereCondition(): void
    {
        $table = 'words';
        $fields = ['word', 'hyphenated'];
        $condition = 'id = ?';
        $expected = 'UPDATE words SET word = ?, hyphenated = ? WHERE id = ?';

        $actual = self::$builder->update($table, $fields)->where($condition)->get();

        $this->assertSame($expected, $actual);
    }

    public function testDelete(): void
    {
        $table = 'words';
        $expected = 'DELETE FROM words';

        $actual = self::$builder->delete($table)->get();

        $this->assertSame($expected, $actual);
    }

    public function testDeleteWithWhereCondition(): void
    {
        $table = 'words';
        $condition1 = 'id = ?';
        $condition2 = 'word LIKE ?';
        $expected = 'DELETE FROM words WHERE id = ? OR word LIKE ?';
        $actual = self::$builder
            ->delete($table)
            ->where($condition1)
            ->where($condition2, OperatorType::OR)
            ->get();

        $this->assertSame($expected, $actual);
    }

    public function testQueryTypeNotSelected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        self::$builder->get();
    }

    public function testInvalidSelectQuery(): void
    {
        $table = 'words';

        $this->expectException(\InvalidArgumentException::class);

        self::$builder->select($table, [])->get();
    }

    public function testInvalidInsertQuery(): void
    {
        $table = 'words';

        $this->expectException(\InvalidArgumentException::class);

        self::$builder->insert($table, [])->get();
    }

    public function testInvalidUpdateQuery(): void
    {
        $table = 'words';

        $this->expectException(\InvalidArgumentException::class);

        self::$builder->update($table, [])->get();
    }
}
