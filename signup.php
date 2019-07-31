<html>
<head>
	<title>FAHS Flex System</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" href="favicon.ico">
</head>

<body>
	<div class="topnav">
		<img id="logo" src="faflexlogo.svg">
		<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
		<a id="schedulebutton" href="schedule.html" class="disable-select">My Schedule</a>
	</div>
	<div id="signupmenu">
		<p id="searchtxt"><?php echo $_GET["name"]; ?></p>
		<table id="weektable" style="display:block; top:30%">
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
	</div>
	<table id="studenttable" style="display:block; top:50%">

	</table>
	<script type="text/javascript" src="scripts/listscript.js"></script>
</body>
</html>
