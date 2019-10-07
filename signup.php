<html>
<head>
	<title>FAHS Flex System</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" href="favicon.ico">
	<meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>

<body>
	<div class="topnav">
		<a href="index.html"><img id="logo"  href="index.html" src="faflexlogo.svg"></a>
		<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
		<a id="schedulebutton" class="disable-select">My Schedule</a>
	</div>
	<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
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
				<td><input type="checkbox" id="monchk" name="mon" value="Monday"></td>
				<td><input type="checkbox" id="tuechk" name="tue" value="Tuesday"></td>
				<td><input type="checkbox" id="wedchk" name="wed" value="Wednesday"></td>
				<td><input type="checkbox" id="thuchk" name="thu" value="Thursday"></td>
				<td><input type="checkbox" id="frichk" name="fri" value="Friday"></td>
			  </tr>
			</table>
		<!-- </div> -->
		<button id="confirmsignup" type="button" onclick="confirmsignup()">Sign Up</button>
	</div>
	<div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
	<a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout();">Sign out</a>
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
<script>loadData('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json', JSON.parse(sessionStorage.getItem('myUserEntity'))["Email"]);</script>
<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>
</html>
