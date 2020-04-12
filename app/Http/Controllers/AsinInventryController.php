<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SessionData;
use App\Exports\AsinInventryExport;
use Excel;

class AsinInventryController extends Controller
{

	public function index()
	{
		$assets = [];
		$items = SessionData::getAsinInventrySectionData();
		$assts = SessionData::getSessionData();
		foreach ($assts as $key => $asst)
		{
			if(!empty($asst['asset']))
			{
				if(!isset($assets['asin'.$asst['aid']])) $assets['asin'.$asst['aid']] = ["active"=>[],"removed"=>[]];
				$assets['asin'.$asst['aid']][$asst['status']][] = $asst['asset'];
			}
		}
		return view('admin.asininventry.index', compact('items', 'assets'));
	}

	public function RemoveAsset(Request $Request)
	{
		if( isset( $Request->assetIds ))
		{
			$assets = explode("\r\n",$Request['assetIds']) ;

			$FailedToRemoveCount = 0;
  			$SuccessfullyRemovedCount = 0;

  			foreach ($assets as $key => $asset)
  			{
  				$assetData = SessionData::CheckAssetExist($asset);

  				if( isset(  $assetData['asset'] )  )
  				{
  					$SuccessfullyRemovedCount++;
  					SessionData::updateSessiontStatus($asset, 'removed');
  				}
  				else
  				{
  					$FailedToRemoveCount++;
  				}
  			}

  			if($FailedToRemoveCount > 1 && $SuccessfullyRemovedCount < 1 )
		    {
		      	return redirect()->route('asininventry.removeasset')->with('error', 'Asset Id Not Match.');
		    }
		    elseif($FailedToRemoveCount >= 1 && $SuccessfullyRemovedCount >= 1 )
		    {
		      	return redirect()->route('asininventry.removeasset')->with('success', " $FailedToRemoveCount Asset Id Not Match ,$SuccessfullyRemovedCount Asset Id removed Successfully.");
		    }
		    else if( $FailedToRemoveCount < 1 && $SuccessfullyRemovedCount >= 1  )
		    {
		      	return redirect()->route('asininventry.removeasset')->with('success', 'Asset Id Remove Successfully.');
		    }
		}
		else
		{
			return view('admin.asininventry.removeasset');
		}
	}

	public function exportInventry()
	{
		return Excel::download(new AsinInventryExport, 'Asin.xlsx');
	}
}