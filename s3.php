<?php
// System Setup
require_once 'includes/s3-service.php';

if ($_FILES) { // If images supplied, upload to S3

    try {
        $s3Service = new S3Service();
        //Create a S3Client

        $image_display = [];

        foreach ($_FILES as $file) {

            if ($file['error'] == 0) {

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $size = $file['size'];

                if (in_array($ext, $valid_filetypes)) {

                    $uuid = \Ramsey\Uuid\Uuid::uuid4();
                    $file_name = $uuid->toString() . '.' . $ext;

                    // putObject method sends data to the chosen bucket
                    $response = $s3Service->uploadImage($file['tmp_name'], 'uploads/' . $file_name);
                    $image_display[] = array($file_name, $file['name'], $ext, $size);
                }
            }
        }
    } catch (Exception $e) {
        echo "Error > {$e->getMessage()}";
    }

}
