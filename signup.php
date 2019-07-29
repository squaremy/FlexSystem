<html>
<head>
	<title>FAHS Flex System</title>
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<div class="topnav">
		<img id="logo" src="logo.png">
		<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
		<a id="schedulebutton" href="schedule.html" class="disable-select">My Schedule</a>
	</div>
	<div id="signupmenu">
		<p id="searchtxt"><?php echo $_GET["name"]; ?></p>
		<table id="weektable" style="display:block; top:30%" border=1>
		  <tr>
			<th width="15%">Monday</th>
			<th width="15%">Tuesday</th>
			<th width="15%">Wednesday</th>
			<th width="15%">Thursday</th>
			<th width="15%">Friday</th>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="mon" value="Monday"></td>
			<td><input type="checkbox" name="tue" value="Tuesday"></td>
			<td><input type="checkbox" name="wed" value="Wednesday"></td>
			<td><input type="checkbox" name="thu" value="Thursday"></td>
			<td><input type="checkbox" name="fri" value="Friday"></td>
		  </tr>
		</table>
	<div>
	<!--<script type="text/javascript" src="scripts/script.js"></script>-->
</body>
</html>
