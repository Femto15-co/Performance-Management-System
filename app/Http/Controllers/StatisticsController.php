<?php

namespace App\Http\Controllers;

use App\Bonus;
use App\Defect;
use App\Report;
use App\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class StatisticsController extends Controller
{
    /**
     * User Repository
     * @var
     */
    protected $userService;

    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->statistics = Config::get('pms.statistics');

       
      
    }

    public function index()
    {
    	return view('statistics');
    }
    /**
     * Get user's related statistics
     * @param  Request $request The request
     * @return string           JSON with the statistics needed
     */
    public function get(Request $request)
    {
        //get EGP from config
        $currency="0 ".$this->statistics['Currency'];
       
        $result=[$currency,'0 (0 Days)','0 of 0'];

    	if (!$request->has('month'))
        {
    		return $result;
    	}
    	//Date formating for start and end
    	$timeStamp=strtotime("01-".$request->month);
    	$dateStart=date('Y-m-d',$timeStamp);
    	$dateEnd=date('Y-m-d',strtotime('next month',$timeStamp));
        $user=Auth::User();
        try {

            /**
            * Get all bonuses defects and reports within that month
            */
           
            //Boot model
            $this->userService->userRepository->setModel($user);

            //Bonuses
            $result[0]=$this->userService->userRepository->getBonuses($dateStart,$dateEnd);
            $result[0]=$result[0] ." ".$this->statistics['Currency'];

            //Defects
            $result[1]=$this->userService->userRepository->getDefects($dateStart,$dateEnd);

            //Reports
            $result[2]=$this->userService->userRepository->getScoreOfReport($dateStart,$dateEnd);

            //un-boot model
            $this->userService->userRepository->resetModel();
            
         } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
        
        
    	return $result;
    }

}
