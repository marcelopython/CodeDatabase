<?php

namespace CodePress\CodeDatabase\Tests;


use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use CodePress\CodeDatabase\Criteria\FindByNameAndDescription;
use CodePress\CodeDatabase\Models\Category;
use CodePress\CodeDatabase\Repository\CategoryRepository;
use Illuminate\Database\Eloquent\Builder;

class FindByNameAndDescriptionTest extends AbstractTestCase
{

    /**
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository
     */
    private $repository;
    private $criteria;

    public function setUp():void
    {
        parent::setUp();
        $this->migrate();
        $this->createCategory();
        $this->repository = new CategoryRepository();
        $this->criteria = new FindByNameAndDescription('Category 1', 'Description 1');
    }

    public function testIfInstanceOfCriteriaInterface()
    {
        $this->assertInstanceOf(CriteriaInterface::class, $this->criteria);
    }

    public function testIfApplyReturnsQuerybuild()
    {
        $class = $this->repository->model();
        $result = $this->criteria->apply(new $class, $this->repository);
        $this->assertInstanceOf(Builder::class, $result);
    }

    public function testIfApplyReturnData()
    {
        $class = $this->repository->model();
        $result = $this->criteria->apply(new $class, $this->repository)->get()->first();
        $this->assertEquals('Category 1', $result->name);
        $this->assertEquals('Description 1', $result->description);
    }

    private function createCategory()
    {
        category::create([
            'name' => 'Category 1',
            'description' => 'Description 1'
        ]);
        category::create([
            'name' => 'Category 2',
            'description' => 'Description 2'
        ]);
        category::create([
            'name' => 'Category 3',
            'description' => 'Description 3'
        ]);
    }

}