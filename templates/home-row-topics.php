<?php if ($topics) { ?>
<div class="home-row hide">
<h2 class="carousel-title">
  <div class="show-feed">
    <a href="topics.php">View all topics</a>
  </div>
  Popular Topics
</h2>

<div class="topic-section pt20">
  <?php foreach ($topics as $topic => $count) { ?>
    <a href="feed-topic.php?topic=<?=$topic?>"><div class="topic-term"><?=ucwords($topic)?></div></a>
  <?php } ?>
</div>

<div class="feed-separator pt0"></div>
</div>
<?php } ?>
