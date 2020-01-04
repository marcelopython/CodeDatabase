<?php

namespace CodePress\CodeDatabase;

use CodePress\CodeDatabase\Contracts\CriteriaCollection;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use CodePress\CodeDatabase\Contracts\RepositoryInterface;
use phpDocumentor\Reflection\Types\Integer;

abstract class AbstractRepository implements RepositoryInterface, CriteriaCollection
{

    protected object $model;
    protected bool $isIgnoreCriteria = false;

    protected array $criteriaCollection = [];

    public function __construct()
    {
        $this->makeModel();
    }

    public abstract function model();

    public function makeModel()
    {
        $class = $this->model();
        $this->model  = new $class;
        return $this->model;
    }

    public function all($columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->get($columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->find($id);
        return $model->delete();
    }

    public function find(int $id, array $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->findOrFail($id, $columns);
    }

    public function findBy(string $field, $value, array $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($field, $value)->get($columns);
    }

    public function addCriteria($criteria): AbstractRepository
    {
        $this->criteriaCollection[] = $criteria;
        return $this;
    }

    public function getCriteriaCollection()
    {
        return $this->criteriaCollection;
    }

    public function getByCriteria(CriteriaInterface $criteria): AbstractRepository
    {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }

    public function applyCriteria(): AbstractRepository
    {
        if($this->isIgnoreCriteria) {
            return $this;
        }

        foreach ($this->getCriteriaCollection() as $criteria) {
            $this->model = $criteria->apply($this->model, $this);
        }
        return $this;
    }

    public function ignoreCriteria(bool $isIgnore = true): AbstractRepository
    {
        $this->isIgnoreCriteria = $isIgnore;
        return $this;
    }

    public function clearCriteria(): AbstractRepository
    {
        $this->criteriaCollection = [];
        $this->makeModel();
        return $this;
    }
}