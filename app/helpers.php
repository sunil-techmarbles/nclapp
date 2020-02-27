<?php

	function implodeSupplieEmails($array )
	{
		$r = array();
		foreach ($array as $key => $value)
		{
			$r[$key] = $value->email;
		}
		return implode(',', $r);
	}


	function supplieEmialArray($object)
	{
		$r = array();
		foreach ($object as $key => $value)
		{
			$r[$key] = $value->email;
		}
		return $r;
	}

	function ifnull($var, $default='')
	{
        return is_null($var) ? $default : $var;
    }

    function explodeSupplieAsinsModels($array)
    {
    	$r = array();
		foreach ($array as $key => $value)
		{
			$r[$key] = $value->asin_model_id;
		}
		return $r;
    }

	function resultInReadableform($result)
   	{
   		$output = [];
   		foreach ($result as $key => $value)
   		{
   			$keys = array_keys($value->toArray());
            foreach ($keys as $k => $v) {
                if($v == 'get_supplie_asin_models')
                {
                    break;
                }
                $output[$key][$v] = $value[$v];
            }
            if($value['getSupplieAsinModels'])
            {
                $AsinModels = explodeSupplieAsinsModels($value['getSupplieAsinModels']);
            }
            else{
                $AsinModels = [];
            }
            $output[$key]['models'] = $AsinModels;
   		}
   		return $output;
   	}

?>