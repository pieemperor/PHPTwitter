<?php
session_start();
$following = false;
include("dbconfig.php");
if(!isset($_GET['id'])){
	header("Location: index.php");
}
?>
<a href="index.php"><button class="btn btn-default">Return to home page</button></a>
<a href="viewAllUsers.php"><button class="btn btn-default">View All Twitter Users</button></a>

<?php
$q = "SELECT username from twitter_users WHERE id = ?";
$statement = $db->prepare($q);
$statement->execute(array($_GET['id']));
$row = $statement->fetch();
echo "<h1>{$row['username']}'s Tweets</h1>";

include("header.php");
if(isset($_SESSION['loggedin'])){
	//Check to see if user is already following the displayed user
	$q = "SELECT * FROM twitter_follows WHERE follower_id = ? AND followed_id = ?";
	$statement = $db->prepare($q);
	$statement->execute(array($_SESSION['userid'], $_GET['id']));

	if ($statement -> rowCount() == 1){
		$following = true;
	}

	/******************** Put follow into the database  *************************/

	if(isset($_GET['unfollowButton']) && $following){
		$q = "DELETE FROM twitter_follows WHERE follower_id = ? AND followed_id = ?"; 
		$statement = $db->prepare($q);
		$statement->execute(array($_SESSION['userid'], $_GET['id']));
		$following = false;
		}

	if(isset($_GET['followButton']) && !$following){
		$q = "INSERT INTO twitter_follows (follower_id, followed_id) VALUES (?,?)"; 
		$statement = $db->prepare($q);
		$statement->execute(array($_SESSION['userid'], $_GET['id']));
		$following = true;
	}


	/******************** DISPLAY FOLLOW BUTTON  *************************/
	if(!$following && $_SESSION['userid'] != $_GET['id']){
	?>
	<form name='follow' method="get" action='<?php echo $_SERVER['PHP_SELF']; ?>'>
		<button type="submit" name="followButton" value="">Follow</button>
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
	</form>

	<?php
	}
	/******************** DISPLAY UNFOLLOW BUTTON  *************************/
	if($following){
		?>
	<form name='unfollow' method="get" action='<?php echo $_SERVER['PHP_SELF']; ?>'>
		<button type="submit" name="unfollowButton" value="">Unfollow</button>
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
	</form>
	<?php
	}
}//end if logged in

/******************** DISPLAY ALL THE USER'S TWEETS *************************/
$q = "SELECT twitter_users.username, twitter_tweets.body, twitter_tweets.time_created FROM twitter_tweets JOIN twitter_users 
			ON twitter_tweets.user_id = twitter_users.id WHERE twitter_tweets.user_id = ? ORDER BY twitter_tweets.time_created DESC";
$statement = $db->prepare($q);
$statement->execute(array($_GET['id']));

if($statement->rowCount() > 0){
?>

<table cellSpacing="2" cellPadding="6" align="center" border="1">
    <tr>
       <th>Tweet</th>
       <th>Time of Tweet</th>
    </tr>

 <?php
while($row=$statement->fetch()){
  ?>
		<tr>
      <td><?php echo $row['body'];?></td>
			<td><?php $date = date_create($row['time_created']); echo date_format($date, 'M jS, Y g:ia');?></td>
    </tr>
<?php
}
}else{echo "<h1>This user has no tweets yet</h1>";}
  ?>
  </table>

<?php
function cleanInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>