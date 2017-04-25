<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryContract
{
    protected $model;

    /**
     * Keeps an image of original non-booted model
     * @var Model
     */
    protected $originalModel;
    /**
     * Get Class Model
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return RepositoryContract $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * un-boot model
     */
    public function resetModel()
    {
        $this->model = $this->originalModel;
    }

    /**
     * Add New Entity
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function addItem(array $data)
    {
        if (!$data) {
            throw new \Exception(trans('general.no_data'));
        }

        $item = $this->model->create($data);
        if (!$item) {
            throw new \Exception(trans('general.not_created'));
        }

        return $item;
    }

    /**
     * Get Single Entity By id
     * @param $itemId
     * @param array $relations to eager load
     * @return Model
     * @throws \Exception
     */
    public function getItemByID($itemId, $relations = [])
    {
        $item = $this->model->with($relations)->find($itemId);

        if (!$item) {
            throw  new \Exception(trans('general.not_found'));
        }

        return $item;
    }

    /**
     * update entity by key-value
     * @param $attributeValue
     * @param array $data
     * @param string $attribute
     * @return mixed
     * @throws \Exception
     */
    public function editItem($attributeValue, $data, $attribute = "id")
    {
        $fillable = array_flip($this->model->getFillable());

        $updated = $this->model->where($attribute, '=', $attributeValue)
            ->update(array_intersect_key($data, $fillable));

        if (!$updated) {
            throw new \Exception('general.not_updated');
        }

        return $updated;
    }

    /**
     * Delete item by attribute
     * @param $attributeValue
     * @param string $attribute
     * @return mixed
     * @throws \Exception
     */
    public function deleteItem($attributeValue, $attribute = "id")
    {
        $deleted = $this->model->where($attribute, '=', $attributeValue)->delete();

        if (!$deleted) {
            throw new \Exception(trans('general.not_deleted'));
        }

        return $deleted;
    }

    /**
     * Get All Entities at table
     * @return array $items
     * @throws \Exception
     */
    public function getAllItems()
    {
        $items = $this->model->all();

        if (!$items || $items->isEmpty()) {
            throw new \Exception(trans('general.no_data'));
        }

        return $items;
    }

    /**
     * Ensure model is booted
     * @param string $key that must exist to indicate boot
     * @return bool
     * @throws \Exception
     */
    public function ensureBooted($key = 'id')
    {
        //Check if report model is booted
        if (!$this->model->$key) {
            throw new \Exception(trans('general.not_found'));
        }

        return true;
    }
}

