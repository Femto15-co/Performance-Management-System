<?php

namespace App\Repositories\Defect;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;


/**
 * DefectRepository is a class that contains common queries for bonuses
 */
class DefectRepository extends BaseRepository implements DefectInterface
{
    /**
    * DefectRepository constructor.
    * Inject whatever passed model
    * @param Model $model
    */
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->originalModel = $this->getModel();
    }

    /**
     * get Comment id by defect_user id
     * @param  integer $defectAttachmentId defect_user id
     * @return integer  comment id
     */
    public function getCommentId($defectAttachmentId)
    {
        $commentId = DB::table('defect_user')
             ->where('id',$defectAttachmentId)->first()->comment_id;
        return $commentId;
    }

}