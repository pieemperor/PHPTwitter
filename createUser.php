<?php
	include("dbconfig.php");
  $givenName = $username = $familyName = $nameErr = $password = "";
  $usrTakenErr = "I'm sorry. That username is already taken";

  if(isset($_POST['username'])){$username = cleanInput($_POST['username']);}
  if(isset($_POST['password'])){$password = cleanInput($_POST['password']);}
  if(isset($_POST['givenName'])){$givenName = cleanInput($_POST['givenName']);} else{$givenName = null;}
  if(isset($_POST['familyName'])){$familyName = cleanInput($_POST['familyName']);} else{$familyName = null;}

  if(isset($_POST['submitAccount'])){
    if(!empty($username) && !checkUsername($username) && !empty($password)){
      $q = "INSERT INTO twitter_users (username, password, given_name, family_name) VALUES (?, ?, ?, ?)";
      $statement = $db->prepare($q);
      $statement->execute(array($username, md5($password), $givenName, $familyName));
      $_GET['inserted'] = true;
      header("Location: createUser.php?inserted");
    }
    else{
      header("Location: createUser.php?failure");
    }
  }

//Returns true if username is taken - Returns false if username is not taken
function checkUsername($username){
	include("dbconfig.php");
	$count = 0;
  $usernameQuery = "SELECT * FROM twitter_users WHERE username = ?";
  $stmt = $db->prepare($usernameQuery);
  $stmt->execute(array($username));

	while($row = $stmt->fetch()){
		$count++;
	}
  if($count == 1){
    $usernameAvailable = true;
  }
  else{
    $usernameAvailable = false;
  }
  return $usernameAvailable;
}

//function to clean the inputs - Got help from W3 Schools
function cleanInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if(isset($_GET['inserted']))
{
  ?>
    <strong>Account created successfully </strong> <a href="index.php">HOME</a>!
    <?php
}
else if(isset($_GET['failure']))
{
  ?>
    <strong>ERROR creating account!</strong>
    <?php
}

include("header.php");
?>

<style>
.error {color: #FF0000;}
</style>
<form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
  Username: <input type="text" name="username" value="<?php echo $username;?>">
  <span class="error">* <?php if(checkUsername($username)){echo $usrTakenErr;}?></span>
  <br><br>
  Password: <input type="password" name="password" value="<?php echo $password;?>">
  <span class="error">*</span>
  <br><br>
  First Name: <input type="text" name="givenName" value="<?php echo $givenName;?>">
  <br><br>
  Last Name: <input type="text" name="familyName" value="<?php echo $familyName;?>">
  <br><br>
  <button type="submit" name="submitAccount" value="Submit" class="btn btn-primary">Submit</button>
</form>

	<a href="index.php"><button class="btn btn-default">Return to login page</button></a>