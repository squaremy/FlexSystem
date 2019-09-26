<!DOCTYPE html>
<html>
	<head>
		<title>My Schedule</title>
		<link rel="stylesheet" href="css/style.css"/>
		<link rel="apple-touch-icon" href="/faflexappicon.png"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<link rel="icon" href="favicon.ico"/>
		<meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
		<script src="https://apis.google.com/js/platform.js" async defer></script>
		<?php
			$connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!", "techmeds_FlexSystem") or die("Connection to database failed: " . mysqli_connect_error());
			$user = $_GET["user"];
			$sql = "SELECT * FROM `$user`";
			$data = null;
			if(!$data = mysqli_query($connect, $sql)) {
				die("error: " . mysqli_error($connect));
			}
			$parsedData = mysqli_fetch_row($data);
			$name = $parsedData["name"];
			$email = $parsedData["email"];
			$room = $parsedData["room"];
			$day = $parsedData["day"];
			$type = $parsedData["type"];

			// update student & teacher data
		?>
	</head>

	<body>
		<div class="topnav">
			<img id="logo" href="index.html" src="faflexlogo.svg"/>
			<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
			<a id="schedulebutton" href="schedule.html" class="disable-select">My Schedule</a>
		</div>
		<div class="signupmenu">
			<p id="searchtxt"></p>
			<table id="weektable">
				<?php
					echo "<tr>";
					for($i = 0; $i < mysqli_num_rows($data); $i++) {
						mysqli_data_seek($data, $i);
						$parsedData = mysqli_fetch_row($data);
						$day = $parsedData["day"];
						echo "<th>" . $day . "</th>";
					}
					echo "</tr><tr>";

					if($type == "student") {
						for($i = 0; $i < mysqli_num_rows($data); $i++) {
							mysqli_data_seek($data, $i);
							$parsedData = mysqli_fetch_row($data);
							$teacher = $parsedData["teacher"];
							echo "<td>" . $teacher . "</td>";
						}
						echo "</tr>";
					} else {
						for($i = 0; $i < mysqli_num_rows($data); $i++) {
							mysqli_data_seek($data, $i);
							$parsedData = mysqli_fetch_row($data);
							$available = $parsedData["available"];
							echo "<td>" . $available . "</td>";
						}
						echo "</tr>";
					}
				 ?>
			</table>
			<table id="flexstudents">
				<?php
					if($type == 'teacher') {

					}
				 ?>
			</table>
			<table id="visitingstudents">
				<?php
					if($type == 'teacher') {

					}
				 ?>
			</table>
		</div>
		<div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
		<a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout()">Sign out</a>
		<!-- <script type="text/javascript" src="scripts/schedule.js"></script> -->
		<!-- <script>loadUser()</script> -->
		<script type="text/javascript" src="scripts/signin.js"></script>
	</body>
<!-- <script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script> -->
</html>
