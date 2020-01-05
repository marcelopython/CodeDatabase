<?php

namespace CodePress\CodeDatabase\Tests;


use CodePress\CodeDatabase\Criteria\FindByDescription;
use CodePress\CodeDatabase\Criteria\FindByName;
use CodePress\CodeDatabase\Criteria\FindByNameAndDescription;
use CodePress\CodeDatabase\Criteria\OrderByDescById;
use CodePress\CodeDatabase\Criteria\OrderDescByName;
use CodePress\CodeDatabase\Models\Category;
use CodePress\CodeDatabase\Repository\CategoryRepository;
use CodePress\CodeDatabase\Contracts\CriteriaCollection;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use Illuminate\Database\Query\Builder;
use Mockery as m;

class CategoryRepositoryCriteriaTest extends AbstractTestCase
{

    /**
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository
     */
    private object $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->migrate();
        $this->repository = new CategoryRepository();
        $this->createCategory();
    }

    public function testIsInstanceOfCriteriaCollenction()
    {
        $this->assertInstanceOf(CriteriaCollection::class, $this->repository);
    }

    public function testCanGetCriteriaCollection()
    {
        $result = $this->repository->getCriteriaCollection();
        $this->assertCount(0, $result);
    }

    public function testCanAddCriteria()
    {
        $mockCriteria = m::mock(CriteriaInterface::class);
        $result = $this->repository->addCriteria($mockCriteria);
        $this->assertInstanceOf(CategoryRepository::class, $result);
        $this->assertCount(1, $this->repository->getCriteriaCollection());
    }

    public function testCanGetByCriteria()
    {
        $criteria = new FindByNameAndDescription('Category 1', 'Description 1');
        $repository = $this->repository->getByCriteria($criteria);
        $this->assertInstanceOf(CategoryRepository::class, $repository);

        $result = $repository->all();
        $this->assertCount(1, $result);
        $result = $result->first();
        $this->assertEquals('Category 1', $result->name);
        $this->assertEquals('Description 1', $result->description);
    }

    public function testCanApplyCriteria()
    {
        $this->createCategoryDescription();

        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();

        $this->repository
            ->addCriteria($criteria1)
            ->addCriteria($criteria2);

        $repository = $this->repository->applyCriteria();
        $this->assertInstanceOf(CategoryRepository::class, $repository);

        $result = $repository->all();
        $this->assertCount(3, $result);

        $this->assertEquals('Category Dois', $result[1]->name);
        $this->assertEquals('Category Um', $result[0]->name);

    }

    public function testCanListAllCategoriesWithCriteria()
    {
        $this->createCategoryDescription();
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();

        $this->repository
            ->addCriteria($criteria1)
            ->addCriteria($criteria2);

        $result = $this->repository->all();

        $this->assertCount(3, $result);
        $this->assertEquals('Category Um', $result[0]->name);
        $this->assertEquals('Category Dois', $result[1]->name);

    }


    public function testCanFindCategoryWithCriteria()
    {

        $this->createCategoryDescription();

        $criteria1 = new FindByDescription('Description');
        $criteria2 = new FindByName('Category Um');

        $this->repository
            ->addCriteria($criteria1)
            ->addCriteria($criteria2);

        $result = $this->repository->find(5);

        $this->assertEquals('Category Um', $result->name);
    }

    public function testCanFindByWithCriteria()
    {
        $this->createCategoryDescription();
        $criteria1 = new FindByName('Category Dois');
        $criteria2 = new OrderByDescById();
        $this->repository->addCriteria($criteria1);
        $this->repository->addCriteria($criteria2);

        $result = $this->repository->findBy('description', 'Description');

        $this->assertCount(2, $result);
        $this->assertEquals(6, $result[0]['id']);
        $this->assertEquals('Category Dois', $result[0]['name']);

        $this->assertEquals(4, $result[1]['id']);
        $this->assertEquals('Category Dois', $result[1]['name']);
    }


    public function testCanIgnoreCriteria()
    {
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('isIgnoreCriteria');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result);

        $this->repository->ignoreCriteria(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);

        $this->repository->ignoreCriteria(false);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result);

        $this->repository->ignoreCriteria();
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);

        $this->assertInstanceOf(CategoryRepository::class, $this->repository->ignoreCriteria());
    }

    public function testCanIgnoreCriteriaWithApplyCriteria()
    {
        $this->createCategoryDescription();

        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();

        $this->repository
            ->addCriteria($criteria1)
            ->addCriteria($criteria2);

        $this->repository->ignoreCriteria();
        $this->repository->applyCriteria();
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);

        $this->repository->ignoreCriteria(false);
        $repository = $this->repository->applyCriteria();
        $this->assertInstanceOf(CategoryRepository::class, $repository);

        $result = $repository->all();

        $this->assertCount(3, $result);
        $this->assertEquals('Category Dois', $result[1]->name);
        $this->assertEquals('Category Um', $result[0]->name);

    }

    public function testCanClearCriteria()
    {
        $this->createCategoryDescription();
        $criteria1 = new FindByName('Category Dois');
        $criteria2 = new OrderByDescById();
        $this->repository->addCriteria($criteria1);
        $this->repository->addCriteria($criteria2);

        $this->assertInstanceOf(CategoryRepository::class, $this->repository->clearCriteria());

        $result = $this->repository->findBy('description', 'Description');

        $this->assertCount(3, $result);
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);

    }

    private function createCategoryDescription()
    {
        category::create([
            'name' => 'Category Dois',
            'description' => 'Description'
        ]);
        category::create([
            'name' => 'Category Um',
            'description' => 'Description'
        ]);

        category::create([
            'name' => 'Category Dois',
            'description' => 'Description'
        ]);

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