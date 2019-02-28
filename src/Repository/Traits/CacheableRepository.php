<?php

namespace SevenyMedia\Repository\Traits;

use Prettus\Repository\Traits\CacheableRepository as BaseCacheableRepository;

/**
 * Class CacheableRepository
 * @package SevenyMedia\Repository\Traits
 */
trait CacheableRepository
{

    use BaseCacheableRepository;

    /**
     * Get cache minutes
     *
     * @return int
     */
    public function getCacheSeconds()
    {
        return $this->getCacheMinutes() / 60;
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        if (false === $this->allowedCache('all') || true === $this->isSkippedCache()) {
            return parent::all($columns);
        }

        $key = $this->getCacheKey('all', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($columns) {
            return parent::all($columns);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null  $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = 'paginate')
    {
        if (false === $this->allowedCache('paginate') || true === $this->isSkippedCache()) {
            return parent::paginate($limit, $columns, $method);
        }

        $key = $this->getCacheKey('paginate', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($limit, $columns, $method) {
            return parent::paginate($limit, $columns, $method);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        if (false === $this->allowedCache('find') || true === $this->isSkippedCache()) {
            return parent::find($id, $columns);
        }

        $key = $this->getCacheKey('find', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($id, $columns) {
            return parent::find($id, $columns);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        if (false === $this->allowedCache('findByField') || true === $this->isSkippedCache()) {
            return parent::findByField($field, $value, $columns);
        }

        $key = $this->getCacheKey('findByField', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($field, $value, $columns) {
            return parent::findByField($field, $value, $columns);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        if (false === $this->allowedCache('findWhere') || true === $this->isSkippedCache()) {
            return parent::findWhere($where, $columns);
        }

        $key = $this->getCacheKey('findWhere', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($where, $columns) {
            return parent::findWhere($where, $columns);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        if (false === $this->allowedCache('getByCriteria') || true === $this->isSkippedCache()) {
            return parent::getByCriteria($criteria);
        }

        $key = $this->getCacheKey('getByCriteria', func_get_args());
        $seconds = $this->getCacheSeconds();
        $value = $this->getCacheRepository()->remember($key, $seconds, function () use ($criteria) {
            return parent::getByCriteria($criteria);
        });

        $this->resetModel();
        $this->resetScope();
        return $value;
    }

}
