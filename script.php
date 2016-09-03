<?php
// echo __DIR__;
$url = "sheltered-wildwood-9601.herokuapp.com";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);
$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if(200 == $retcode) {
	echo "I am working";
}
else {
	echo "I am sleeping";
}