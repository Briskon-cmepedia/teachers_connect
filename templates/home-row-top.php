<?php if ($posts) { ?>
<div class="home-row-top hide">
<div class="carousel-promo" data-slick='{"slidesToShow": 2, "slidesToScroll": 2}'>
  <?php foreach ($posts as $post) { ?>

    <!-- <a href="view.php?id=<?=$post['id']?>"> -->
    <div class="promo">
      <div class="content">
        <div class="preview-content">
          <?=$post['text']?>
        </div>
      </div>
    </div>
    <!-- </a> -->

<?php } ?>
</div>
<div class="feed-separator pt10"></div>
</div>
<?php } ?>
