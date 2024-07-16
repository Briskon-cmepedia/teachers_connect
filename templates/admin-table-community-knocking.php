

<div class="row card bootstrapiso">

  <?php if (count($members) > 0) { ?>

  <form id="table-community-members" action="admin-process-members.php" method="POST">

    <table class="table-select" data-order='[[ 4, "desc" ]]' data-page-length="25">
      <thead>
        <tr>
          <th data-orderable="false"></th>
          <th data-orderable="false"></th>
          <th data-sort="string">Name</th>
          <th data-sort="string">Email</th>
          <th data-sort="int" data-sort-default="desc" data-sort-onload="yes">Date Joined TC</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($members as $member) { ?>
        <tr>
          <td class="valign-mid"><?=$member['id']?></td>
          <td class="col-avatar">
              <div class="small">
              <?php if ( (strpos($member['avatar'], 'Object') == false) AND ($member['avatar'] != NULL) ) { ?>
              <img class="avatar" alt="avatar" src="image.php?id=<?=$member['avatar']?>&height=60" onerror="this.src='img/robot.svg'">
              <?php } else { ?>
              <img class="avatar" src="img/robot.svg" alt="robot avatar">
              <?php } ?>
              </div>
          </td>
          <td class="full-name valign-mid">
            <a href="profile.php?id=<?=$member['id']?>"><?=ucwords(strtolower($member['firstName']))?> <?=ucwords(strtolower($member['lastName']))?></a>
            <div class="subtext"><?=$member['city']?></div>
          </td>
          <td class="valign-mid"><?=$member['email']?></td>
          <td class="date valign-mid" data-sort="<?=$member['time']['$date']?>"><?=timestamp($member['time']['$date'], 'D j M Y g:i a')?></td>
        </tr>
      <?php  } ?>
      </tbody>
    </table>

    <input type="hidden" name="group_name" value="<?=$group_name?>">
    <input type="hidden" name="group_id" value="<?=$group_id?>">
    <input type="hidden" name="group_tile" value="<?=$group_tile?>">
    <input type="hidden" name="group_action" value="approve">
    <div class="dt-button-edit btn-group"> <input class="btn btn-success buttons-html5" id="button-member-remove" name="submit" type="submit" value="Approve"> </div>

  </form>

<?php } else { ?>

  <div class="alert-error">
    There are no membership requests currently.
  </div>

<?php } ?>
</div>
