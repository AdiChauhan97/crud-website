<?php
session_start();
require_once "pdo.php";

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset($_SESSION["account"]);
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        #$failure = "Email and password are required";
        $_SESSION["error"] = "Username and password are required";
        header( 'Location: login.php' ) ;
        return;
    }
    else if (strpos($_POST['email'], '@') === false) {
      #$failure = 'Email must have an at-sign (@)';
      $_SESSION["error"] = "Email must have an at-sign (@)";
      header( 'Location: login.php' ) ;
      return;
    }
      else {
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
         WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row !== false ) {
         $_SESSION['name'] = $row['name'];
         $_SESSION['user_id'] = $row['user_id'];
         header("Location: index.php");
         return;
         }
         else {
            $_SESSION["error"] = "Incorrect password";
            header( 'Location: login.php' ) ;
            return;
        }
    }
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<title>Aditya Chauhan</title>
<?php require_once "head.php" ?>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
}
?>
<form method="POST">
<label for="email">User Name</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</div>
</body>
