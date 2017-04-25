<?php
/**
 * Created by PhpStorm.
 * User: mustafa
 * Date: 25/04/17
 * Time: 12:44 ص
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    /**
     * @param Model $model
     * @return mixed
     */
    public function setModel(Model $model);

    /**
     * @return Model $model
     */
    public function getModel();

    /**
     * un-boot model
     */
    public function resetModel();


    /**
     * Add New Entity
     * @param array $data
     * @return Model
     */
    public function addItem(array $data);

    /**
     * update entity by key-value
     * @param $attributeValue
     * @param array $data
     * @param string $attribute
     * @return mixed
     * @throws \Exception
     */
    public function editItem($attributeValue, $data, $attribute = "id");

    /**
     * Get Single Entity By id
     * @param $itemId
     * @param array $relations to eager load
     * @return Model
     */
    public function getItemByID($itemId, $relations = []);

    /**
     * Delete Item By Id
     * @param $attributeValue
     * @param string $attribute
     * @return mixed
     */
    public function deleteItem($attributeValue, $attribute = "id");

    /**
     * Get All Entities at table
     * @return array $items
     */
    public function getAllItems();

    /**
     * Ensure model is booted
     * @param string $key that must exist to indicate boot
     * @return bool
     * @throws \Exception
     */
    public function ensureBooted($key = 'id');

}
