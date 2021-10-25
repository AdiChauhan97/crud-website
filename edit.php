<?php

require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION['user_id']) ) {
  die('ACCESS DENIED');
}
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
    && isset($_POST['headline']) && isset($_POST['summary'])) {

      $msg = validateProfile();
      if ( is_string($msg)) {
      $_SESSION["error"] = $msg;
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }

    $msg = validatePos();
    if ( is_string($msg)) {
    $_SESSION["error"] = $msg;
      header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
      return;
    }


    $sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em,
    headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':pid' => $_REQUEST['profile_id'],
      ':uid' => $_SESSION['user_id'],
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':he' => $_POST['headline'],
      ':su' => $_POST['summary'])
    );

    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    insertPos($pdo, $_REQUEST['profile_id']);

    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    insertEducations($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = "Record Updated";
    header("Location: index.php");
    return;

}
  $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row === false) {
    $_SESSION['error'] = 'Bad value';
    header('Location: index.php');
    return;
}
$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);
$pid = $row['profile_id'];

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
<title>Aditya Chauhan</title>
 <?php require_once "head.php"; ?>
</head>
<body>
<div class= 'container'>
<h1>Edit Resume</h1>
<?php
flash_messages();
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $f ?>" size="40"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $l ?>" size="40"></p>
<p>Email:
<input type="text" name="email" value="<?= $e ?>"></p>
<p>Position Applied For:
<input type="text" name="headline" value="<?= $h ?>"></p>
<p>Summary:</br>
<textarea name="summary" rows="8" cols="80"><?= $s ?></textarea>

<?php
$countEdu = 0;
echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
echo('<div id="edu_fields">'."\n");

  foreach( $schools as $school) {
    $countEdu++;
    echo('<div id="edu'.$countEdu.'">'."\n");
    echo('<p>Year: <input type="text" name="edu_year'.$countEdu.'"');
    echo('value="'.$school['year'].'"/>'."\n");
    echo('<input type="button" value="-"');
    echo('onclick="$(\'#edu'.$countEdu.'\').remove(); return false;">'."\n");
    echo("</p>\n");
    echo("<p>School: ");
    echo('<input type="text" size="80" class="school" name="edu_school'.$countEdu.'"'); 
    echo('value="'.htmlentities($school['name']).'"/>'."\n");
    echo('</div>');
    //echo(htmlentities($school['name'])."\n");
    //echo("\n</textarea>\n</div>\n");
  }
echo("</div></p>\n");


$countPos = 0;
echo('<p>Previous Positions: <input type="Submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
foreach( $positions as $position) {
  $countPos++;
  echo('<div id="position'.$countPos.'">'."\n");
  echo('<p>Year: <input type="text" name="year'.$countPos.'"');
  echo('value="'.htmlentities($position['year']).'"/>'."\n");
  echo('<input type="button" value="-"');
  echo('onclick="$(\'#position'.$countPos.'\').remove(); return false;">'."\n");
  echo("</p>\n");
  echo('<textarea name="desc'.$countPos.'" rows="8" cols="80">'."\n");
  echo(htmlentities($position['description'])."\n");
  echo("\n</textarea>\n</div>\n");
}
echo("</div></p>\n");
?>
<input type="hidden" name="profile_id" value="<?= $pid ?>">
<p>
<input type="submit" value="Save">
<a href="index.php">Cancel</a>
</p>

</form>
<script>
countPos = <?= $countPos ?> ;
countEdu = <?= $countEdu ?>;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);

        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        // Grab some HTML with hot spots and insert into the DOM
        var source  = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        // Add the even handler to the new ones
        $('.school').autocomplete({
            source: "school.php"
        });

    });

    $('.school').autocomplete({
        source: "school.php"
    });

});

</script>
<!-- HTML with Substitution hot spots -->
<script id="edu-template" type="text">
  <div id="edu@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
    <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
  </div>
</script>
</div>
</body>
