<?php

function _m_d_yyyy_($date){
	$in_word = "";
	if($date != "0000-00-00"){
		$d = explode("-", $date);
		$months = array("January","February","March","April","May","June","July","August","September","October","November","December");
		$in_word = $months[(int)$d[1] - 1]." ".$d[2].", ".$d[0];
	}else{
		$in_word = $date;
	}
	return $in_word;
}

function get_complete_name($name){
	$fnwd = array(
		"Gerna M. Manatad"=>"GERNA M. MANATAD, MD, PHSAE, CESE, MDM",
		"Jose  R. Llacuna Jr."=>"JOSE R. LLACUNA,JR.,MD,MPH,CESO III"
	);
	return (array_key_exists($name,$fnwd)) ? $fnwd[$name] : $name;
}

?>