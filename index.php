<?php

/************************* REDIRECT TO CLIENT'S HOME PAGE ***************************/
	session_start();
	header("Cache-Control: no-cache, must-revalidate");
	include("dbconfig.php");


	if(isset($_SESSION['loggedin'])){
		header("Location: userHome.php");
	}
/************************** HANDLING CLIENT LOGIN ATTEMPT ***************************/
	if((isset($_POST['username']))
		&&(isset($_POST['password']))
		&&(isset($_POST['loginbutton'])))
	{
		
		// Now, let's try to access the database table containing the users
		try
		{
			$query = "SELECT * FROM twitter_users WHERE username = :user and password = :pw";
			$statement = $db -> prepare($query);
			$statement -> execute(array(
				'user' => $_POST['username'], 
				'pw' => md5($_POST['password']))
			);
			if ($statement -> rowCount() == 1)
			{
				$_SESSION['loggedin']=TRUE;
				while($row=$statement->fetch()){
					$id = $row['id'];
				}
				$_SESSION['userid'] = $id;
				// Get the user details from the SINGLE returned database row
				$row = $statement -> fetch();
				 header("Location: userHome.php");

			}
			else
				echo("<h1>Invalid username or password.</h1>");		

			// Close the statement and the database
			$statement = null;
			$db = null;
		}
		catch (Exception $error) 
		{
			echo "Error occurred accessing user privileges: " . $error->getMessage();
		}
	}

	include("header.php");
	echo "<h1 class='text-primary'>Login to Twitter</h1>";
/*********************** PRESENTING CLIENT WITH LOGIN SCREEN ************************/
	if(!isset($_SESSION['loggedin']))
	{
?>
		<form name='login' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
			User name: <input type='text' name='username' value="<?php if(isset($_POST['username'])) echo $_POST['username'];?>" /><br />
			Password: <input type='password' name='password' value="<?php if(isset($_POST['password'])) echo $_POST['password'];?>"/><br />
			<input type='submit' class="btn btn-primary" name='loginbutton' value='Login' />
		</form>
<?php
	}
?>
<a href="createUser.php"><button class="btn btn-default">Create an Account</button></a>
<a href="viewAllUsers.php"><button class="btn btn-default">View All Twitter Users</button></a>
