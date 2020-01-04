<?php

namespace CodePress\CodeDatabase\Criteria;


use CodePress\CodeDatabase\Contracts\RepositoryInterface;

class OrderByDescById
{

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->orderBy('id', 'DESC');
    }

}