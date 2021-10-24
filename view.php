<?php

require_once "pdo.php";
require_once "util.php";

session_start();

$stmt = $pdo->query("SELECT first_name, last_name, email, headline, summary, profile_id FROM profile");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $f = htmlentities($row['first_name']);
  $l = htmlentities($row['last_name']);
  $e = htmlentities($row['email']);
  $h = htmlentities($row['headline']);
  $s = htmlentities($row['summary']);

}
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
?>

<html>
<head>
<title>Aditya Chauhan</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class='container'>
<h1>Profile Information</h1>
<p> First Name: <?= $f ?></p>
<p> Last Name: <?= $l ?></p>
<p> Email: <?= $e ?></p>
<p> Headline: <?= $h ?></p>
<p> Summary: <?= $s ?></p>
<p>Position:</p>
<ul>
<?php
$pos = 0;
foreach($positions as $position) {
  $pos++;
  echo('<li>'.$position['year'].": ".$position['description'].'</li>');

}
?>
</ul>
<p>Education:</p>
<ul>
<?php
$edu = 0;
foreach($educations as $education) {
  $edu++;
  echo('<li>'.$education['year'].": ".$education['name'].'</li>');
}
?>
</ul>
<p><a href="index.php">Cancel</a></p></p>
</div>
</body>
</html>
