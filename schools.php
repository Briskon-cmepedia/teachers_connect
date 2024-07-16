<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

// Variable Setup
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

function get_schools($limit = null, $terms) {
  if (null === $limit) {
    $limit = 10;
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $regex = new MongoDB\BSON\Regex($terms, 'i');
	$query = new MongoDB\Driver\Query(['name' => $regex], ['limit' => $limit, 'projection' => ['name' => 1, 'city' => 1, 'state' => 1]]);
	$cursor = $mongo->executeQuery('tc.schools', $query);
  $json = "[";
  foreach ($cursor as $document) {
    $bson = MongoDB\BSON\fromPHP($document);
    $bson_json = MongoDB\BSON\toJSON($bson);
    // $php_array = json_decode($bson_json, true);
    // $php_array['_id'] = $php_array['_id']['$oid'];
    //$json = $json . json_encode($php_array, true) . ',';
    $json = $json . $bson_json . ',';
  }
  $json = substr($json, 0, -1);
  $json = $json . ']';
  echo $json;

}

header('Content-Type: application/json');
get_schools($_GET['limit'],  $_GET['terms']);

 ?>
