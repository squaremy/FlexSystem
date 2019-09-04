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
		<img id="logo"  href="index.html" src="faflexlogo.svg">
		<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
		<a id="schedulebutton" href="schedule.html" class="disable-select">My Schedule</a>
	</div>
	<div id="signupmenu">
		<p id="searchtxt"><?php echo $_GET["name"]; ?></p>
		<!-- <div id="weektable"> -->
			<table id="weektable">
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
		<!-- </div> -->
		<button id="confirmsignup" type="button" onclick="confirmsignup">Sign Up</button>
	</div>
</body>
<script type="text/javascript" src="scripts/schedule.js"></script>
<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>
</html>
