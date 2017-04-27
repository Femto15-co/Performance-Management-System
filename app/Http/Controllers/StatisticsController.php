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
        $this->statistics = Config::get('bmf.statistics');

       
      
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
        $result=$this->userService->userRepository->emptyResult();

    	if (!$request->has('month'))
    	{
    		return $result;
    	}
    	//Date formating for start and end
    	$timeStamp=strtotime("01-".$request->month);
    	$dateStart=date('Y-m-d',$timeStamp);
    	$dateEnd=date('Y-m-d',strtotime('next month',$timeStamp));
    	/**
    	 * Get all bonuses defects and reports within that month
    	 */
    	//Bonuses
        $bonusesTotal=0;
        $result[0]=$this->userService->userRepository->bonusesOfUser($dateStart,$dateEnd,$bonusesTotal);
        
    	//Defects
    	$defectsTotal=0;
    	$result[1]=$this->userService->userRepository->defectsOfUser($dateStart,$dateEnd,$defectsTotal);

    	//Reports
    	$reportsCount=0;
	    $result[2]=$this->userService->userRepository->getScoreOfUserReport($dateStart,$dateEnd,$reportsCount);
        
    	return $result;
    }
}
