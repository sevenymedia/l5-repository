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
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->application = new \Illuminate\Container\Container();
        $this->model = m::mock(\Illuminate\Database\Eloquent\Model::class);
    }

    protected function repositoryReflector(): \ReflectionClass
    {
        return new \ReflectionClass(RepositoryStub::class);
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

        $reflectionClass = new \ReflectionClass(RepositoryStub::class);

        if (null !== $model) {
            $property = $reflectionClass->getProperty('model');
            $property->setAccessible(true);
            $this->assertSame($model, $property->getValue($repository));
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

    public function testInitiateModel()
    {
        $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection()
        );

        $reflectionClass = new \ReflectionClass(RepositoryStub::class);
        $method = $reflectionClass->getMethod('initiateModel');
        $method->setAccessible(true);
        $method->invokeArgs($repository, []);

        $property = $reflectionClass->getProperty('model');
        $property->setAccessible(true);

        $this->assertInstanceOf(ModelStub::class, $property->getValue($repository));
    }

    public function testInitiateModelDoesNotOverwrite()
    {
        $repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model = new ModelStub()
        );

        $reflectionClass = new \ReflectionClass(RepositoryStub::class);
        $method = $reflectionClass->getMethod('initiateModel');
        $method->setAccessible(true);
        $method->invokeArgs($repository, []);

        $property = $reflectionClass->getProperty('model');
        $property->setAccessible(true);

        $this->assertSame($model, $property->getValue($repository));
    }

    public function testResetModelCreatesNewModel()
    {
        ($repository = new RepositoryStub(
            $this->application,
            new \Illuminate\Support\Collection(),
            $model = new ModelStub()
        ))->resetModel();

        $reflectionClass = new \ReflectionClass(RepositoryStub::class);
        $property = $reflectionClass->getProperty('model');
        $property->setAccessible(true);

        $this->assertNotSame($model, $property->getValue($repository));
    }



    public function providerTestConstructor(): array
    {
        return [
            [],
            [$this->model,],
            [null, 'foo',],
            [null, null, 'foo',],
        ];
    }

}

class ModelStub extends Model
{

}

class RepositoryStub extends BaseRepository
{

    public function model()
    {
        return ModelStub::class;
    }

}
