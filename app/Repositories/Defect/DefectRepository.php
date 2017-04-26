<?php

namespace App\Repositories\Defect;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;


/**
 * BonusRepository is a class that contains common queries for bonuses
 */
class BonusRepository extends BaseRepository implements DefectInterface
{

    /**
     * Holds user model
     * @var Model
     */
    protected $defectModel;

    /**
    * UserRepository constructor.
    * Inject whatever passed model
    * @param Model $bonusModel
    */
    public function __construct(Model $defectModel)
    {
        $this->defectModel = $defectModel;
    }

    /**
    * get all defects
    * @throws \Exception
    * @return defect[]
    */
    public function getAll()
    {
        $defect = $this->defectModel->all();
        if ($defect->isEmpty()) {
             throw new \Exception('defects.not_added');
        }
        return $defect;
    }
    /**
    * update defect
    * @param $data
    * @throws \Exception
    */
    public function update($userId, $defectAttachmentId,$requestDefect)
    {
        //Update defect
        if(!DB::table('defect_user')->where(
                [
                    'id' => $defectAttachmentId,
                    'user_id' => $userId,
                ])->update(['defect_id' => $requestDefect]); )
        {
            throw new \Exception('reports.not_updated');
        }
    }
    /**
    * attach defect to user
    * @param $user
    * @param $defectId
    * @throws \Exception
    */
    public function attachToUser($user,$defectId){
        if(!$user->defects()->attach($defectId)){
            throw new \Exception('reports.not_created');
        }
    }
    /**
    * delete defects from database
    * @param $defectAttachmentId
    * @throws \Exception
    */
    public function destroy($defectAttachmentId){
        if(!DB::table('defect_user')->where('id', $defectAttachmentId)->delete()){
            throw new \Exception('defects.not_deleted');
        }
    }
    


   


    



}