<?php
function debug_var($var, $exit = FALSE) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";

	if($exit)
		exit;
}

function deliver_response($status, $status_message, $data = NULL, $type = "json") {
	header("HTTP/1.1: $status $status_message");

	if($type == "xml") {
		$response['http_code'] = $status;
		$response['status_message'] = $status_message;
		$response['data'] = $data;

		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
		array_to_xml($response, $xml);
		return $xml->asXML();
	}
	else {
		$response['http_code'] = $status;
		$response['status_message'] = $status_message;
		$response['data'] = $data;

		return json_encode($response);
	}
}

function generate_signature($key, $secret, $tStamp) {
	$apiKey = $key;

	$secretKey = $secret;

	// Generates a random string of ten digits
	$salt = md5("$apiKey&$tStamp");	// We hash the salt so it will hard to decypher

	// Computes the signature by hashing the salt with the secret key as the key
	$signature = hash_hmac('sha256', $salt, $secretKey, true);

	// base64 encode...
	$encodedSignature = base64_encode($signature);

	// urlencode...
	$encodedSignature = urlencode($encodedSignature);

	// echo "Voila! A signature: " . $encodedSignature;
	return $encodedSignature;
}

function time_out_of_bound($now, $timestamp) {
	$drift = 10;	// 10 second time from now
    if (abs($timestamp - $now) > $drift) {
        return true;
    }

    return false;
}

if (!function_exists('getallheaders'))
{
    function getallheaders()
    { 
		$headers = ''; 
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
			   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
    }
}

// API Part
function hello_world() {
	$output = array(
		'hello', 'world', 'api', 'restful'
	);
	echo deliver_response(200, "ok", $output);
	die;
}