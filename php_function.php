<?php
function order_array_num ($array, $key, $order = "ASC") 
{ 
	$tmp = array(); 
	foreach($array as $akey => $array2) 
	{ 
		$tmp[$akey] = $array2[$key]; 
	} 
	
	if($order == "DESC") 
	{arsort($tmp , SORT_NUMERIC );} 
	else 
	{asort($tmp , SORT_NUMERIC );} 
	$tmp2 = array();       
	$i = 1;
	foreach($tmp as $key => $value) 
	{ 
		$tmp2[$i] = $array[$key]; 
		$i++;
	}
	
	return $tmp2; 
}

function substr_in_array($haystack, $needle){
 
	$found = array();
	
	// cast to array 
	$needle = (array) $needle;
	
	// map with preg_quote 
	$needle = array_map('preg_quote', $needle);
	
	// loop over  array to get the search pattern 
	foreach ($needle as $pattern)
	{
		if (count($found = preg_grep("/$pattern/", $haystack)) > 0) {
			return $found;
		}
	}
	
	// if not found 
	return NULL;
}

function nicetime($date)
{
	if(empty($date)) {
		return NULL;
	}
	
	$periods		= array("detik", "menit", "jam", "hari", "minggu", "bulan", "tahun", "puluh tahun");
	$lengths		= array("60","60","24","7","4.35","12","10");
	
	$now			= time();
	$unix_date		= strtotime($date);
	
	// check validity of date
	if(empty($unix_date)) {    
		return NULL;
	}
	
	// is it future date or past date
	if($now > $unix_date) {    
		$difference	= $now - $unix_date;
		$tense		= "yang lalu";
	
	} else {
		$difference	= $unix_date - $now;
		$tense		= "dari sekarang";
	}
	
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	
	$difference = round($difference);
	
	return "$difference $periods[$j] {$tense}";
}

function print_message($message, $max_length)
{
	$result = "";
	$array_message = explode(" ", $message);
	$total_array_message = count($array_message);
	if($total_array_message == 0)	return substr($message, 0, $max_length);	//if no space, show only max_length character.
	$index = 0;
	for($i = 0; $i < $total_array_message; $i++)
	{
		$index += strlen($array_message[$i]." ");
		if($index <= $max_length)	{$result .= $array_message[$i]." ";}
		elseif($index > $max_length)	break;
	}
	return $result;
}
?>