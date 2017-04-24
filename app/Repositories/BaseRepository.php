<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryContract
{
    protected $model;

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

        foreach ($data as $key => $value) {
            if ($value) {
                $this->model->$key = $value;
            }
        }

        $this->model->save();
        return $this->model;
    }

    /**
     * Get Single Entity By id
     * @param $itemId
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function editItem($itemId, array $data)
    {
        $item =  $this->getItemByID($itemId);

        if ($data) {
            throw new \Exception(trans('general.no_data'));
        }

        foreach ($data as $key => $value) {
            if ($value) {
                $item->$key = $value;
            }
        }
        $updated  = $item->save();

        if (!$updated) {
            throw  new \Exception(trans('general.not_updated'));
        }

        return $item;
    }

    /**
     * Get Single Entity By id
     * @param $itemId
     * @return Model
     * @throws \Exception
     */
    public function getItemByID($itemId)
    {
        $item = $this->model->find($itemId);
        if (!$item) {
            throw  new \Exception(trans('general.not_found'));
        }
        return $item;
    }

    /**
     * Delete Item By Id
     * @param integer $itemId
     * @return int
     * @throws \Exception
     */
    public function deleteItemById($itemId)
    {
        $deleted = $this->model->delete($itemId);
        if (!$deleted) {
            throw new \Exception(trans('general.not_deleted'));
        }
        return $deleted;
    }

    /**
     * Get All Entities at table
     * @return array $items
     */
    public function getAllItems()
    {
        return $this->model->all();
    }
}
