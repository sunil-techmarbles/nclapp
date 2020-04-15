<?php
namespace App\Http\Controllers;

use Illuminate\Http\request;
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

	public function RemoveAsset(Request $request)
	{
		$pageaction = isset($request->pageaction) ? $request->pageaction : '';
		if($request->isMethod('post'))
		{
			if(isset($request->assetIds))
			{
				$assets = explode("\r\n",$request['assetIds']) ;

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
					$status = '';
					$message = '';
					if($FailedToRemoveCount > 1 && $SuccessfullyRemovedCount < 1 )
			    {
			    	$status = 'error';
			    	$message = 'Asset Id Not Match.';
			    }
			    elseif($FailedToRemoveCount >= 1 && $SuccessfullyRemovedCount >= 1 )
			    {
			    	$status = 'success';
			    	$message = '{$FailedToRemoveCount} Asset Id Not Match ,{$SuccessfullyRemovedCount} Asset Id removed Successfully.';
			    }
			    else if( $FailedToRemoveCount < 1 && $SuccessfullyRemovedCount >= 1  )
			    {
			    	$status = 'error';
			    	$message = 'Asset Id Remove Successfully.';
			    }
			    return redirect()->route('asininventry.removeasset',['pageaction' => $pageaction])
			    	->with($status,$message);
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