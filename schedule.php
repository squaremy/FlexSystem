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
					echo $name;
					$lastName = strtolower($names[1]);
					$nameToSearch = $lastName + substr($names[0], 1, 1);
					echo $nameToSearch;
					$query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$nameToSearch%'";
					if(!$result = mysqli_query($connect, $query)) {
						echo "Query failed: " . mysqli_error($connect);
					}
					while($tables = mysqli_fetch_array($result)) {
						foreach($tables as $t) {
							$data = getStudentData($t, 0, $connect);
							if($data != null && $data["name"] == $name) return $t;
						}
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
				if($_GET["signedup"] == '1') {
					$swapMon = filter_var($_GET["mon"], FILTER_VALIDATE_BOOLEAN);
					$swapTue = filter_var($_GET["tue"], FILTER_VALIDATE_BOOLEAN);
					$swapWed = filter_var($_GET["wed"], FILTER_VALIDATE_BOOLEAN);
					$swapThu = filter_var($_GET["thu"], FILTER_VALIDATE_BOOLEAN);
					$swapFri = filter_var($_GET["fri"], FILTER_VALIDATE_BOOLEAN);

					if($swapMon == true) {
						mysqli_data_seek($data, 0);
						$parsedData = mysqli_fetch_array($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						$available = !$available;
						$query = "UPDATE `$user` SET available='$available' WHERE day='Monday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						$flexStudentsStr = $parsedData["flexStudents"];
						$flexStudents = explode(";", $flexStudentsStr);
						foreach($flexStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Monday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}

						$visitingStudentsStr = $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $visitingStudentsStr);
						foreach($visitingStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Monday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					} else if($swapTue == true) {
						mysqli_data_seek($data, 1);
						$parsedData = mysqli_fetch_array($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						$available = !$available;
						$query = "UPDATE `$user` SET available='$available' WHERE day='Tuesday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						$flexStudentsStr = $parsedData["flexStudents"];
						$flexStudents = explode(";", $flexStudentsStr);
						foreach($flexStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Tuesday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}

						$visitingStudentsStr = $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $visitingStudentsStr);
						foreach($visitingStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Tuesday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					} else if($swapWed == true) {
						mysqli_data_seek($data, 2);
						$parsedData = mysqli_fetch_array($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						$available = !$available;
						$query = "UPDATE `$user` SET available='$available' WHERE day='Wednesday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						$flexStudentsStr = $parsedData["flexStudents"];
						$flexStudents = explode(";", $flexStudentsStr);
						foreach($flexStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Wednesday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}

						$visitingStudentsStr = $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $visitingStudentsStr);
						foreach($visitingStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Wednesday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					} else if($swapThu == true) {
						mysqli_data_seek($data, 3);
						$parsedData = mysqli_fetch_array($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						$available = !$available;
						$query = "UPDATE `$user` SET available='$available' WHERE day='Thursday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						$flexStudentsStr = $parsedData["flexStudents"];
						$flexStudents = explode(";", $flexStudentsStr);
						foreach($flexStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Thursday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}

						$visitingStudentsStr = $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $visitingStudentsStr);
						foreach($visitingStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Thursday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					} else if($swapFri == true) {
						mysqli_data_seek($data, 4);
						$parsedData = mysqli_fetch_array($data);
						$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
						$available = !$available;
						$query = "UPDATE `$user` SET available='$available' WHERE day='Friday'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						$flexStudentsStr = $parsedData["flexStudents"];
						$flexStudents = explode(";", $flexStudentsStr);
						foreach($flexStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Friday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}

						$visitingStudentsStr = $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $visitingStudentsStr);
						foreach($visitingStudents as $studentName) {
							$studentTable = getStudentTable($studentName, $connect);
							$goingTo = "undecided";
							if($studentTable != null) {
								$query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE day='Friday'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					}
				} else if($_GET["signedup"] == '2') {
					$tokick = explode(";", $_GET["tockick"]);
					$visitingStudentsStr = $parsedData["visitingStudents"];
					$visitingStudents = explode(";", $visitingStudentsStr);
					foreach($tokick as $studentName) {
						$studentTable = getStudentTable($studentName, $connect);
						if($studentTable != null) {
							$dayOfWeek = getdate()['wday']-1;
							$query = "UPDATE `$studentTable` SET teacher='undecided' WHERE day='$dayOfWeek'";
							if(!mysqli_query($connect, $query)) {
								echo "Query failed: " . mysqli_error($connect);
							}
						}
					}
				}

				for($day = 0; $day < 5; $day++) {
					mysqli_data_seek($data, $day);
					$parsedData = mysqli_fetch_assoc($data);
					$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
					if($available == false) {
						$query = "UPDATE `$user` SET visitingStudents='NONE' WHERE id='$day'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}
					} else { // student is undecided, remove from list
						echo $parsedData["visitingStudents"];
						$visitingStudents = explode(";", $parsedData["visitingStudents"]);
						$newVisitingStudents = [];
						$count = 0;
						foreach($visitingStudents as $studentName) {
							echo $studentName;
							$studentTable = getStudentTable($studentName, $connect);
							if($studentTable != null) {
								$studentData = getStudentData($studentTable, $day, $connect);
								if($studentData["teacher"] != "undecided") {
									$newVisitingStudents[$count] = $studentData["name"];
									$count += 1;
								}
							}
						}
						$newVisitingStudentsStr = implode(";", $newVisitingStudents);
						$query = "UPDATE `$user` SET visitingStudents='$newVisitingStudentsStr' WHERE id='$day'";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}
					}
				}
			}
		?>
	</head>

	<body>
		<div class="topnav">
			<img id="logo" href="index.html" src="faflexlogo.svg"/>
			<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
			<a id="schedulebutton" class="disable-select">My Schedule</a>
		</div>
		<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
		<div id="signupmenu">
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
							if($available == true) echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">AVAILABLE</a></td>";
							else echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">BLOCKED</a></td>";
						}
						echo "</tr>";
					}
				 ?>
			</table>
			<table id="flexstudents">
				<?php
					if($type == 'teacher') {
						$dayOfWeek = getdate()['wday']-1;
						if($dayOfWeek < 5 && $dayOfWeek >= 0) {
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
			<?php
				if($type == 'teacher') {
					echo "<button id=\"kickbutton\" onclick=\"kickSelected()\">Kick Selected Students</button>";
				}
			?>
		</div>
		<div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
		<a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout()">Sign out</a>
		<footer>
			<div class="foot">
				&copy; 2019 Jordan Martin and Grant Gupton
				<br/>
				Class of 2020
			</div>
		</footer>
		<script type="text/javascript">
			function kickSelected() {
				// get each row in each table and get the name of the checkboxes that are checked, add that to a schedule php link which will reload and update the data
				var table1 = document.getElementById("flexstudents");
				var table2 = document.getElementById("visitingstudents");
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=2" + "&tokick=";
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
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=1" + day;
				window.location.href = 'schedule.php?' + extension;
			}
		</script>
		<!-- <script>loadUser()</script> -->
		<script type="text/javascript" src="scripts/signin.js"></script>
	</body>
<!-- <script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script> -->
</html>
