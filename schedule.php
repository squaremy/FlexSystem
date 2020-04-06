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
			<a href="index.php"><img id="logo" src="faflexlogo.svg"></a>
			<a id="schedulebutton" class="disable-select">My Schedule</a>
			<a id="signupbutton" href="index.php" class="disable-select">Sign Up</a>
		</div>
		<script type="text/javascript" src="scripts/linkSchedulePHP.js"></script>
		<script type="text/javascript" src="scripts/script.js"></script>
		<?php
			include "scripts/schedule.php";
			include "scripts/adminConstants.php";

			$user = $_GET["user"];
			$name = $_GET["name"];
			$authCookie = $_COOKIE["auth"];

			$query = "SELECT * FROM `Login Cookies` WHERE cookie='$authCookie'";
			if(!$values = mysqli_query($connect, $query)) {
				echo "Query failed: " . mysqli_error($connect);
			} else {
				$data = mysqli_fetch_assoc($values);
				if($data == null) {
					// add login cookie to correct user
					$query = "SELECT * FROM `Login Cookies` WHERE email='$user'";
					if(!$values = mysqli_query($connect, $query)) {
						echo "Query failed: " . mysqli_error($connect);
					} else {
						$data = mysqli_fetch_assoc($values);
						if($data != null) {
							$query = "UPDATE `Login Cookies` SET cookie='$authCookie' WHERE email='$user'";
							if(!mysqli_query($connect, $query)) {
								echo "Query failed: " . mysqli_error($connect);
							}
						} else {
							$query = "INSERT INTO `Login Cookies` (email, cookie) VALUES ('$user', '$authCookie')";
							if(!mysqli_query($connect, $query)) {
								echo "Query failed: " . mysqli_error($connect);
							}
						}
					}
				} else {
					if($user != $data['email']) {
						// reset login cookie and add to database
						echo '<script>
						var d = new Date();
			      d.setTime(d.getTime() + (60 * 24 * 60 * 60 * 1000));
						document.cookie = "auth=" + randomHexCode() + ";expires=" + d.toUTCString() + ";path=/"
						</script>';
						$query = "SELECT * FROM `Login Cookies` WHERE email='$user'";
						if(!$results = mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						} else {
							$data = mysqli_fetch_assoc($results);
							if($data != null) {
								$query = "UPDATE `Login Cookies` SET cookie='$authCookie' WHERE email='$user'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							} else {
								$query = "INSERT INTO `Login Cookies` (email, cookie) VALUES ('$user', '$authCookie')";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					} else {
						// do nothing, user is authorized
					}
				}
			}

			if(getdate()['wday'] < lastAccessedDay($connect)) resetTables($connect, getdate()['wday']);
			else {
				$day = getdate()['wday'];
				$query = "UPDATE `Previous Access Date` SET Day=$day";
				if(!mysqli_query($connect, $query)) {
					echo "Query failed: " . mysqli_error($connect);
				}
			}

			$selectedDay = getdate()['wday'] - 1;
			if($_GET["selectedDay"] != null && $_GET["selectedDay"] != '') {
				$selectedDay = filter_var($_GET["selectedDay"], FILTER_VALIDATE_INT);
			}
			if($selectedDay > 4) $selectedDay = 4;
			else if($selectedDay < 0) $selectedDay = 0;

			if($name != '???' && $user != '???' && $_GET["signedup"] != '3') {
				$newUser = createNewUserIfNonexistent($user, $connect);
				if($newUser == true){
					echo "<script type=\"text/javascript\">window.location.href=\"updateFlexroom.php\"</script>";
				} else {
					if(studentTableIsEmpty($user, $connect) == true) {
						addDefaultStudentData($user, $name, $connect);
					}
					if(studentRoomIsEmpty($user, $connect) == true){
						$tempRoom = $_GET["room"];
						if($tempRoom != null && $tempRoom != 'null' && $tempRoom != '') {
							$query = "UPDATE `$user` SET room=\"$tempRoom\",teacher=\"$tempRoom\"";
							if(!mysqli_query($connect, $query)) {
								echo "Query failed: " . mysqli_error($connect);
							}
						} else {
							echo "<script type=\"text/javascript\">window.location.href=\"updateFlexroom.php\"</script>";
						}
					}
				}

				$data = getRawData($user, $connect);
				$parsedData = getTableData($user, $selectedDay, $connect);

				$email = $parsedData["email"];
				$room = $parsedData["room"];
				$day = $parsedData["day"];
				$type = $parsedData["type"];

				if($type == 'admin') {
					echo '<script>
									window.location.href = "admin.php";
								</script>';
				}

				if($type == 'student') {
					if($_GET["signedup"] == '1') {
						$targetTeacher = $_GET["teacher"];
						$goingMon = filter_var($_GET["mon"], FILTER_VALIDATE_BOOLEAN);
						$goingTue = filter_var($_GET["tue"], FILTER_VALIDATE_BOOLEAN);
						$goingWed = filter_var($_GET["wed"], FILTER_VALIDATE_BOOLEAN);
						$goingThu = filter_var($_GET["thu"], FILTER_VALIDATE_BOOLEAN);
						$goingFri = filter_var($_GET["fri"], FILTER_VALIDATE_BOOLEAN);
						if(getdate()['wday']-2 == 0) {
							if($signUpTimeout <= (time() + (19 * 60 * 60)) %(24*60*60)) {
								$goingMon = false;
							}
						}
						else if(getdate()['wday']-2 == 1) {
							$goingMon = false;
							if($signUpTimeout <= (time() + (19 * 60 * 60)) %(24*60*60)) {
								$goingTue = false;
							}
						}
						else if(getdate()['wday']-2 == 2) {
							$goingMon = false;
							$goingTue = false;
							if($signUpTimeout > (time() + (19 * 60 * 60)) %(24*60*60)) {
								$goingWed = false;
							}
						}
						else if(getdate()['wday']-2 == 3) {
							$goingMon = false;
							$goingTue = false;
							$goingWed = false;
							if($signUpTimeout > (time() + (19 * 60 * 60)) %(24*60*60)) {
								$goingThu = false;
							}
						}
						else if(getdate()['wday']-2 == 4) {
							$goingMon = false;
							$goingTue = false;
							$goingWed = false;
							$goingThu = false;
							if($signUpTimeout > (time() + (19 * 60 * 60)) %(24*60*60)) {
								$goingFri = false;
							}
						}
						else if(getdate()['wday']-2 > 4) {
							$goingMon = false;
							$goingTue = false;
							$goingWed = false;
							$goingThu = false;
							$goingFri = false;
						}

						updateSignup($goingMon, $targetTeacher, 0, $user, $connect);
						updateSignup($goingTue, $targetTeacher, 1, $user, $connect);
						updateSignup($goingWed, $targetTeacher, 2, $user, $connect);
						updateSignup($goingThu, $targetTeacher, 3, $user, $connect);
						updateSignup($goingFri, $targetTeacher, 4, $user, $connect);
					} else if($_GET["signedup"] == '2' && $_GET["room"] != null && $_GET["room"] != 'null' && $_GET["room"] != '') {
						$tempRoom = $_GET["room"];
						$query = "UPDATE `$user` SET room=\"$tempRoom\",teacher=\"$tempRoom\"";
						if(!mysqli_query($connect, $query)) {
							echo "Query failed: " . mysqli_error($connect);
						}

						addStudentToHomeroom($name, $tempRoom, $connect);
					}
					for($day = 0; $day < 5; $day++) {
						$data = getRawData($user, $connect);
						mysqli_data_seek($data, $day);
						$parsedData = mysqli_fetch_assoc($data);
						if($parsedData["teacher"] != $parsedData["room"]) {
							$teacherTable = getTeacherTable($parsedData["teacher"], $connect);
							$teacherData = getTableData($teacherTable, $day, $connect);
							$available = filter_var($teacherData["available"], FILTER_VALIDATE_BOOLEAN);
							$locked = $parsedData["locked"];
							$blockedList = explode(";", $teacherData["blockedStudents"]);
							$isBlocked = studentAlreadyInList($parsedData["name"], $blockedList);
							if($teacherTable != null && ((!teacherIsAvailable($teacherTable, $day, $connect) && !$available) || $isBlocked || $locked)) {
								$room = $parsedData["room"];
								$query = "UPDATE `$user` SET teacher=\"$room\" WHERE id='$day'";
								if(!mysqli_query($connect, $query)) {
									echo "Query failed: " . mysqli_error($connect);
								}
							}
						}
					}
				} else {
					if($type == 'floater') {
						$covering = null;
						if($_GET["selected"] != null && $_GET["selected"] != "") {
							$covering = $_GET["selected"];
							$data = getRawData(getTeacherTable($covering, $connect), $connect);
							$parsedData = mysqli_fetch_assoc($data);
						} else if($parsedData['teacherCovering'] != 'NONE') {
							$covering = $parsedData['teacherCovering'];
							$data = getRawData(getTeacherTable($covering, $connect), $connect);
							$parsedData = mysqli_fetch_assoc($data);
						}
						else {
							$data = getRawData($user, $connect);
							$parsedData = mysqli_fetch_assoc($data);
						}
					}

					if($data != null) {
						if($_GET["signedup"] == '1') {
							if($type == 'floater') {
								$data = getRawData($user, $connect);
							}
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
							updateKickedStudents(explode(";", $_GET["tokick"]), $parsedData, $selectedDay, $connect);
							if($type == 'floater') {
								$tempData = getRawData($user, $connect);
								$tempParsedData = mysqli_fetch_assoc($tempData);
								updateKickedStudents(explode(";", $_GET["tokick"]), $tempParsedData, $selectedDay, $connect);
							}
						} else if($_GET["signedup"] == '4') {
							$slots = explode(";", $_GET["slots"]);
							$data = getRawData($user, $connect);
							updateSlots($slots, $data, $connect);
						} else if($_GET["signedup"] == '5') {
							lockStudent($user, $_GET["toLock"], $connect);
						} else if($_GET["signedup"] == '6') {
							blockStudent($user, $_GET["toBlock"], $connect);
						}
						for($day = 0; $day < 5; $day++) {
							if($type == 'floater') {
								$parsedData = getTableData($user, $selectedDay, $connect);
								$teacherEmail = getTeacherTable($covering, $connect);
								if($teacherEmail != null) $data = getRawData($teacherEmail, $connect);
								else $data = null;
							}
							else $data = getRawData($user, $connect);
							mysqli_data_seek($data, $day);
							$parsedData = mysqli_fetch_assoc($data);
							availabilityUpdates($day, $parsedData, $connect);
							if($type == 'floater') {
								$tempData = getRawData($user, $connect);
								mysqli_data_seek($tempData, $day);
								$tempParsedData = mysqli_fetch_assoc($tempData);
								availabilityUpdates($day, $tempParsedData, $connect);
							}
						}
						if($type == 'floater') {
							$parsedData = getTableData($user, $selectedDay, $connect);
							$targetTeacher = getTeacherTable($covering, $connect);
							if($targetTeacher != null) removeWrongStudentsFromHomeroom($targetTeacher, $connect);
						} else removeWrongStudentsFromHomeroom($user, $connect);
					}
				}

				$data = getRawData($user, $connect);
				$parsedData = getTableData($user, $selectedDay, $connect);

				if($type == 'floater') {
					if($parsedData['teacherCovering'] != 'NONE') {
						$teacherEmail = getTeacherTable($covering, $connect);
						$data = getRawData($teacherEmail, $connect);
					} else $data = null;
				}

			} else if($_GET["signedup"] == '3') {
				if(!(filter_var($_GET["floater"], FILTER_VALIDATE_BOOLEAN))) {
					$roomNum = filter_var($_GET["roomNum"], FILTER_VALIDATE_INT);
					$slots = filter_var($_GET["slots"], FILTER_VALIDATE_INT);

					createTeacherTable($user, $name, $roomNum, $slots, $connect);
					$data = getRawData($user, $connect);
					$parsedData = getTableData($user, $selectedDay, $connect);

					$email = $parsedData["email"];
					$room = $parsedData["room"];
					$day = $parsedData["day"];
					$type = $parsedData["type"];
				} else {
					$floaterSchedule = array("", "", "", "", "");
					$floaterSchedule[0] = $_GET["mon"];
					$floaterSchedule[1] = $_GET["tue"];
					$floaterSchedule[2] = $_GET["wed"];
					$floaterSchedule[3] = $_GET["thu"];
					$floaterSchedule[4] = $_GET["fri"];

					createFloaterTable($user, $name, $floaterSchedule, $connect);
				}
			}


		?>
		<p id="searchtxt"><?php echo $name; ?></p>
		<?php
			if($type == 'floater') {
				$data = getRawData($user, $connect);
				$list = array();
				$currentCovered = getTableData($user, $selectedDay, $connect)["teacherCovering"];
				if($_GET["selected"] != null) $currentCovered = $_GET["selected"];
				echo "<table id='underSearchtxtTable' border='1'><tr>";
				for($i = 0; $i < 5; $i++) {
					mysqli_data_seek($data, $i);
					$parsedData = mysqli_fetch_assoc($data);
					$teacherCovering = $parsedData["teacherCovering"];
					if(!studentAlreadyInList($teacherCovering, $list) && $teacherCovering != 'NONE') {
						array_push($list, $teacherCovering);
						if($teacherCovering == $currentCovered) echo "<td class=current>" . $teacherCovering . "</td>";
						else echo "<td onclick=\"selectUserAndDay('$teacherCovering', $selectedDay)\"><a id='available'>" . $teacherCovering . "</a></td>";
					}
				}
				echo "</tr></table>";
				$data = getRawData($user, $connect);
				$parsedData = getTableData($user, $selectedDay, $connect);

				if($currentCovered != "NONE") {
					$teacherEmail = getTeacherTable($currentCovered, $connect);
					$data = getRawData($teacherEmail, $connect);
				} else $data = null;
			}
		?>
		<table id="weektable">
			<?php
				if($data != null) {
					echo "<tr>";
					for($i = 0; $i < mysqli_num_rows($data); $i++) {
						mysqli_data_seek($data, $i);
						$parsedData = mysqli_fetch_assoc($data);
						$day = $parsedData["day"];
						if($type == 'student') {
							if($i == $selectedDay) echo "<th class=current>" . $day . "</th>";
							else echo "<th>" . $day . "</th>";
						} else {
							if($i == $selectedDay) echo "<th class=current><a id='available'>" . $day . "</a></th>";
							else echo "<th onclick=\"selectUserAndDay('$covering', '$i')\"><a id='available'>" . $day . "</a></th>";
						}
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
						if($type == 'floater') {
							$data = getRawData($user, $connect);
						}
						for($i = 0; $i < mysqli_num_rows($data); $i++) {
							mysqli_data_seek($data, $i);
							$parsedData = mysqli_fetch_assoc($data);
							$available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
							if($available == true) echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">AVAILABLE</a></td>";
							else echo "<td onclick=\"swapAvailability($i)\"><a id=\"available\">UNAVAILABLE</a></td>";
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
				}
			 ?>
		</table>
		<?php
			if($type != 'student' && $data != null) {
				echo "<button id=\"kickbutton\" onclick=\"kickSelected()\">Kick Selected Students</button>";
				echo "<button id=\"updateSlots\" onclick=\"updateSlots()\">Update Slots Available</button>";
			}
			if($type != 'student') {
				echo "<button id=\"toggleView\" name=\"reg\" onclick=\"toggleView()\">View Blocked Students</button>";
			}
		?>
		<div id="tableContainer">
			<table id="flexstudents">
				<?php
					if($type == 'floater') {
						$data = getRawData($user, $connect);
						$parsedData = getTableData($user, $selectedDay, $connect);

						if($currentCovered != "NONE") {
							$teacherEmail = getTeacherTable($currentCovered, $connect);
							$data = getRawData($teacherEmail, $connect);
						} else $data = null;
					}

					if($type != 'student' && $data != null) {
						$dayOfWeek = $selectedDay;
						if($dayOfWeek < 5 && $dayOfWeek >= 0) {
							mysqli_data_seek($data, $dayOfWeek);
							$parsedData = mysqli_fetch_assoc($data);
							$flexStudentsStr = $parsedData["flexStudents"];
							$flexStudents = explode(";", $flexStudentsStr);
							$flexStudents = sortByLastName($flexStudents);
							echo "<tr><th>My Students</th><th>Going To</th></tr>";
							foreach($flexStudents as $studentName) {
								$studentTable = getStudentTable($studentName, $connect);
								if($studentTable != null) {
									$studentData = getTableData($studentTable, $dayOfWeek, $connect);
									$studentLock = filter_var($studentData["locked"], FILTER_VALIDATE_INT);
									$goingTo = $studentData["teacher"];
									echo "<tr><td onclick=\"lockStudent('$studentName', $studentLock)\"><a id='available'>$studentName</a></td><td>$goingTo</td>";
								} else echo "<tr><td>$studentName</td><td>Not Registered</td></tr>";
							}
						}
					}
				 ?>
			</table>
			<table id="visitingstudents">
				<?php
					if($type == 'floater') {
						$data = getRawData($user, $connect);
						$parsedData = getTableData($user, $selectedDay, $connect);
						$visitingStudents = explode(";", $parsedData["visitingStudents"]);
						if($covering != null && $covering != "") {
							$teacherEmail = getTeacherTable($covering, $connect);
							$data = getRawData($teacherEmail, $connect);
						}
					}
					if($type != 'student') {
						$dayOfWeek = $selectedDay;
						if($dayOfWeek < 6 && $dayOfWeek >= 0) {
							if($data != null) {
								mysqli_data_seek($data, $dayOfWeek);
								$parsedData = mysqli_fetch_assoc($data);
								$visitingStudentsStr = $parsedData["visitingStudents"];
								if($type == 'floater' && $visitingStudents[0] != null && $visitingStudents[0] != 'NONE' && $visitingStudents[0] != '') $visitingStudents = array_merge($visitingStudents, explode(";", $visitingStudentsStr));
								else $visitingStudents = explode(";", $visitingStudentsStr);
							}
							$visitingStudents = sortByLastName($visitingStudents);
							echo "<tr><th>Kick?</th><th>Visiting Students</th><th>Coming From</th></tr>";
							foreach($visitingStudents as $studentName) {
								$studentTable = getStudentTable($studentName, $connect);
								if($studentTable != null) {
									$studentData = getTableData($studentTable, $dayOfWeek, $connect);
									$comingFrom = $studentData["room"];
									echo "<tr><td><input type=\"checkbox\" name=\"$studentName\" /></td><td onclick=\"blockStudent('$studentName', 0)\"><a id='available'>$studentName</a></td><td>$comingFrom</td>";
								} else echo "<tr><td></td><td>NONE</td><td></td></tr>";
							}
						}
					}
				 ?>
			</table>
			<?php
				if($type != 'student') {
					$blockedList = array();
					echo "<table id=\"blockedstudents\" style=\"display: none\"><tr><th>Blocked Students</th></tr>";
					for($i = $selectedDay; $i < 5; $i++) {
						if($type == 'floater') {
							$data = getRawData($user, $connect);
						}
						if($data != null) {
							mysqli_data_seek($data, $i);
							$parsedData = mysqli_fetch_assoc($data);
							$tempList = explode(";", $parsedData["blockedStudents"]);
							foreach($tempList as $n) {
								if(!studentAlreadyInList($n, $blockedList, $connect)) {
									if($n != null && $n != 'NONE') array_push($blockedList, $n);
								}
							}
						}
					}
					$blockedList = sortByLastName($blockedList);
					for($i = 0; $i < sizeof($blockedList); $i++) {
						$studentName = $blockedList[$i];
						echo "<tr><td onclick=\"blockStudent('$studentName', 1)\"><a id='available'>$studentName</a></td></tr>";
					}
					echo "</table>";
				}

				$data = getRawData($user, $connect);
				$parsedData = getTableData($user, $selectedDay, $connect);

				if($type == 'floater') {
					if($parsedData['teacherCovering'] != 'NONE') {
						$teacherEmail = getTeacherTable($parsedData['teacherCovering'], $connect);
						$data = getRawData($teacherEmail, $connect);
					} else $data = null;
				}
			?>
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
				var table2 = document.getElementById("visitingstudents");
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=2&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&tokick=";
				var checkboxes2 = table2.getElementsByTagName("INPUT");
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

			function selectUserAndDay(toSelect, day) {
				var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=0&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&selected=" + toSelect + "&selectedDay=" + day;
				window.location.href = 'schedule.php?' + extension;
			}

			function blockStudent(student, blockStatus) {
				if(blockStatus == 0) {
					if(confirm("Prevent " + student +" from coming to your room?")) {
						var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=6&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&toBlock=" + student;
						window.location.href = 'schedule.php?' + extension;
					}
				} else {
					if(confirm("Allow " + student + " to sign up your room?")) {
						var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=6&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&toBlock=" + student;
						window.location.href = 'schedule.php?' + extension;
					}
				}
			}

			function lockStudent(student, lockStatus) {
				if(lockStatus == 1) {
					if(confirm("Allow " + student +" to leave?")) {
						var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=5&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&toLock=" + student;
						window.location.href = 'schedule.php?' + extension;
					}
				} else {
					if(confirm("Prevent " + student +" from leaving?")) {
						var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=5&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&toLock=" + student;
						window.location.href = 'schedule.php?' + extension;
					}
				}
			}

			function toggleView() {
				var button = document.getElementById("toggleView");
				if(button.name == "reg") {
					document.getElementById("flexstudents").style.display = "none";
					document.getElementById("visitingstudents").style.display = "none";
					document.getElementById("blockedstudents").style.display = "table";
					button.name = "blockedView";
				} else {
					document.getElementById("flexstudents").style.display = "table";
					document.getElementById("visitingstudents").style.display = "table";
					document.getElementById("blockedstudents").style.display = "none";
					button.name = "reg";
				}
			}
		</script>
		<script type="text/javascript" src="scripts/signin.js"></script>
	</body>
</html>
