<?php // Create new Plates instance
// $templates = new League\Plates\Engine('/var/www/html/templates');
//$templates = new League\Plates\Engine('/var/www/html/templates');

// $templates = new League\Plates\Engine(Config::ROOT_DIR . '/templates');
// if(!property_exists($templates)){echo "1--";
//   $templates = new League\Plates\Engine('../templates');
// }

$templates = new League\Plates\Engine('./templates');

// Register Plates functions
$templates->registerFunction('generate_photos', function () {
  $post_photos_count = count($post_photos);
  $html = '';
  if ($post_photos_count > 0) {
    $i = 1;
    foreach ($post_photos as $post_photo) {
      $html = $html . '<div class="img-preview ';
      if (!$single) {
        $html = $html . 'img' . $i . 'of' . $post_photos_count;
      }
      $html = $html . '"><img ';
      if ($single) {
        $html = $html . 'id="img-preview-featured" ';
      }
      $html = $html . 'src="' . Config::S3URL . $post_photo . '"></div>';
      $i++;
      if (!$single) {
        if($i > 4) break;
      } else {
        break;
      }
    }
    if ( $single AND $post_photos_count > 1 ) {
      $i = 1;
      $html = $html . '<div class="thumbnails">';
      foreach ($post_photos as $post_photo) {
        $html = $html . '<div class="img-thumbnail"><img src="' . Config::S3URL . $post_photo . '" onclick="changeImage(\''.$post_photo.'\')"></div>';
        $i++;
        if($i > 4) break;
      }
      $html = $html . '</div>';
    }
  }
  return $html;
});
