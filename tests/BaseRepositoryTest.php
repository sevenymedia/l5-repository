<?php

namespace SevenyMedia\Tests;

use Illuminate\Database\Eloquent\Model;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use SevenyMedia\Repository\Eloquent\BaseRepository;

class BaseRepositoryTest extends TestCase
{

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $application;

    /**
     * @var \ReflectionClass
     */
    protected $repositoryReflector;

    public function setUp()
    {
        parent::setUp();

        $this->application = new \Illuminate\Container\Container();
    }

    protected function repositoryReflector(): \ReflectionClass
    {
        return $this->repositoryReflector ?: $this->repositoryReflector = new \ReflectionClass(RepositoryStub::class);
    }

    /**
     * @dataProvider providerTestConstructor
     */
    public function testConstructor($model = null, $presenter = null, $validator = null)
    {
        $this->assertInstanceOf(RepositoryStub::class, $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model,
            $presenter,
            $validator
        ));

        $reflectionClass = $this->repositoryReflector();

        if (null !== $model) {
            $this->assertSame($model, $repository->modelProperty());
        }

        if (null !== $presenter) {
            ($property = $reflectionClass->getProperty('presenter'))->setAccessible(true);
            $this->assertSame($presenter, $property->getValue($repository));
        }

        if (null !== $validator) {
            ($property = $reflectionClass->getProperty('validator'))->setAccessible(true);
            $this->assertSame($validator, $property->getValue($repository));
        }
    }

    public function testConstructorWithBuilder()
    {
        $this->assertInstanceOf(RepositoryStub::class, $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $builder = m::mock(\Illuminate\Database\Eloquent\Builder::class)
        ));

        $this->assertSame($builder, $repository->modelProperty());
    }

    public function testInitiateModel()
    {
        $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection()
        );

        $reflectionClass = $this->repositoryReflector();
        ($method = $reflectionClass->getMethod('initiateModel'))->setAccessible(true);
        $method->invokeArgs($repository, []);

        $this->assertInstanceOf(ModelStub::class, $repository->modelProperty());
    }

    public function testInitiateModelDoesNotOverwrite()
    {
        $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model = $this->getModelMock()
        );

        $model->shouldReceive('withCount')->andReturn($builder = \Illuminate\Database\Eloquent\Builder::class);
        $repository->withCount(null);

        $this->assertSame($builder, $repository->modelProperty());
    }

    public function testResetModelCreatesNewModel()
    {
        ($repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model = new ModelStub()
        ))->resetModel();

        $this->assertNotSame($model, $repository->modelProperty());
    }

    /**
     * @dataProvider providerTestApplyConditionsProcessesWhereBoolean
     */
    public function testApplyConditionsProcessesWhereBoolean($argumentBool, string $expectationBool)
    {
        $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model = new ModelStub()
        );

        $model::setConnectionResolver(m::mock(
            \Illuminate\Database\ConnectionResolverInterface::class,
            function(\Illuminate\Database\ConnectionResolverInterface $mock) {
                $mock->shouldReceive('connection')->andReturn(
                    m::mock(\Illuminate\Database\ConnectionInterface::class, function(\Illuminate\Database\ConnectionInterface $mock) {
                        $mock->shouldReceive('getQueryGrammar', 'getPostProcessor');
                    })
                );
            })
        );

        ($reflectionMethod = new \ReflectionMethod($repository, 'applyConditions'))->setAccessible(true);
        $reflectionMethod->invokeArgs($repository, [
            [array_filter([$column = 'foo', $operator = 'LIKE', $value = '%bar%', $argumentBool]),],
        ]);

        $where = $repository->modelProperty()->getQuery()->wheres[0];
        $this->assertSame($column, $where['column']);
        $this->assertSame($operator, $where['operator']);
        $this->assertSame($value, $where['value']);
        $this->assertSame($expectationBool, $where['boolean']);
    }

    public function providerTestConstructor(): array
    {
        return [
            [],
            [$this->getModelMock(),],
            [null, 'foo',],
            [null, null, 'foo',],
        ];
    }

    public function providerTestApplyConditionsProcessesWhereBoolean(): array
    {
        return [
            [null, 'and',],
            ['and', 'and',],
            ['or', 'or',],
        ];
    }

    public function getModelMock(): m\MockInterface
    {
        return m::mock(\Illuminate\Database\Eloquent\Model::class);
    }

}

class ModelStub extends Model
{

}

class RepositoryStub extends BaseRepository
{

    public function modelProperty()
    {
        return $this->model;
    }

    public function model()
    {
        return ModelStub::class;
    }

}
