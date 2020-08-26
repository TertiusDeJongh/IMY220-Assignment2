<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "u19349302_";//they used root
	$password = "1234";
	$database = "dbuser";
	$mysqli = mysqli_connect($server, $username, $password, $database);
	$id = false;
	//need to create new users from the register form:
	$register = false;
	if(isset($_POST["register"])){
		$register = true;
	}
	if($register){
		//add the fields in the table as needed
		$sql = "INSERT INTO tbusers (name, surname ,password, email, birthday) VALUES ('$_POST[regName]', '$_POST[regSurname]', '$_POST[pass1]', '$_POST[regEmail]', '$_POST[regBirthDate]')";
		if(mysqli_query($mysqli, $sql)){
		}
		else{
			echo "uhh ohhh";
		}
		$email = $_POST["regEmail"];
		$pass = $_POST["pass1"];
	}
	else{
		$email = false;
		$pass = false;

	}
	//mow modify these suckers
	if(isset($_POST["login"])){
		$email = isset($_POST["loginName"]) ? $_POST["loginName"] : false;//I changed these up to work with the index
		$pass = isset($_POST["loginPassw"]) ? $_POST["loginPassw"] : false;	
	}
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
	
	//now one has to check for the images upload... which is the submit button on this page
	if(isset($_POST["submit"])){
		//$email = $_FILES["hiddenEmail"];
		//$pass = $_FILES["loginPassw"];
		
		$email = $_POST["hiddenEmail"];
		$pass = $_POST["hiddenPass"];
		if(isset($_FILES["picToUpload"])){
			//echo 'hello';
			$targetDir = "gallery/";
			$uploadFile = $_FILES["picToUpload"];
			$targetFile = $targetDir.basename($uploadFile["name"]);
			$imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
			
			//now check if it is an jpeg image or not...
			$megaByte = 1024*1024;
			if($uploadFile["type"] == "image/jpeg" && $uploadFile["size"] < $megaByte){
				//echo 'hello';  now save it somewhere and add it to the database and get user_id  of the current passworda and email.../
				if(move_uploaded_file($uploadFile["tmp_name"], $targetFile)){
					//now look for stuff in the database and add the name and well stuff
					$q = "SELECT user_id FROM tbusers WHERE email = '$email' AND password = '$pass'";
					$r =  $mysqli->query($q);
					$col = mysqli_fetch_array($r);
					$u_id = $col['user_id'];
					$id = $u_id;
					//now insert in the image gallery table
					$sql = "INSERT INTO tbgallery (user_id, filename) VALUES ('$u_id', '$uploadFile[name]')";
					if(mysqli_query($mysqli, $sql)){
						//echo basename($uploadFile["name"]);
					}
					else{
						echo "uhh ohhh";
					}
				}
				
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Tertius de Jongh">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";?>
				
					 	<form action="login.php" method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<input type="file" class="form-control" name="picToUpload" id="picToUpload" ><br/>
									<input type="hidden" name="hiddenEmail" value="<?php echo $row['email']; ?>">
									<input type="hidden" name="hiddenPass" value="<?php echo $row['password']; ?>">
									<input type="submit" class="btn btn-standard" value="Upload Image" name="submit" >
								</div>
						</form>
		<?php 
				//<div class='col-3' style='background-image: url(gallery/imagename.png)'></div> now add this wth the querry
					if($id){
						$q ="SELECT filename FROM tbgallery WHERE user_id = '$id'" ;
						$res = $mysqli->query($q);
						if(mysqli_num_rows($res) > 0){
							echo "<h2>Image Gallery</h2>
								
								";
		?>					<div class="row imageGallery">
		<?php				while($row = mysqli_fetch_array($res)){
								//echo $row['filename'];
								echo "<div class='col-3' style='background-image: url(gallery/".$row['filename'].")'></div>";
							}
		?>					</div>
		<?php
							
							
						}
					}
				
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>