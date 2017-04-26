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

    	$result=['0 EGP','0 (0 Days)','0 of 0'];
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
    	$bonuses=Auth::user()->bonuses()->where('created_at','>=',$dateStart)
    	->where('created_at','<',$dateEnd)->get();


    	//Go through bonuses and add them up
    	foreach ($bonuses as $bonus) {
    		$bonusesTotal+=$bonus->value;

    	}
    	//Update bonuses result
    	$result[0]=($bonusesTotal)?number_format($bonusesTotal, 2, '.', '')." EGP":$result[0];
    dd($bonusesTotal);
    	//Defects
    	$defectsTotal=0;
    	$defects=Auth::user()->defects()->where('defect_user.created_at','>=',$dateStart)
    	->where('defect_user.created_at','<',$dateEnd)->get();
    	foreach ($defects as $defect) {
    		$defectsTotal+=$defect->score;
    	}
		//Update bonuses result
		$defectsDays=number_format($defectsTotal/6,2,'.','');
    	$result[1]=($defectsTotal)?$defectsTotal." ($defectsDays Days)":$result[1];    	
    	//Reports
    	$reportsOverall=0;
    	$reportsMax=0;
    	$reportsCount=0;
    	$reports=Auth::user()->reports()->where('user_id',Auth::id())
    	->where('created_at','>=',$dateStart)->where('created_at','<',$dateEnd)->get();
    	//Go through reports
    	foreach ($reports as $report) {
    		//The report isn't ready yet
    		if ($report->overall_score==0)
    		{
    			continue;
    		}
    		$reportsOverall+=$report->overall_score;
    		$reportsMax+=$report->max_score;
    		//Increase the number of valid reports
    		$reportsCount++;
    	}

    	//We got some good reports
    	if ($reportsCount>0)
    	{
    		$reportsOverall=$reportsOverall/$reportsCount;
    		$reportsMax=$reportsMax/$reportsCount;
    		//Update result
    		$result[2]="$reportsOverall of $reportsMax";
    	}
    	return $result;
    }
}
