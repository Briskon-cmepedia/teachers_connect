<div class="page-block">
  <h1>Remove from <?=$group_name?> Community</h1>
  <?php if ($members) { ?>

    <div class="card">

    <?php foreach ($members as $key => $member) { ?>

      <div class="profile-data">
        <div class="profile-name mt10"><?=$member['firstName'];?> <?=$member['lastName'];?></div>
        <div class="profile-email mt10"><?=$member['email'];?></div>
        <div class="profile-location mt10"><?=$location;?></div>
      </div>
      <?php end($members);
      if ($key !== key($members)) { ?>
        <div class="clear separator"></div>
      <?php } ?>

    <?php } ?>

    </div>

    <h2>Are you sure you want to remove them?</h2>
    <form method="post">
      <input type="hidden" name="group_id" value="<?=$group_id?>">
      <input type="hidden" name="confirm" value="1">

      <?php foreach ($members as $member) { ?>

        <input type="hidden" name="id[]" value="<?=$member['_id']['$oid']?>">

      <?php } ?>

      <input type="submit" class="button" value="YES"> <a class="button secondary ml30" href="javascript: history.go(-1)">NO</a>
    </form>


  <?php } if ($error != NULL) { ?>

    <h3><?=$error;?></h3>

  <?php } ?>
</div>
