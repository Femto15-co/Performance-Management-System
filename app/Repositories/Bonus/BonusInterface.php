<?php

namespace App\Repositories\Bonus;

/**
 * Interface BonusInterface
 * Simple contract to force implementation of below functions
 */
interface BonusInterface
{
    /**
     * Get bonus for a user and verifies that bonus belongs to user
     * @param  integer $userId The user id
     * @param  integer $bonusId    The bonus id
     * @throws \Exception
     * @return mixed         Return Bonus object on success or redirect with error on failure
     */
    public function getBonusForAUser($userId, $bonusId);
}