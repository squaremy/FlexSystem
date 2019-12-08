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
	</head>

	<body>
		<div class="topnav">
			<a href="index.html"><img id="logo" src="faflexlogo.svg"></a>
			<a id="schedulebutton" class="disable-select">My Schedule</a>
			<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
		</div>
		<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
		<?php
			include "scripts/schedule.php";

			$connect = mysqli_connect("localhost", "franklin_flexsys", "PASSWORD", "franklin_flexSystem") or die("Connection to database failed: " . mysqli_connect_error());
			$user = $_GET["user"];
			$name = $_GET["name"];

			if($name != '???' && $user != '???' && $_GET["signedup"] != '3') {
				$newUser = createNewUserIfNonexistent($user, $connect);
				if($newUser == true){
					echo "<script type=\"text/javascript\">window.location.href=\"updateHomeroom.php\"</script>";
				} else {
					if(studentTableIsEmpty($user, $connect) == true) {
						addDefaultStudentData($user, $name, $connect);
					}
					if(studentRoomIsEmpty($user, $connect) == true){
						$tempRoom = $_GET["room"];
						if($tempRoom != null && $tempRoom != 'null' && $tempRoom != '') {
							$query = "UPDATE `$user` SET room='$tempRoom',teacher='$tempRoom'";
							if(!mysqli_query($connect, $query)) {
								echo "Query failed: " . mysqli_error($connect);
							}
						} else {
							echo "<script type=\"text/javascript\">window.location.href=\"updateHomeroom.php\"</script>";
						}
					}
				}

				$data = getRawData($user, $connect);
				$parsedData = updateCurrentData($user, $connect);

				$email = $parsedData["email"];
				$room = $parsedData["room"];
				$day = $parsedData["day"];
				$type = $parsedData["type"];

				if($type == 'student') {
					if($_GET["signedup"] == '1') {
						$targetTeacher = $_GET["teacher"];
						$teacherTable = getTeacherTable($targetTeacher, $connect);
						$teacherData = getTableData($teacherTable, 0, $connect);
						$goingMon = filter_var($_GET["mon"], FILTER_VALIDATE_BOOLEAN);
						$goingTue = filter_var($_GET["tue"], FILTER_VALIDATE_BOOLEAN);
						$goingWed = filter_var($_GET["wed"], FILTER_VALIDATE_BOOLEAN);
						$goingThu = filter_var($_GET["thu"], FILTER_VALIDATE_BOOLEAN);
						$goingFri = filter_var($_GET["fri"], FILTER_VALIDATE_BOOLEAN);
						updateSignup($goingMon, $targetTeacher, 0, $user, $connect);
						updateSignup($goingTue, $targetTeacher, 1, $user, $connect);
						updateSignup($goingWed, $targetTeacher, 2, $user, $connect);
						updateSignup($goingThu, $targetTeacher, 3, $user, $connect);
						updateSignup($goingFri, $targetTeacher, 4, $user, $connect);
					} else if($_GET["signedup"] == '0' && $_GET["room"] != null && $_GET["room"] != 'null' && $_GET["room"] != '') {
						$tempRoom = $_GET["room"];
						$query = "UPDATE `$user` SET room='$tempRoom',teacher='$tempRoom'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}
					}
					for($day = 0; $day < 5; $day++) {
						$data = getRawData($user, $connect);
						mysqli_data_seek($data, $day);
						$parsedData = mysqli_fetch_assoc($data);
						if($parsedData["teacher"] != $parsedData["room"]) {
							$teacherTable = getTeacherTable($parsedData["teacher"], $connect);
							$available = filter_var($teacherTable["available"], FILTER_VALIDATE_BOOLEAN);
							if($teacherTable != null && !teacherIsAvailable($teacherTable, $day, $connect) && !$available) {
								$room = $parsedData["room"];
								$query = "UPDATE `$user` SET teacher='$room' WHERE id='$day'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					}
				} else if($type == 'teacher'){
					if($_GET["signedup"] == '1') {
						$swapMon = filter_var($_GET["mon"], FILTER_VALIDATE_BOOLEAN);
						$swapTue = filter_var($_GET["tue"], FILTER_VALIDATE_BOOLEAN);
						$swapWed = filter_var($_GET["wed"], FILTER_VALIDATE_BOOLEAN);
						$swapThu = filter_var($_GET["thu"], FILTER_VALIDATE_BOOLEAN);
						$swapFri = filter_var($_GET["fri"], FILTER_VALIDATE_BOOLEAN);

						flipAvailability($swapMon, $data, 0, $user, $connect);
						flipAvailability($swapTue, $data, 1, $user, $connect);
						flipAvailability($swapWed, $data, 2, $user, $connect);
						flipAvailability($swapThu, $data, 3, $user, $connect);
						flipAvailability($swapFri, $data, 4, $user, $connect);
					} else if($_GET["signedup"] == '2') {
						updateKickedStudents(explode(";", $_GET["tokick"]), $parsedData, $connect);
					} else if($_GET["signedup"] == '4') {
						$slots = explode(";", $_GET["slots"]);
						$data = getRawData($user, $connect);
						updateSlots($slots, $data, $connect);
					}
					for($day = 0; $day < 5; $day++) {
						$data = getRawData($user, $connect);
						mysqli_data_seek($data, $day);
						$parsedData = mysqli_fetch_assoc($data);
						availabilityUpdates($day, $parsedData, $user, $connect);
					}
				}
			} else if($_GET["signedup"] == '3') {
				$roomNum = filter_var($_GET["roomNum"], FILTER_VALIDATE_INT);
				$flexStudents = $_GET["flexStudents"];
				$slots = filter_var($_GET["slots"], FILTER_VALIDATE_INT);

				createTeacherTable($user, $name, $roomNum, $flexStudents, $slots, $connect);
				$data = getRawData($user, $connect);
				$parsedData = updateCurrentData($user, $connect);

				$email = $parsedData["email"];
				$room = $parsedData["room"];
				$day = $parsedData["day"];
				$type = $parsedData["type"];
			}
		?>
		<p id="searchtxt"><?php echo $name; ?></p>
		<table id="weektable">
			<?php
				echo "<tr>";
				for($i = 0; $i < mysqli_num_rows($data); $i++) {
					mysqli_data_seek($data, $i);
					$parsedData = mysqli_fetch_assoc($data);
					$day = $parsedData["day"];
					echo "<th>" . $day . "</th>";
				}
				echo "</tr><tr>";

				if($type == 'student') {
					for($i = 0; $i < mysqli_num_rows($data); $i++) {
						mysqli_data_seek($data, $i);
						$parsedData = mysqli_fetch_assoc($data);
						$teacher = $parsedData["teacher"];
						echo "<td>" . $teacher . "</td>";
					}
					echo "</tr>";
				} else {
					for($i = 0; $i < mysqli_num_rows($data); $i++) {
						mysqli_data_seek($data, $i);
						$parsedData = mysqli_fetch_assoc($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						if($available == true) echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">AVAILABLE</a></td>";
						else echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">BLOCKED</a></td>";
					}
					echo "</tr><tr>";
					for($i = 0; $i < mysqli_num_rows($data); $i++) {
						mysqli_data_seek($data, $i);
						$parsedData = mysqli_fetch_assoc($data);
						$slots = filter_var($parsedData["slots"], FILTER_VALIDATE_INT);
						echo "<td><input type=\"number\" class=\"slotsInput\" id=\"numbox$i\"placeholder=\"Number Of Possible Visiting Students\" value=\"$slots\"></td>";
					}
					echo "</tr>";
				}
			 ?>
		</table>
		<?php
			if($type == 'teacher') {
				echo "<button id=\"kickbutton\" onclick=\"kickSelected()\">Kick Selected Students</button>";
				echo "<button id=\"updateSlots\" onclick=\"updateSlots()\">Update Slots Available</button>";
			}
		?>
		<div id="tableContainer">
			<table id="flexstudents">
				<?php
					if($type == 'teacher') {
						$dayOfWeek = getdate()['wday']-1;
						if($dayOfWeek < 5 && $dayOfWeek >= 0) {
							mysqli_data_seek($data, $dayOfWeek);
							$parsedData = mysqli_fetch_assoc($data);
							$flexStudentsStr = $parsedData["flexStudents"];
							$flexStudents = explode(";", $flexStudentsStr);
							echo "<tr><th>My Students</th><th>Going To</th></tr>";
							foreach($flexStudents as $studentName) {
								$studentTable = getStudentTable($studentName, $connect);
								if($studentTable != null) {
									$studentData = getTableData($studentTable, $dayOfWeek, $connect);
									$goingTo = $studentData["teacher"];
									echo "<tr><td>$studentName</td><td>$goingTo</td>";
								} else echo "<tr><td>$studentName</td><td>Not Registered</td></tr>";
							}
						}
					}
				 ?>
			</table>
			<table id="visitingstudents">
				<?php
					if($type == 'teacher') {
						$dayOfWeek = getdate()['wday']-1;
						if($dayOfWeek < 6 && $dayOfWeek >= 0) {
							mysqli_data_seek($data, $dayOfWeek);
							$parsedData = mysqli_fetch_assoc($data);
							$visitingStudentsStr = $parsedData["visitingStudents"];
							$visitingStudents = explode(";", $visitingStudentsStr);
							echo "<tr><th>Kick?</th><th>Visiting Students</th><th>Coming From</th></tr>";
							foreach($visitingStudents as $studentName) {
								$studentTable = getStudentTable($studentName, $connect);
								if($studentTable != null) {
									$studentData = getTableData($studentTable, $dayOfWeek, $connect);
									$comingFrom = $studentData["room"];
									echo "<tr><td><input type=\"checkbox\" name=\"$studentName\" /></td><td>$studentName</td><td>$comingFrom</td>";
								} else echo "<tr><td></td><td>NONE</td><td></td></tr>";
							}
						}
					}
				 ?>
			</table>
		</div>
		<div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
		<a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout()">Sign out</a>
		<div id="footerSpace"></div>
		<footer>
			<div class="foot">
				&copy; 2019 Jordan Martin and Grant Gupton
				<br/>
				Class of 2020
			</div>
		</footer>
		<script type="text/javascript">
			function updateSlots() {
				var monSlots = document.getElementById("numbox0").value;
				var tueSlots = document.getElementById("numbox1").value;
				var wedSlots = document.getElementById("numbox2").value;
				var thuSlots = document.getElementById("numbox3").value;
				var friSlots = document.getElementById("numbox4").value;
				var slots = monSlots + ";" + tueSlots + ";" + wedSlots + ";" + thuSlots + ";" + friSlots;

				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=4&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&slots=" + slots;
				window.location.href = "schedule.php?" + extension;
			}

			function kickSelected() {
				var table1 = document.getElementById("flexstudents");
				var table2 = document.getElementById("visitingstudents");
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=2&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&tokick=";
				var checkboxes1 = table1.getElementsByTagName("INPUT");
				var checkboxes2 = table2.getElementsByTagName("INPUT");
				for(var i = 0; i < checkboxes1.length; i++) {
					if(checkboxes1[i].checked) {
						extension += checkboxes1[i].name + ";";
					}
				}
				for(var i = 0; i < checkboxes2.length; i++) {
					if(checkboxes2[i].checked) {
						extension += checkboxes2[i].name + ";";
					}
				}

				window.location.href = "schedule.php?" + extension;
			}

			function swapAvailability(dayOfWeek) {
				var day = "";
				switch(dayOfWeek) {
					case 0:
						day = "&mon=1&tue=0&wed=0&thu=0&fri=0";
						break;
					case 1:
						day = "&mon=0&tue=1&wed=0&thu=0&fri=0";
						break;
					case 2:
						day = "&mon=0&tue=0&wed=1&thu=0&fri=0";
						break;
					case 3:
						day = "&mon=0&tue=0&wed=0&thu=1&fri=0";
						break;
					case 4:
						day = "&mon=0&tue=0&wed=0&thu=0&fri=1";
						break;
				}
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=1&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + day;
				window.location.href = 'schedule.php?' + extension;
			}
		</script>
		<script type="text/javascript" src="scripts/signin.js"></script>
	</body>
</html>
