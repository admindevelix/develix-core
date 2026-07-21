<?php

namespace Tests\Unit;

use Core\Database\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function testToSqlWithoutFilters(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertEquals(
            'SELECT * FROM users',
            $builder->toSql()
        );
    }

    public function testToSqlWithWhere(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder->where('email', '=', 'diego@develix.com.br');

        $this->assertEquals(
            'SELECT * FROM users WHERE email = :where_0',
            $builder->toSql()
        );

        $this->assertEquals(
            [':where_0' => 'diego@develix.com.br'],
            $builder->getBindings()
        );
    }

    public function testToSqlWithOrWhere(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder
            ->where('status', '=', 'active')
            ->orWhere('status', '=', 'pending');

        $this->assertEquals(
            'SELECT * FROM users WHERE status = :where_0 OR status = :where_1',
            $builder->toSql()
        );

        $this->assertEquals(
            [
                ':where_0' => 'active',
                ':where_1' => 'pending',
            ],
            $builder->getBindings()
        );
    }

    public function testToSqlWithWhereIn(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder->whereIn('id', [10, 20, 30]);

        $this->assertEquals(
            'SELECT * FROM users WHERE id IN (:where_0, :where_1, :where_2)',
            $builder->toSql()
        );

        $this->assertEquals(
            [
                ':where_0' => 10,
                ':where_1' => 20,
                ':where_2' => 30,
            ],
            $builder->getBindings()
        );
    }

    public function testToSqlWithWhereNull(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder->whereNull('deleted_at');

        $this->assertEquals(
            'SELECT * FROM users WHERE deleted_at IS NULL',
            $builder->toSql()
        );

        $this->assertEquals(
            [],
            $builder->getBindings()
        );
    }

    public function testToSqlWithSelectedColumns(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder->select(['id', 'name', 'email']);

        $this->assertEquals(
            'SELECT id, name, email FROM users',
            $builder->toSql()
        );
    }

    public function testToSqlWithOrderBy(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder
            ->orderBy('name', 'ASC')
            ->orderBy('id', 'DESC');

        $this->assertEquals(
            'SELECT * FROM users ORDER BY name ASC, id DESC',
            $builder->toSql()
        );
    }

    public function testToSqlWithGroupBy(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'orders');

        $builder
            ->select(['status', 'COUNT(*) AS total'])
            ->groupBy('status');

        $this->assertEquals(
            'SELECT status, COUNT(*) AS total FROM orders GROUP BY status',
            $builder->toSql()
        );
    }

    public function testToSqlWithHaving(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'orders');

        $builder
            ->select(['status', 'COUNT(*) AS total'])
            ->groupBy('status')
            ->having('COUNT(*)', '>', 5);

        $this->assertEquals(
            'SELECT status, COUNT(*) AS total FROM orders GROUP BY status HAVING COUNT(*) > :having_0',
            $builder->toSql()
        );

        $this->assertEquals(
            [':having_0' => 5],
            $builder->getBindings()
        );
    }

    public function testToSqlWithLimitAndOffset(): void
    {
        $pdo = $this->createStub(PDO::class);

        $builder = new QueryBuilder($pdo, 'users');

        $builder
            ->limit(10)
            ->offset(20);

        $this->assertEquals(
            'SELECT * FROM users LIMIT 10 OFFSET 20',
            $builder->toSql()
        );
    }

    public function testFindReturnsRecordById(): void
    {
        $expectedUser = [
            'id' => 15,
            'name' => 'Diego',
        ];

        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([':where_0' => 15]);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([$expectedUser]);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE id = :where_0 LIMIT 1')
            ->willReturn($statement);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertSame(
            $expectedUser,
            $builder->find(15)
        );
    }

    public function testFirstReturnsFirstRecord(): void
    {
        $expectedUser = [
            'id' => 1,
            'name' => 'Diego',
        ];

        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([]);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([$expectedUser]);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users LIMIT 1')
            ->willReturn($statement);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertSame(
            $expectedUser,
            $builder->first()
        );
    }

    public function testGetReturnsAllRecords(): void
    {
        $expectedUsers = [
            ['id' => 1, 'name' => 'Diego'],
            ['id' => 2, 'name' => 'Laila'],
        ];

        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([]);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedUsers);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users')
            ->willReturn($statement);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertSame(
            $expectedUsers,
            $builder->get()
        );
    }

    public function testCountReturnsTotalRecords(): void
    {
        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([]);

        $statement
        ->expects($this->once())
        ->method('fetchAll')
        ->with(PDO::FETCH_ASSOC)
        ->willReturn([
            ['aggregate' => 3],
        ]);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT COUNT(*) AS aggregate FROM users LIMIT 1')
            ->willReturn($statement);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertSame(
            3,
            $builder->count()
        );
    }

    public function testExistsReturnsTrueWhenRecordExists(): void
    {
        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([]);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['aggregate' => 1],
            ]);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT COUNT(*) AS aggregate FROM users LIMIT 1')
            ->willReturn($statement);

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertTrue(
            $builder->exists()
        );
    }

    public function testInsertExecutesInsertStatement(): void
    {
        $statement = $this->createMock(\PDOStatement::class);

        $statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'name' => 'Diego',
                'email' => 'diego@email.com',
            ])
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with(
                'INSERT INTO users (name, email) VALUES (:name, :email)'
            )
            ->willReturn($statement);

        $pdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('15');

        $builder = new QueryBuilder($pdo, 'users');

        $this->assertSame(
            15,
            $builder->insert([
                'name' => 'Diego',
                'email' => 'diego@email.com',
            ])
        );
    }
}