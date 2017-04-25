<?php

namespace App\Repositories\Bonus;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;


/**
 * BonusRepository is a class that contains common queries for bonuses
 */
class BonusRepository extends BaseRepository implements BonusInterface
{
    /**
     * UserRepository constructor.
     * Inject whatever passed model
     * @param Model $bonus
     */
    public function __construct(Model $bonus)
    {
        $this->setModel($bonus);
    }

    /**
     * Create new bonus
     * @param $data array of key-value pairs
     * @return Model
     * @throws \Exception
     */
    public function create($data)
    {
        $bonus = $this->getModel()->create($data);
        if (!$bonus) {
            throw new \Exception(trans('bonuses.not_added'));
        }
        return $bonus;
    }


    /**
     * update bonus
     * @param $data
     * @throws \Exception
     */
    public function update($id, $data, $attribute = "id")
    {
        if(!$this->getModel()->where($attribute, '=', $id)->update(array_intersect_key($data, array_flip($this->getModel()->getFillable()))) )
        {
            throw new \Exception('reports.not_updated');
        }
    }


    /**
     * Get bonus for a user and verifies that bonus belongs to user
     * @param  integer $userId The user id
     * @param  integer $bonusId    The bonus id
     * @throws \Exception
     * @return mixed         Return Bonus object on success or redirect with error on failure
     */
    public function getBonusForAUser($userId, $bonusId)
    {
        $bonus = $this->getModel()->where(['id' => $bonusId, 'user_id' => $userId])->first();

        if (!$bonus) {
            throw new \Exception(trans('bonuses.not_found'));
        }

        return $bonus;
    }

    /**
     * Delete bonus from database
     * @param $id
     * @param string $attribute
     * @throws \Exception
     */
    public function destroy($id, $attribute="id")
    {
        if(!$this->getModel()->where($attribute, '=', $id)->delete())
        {
            throw new \Exception('bonuses.not_deleted');
        }
    }



}