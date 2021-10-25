<?php

require_once "pdo.php";
require_once "util.php";

session_start();

$stmt = $pdo->query("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
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
<h1>Resume Information</h1>
<p><strong>First Name: </strong><?= $f ?></p>
<p><strong>Last Name: </strong><?= $l ?></p>
<p><strong>Email: </strong><?= $e ?></p>
<p><strong>Position Applied For: </strong><?= $h ?></p>
<p><strong>Summary: </strong><?= $s ?></p>
<p><strong>Education:</strong></p>
<ul>
<?php
$edu = 0;
foreach($educations as $education) {
  $edu++;
  echo('<li>'.$education['year'].": ".$education['name'].'</li>');
}
?>
</ul>
<p><strong>Previous Positions:</strong></p>
<ul>
<?php
$pos = 0;
foreach($positions as $position) {
  $pos++;
  echo('<li>'.$position['year'].": ".$position['description'].'</li>');

}
?>
</ul>
<p><a href="index.php">Cancel</a></p></p>
</div>
</body>
</html>
