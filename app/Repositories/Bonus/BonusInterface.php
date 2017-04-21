<?php

namespace App\Repositories\Bonus;

/**
 * Interface BonusInterface
 * Simple contract to force implementation of below functions
 */
interface BonusInterface
{
    public function create($data);
    public function getBonusForAUser($userId, $bonusId);
    public function destroy($id, $attribute = "id");

}