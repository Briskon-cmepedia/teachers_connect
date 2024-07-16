<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    $file_ext = strtolower(pathinfo(parse_url($_GET['id'])['path'], PATHINFO_EXTENSION));

    if ( in_array( $file_ext, $document_filetypes ) ) {

      $S3url = Config::S3URL . $_GET['id'];

      $activity_data[] = $_GET['name'];
      $activity_data[] = $S3url;
      $activity_data[] = $file_ext;
      new_activity_log($_SESSION['uid'], 'downloaded file', $activity_data);
      $keyPath =  "uploads/".$_GET['id'];
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
          'Bucket' => Config::BUCKET,
          'Key'    => $keyPath
        ));

      //  print_r($result); die;
         

     // $file = fopen($result, 'rb');

      header('Pragma: public');
      header('Cache-Control: max-age=86400');
      header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
      if ($file_ext == 'doc' OR $file_ext == 'docx') {
        header('Content-type: application/msword');
      }
      if ($file_ext == 'ppt' OR $file_ext == 'pptx') {
        header('Content-type: application/vnd.ms-powerpoint');
      }
      if ($file_ext == 'xls' OR $file_ext == 'xlsx') {
        header('Content-type: application/vnd.ms-excel');
      }
      if ($file_ext == 'pages') {
        header('Content-type: application/vnd.apple.pages');
      }
      if ($file_ext == 'key') {
        header('Content-type: application/vnd.apple.keynote');
      }
      if ($file_ext == 'numbers') {
        header('Content-type: application/vnd.apple.numbers');
      }
      if ($file_ext == 'pdf') {
        header('Content-type: application/pdf');
      }
      header ( 'Content-Disposition: attachment; filename="' . rawurldecode($_GET['name']) . '"' );
      echo $result['Body'];
      fpassthru($file);
      exit;
    } catch (Exception $e) {
      die("Error: " . $e->getMessage());
    }

    }

  } else {

    die();

  }

} else {

  die();

}
