<?php
// System Setup
require 'includes/startup.php';
require Config::AUTOLOAD_PATH;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;


$keyPath = 'uploads/18ba8828-fa1d-4d64-9a05-1943fa05657e.jpg'; // file name(can also include the folder name and the file name. eg."member1/IoT-Arduino-Monitor-circuit.png")
    
//S3 connection 
  try {
    $s3 = S3Client::factory(
      array(
        'credentials' => array(
          'key' => Config::ACCESS_KEY,
          'secret' =>  Config::SECRET_KEY,
        ),
        'version' => Config::AWS_VERSION,
        'region'  => Config::AWS_REGION
      )
    );
    //to get the file information from S3
    $result = $s3->getObject(array(
      'Bucket' => 'teachersconnectdev',
      'Key'    => $keyPath
    ));
    
    header("Content-Type: {$result['ContentType']}");
    header('Content-Disposition: filename="' . basename($keyPath) . '"'); // used to download the file.
    echo $result['Body'];
  } catch (Exception $e) {
    die("Error: " . $e->getMessage());
  }
die;
try {
  //Create a S3Client
  $s3Client = new S3Client([
    'version'     => Config::AWS_VERSION,
    'region'      => Config::AWS_REGION,
    'endpoint'    => Config::AWS_ENDPOINT,
    'credentials' => [
       'key'      => Config::ACCESS_KEY,
       'secret'   => Config::SECRET_KEY,
    ],
  ]);
  // Save object to a file.
  $result = $s3Client->getObject(array(
      'Bucket' => 'teachersconnectdev',
      'Key' => 'uploads/18ba8828-fa1d-4d64-9a05-1943fa05657e.jpg',
      'SaveAs' =>  '18ba8828-fa1d-4d64-9a05-1943fa05657e.jpg'
  ));
} catch (S3Exception $e) {
  echo $e->getMessage() . "\n";
}
print_r($result);
$img = new Imagick($result);
      autorotate($img);
      if ($_GET['width']) {
        $img->scaleImage($_GET['width'], 0);
      } elseif ($_GET['height']) {
        $img->scaleImage(0, $_GET['height']);
      }

echo 123;
die;

// $credentials = new Aws\Credentials\Credentials('AKIAZO73X5BR6TEU76Y6', 'oyMctT3hh5uoSG5GPvGYq/4G5hIuM/xh8hVMedQT');

// $s3 = new Aws\S3\S3Client([
//     'version'     => 'latest',
//     'region'      => 'us-east-1',
//     'credentials' => $credentials
// ]);
// $result = $s3->listBuckets();
// print_r($result);


$aws = new Aws\Credentials\Credentials('AKIAZO73X5BR6TEU76Y6', 'oyMctT3hh5uoSG5GPvGYq/4G5hIuM/xh8hVMedQT');
// Get references to resource objects



$bucket = $aws->s3->bucket('teachersconnectdev');

$object = $bucket->object('uploads/18ba8828-fa1d-4d64-9a05-1943fa05657e.jpg');
print_r($object);
 die;
// require 'includes/checkup.php';

// if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    $file_ext = strtolower(pathinfo(parse_url($_GET['id'])['path'], PATHINFO_EXTENSION));

    // if ( in_array( $file_ext, $image_filetypes ) ) {
      $abc ="https://tcwa.nyc3.digitaloceanspaces.com/uploads/1e4cc73f-ae7f-409e-bfc8-42d9308b77b8.jpg";

     // $abc = "http://teachersconnectdev.s3.amazonaws.com/uploads/18ba8828-fa1d-4d64-9a05-1943fa05657e.jpg";
      $img = new Imagick($abc);
      autorotate($img);
      if ($_GET['width']) {
        $img->scaleImage($_GET['width'], 0);
      } elseif ($_GET['height']) {
        $img->scaleImage(0, $_GET['height']);
      }

      header('Pragma: public');
      header('Cache-Control: max-age=86400');
      header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
      header('Content-type: '. $img->getImageMimeType());
      echo $img->getImageBlob();

    // }

    // echo file_get_contents(Config::S3URL . $_GET['id']);

  } else {

    die();

  }

// } else {
//
//   die();
//
// }
