<?php

session_start();

$form_login = "<html lang="en"><head><link rel='stylesheet' href='css/normalize.css'><link rel='stylesheet' type='text/css' href='css/styles.css'></head><body><div class='block-login'><form method='post'><input name='username' type='text'><input name='password' type='password'><input name='button-login' type='submit' value='Login'></form></div></body></html>";

if (isset($_REQUEST['logout'])) {
   session_unset();
}

if ( $_POST['username'] AND $_POST['password'] ) {

  if ( ( password_verify($_POST['username'], '$2y$10$wxjMZSfm1H1HBS2zd8eJr.gow1knzd1M99c8j7hG2pKJjktpEpBqu') AND password_verify($_POST['password'], '$2y$10$5AuEo/zkEDoyi45BUyYxI.Lh0ILTuMflMn23i6/aTwQojsS/ibcPC') ) OR ( password_verify($_POST['username'], '$2y$10$eH25ZNOLpuCmJBpgaQk2oeTX.Z1MNztTJu5PcyUmVJh6UDlULx//u') AND password_verify($_POST['password'], '$2y$10$q.WWHmP2Pmfemt3Rifogi.A4DoRAMwbE/0JTCLHBUh/QAfJmrRmo6') ) OR ( password_verify($_POST['username'], '$2y$10$fPEni4uEtQmvRQ/jr1JVMedu/1aucSu3jEyIZwrBp.91mz9zPKRMK') AND password_verify($_POST['password'], '$2y$10$iz52MOk.S249uIBG1ZP1lObLd7ZQgtEB7GDTfU7i/NnYn7ipzeEMG') ) OR ( password_verify($_POST['username'], '$2y$10$65M15YThCRLT5.EivcMCSuTyJL7U3c3nyxGKte1DsQDnOhG1Ry9lK') AND password_verify($_POST['password'], '$2y$10$6HLIAc/KjxBVwKcR6q6Lz.RvdS75u2f5ZdyXDL0dQVEH2TbWyfcly') ) ) {

    date_default_timezone_set('America/New_York');
    try {

      // Create or open a database file
      $db = new PDO('sqlite:../login.sqlite3');

      // Creating a table
      $db->exec(
      "CREATE TABLE IF NOT EXISTS attempts (
          id INTEGER PRIMARY KEY,
          user_time TEXT,
          user_ip TEXT,
          user_agent TEXT,
          user_email TEXT,
          user_outcome TEXT)"
      );

      // Delete table when required
      // $db->exec(
      // "DROP TABLE IF EXISTS attempts"
      // );

      //now output the data to a simple html table...

         print "<script type='text/javascript' src='jquery-latest.js'></script>
<script type='text/javascript' src='jquery.tablesorter.min.js'></script><script type='text/javascript'>$(document).ready(function(){ $('#myTable').tablesorter(); } );</script>";
         print "<style>body{font-family:'Open Sans';} table{border-collapse: collapse;font-size:10px;}table, th, td {border:1px #D4D6D9 solid;}</style>";
         print "<table id='myTable' class='tablesorter'>";
         print "<thead><tr><th>Id</th><th>Time</th><th>IP</th><th>Agent</th><th>Email</th><th>Outcome</th></tr></thead>";
         print "<tbody>";
         $result = $db->query('SELECT * FROM attempts');
         foreach($result as $row)
         {
           print "<tr><td>".$row['id']."</td>";
           print "<td>".date('d M Y H:i:s',$row['user_time'])."</td>";
           print "<td>".$row['user_ip']."</td>";
           print "<td>".$row['user_agent']."</td>";
           print "<td>".$row['user_email']."</td>";
           print "<td>".$row['user_outcome']."</td></tr>";
         }
         print "</tbody>";
         print "</table>";

      // close the database connection
      $db = NULL;

    } catch (Exception $e) {

      echo $e->getMessage();
      die();

    }


  } else {

    echo $form_login;

  }

} else {

  echo $form_login;

}
