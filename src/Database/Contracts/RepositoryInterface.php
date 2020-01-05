<?php

namespace CodePress\CodeDatabase\Contracts;

interface RepositoryInterface
{

    public function all($columns = array('*'));

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function find(int $id, array $columns = array('*'));

    public function findBy(string $field, $value, array $columns = array('*'));
    
}