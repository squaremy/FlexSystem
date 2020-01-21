<html>
<head>
	<title>FAHS Flex</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<!-- <meta name="apple-mobile-web-app-capable" content="yes"> -->
	<link rel="icon" href="favicon.ico">
	<meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>

<body>
	<div class="topnav">
		<a href="index.html"><img id="logo" src="faflexlogo.svg"></a>
		<a id="schedulebutton" class="disable-select">My Schedule</a>
		<a id="signupbutton" href="index.php" class="disable-select">Sign Up</a>
	</div>
	<p id="searchtxt">Type in a teacher below to sign up for flex.</p>
	<input id="searchbar" type="text" onkeyup="searchFilter1()" placeholder="Search for a teacher...">
	<table id="teachertable" border=1>
		<?php
		include "scripts/schedule.php";
		include "scripts/adminConstants.php";

		$query = "SHOW TABLES FROM franklin_flexSystem";
		if(!$result = mysqli_query($connect, $query)) {
			echo "Failed to obtain tables..." . mysqli_error($connect);
		} else {
			$counter = 0;
			while($tables = mysqli_fetch_array($result)) {
				$data = getTableData($tables[0], 0, $connect);
				if($data["type"] != null && $data["type"] == "teacher") {
					$name = $data["name"];
					if($counter == 0) echo "<tr>";
					else if($counter%4 == 0) echo "</tr><tr>";
					echo "<td style='padding: 10px;' id='$name' onclick='teacherclick(this);'>$name</td>";
					$counter++;
				}
			}
			echo "</tr><script>addTeachersToList()</script>";
		}
		?>
	</table>
	<script type="text/javascript" src="scripts/script.js"></script>
	<script type="text/javascript" src="scripts/signin.js"></script>
	<script type="text/javascript" src="scripts/schedule.js"></script>
	<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
	<div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
	<a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout();">Sign out</a>
	<div id="footerSpace"></div>
	<footer>
		<div class="foot">
			&copy; 2019 Jordan Martin and Grant Gupton
			<br/>
			Class of 2020
		</div>
	</footer>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</body>
</html>
