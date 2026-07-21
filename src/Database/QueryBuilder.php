<?php

namespace Core\Database;

use PDO;
use RuntimeException;

class QueryBuilder
{
    private array $columns = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $groups = [];
    private array $havings = [];
    private array $havingBindings = [];

    public function __construct(
        private PDO $pdo,
        private string $table
    ) {
    }

    public function having(
        string $column,
        mixed $operator,
        mixed $value = null
    ): static {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $allowedOperators = [
            '=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE',
        ];

        $operator = strtoupper((string) $operator);

        if (!in_array($operator, $allowedOperators, true)) {
            throw new RuntimeException(
                "Operador não permitido: {$operator}"
            );
        }

        $placeholder = ':having_' . count($this->havingBindings);

        $this->havings[] = "{$column} {$operator} {$placeholder}";
        $this->havingBindings[$placeholder] = $value;

        return $this;
    }

    private function buildHaving(): string
    {
        if ($this->havings === []) {
            return '';
        }

        return ' HAVING ' . implode(' AND ', $this->havings);
    }

    public function groupBy(array|string $columns): static
    {
        $this->groups = is_array($columns)
            ? $columns
            : func_get_args();

        return $this;
    }

    private function buildGroup(): string
    {
        if ($this->groups === []) {
            return '';
        }

        return ' GROUP BY ' . implode(', ', $this->groups);
    }

    public function select(array|string $columns = ['*']): static
    {
        $this->columns = is_array($columns)
            ? $columns
            : func_get_args();

        return $this;
    }

    public function where(
        string $column,
        mixed $operator,
        mixed $value = null
    ): static {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $allowedOperators = [
            '=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE',
        ];

        $operator = strtoupper((string) $operator);

        if (!in_array($operator, $allowedOperators, true)) {
            throw new RuntimeException(
                "Operador não permitido: {$operator}"
            );
        }

        $placeholder = ':where_' . count($this->bindings);

        $this->wheres[] = [
            'boolean' => 'AND',
            'condition' => "{$column} {$operator} {$placeholder}",
        ];
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function orWhere(
        string $column,
        mixed $operator,
        mixed $value = null
    ): static {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $allowedOperators = [
            '=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE',
        ];

        $operator = strtoupper((string) $operator);

        if (!in_array($operator, $allowedOperators, true)) {
            throw new RuntimeException(
                "Operador não permitido: {$operator}"
            );
        }

        $placeholder = ':where_' . count($this->bindings);

        $this->wheres[] = [
            'boolean' => 'OR',
            'condition' => "{$column} {$operator} {$placeholder}",
        ];

        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        if ($values === []) {
            throw new RuntimeException(
                'whereIn exige pelo menos um valor.'
            );
        }

        $placeholders = [];

        foreach ($values as $value) {
            $placeholder = ':where_' . count($this->bindings);

            $placeholders[] = $placeholder;
            $this->bindings[$placeholder] = $value;
        }

        $this->wheres[] = [
            'boolean' => 'AND',
            'condition' => sprintf(
                '%s IN (%s)',
                $column,
                implode(', ', $placeholders)
            ),
        ];

        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->wheres[] = [
            'boolean' => 'AND',
            'condition' => "{$column} IS NULL",
        ];

        return $this;
    }

    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): static {
        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new RuntimeException(
                "Direção de ordenação inválida: {$direction}"
            );
        }

        $this->orders[] = "{$column} {$direction}";

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function get(): array
    {
        $sql = $this->toSql();

        $statement = $this->pdo->prepare($sql);
        $statement->execute(
            array_merge(
                $this->bindings,
                $this->havingBindings
            )
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array
    {
        $this->limit(1);

        $result = $this->get();

        return $result[0] ?? null;
    }

    public function find(int|string $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function count(string $column = '*'): int
    {
        $originalColumns = $this->columns;

        $this->columns = ["COUNT({$column}) AS aggregate"];

        $result = $this->first();

        $this->columns = $originalColumns;

        return (int) ($result['aggregate'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $data): int
    {
        if ($data === []) {
            throw new RuntimeException(
                'Nenhum dado informado para inserção.'
            );
        }

        $columns = array_keys($data);

        $placeholders = array_map(
            fn (string $column): string => ':' . $column,
            $columns
        );

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        if ($data === []) {
            throw new RuntimeException(
                'Nenhum dado informado para atualização.'
            );
        }

        if ($this->wheres === []) {
            throw new RuntimeException(
                'Atualização sem cláusula WHERE não permitida.'
            );
        }

        $sets = [];
        $bindings = $this->bindings;

        foreach ($data as $column => $value) {
            $placeholder = ':update_' . $column;
            $sets[] = "{$column} = {$placeholder}";
            $bindings[$placeholder] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s',
            $this->table,
            implode(', ', $sets)
        );

        $sql .= $this->buildWhere();

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement->rowCount();
    }

    public function delete(): int
    {
        if ($this->wheres === []) {
            throw new RuntimeException(
                'Exclusão sem cláusula WHERE não permitida.'
            );
        }

        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhere();

        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);

        return $statement->rowCount();
    }

    private function buildWhere(): string
    {
        if ($this->wheres === []) {
            return '';
        }

        $sql = ' WHERE ';

        foreach ($this->wheres as $index => $where) {

            if ($index > 0) {
                $sql .= ' ' . $where['boolean'] . ' ';
            }

            $sql .= $where['condition'];
        }

        return $sql;
    }

    private function buildOrder(): string
    {
        if ($this->orders === []) {
            return '';
        }

        return ' ORDER BY ' . implode(', ', $this->orders);
    }

    private function buildLimit(): string
    {
        $sql = '';

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    public function getBindings(): array
    {
        return array_merge(
            $this->bindings,
            $this->havingBindings
        );
    }

    public function toSql(): string
    {
        $sql = sprintf(
            'SELECT %s FROM %s',
            implode(', ', $this->columns),
            $this->table
        );

        $sql .= $this->buildWhere();
        $sql .= $this->buildGroup();
        $sql .= $this->buildHaving();
        $sql .= $this->buildOrder();
        $sql .= $this->buildLimit();

        return $sql;
    }
}