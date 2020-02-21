<?php

	function implodeSupplieEmails($array ){
		$r = array();
		foreach ($array as $key => $value)
		{
			$r[$key] = $value->email;
		}
		return implode(',', $r);
	}

?>