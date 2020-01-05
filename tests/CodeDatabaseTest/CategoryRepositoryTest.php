<?php

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Models\Category;
use CodePress\CodeDatabase\Repository\CategoryRepository;
use Mockery as m;

class CategoryRepositoryTest extends AbstractTestCase
{
    /**
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository
     */
    private $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->migrate();
        $this->repository = new CategoryRepository();
        $this->createCategory();
    }

    public function testCanModel()
    {
        $this->assertEquals(Category::class, $this->repository->model());
    }

    public function testCanMakeModel()
    {

        $this->repository->makeModel();
        $this->assertInstanceOf(Category::class, $this->repository->makeModel());

        $reflectionClass = new \ReflectionClass($this->repository);
        $refectionProperty = $reflectionClass->getProperty('model');
        $refectionProperty->setAccessible(true);
        $this->assertInstanceOf(Category::class, $refectionProperty->getValue($this->repository));
    }

    public function testCanMakeModelInConstructor()
    {
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);

        $this->assertInstanceOf(Category::class, $reflectionProperty->getValue($this->repository));

    }

    public function testCanListAllCategories()
    {
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertNotNull($result[0]->description);

        $result = $this->repository->all(['name']);
        $this->assertNull($result[0]->description);
    }

    public function testCanCreateCategory()
    {
        $result = $this->repository->create([
            'name' => 'Category 4',
            'description' => 'description 4'
        ]);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category 4', $result->name);
        $this->assertEquals('description 4', $result->description);

        $result = Category::find(4);
        $this->assertEquals('Category 4', $result->name);
        $this->assertEquals('description 4', $result->description);
    }

    public function testCanUpdateCategory()
    {
        $result = $this->repository->update([
            'name' => 'Category Atualizada',
            'description' =>'Description Atualizada'
        ], 1);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);

        $result = Category::find(1);
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);

    }

    public function testCanDeleteCategory()
    {
        $destroy = $this->repository->delete(1);
        $result = Category::all();
        $this->assertCount(2, $result);
        $this->assertEquals(true, $destroy);
    }

    public function testCanFindCategoryEithColumns()
    {
        $result = $this->repository->find(1, ['name']);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertNull($result->description);
    }

    public function testCanFindCategory()
    {
        $result = $this->repository->find(1);
        $this->assertInstanceOf(Category::class, $result);
    }

    public function testCanFindCategpries()
    {
        $result = $this->repository->findBy('name', 'Category 1');
        $this->assertCount(1, $result);

        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertEquals('Category 1', $result[0]->name);

        $result = $this->repository->findBy('name', 'Category 10');
        $this->assertCount(0, $result);

        $result = $this->repository->findBy('name', 'Category 1', ['name']);
        $this->assertCount(1, $result);

        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertNull($result[0]->description);

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