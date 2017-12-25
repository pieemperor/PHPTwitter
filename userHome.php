<?php
  session_start();
	$liked = false;
	include("dbconfig.php");

if(!isset($_SESSION['loggedin'])){
	header("Location: index.php");
}
	/***************************** HANDLING CLIENT LOGOUT *******************************/
	if(isset($_POST['logout']))
	{
		$_SESSION = array();
		session_destroy();
    header("Location: index.php");
	}
	include("header.php");
	?>
	<h1 class="text-primary">Welcome to your home page!</h1>
	<nav class="col-md-12">
			<a href="viewAllUsers.php"><button class="btn btn-default col-md-2" >View All Twitter Users</button></a>
      <form name='logout' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
				<button type='submit' class="btn btn-default col-md-2" name='logout'>Click to logout</button>
      </form>
	</nav>
	<?php
/***************************** HANDLING TWEETS *******************************/
	if(isset($_POST['createTweet']) && isset($_POST['tweet'])){
		if(strlen($_POST['tweet']) > 0 && strlen($_POST['tweet']) < 141){
			$tweetContent = cleanInput($_POST['tweet']);
			$q = "INSERT INTO twitter_tweets (body, user_id, time_created) VALUES (?,?,NOW())";
			$statement = $db->prepare($q);
			$statement->execute(array(cleanInput($tweetContent), $_SESSION['userid']));
		}
	}

	if(isset($_SESSION['loggedin'])){
	?>

<div class="row">
	<form name="makeATweet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<textarea class="col-md-3 col-md-offset-4" style="display:block; margin-bottom:25px;" rows="4" cols="40" name="tweet" placeholder="Enter tweet here" maxlength="140"></textarea>
		<button type="submit"  style="margin-top:53px;"class="btn btn-primary col-md-1" name="createTweet" value="newTweet" >Tweet</button>
	</form>
</div>


<?php
	}//end of forms
/************ DISPLAY ALL THE USER'S OWN TWEETS AND ALL OF THE USERS YOU'RE FOLLOWING *******************/
$q = "SELECT twitter_users.id, twitter_users.username, twitter_tweets.body, twitter_tweets.time_created, twitter_tweets.id AS tweetid, twitter_tweets.user_id
			FROM twitter_users JOIN twitter_tweets ON twitter_users.id = twitter_tweets.user_id 
			LEFT JOIN twitter_follows ON twitter_users.id = twitter_follows.followed_id 
			WHERE twitter_follows.followed_id = twitter_tweets.user_id AND twitter_follows.follower_id = ? 
			OR twitter_tweets.user_id = ? ORDER BY twitter_tweets.time_created DESC";
$statement = $db->prepare($q);
$statement->execute(array($_SESSION['userid'], $_SESSION['userid']));
?>

<table>
    <tr>
      <th>Username</th>
      <th>Tweet</th>
			<th>Time Created</th>
    </tr>

 <?php
while($row=$statement->fetch()){
  ?>
		<tr>
      <td><a href="userPage.php?id=<?php echo $row['user_id']?>" class="btn btn-primary btn-block"><?php echo $row['username'];?></a></td>
      <td><?php echo $row['body'];?></td>
			<td><?php $date = date_create($row['time_created']); echo date_format($date, 'M jS, Y g:ia');?></td>
    </tr>
<?php
}
?>
  </table>
<?php

include("footer.php");

function cleanInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>