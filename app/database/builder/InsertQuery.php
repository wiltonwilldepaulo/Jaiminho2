<?php

namespace app\database\builder;

class InsertQuery
{
    private string $table;
    public static function table(string $table): self
    {
        $self = new self;
        $self->table = $table;
        return $self;
    }
    public function save(array $FieldsAndValues)
    {
        $key = implode(',', array_keys($FieldsAndValues));
        $placeHolder = ':' . implode(',:', array_keys($FieldsAndValues));
        $query = "insert into $this->table ($key) values ($placeHolder); ";
        echo $query;
    }
}
