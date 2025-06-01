
<?php 

if (isset($_POST['OK'])) {
	header("location: Index.php");
}
 ?>
<!DOCTYPE html>
<html>
<head>
	<title>	</title>
</head>
<body>
<h1>	Notification</h1>

<form method="post">
<button name="OK" type="submit" onclick="return confirm ('Voulez-vous aller sur la page index ? ')" >Notification</button>

</form>
</body>
</html>