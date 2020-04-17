<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use Redirect;
use File;
use Config;
use App\Tracker;
use App\MessageLog;

class TrackerController extends Controller
{
    public $basePath;
	/**
     * Instantiate a new ShopifyController instance.
     */
	public function __construct($searchDataArray=[])
	{
        $this->basePath = base_path().'/public';
    }

    public function index(Request $request)
    {
        if(isset($request->a))
        {
            if(isset($request->actions))
            {
                if (File::exists($this->basePath.'/actions.txt'))
                {
                    file_put_contents("actions.txt", $request->actions);
                }
            }
        }
        if(File::exists($this->basePath.'/actions.txt'))
        {
            $trackerAction = file_get_contents($this->basePath.'/actions.txt');
            $actions = ($trackerAction) ? explode(PHP_EOL,$trackerAction) : config('constants.trackerAction');
        }
        $currentUser = Sentinel::getUser()->first_name;
        if(isset($request->p))
        {
            $dateTo = date("Y-m-d")." 23:59:59";
            $dateFrom = date("Y-m-d")." 00:00:00";
            $dates = date("m/d/Y",strtotime($dateFrom)) . " - " . date("m/d/Y",strtotime($dateTo));
            if($request->has('dates'))
            {
                $dates = $request->get('dates');
            }
            $user = $request->get("user");
            $act = $request->get("activity");
            $acts = Tracker::getSearchFilterResult($request);
            $total   = count($acts);
            $totalHours = 0;
            $actions = [];
            $dactions = [];
            $users = [];
            $dusers = [];
            $aactions = [];
            $adactions = [];
            $mindate = 0;
            $maxdate = 0;

            foreach($acts as $a)
            {
                if(empty($actions[$a['user']])) $actions[$a['user']] = [];
                if(empty($dactions[$a['user']])) $dactions[$a['user']] = [];
                
                if(empty($actions[$a['user']][$a['activity']])) $actions[$a['user']][$a['activity']] = 1;
                else $actions[$a['user']][$a['activity']]++;
                
                if(empty($dactions[$a['user']][$a['activity']])) $dactions[$a['user']][$a['activity']] = $a['duration'];
                else $dactions[$a['user']][$a['activity']]+=$a['duration'];
                
                if(empty($aactions[$a['activity']])) $aactions[$a['activity']] = 1;
                else $aactions[$a['activity']]++;
                
                if(empty($adactions[$a['activity']])) $adactions[$a['activity']] = $a['duration'];
                else $adactions[$a['activity']]+=$a['duration'];
                
                if(empty($users[$a['user']])) $users[$a['user']] = 1;
                else $users[$a['user']]++;
                
                if(empty($dusers[$a['user']])) $dusers[$a['user']] = $a['duration'];
                else $dusers[$a['user']]+=$a['duration'];
                
                $odate = strtotime($a["start"]);
                if($odate < $mindate || $mindate==0) $mindate = $odate;
                if($odate > $maxdate) $maxdate = $odate;
                $totalHours += $a['duration']/3600;
            }

            $days = ($maxdate - $mindate) / 86400;
            $weeks = $days / 7;
            $months = $days / 30.4;
            if($days == 0)
            {
               $daily = $total; 
               $ddaily = $totalHours;
            }
            else
            {
               $daily = $total / $days; 
               $ddaily = $totalHours / $days;
            }
            if($weeks == 0)
            {
               $weekly  = $total;
               $dweekly  = $totalHours;
            }
            else
            {
               $weekly  = $total / $weeks;
               $dweekly  = $totalHours / $weeks;
            }
            if($months == 0)
            {
                $monthly = $total;
                $dmonthly = $totalHours;
            }
            else
            {
                $monthly = $total / $months;
                $dmonthly = $totalHours / $months;
            }
            $userList = Tracker::getUserRecord();
            $actionList = Tracker::getActivityRecord();
            $userList = ($userList) ? array_filter($userList->toArray()) : [];
            $actionList = ($actionList) ? array_filter($actionList->toArray()) : [];
            $user = Sentinel::getUser();
            $adminAccess = false;
            if ($user->inRole('admin'))
            {
                return view('admin.tracker.report', compact('currentUser', 'userList', 'actionList', 'actions', 'dactions', 'users', 'dusers', 'dates', 'act', 'total', 'daily', 'monthly', 'ddaily', 'dweekly', 'weekly', 'dmonthly', 'totalHours'));
            }
            else
            {
                return redirect()->route('tracker',['pageaction' => request()->get('pageaction')])
                ->with('error',"Permission denied");
            }
        }
    	return view('admin.tracker.index', compact('currentUser', 'actions'));
    }

    public function timeTracker(Request $request)
    {
        if($request->ajax())
        {
            $currentUser = Sentinel::getUser()->first_name;
            date_default_timezone_set('US/Eastern');
            $start = date("Y-m-d H:i:s",strtotime($request->start));
            if(!empty($request->count)) $cnt = $request->count;
            else $cnt = 1;
            if(!is_numeric($cnt)) $cnt =1;
            $duration = $request->duration / $cnt;
            for($i=0;$i<$cnt;$i++)
            {
                $data =  [
                    "user"     => $currentUser,
                    "activity" => preg_replace( "/\r|\n/", "", $request->act),
                    "start"    => $start,
                    "duration" => $duration 
                ];
                Tracker::addRecord((object) $data);
            }
            return response()->json([
                'message' => "Record Saved: ".$request->act.", duration: ".$request->duration." sec.",
                'type' => 'success',
                'status' => true
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'something went wrong with ajax request',
                'type' => 'error',
                'status' => false
            ]);
        }
    }
}