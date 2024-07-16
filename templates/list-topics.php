<div id="topics">
  <div class="topics-body">
    <div class="page-title">
      <h1>Topics</h1>
    </div>
    <div class="topic-list no-padding">
      <?php foreach ($topics as $topic) { ?>
        <div class="topic-header">
          <h2><?=ucwords($topic['group'])?></h2>
        </div>
        <div class="topic-section">
          <?php foreach ($topic['topics'] as $term) { ?>
            <a href="feed-topic.php?topic=<?=$term?>">
              <?php if (in_array(strtolower($term), $topics_followed)) { ?>
              <div class="topic-term followed">
              <?php } else { ?>
              <div class="topic-term">
              <?php } ?>
                <?=ucwords($term)?>
              </div>
            </a>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
