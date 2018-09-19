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
            $property = $reflectionClass->getProperty('presenter');
            $property->setAccessible(true);
            $this->assertSame($presenter, $property->getValue($repository));
        }

        if (null !== $validator) {
            $property = $reflectionClass->getProperty('validator');
            $property->setAccessible(true);
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
        $method = $reflectionClass->getMethod('initiateModel');
        $method->setAccessible(true);
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

    public function providerTestConstructor(): array
    {
        return [
            [],
            [$this->getModelMock(),],
            [null, 'foo',],
            [null, null, 'foo',],
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
