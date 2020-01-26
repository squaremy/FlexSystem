<html>
<head>
	<title>Sign Up</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<!-- <meta name="apple-mobile-web-app-capable" content="yes"> -->
	<link rel="icon" href="favicon.ico">
	<meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>

<body>
	<div class="topnav">
		<a href="index.php"><img id="logo" src="faflexlogo.svg"></a>
		<a id="schedulebutton" class="disable-select">My Schedule</a>
		<a id="signupbutton" href="index.php" class="disable-select">Sign Up</a>
	</div>
	<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
	<p id="searchtxt"><?php echo $_GET["name"]; ?></p>
	<p id="underSearchtxt"><?php
		include "scripts/schedule.php";
		include "scripts/adminConstants.php";

		$teacherTable = getTeacherTable($_GET["name"], $connect);
		$teacherData = null;
		if($teacherTable != null && $teacherTable != '' && $teacherTable != "") $teacherData = getTableData($teacherTable, 0, $connect);
		if($teacherTable != null && $teacherData != null && $teacherData["room"] != null && $teacherData["room"] != "" && $teacherData["room"] != '') echo "rm " . $teacherData["room"];
		$myData = getTableData($_GET["user"], 0, $connect);
		$type = $myData["type"];
	?></p>
		<table id="weektable">
		  <tr>
			<th width="15%">Monday</th>
			<th width="15%">Tuesday</th>
			<th width="15%">Wednesday</th>
			<th width="15%">Thursday</th>
			<th width="15%">Friday</th>
		  </tr>
		  <tr>
			<td><input type="checkbox" id="monchk" name="mon" value="Monday"></td>
			<td><input type="checkbox" id="tuechk" name="tue" value="Tuesday"></td>
			<td><input type="checkbox" id="wedchk" name="wed" value="Wednesday"></td>
			<td><input type="checkbox" id="thuchk" name="thu" value="Thursday"></td>
			<td><input type="checkbox" id="frichk" name="fri" value="Friday"></td>
		  </tr>
		</table>
	<?php
		if($type == "student" && $signUpTimeout > (time() + (19 * 60 * 60)) %(24*60*60)) echo "<button id=\"confirmsignup\" type=\"button\" onclick=\"confirmsignup()\">Sign Up</button>";
		else if($signUpTimeout < (time() + (19 * 60 * 60)) % (24*60*60)) echo "<button id=\"confirmsignup\" type=\"button\" onclick=\"confirmsignup()\" disabled>Past Sign Up Time...</button>";
	?>
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
</body>
<script type="text/javascript" src="scripts/schedule.js"></script>
<script type="text/javascript" src="scripts/signin.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</html>
