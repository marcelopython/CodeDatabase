<?php


namespace CodePress\CodeDatabase\Criteria;


use CodePress\CodeDatabase\Contracts\RepositoryInterface;

class FindByDescription
{

    private $description;

    public function __construct($description)
    {
        $this->description = $description;
    }

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('description', $this->description);
    }
}