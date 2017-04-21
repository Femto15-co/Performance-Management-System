<?php
namespace App\Services;

use App\Repositories\Bonus\BonusInterface;

class BonusService
{
    public $bonusRepository;

    public function __construct(BonusInterface $bonusRepository)
    {
        $this->bonusRepository = $bonusRepository;
    }
}