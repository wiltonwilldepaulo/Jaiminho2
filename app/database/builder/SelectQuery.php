<?php

namespace app\database\builder;

class SelectQuery
{
    private string $table;
    private string $fields;
    private array $where;
    private array $binds;
    public static function select(string $fields = '*'): self
    {
        $self = new self;
        $self->fields = $fields;
        return $self;
    }
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }
    public function join(): self
    {
        return $this;
    }
    public function limit(int $start, int $end): self
    {
        return $this;
    }
    public function order(string $field, string $typeOrder = 'desc'): self
    {
        return $this;
    }
    public function where(string $field, string $operator, string|int|float $value, string $logic = null): self
    {
        return $this;
    }
    public function fetch(): ?array
    {
        if (!$this->table) {
            throw new \Exception('Para executar a consulta é necessário informa a tabela');
        }
        return [];
    }
    public function fetchAll(): ?array
    {
        return [];
    }
}
