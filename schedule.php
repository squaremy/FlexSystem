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
			function getStudentTable($name, $connect) {
				if($name != 'NONE') {
					$names = explode(" ", $name);
					$lastName = strtolower($names[1]);
					$query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$lastName%'";
					if(!$result = mysqli_query($connect, $query)) {
						echo "Query failed: " . mysqli_error($connect);
					}
					while($tables = mysqli_fetch_array($result)) {
						$studentTable = $tables[0];
						return $studentTable;
					}
				}
				return null;
			}

			function getStudentData($table, $desiredDay, $connect) {
				$query = "SELECT * FROM `$table` WHERE id=$desiredDay";
				if(!$data = mysqli_query($connect, $query)) {
					echo "Query failed: " . mysqli_error($connect);
				}
				$toReturn = mysqli_fetch_array($data);
				return $toReturn;
			}

			function teacherIsAvailable($table, $desiredDay, $connect) {
				$query = "SELECT available FROM `$table` WHERE day='$desiredDay'";
				if(!$data = mysqli_query($connect, $query)) {
					echo "Query failed: " . mysqli_error($connect);
				}
				$readableData = mysqli_fetch_array($data);
				$result = filter_var($readableData["available"], FILTER_VALIDATE_BOOLEAN);
				return $result;
			}

			function getTeacherTable($teacherLastName, $connect) {
				$teacherLastName = strtolower($teacherLastName);
				$query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$teacherLastName%'";
				if(!$result = mysqli_query($connect, $query)) {
					echo "Query failed: " . mysqli_error($connect);
				}
				while($tables = mysqli_fetch_array($result)) {
					$teacherTable = $tables[0];
					return $teacherTable;
				}
			}

			function addStudentToVisitList($teacherLastName, $studentName, $desiredDay, $connect, $room) {
				$teacherLastName = strtolower($teacherLastName);
				$roomNames = explode(" ", $room);
				$roomLastName = strtolower($roomNames[1]);
				if($teacherLastName != $roomLastName) {
					$teacherTable = getTeacherTable($teacherLastName, $connect);
					$query = "SELECT visitingStudents FROM `$teacherTable` WHERE day='$desiredDay'";
					if(!$tableDataRaw = mysqli_query($connect, $query)) {
						echo "Query failed: " . mysqli_error($connect);
					}
					$tableData = mysqli_fetch_array($tableDataRaw);
					$visitingStudents = $tableData["visitingStudents"];
					if(strpos($visitingStudents, $studentName) !== false) {
						echo "Student already visiting!";
					} else {
						if($visitingStudents == "NONE") $visitingStudents = $studentName;
						else $visitingStudents = $visitingStudents . ";" . $studentName;
					}
					$query = "UPDATE `$teacherTable` SET visitingStudents='$visitingStudents' WHERE day='$desiredDay'";
					if(!mysqli_query($connect, $query)) {
						echo "Query failed: " . mysqli_error($connect);
					}
				}
			}

			$connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!", "techmeds_FlexSystem") or die("Connection to database failed: " . mysqli_connect_error());
			$user = $_GET["user"];
			$sql = "SELECT * FROM `$user`";
			$data = null;
			if(!$data = mysqli_query($connect, $sql)) {
				die("error: " . mysqli_error($connect));
			}
			$parsedData = mysqli_fetch_assoc($data);
			$name = $parsedData["name"];
			$email = $parsedData["email"];
			$room = $parsedData["room"];
			$day = $parsedData["day"];
			$type = $parsedData["type"];


			if($type == 'student' && $_GET["signedup"] == '1') {
				$targetTeacher = $_GET["teacher"];
				$names = explode(" ", $targetTeacher);
				$lastName = $names[1];
				$goingMon = filter_var($_GET["mon"], FILTER_VALIDATE_BOOLEAN);
				$goingTue = filter_var($_GET["tue"], FILTER_VALIDATE_BOOLEAN);
				$goingWed = filter_var($_GET["wed"], FILTER_VALIDATE_BOOLEAN);
				$goingThu = filter_var($_GET["thu"], FILTER_VALIDATE_BOOLEAN);
				$goingFri = filter_var($_GET["fri"], FILTER_VALIDATE_BOOLEAN);

				if($goingMon == true) {
					$teacherTable = getTeacherTable($lastName, $connect);
					if(teacherIsAvailable($teacherTable, 'Monday', $connect)) {
						$query = "UPDATE `$user` SET teacher='$targetTeacher' WHERE day='Monday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToVisitList($lastName, $name, 'Monday', $connect, $room);
					} else echo "Teacher unavailable...";
				}
				if($goingTue == true) {
					$teacherTable = getTeacherTable($lastName, $connect);
					if(teacherIsAvailable($teacherTable, 'Tuesday', $connect)) {
						$query = "UPDATE `$user` SET teacher='$targetTeacher' WHERE day='Tuesday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToVisitList($lastName, $name, 'Tuesday', $connect, $room);
					} else echo "Teacher unavailable...";
				}
				if($goingWed == true) {
					$teacherTable = getTeacherTable($lastName, $connect);
					if(teacherIsAvailable($teacherTable, 'Wednesday', $connect)) {
						$query = "UPDATE `$user` SET teacher='$targetTeacher' WHERE day='Wednesday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToVisitList($lastName, $name, 'Wednesday', $connect, $room);
					} else echo "Teacher unavailable...";
				}
				if($goingThu == true) {
					$teacherTable = getTeacherTable($lastName, $connect);
					if(teacherIsAvailable($teacherTable, 'Thursday', $connect)) {
						$query = "UPDATE `$user` SET teacher='$targetTeacher' WHERE day='Thursday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToVisitList($lastName, $name, 'Thursday', $connect, $room);
					} else echo "Teacher unavailable...";
				}
				if($goingFri == true) {
					$teacherTable = getTeacherTable($lastName, $connect);
					if(teacherIsAvailable($teacherTable, 'Friday', $connect)) {
						$query4 = "UPDATE `$user` SET teacher='$targetTeacher' WHERE day='Friday'";
						if(!mysqli_query($connect, $query4)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToVisitList($lastName, $name, 'Friday', $connect, $room);
					} else echo "Teacher unavailable...";
				}
			} else {
				// may need to update teacher data here at some point
			}
		?>
	</head>

	<body>
		<div class="topnav">
			<img id="logo" href="index.html" src="faflexlogo.svg"/>
			<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
			<a id="schedulebutton" href="schedule.html" class="disable-select">My Schedule</a>
		</div>
		<div class="signupmenu">
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

					if($type == "student") {
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
							if($available == true) echo "<td><a id=\"available\">AVAILABLE</a></td>";
							else echo "<td><a id=\"available\">BLOCKED</a></td>";
						}
						echo "</tr>";
					}
				 ?>
			</table>
			<table id="flexstudents">
				<?php
					if($type == 'teacher') {
						$dayOfWeek = getdate()['wday']-1;
						if($dayOfWeek < 6 && $dayOfWeek >= 0) {
							mysqli_data_seek($data, $dayOfWeek);
							$parsedData = mysqli_fetch_assoc($data);
							$flexStudentsStr = $parsedData["flexStudents"];
							$flexStudents = explode(";", $flexStudentsStr);
							echo "<tr><th>Kick?</th><th>My Students</th><th>Going To</th></tr>";
							foreach($flexStudents as $studentName) {
								$studentTable = getStudentTable($studentName, $connect);
								if($studentTable != null) {
									$studentData = getStudentData($studentTable, $dayOfWeek, $connect);
									$goingTo = $studentData["teacher"];
									echo "<tr><td><input type=\"checkbox\" name=\"$studentName\" /></td><td>$studentName</td><td>$goingTo</td>";
								} else echo "<tr><td></td><td>NONE</td><td></td></tr>";
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
									$studentData = getStudentData($studentTable, $dayOfWeek, $connect);
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
		<!-- <script type="text/javascript" src="scripts/schedule.js"></script> -->
		<!-- <script>loadUser()</script> -->
		<script type="text/javascript" src="scripts/signin.js"></script>
	</body>
<!-- <script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script> -->
</html>
