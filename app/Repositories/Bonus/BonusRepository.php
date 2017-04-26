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
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->originalModel = $this->getModel();
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
}