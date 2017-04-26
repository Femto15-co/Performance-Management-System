<?php

namespace App\Repositories\Defect;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;


/**
 * DefectRepository is a class that contains common queries for bonuses
 */
class DefectRepository extends BaseRepository implements DefectInterface
{

    

    /**
    * DefectRepository constructor.
    * Inject whatever passed model
    * @param Model $bonusModel
    */
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->originalModel = $this->getModel();
    }

}