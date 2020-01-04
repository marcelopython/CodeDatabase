<?php

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\AbstractRepository;
use CodePress\CodeDatabase\Models\Category;
use Mockery as m;

class AbstractRepositoryTest extends AbstractTestCase
{

    public function setUp():void
    {
        parent::setUp();
        $this->migrate();
        Category::create([
            'name' => 'category', 'description' => 'description Category'
        ]);
        echo Category::all()->first()->name;
    }

    protected function useSqliteConnection($app)
    {
        $app->config->set('database.default', 'codeDatabase');
    }

    public function testShouldReturnAllWithoutArguments()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'name';
        $mockStd->description = 'description';

        $mockRepository->shouldReceive('all')->andReturn([$mockStd, $mockStd, $mockStd]);

        $this->assertCount(3, $mockRepository->all());
        $this->assertInstanceOf(\stdClass::class, $mockRepository->all()[0]);

    }

    public function testShouldReturnAllWithArguments()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'name';

        $mockRepository->shouldReceive('all')
            ->with(['id', 'name'])
        ->andReturn([$mockStd, $mockStd, $mockStd]);

        $this->assertCount(3, $mockRepository->all(['id', 'name']));
        $this->assertInstanceOf(\stdClass::class, $mockRepository->all(['id', 'name'])[0]);

    }

    public function testShouldReturnCreate()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'stdClassName';

        $mockRepository
        ->shouldReceive('create')
        ->with(['name'=> 'stdClassName'])
        ->andReturn($mockStd);

        $result = $mockRepository->create(['name' => 'stdClassName']);
        $this->assertEquals(1, $result->id);
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testShoudReturnUpdateSuccess()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'name';

        $mockRepository
            ->shouldReceive('update')
            ->with(['name'=> 'stdClassName'], 1)
            ->andReturn($mockStd);

        $result = $mockRepository->update(['name' => 'stdClassName'], 1);
        $this->assertEquals(1, $result->id);
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testShoudDelete()
    {
        $mockRepostory = m::mock(AbstractRepositoryTest::class);

        $mockRepostory
            ->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        $result = $mockRepostory->delete(1);
        $this->assertEquals(true, $result);

    }

    public function testShoudFindWithoutColumns()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 2;
        $mockStd->name =  'name';
        $mockStd->description =  'description';

        $mockRepository
            ->shouldReceive('find')
            ->with(2)
            ->andReturn($mockStd);

        $result =$mockRepository->find(2);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals(2, $result->id);


    }

    public function testShoudFindWithColumns()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 2;
        $mockStd->name =  'name';

        $mockRepository
            ->shouldReceive('find')
            ->with(2, ['id', 'name'])
        ->andReturn($mockStd);

        $result = $mockRepository->find(2, ['id', 'name']);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals(2, $result->id);

    }

    public function testShouldReturnFindByWithColumns()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'name';

        $mockRepository->
            shouldReceive('findBy')->
            with('name', 'my-data', ['id', 'name'])
            ->andReturn([$mockStd, $mockStd, $mockStd]);

        $result = $mockRepository->findBy('name', 'my-data', ['id', 'name']);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\stdClass::class, $result[0]);

    }
    public function testShouldReturnFindByEmpty()
    {
        $mockRepository = m::mock(AbstractRepositoryTest::class);
        $mockStd = m::mock(\stdClass::class);
        $mockStd->id = 1;
        $mockStd->name = 'name';

        $mockRepository->
        shouldReceive('findBy')->
        with('name', '', ['id', 'name'])
            ->andReturn([]);

        $result = $mockRepository->findBy('name', '', ['id', 'name']);
        $this->assertCount(0, $result);

    }


}