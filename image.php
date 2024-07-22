<?php
// System Setup
require 'includes/startup.php';
require 'includes/s3-service.php';
// require 'includes/checkup.php';

// if ($_SESSION['user'] == $_SERVER['REMOTE_ADDR']) { // Display view if user has valid session

// Variable Setup
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

// Display site when not in maintenance mode or when bypassing maintenance lock with status
if ((Config::SERVER !== 'maintenance') || ((Config::SERVER === 'maintenance') && $_SESSION['bsm'])) {

    $s3Service = new S3Service();
    $remoteFileName = Config::S3URL . $_GET['id'];
    // print_r($remoteFileName);
    // exit();
    $fileName = ltrim(parse_url($remoteFileName, PHP_URL_PATH), '/');
    
    $imageBlob = $s3Service->getImageStream($fileName);
    if (null === $imageBlob) {
        exit();
    }
    $img = new Imagick();
    $img->readImageBlob($imageBlob);
    autorotate($img);
    if ($_GET['width']) {
        $img->scaleImage($_GET['width'], 0);
    } elseif ($_GET['height']) {
        $img->scaleImage(0, $_GET['height']);
    }

    header('Pragma: public');
    header('Cache-Control: max-age=86400');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
    header('Content-type: ' . $img->getImageMimeType());
    echo $img->getImageBlob();
}
exit();