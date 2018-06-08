<?php

namespace SevenyMedia\Repository\Eloquent;

use Illuminate\Contracts\Container\Container as ApplicationContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Eloquent\BaseRepository as PrettusBaseRepository;

abstract class BaseRepository extends PrettusBaseRepository
{

    protected $modelClass;

    protected function initiateModel()
    {
        $modelClass = $this->model();
        if (true === $this->model instanceof $modelClass) {
            return;
        }
        $this->makeModel();
    }

    public function __construct(ApplicationContract $app, Collection $collection, $model = null, $presenter = null, $validator = null)
    {
        // Do NOT call parent constructor to make repository testable

        $this->app = $app;
        $this->criteria = $collection;

        if (null !== $model && true === $model instanceof Model) {
            $this->model = $model;
        }

        if (null !== $presenter) {
            $this->presenter = $presenter;
        }

        if (null !== $validator) {
            $this->validator = $validator;
        }
    }

    public function resetModel()
    {
        $this->model = null;
        $this->makeModel();
    }

    public function lists($column, $key = null)
    {
        $this->initiateModel();

        return parent::lists($column, $key);
    }

    public function pluck($column, $key = null)
    {
        $this->initiateModel();

        return parent::pluck($column, $key);
    }

    public function all($columns = ['*'])
    {
        $this->initiateModel();

        return parent::all($columns);
    }

    public function first($columns = ['*'])
    {
        $this->initiateModel();

        return parent::first($columns);
    }

    public function firstOrNew(array $attributes = [])
    {
        $this->initiateModel();

        return parent::firstOrNew($attributes);
    }

    public function firstOrCreate(array $attributes = [])
    {
        $this->initiateModel();

        return parent::firstOrCreate($attributes);
    }

    public function paginate($limit = null, $columns = ['*'], $method = 'paginate')
    {
        $this->initiateModel();

        return parent::paginate($limit, $columns, $method);
    }

    public function find($id, $columns = ['*'])
    {
        $this->initiateModel();

        return parent::find($id, $columns);
    }

    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->initiateModel();

        return parent::findByField($field, $value, $columns);
    }

    public function findWhere(array $where, $columns = ['*'])
    {
        $this->initiateModel();

        return parent::findWhere($where, $columns);
    }

    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $this->initiateModel();

        return parent::findWhereIn($field, $values, $columns);
    }

    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->initiateModel();

        return parent::findWhereNotIn($field, $values, $columns);
    }

    public function create(array $attributes)
    {
        $this->initiateModel();

        return parent::create($attributes);
    }

    public function update(array $attributes, $id)
    {
        $this->initiateModel();

        return parent::update($attributes, $id);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->initiateModel();

        return parent::updateOrCreate($attributes, $values);
    }

    public function delete($id)
    {
        $this->initiateModel();

        return parent::delete($id);
    }

    public function deleteWhere(array $where)
    {
        $this->initiateModel();

        return parent::deleteWhere($where);
    }

    public function has($relation)
    {
        $this->initiateModel();

        return parent::has($relation);
    }

    public function with($relations)
    {
        $this->initiateModel();

        return parent::with($relations);
    }

    public function withCount($relations)
    {
        $this->initiateModel();

        return parent::withCount($relations);
    }

    public function whereHas($relation, $closure)
    {
        $this->initiateModel();

        return parent::whereHas($relation, $closure);
    }

    public function hidden(array $fields)
    {
        $this->initiateModel();

        return parent::hidden($fields);
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->initiateModel();

        return parent::orderBy($column, $direction);
    }

    public function visible(array $fields)
    {
        $this->initiateModel();

        return parent::visible($fields);
    }

    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->initiateModel();

        return parent::getByCriteria($criteria);
    }

    public function resetCriteria()
    {
        $this->criteria = $this->app->make(\get_class($this->criteria));
        return $this;
    }

    protected function applyScope()
    {
        $this->initiateModel();

        return parent::applyScope();
    }

    protected function applyCriteria()
    {
        $this->initiateModel();

        return parent::applyCriteria();
    }

    protected function applyConditions(array $where)
    {
        $this->initiateModel();

        parent::applyConditions($where);
    }

}
