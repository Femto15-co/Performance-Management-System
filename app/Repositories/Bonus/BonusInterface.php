<?php

namespace App\Repositories\Bonus;

/**
 * Interface BonusInterface
 * Simple contract to force implementation of below functions
 */
interface BonusInterface
{
    /**
     * Create new bonus
     * @param $data array of key-value pairs
     * @return Model
     * @throws \Exception
     */
    public function create($data);

    public function getBonusForAUser($userId, $bonusId);
    public function destroy($id, $attribute = "id");
    public function update($id, $data, $attribute = "id");
}