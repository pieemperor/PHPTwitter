<?php
include("header.php");
include("dbconfig.php");
$stmnt = $db->prepare("SELECT * FROM twitter_users");
$stmnt->execute();
?>
<h1 class="text-primary">Index of Users</h1>
	<a href="index.php"><button class="btn btn-default">Return to home page</button></a>
<table cellSpacing="2" cellPadding="6" align="center" border="1">
    <tr>
       <th>ID</th>
       <th>Username</th>
       <th>Given Name</th>
       <th>Family Name</th>
    </tr>

 <?php
while($row=$stmnt->fetch()){
  ?>
		<tr>
       <td><a href="userPage.php?id=<?php echo $row['id']?>"><button class="btn btn-primary btn-block"><?php echo $row['id'];?></button></a></td>
       <td><?php echo $row['username'];?></td>
       <td><?php echo $row['given_name'];?></td>
       <td><?php echo $row['family_name'];?></td>
    </tr>
<?php
}//end while

?>
  </table>
