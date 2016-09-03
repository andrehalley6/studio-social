<?php
include "api_function.php";
// phpinfo();

header("Content-Type: application/json");

// Do the general validation here
// Get controller method
$method = $_GET['method'];

// Get request method (GET, POST, PUT, DELETE)
$request_method = $_SERVER['REQUEST_METHOD'];

// Get PHP Header
$http_header = getallheaders();
// $output[] = $http_header;
// $output[] = $method;
// $output[] = $request_method;
// echo deliver_response(200, "ok", $output);
// exit;
// Check header sent, we need to have Accept, X-const-id, X-timestamp, and X-signature on header request
if(!isset($http_header['Accept']) || !isset($http_header['X-Const-Id']) || !isset($http_header['X-Timestamp']) || !isset($http_header['X-Signature'])) {
	echo deliver_response(400, "fail", $http_header);
	exit;
}

// Check header accept parameter
if($http_header['Accept'] != "application/json" && $http_header['Accept'] != "text/xml") {
	echo deliver_response(400, "fail", "Content Type Not Supported");
	exit;
}

// Check timestamp sent, if too early or too late (we set time difference 10 seconds, take a look at site_helper.php file) we consider it error
if(time_out_of_bound((int) gmdate('U'), $http_header['X-Timestamp'])) {
	echo deliver_response(400, "fail", "Timestamp Not Valid");
	exit;
}

// Check if no method requested
if(empty($method)) {
	echo deliver_response(404, "fail", "Method Parameter Can Not Empty");
	exit;
}

// Check if method not exists
if(!function_exists($method)) {
	echo deliver_response(404, "fail", "Method Not Found");
	exit;
}

// return $method;

// $returnArray = $$method;
// $methodMatching = array(
//  '123rf.contributor.getDownloadList' => array(
//   'object' => 'contributor',
//   'method' => 'getDownloadList',
//   'auth_key_check' => 0,
//  ),

hello_world();