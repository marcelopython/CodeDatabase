<?php

namespace CodePress\CodeDatabase\Contracts;


interface CriteriaCollection
{
    public function addCriteria(CriteriaInterface $criteria);

    public function getCriteriaCollection();

    public function getByCriteria(CriteriaInterface $criteria);

    public function applyCriteria();

    public function ignoreCriteria(bool $isIgnore = true);

    public function clearCriteria();

}