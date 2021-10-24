<!DOCTYPE html>
<?php
session_start();
require_once "pdo.php";
?>
<html>
<head>
<title>Aditya Chauhan</title>
<?php require_once "head.php"; ?>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
}
</style>
</head>
<body>
<div class= 'container'>
<h1>Resume Recorder</h1>
<?php
if ( ! isset($_SESSION['user_id']) ) {
  echo("<p><a href='login.php'>Please log in</a></p>");
  //echo("<p><a href='add.php'>add.php</a> Should fail if not logged in.</p>");

}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
  }
if ( isset($_SESSION['user_id']) ) {
  echo('<p>Please add your resumes here.</p>');
  echo('<table border="1">'."\n");
  $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
  echo('<tr><th>Name</th><th>Headline</th><th>Action</th><tr>');
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $f = htmlentities($row['first_name']);
      $l = htmlentities($row['last_name']);
      echo "<tr><td>";
      echo('<a href="view.php?profile_id='.$row['profile_id'].'"> '.$f.' '.$l.' </a>');
      echo("</td><td>");
      echo(htmlentities($row['headline']));
      echo("</td><td>");
      echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
      echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
      echo("</td></tr>");

  }
  echo("</table>");
  #$sql = "SELECT COUNT(*) FROM profile";
  #$rows = $pdo->query('select count(*) from profile')->fetchColumn();
  #if ($row == false) {
  #  echo("No rows found");
  #}
  echo("<p><a href='add.php'>Add New Entry</a></p>");
  echo("<a href='logout.php'>Logout</a>");
}
 ?>
</div>
</body>
