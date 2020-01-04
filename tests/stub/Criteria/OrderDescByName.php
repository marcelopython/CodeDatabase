<?php

namespace CodePress\CodeDatabase\Criteria;


use CodePress\CodeDatabase\Contracts\RepositoryInterface;

class OrderDescByName
{

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->orderBy('name', 'DESC');
    }
}