<?php

switch ($_SERVER['REQUEST_METHOD']  ) {
  case 'PUT':
  case 'POST':
  case 'GET':
  case 'HEAD':
  case 'DELETE':
  case 'OPTIONS':

    break;
  default:
     break;
}

switch($mode){

$request = file_get_contents('php://input');

 
//  return associative array of JSON
 $input = json_decode($request, true);

 $input['request'] = $request;

 $input['rx_datetime'] = date('Y-m-d H:i:s') ;
 header("Content-type: application/json");
 echo json_encode($input);


}

?>

