<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  function get_names($terms, $limit = null, $ignore = null) {
    $ignored = [];
    if ($limit === null) {
      $limit = 10;
    }
    if ($ignore === null) {
      $ignored[] = new MongoDB\BSON\ObjectID($_SESSION['uid']);
    } else {
      foreach($ignore as $ignoree) {
        $ignored[] = new MongoDB\BSON\ObjectID($ignoree);
      }
    }
  	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
    $regex = new MongoDB\BSON\Regex($terms, 'i');
    $query = new MongoDB\Driver\Query(
      ['$or' =>
        [
          [ '$and' =>
              [
                [ 'firstName' => $regex ],
                [ '_id' =>
                    [ '$nin' => $ignored ]
                ],
                [ 'trusted' => TRUE ]
              ]
          ],
          [ '$and' =>
              [
                [ 'lastName' => $regex ],
                [ '_id' =>
                    [ '$nin' => $ignored ]
                ],
                [ 'trusted' => TRUE ]
              ]
          ]
        ]
      ],
      [
        'limit' => $limit,
        'projection' => [
          '_id' => 1,
          'firstName' => 1,
          'lastName' => 1,
          'avatar' => 1
        ]
      ]
    );
  	$cursor = $mongo->executeQuery('tc.users', $query);

    $contacts = [];
    foreach ($cursor as $document) {
      array_push($contacts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
    }
    return $contacts;

  }

  $names = json_decode(json_encode(get_names($_GET['terms'], $_GET['limit'], $_GET['participants'])), true);

  foreach ($names as &$name) {
    $name['id'] = $name['_id']['$oid'];
    $name['fullName'] = $name['firstName'] . ' ' . $name['lastName'];
    if ($name['avatar'] == '') {
      $name['avatarImage'] = 'img/robot.svg';
    } else {
      $name['avatarImage'] = 'image.php?id=' . $name['avatar'] . '&amp;width=200';
    }
  }

  header('Content-Type: application/json');
  echo json_encode($names);

}

?>
