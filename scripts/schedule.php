<?php
  function getStudentTable($name, $connect) {
    if($name != 'NONE') {
      $query = "SHOW TABLES FROM franklin_flexSystem";
      if(!$result = mysqli_query($connect, $query)) {
        echo "scripts/schedule.php:9::Query failed: " . mysqli_error($connect);
      }
      while($tables = mysqli_fetch_array($result)) {
        foreach($tables as $t) {
          $data = getTableData($t, 0, $connect);
          if($data != null && $data["name"] == $name && $data["type"] == 'student') return $t;
        }
      }
    }
    return null;
  }

  function getTableData($table, $desiredDay, $connect) {
    $query = "SELECT * FROM `$table` WHERE id=$desiredDay";
    if(!$data = mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:24::Query failed: " . mysqli_error($connect);
    }
    $toReturn = mysqli_fetch_assoc($data);
    return $toReturn;
  }

  function teacherIsAvailable($table, $desiredDay, $connect) {
    $readableData = getTableData($table, $desiredDay, $connect);
    $available = filter_var($readableData["available"], FILTER_VALIDATE_BOOLEAN);
    $slotsUsed = filter_var($readableData["slotsUsed"], FILTER_VALIDATE_INT);
    $slots = filter_var($readableData["slots"], FILTER_VALIDATE_INT);
    return ($available && $slotsUsed < $slots);
  }

  function getTeacherTable($teacherName, $connect) {
    $query = "SHOW TABLES FROM franklin_flexSystem";
    if(!$result = mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:46::Query failed: " . mysqli_error($connect);
    }
    while($tables = mysqli_fetch_array($result)) {
      foreach($tables as $t) {
        $data = getTableData($t, 0, $connect);
        if($data != null && $data["name"] == $teacherName && ($data["type"] == 'teacher' || $data["type"] == 'floater')) return $t;
      }
    }
    return null;
  }

  function addStudentToVisitList($teacherTableData, $studentName, $desiredDay, $connect, $room) {
    $email = $teacherTableData["email"];
    $slotsUsed = $teacherTableData["slotsUsed"] + 1;
    if($teacherTableData["name"] != $room) {
      $visitingStudents = $teacherTableData["visitingStudents"];
      if(strpos($visitingStudents, $studentName) !== false) {
        // do nothing, name is already in the list
      } else {
        if($visitingStudents == null || $visitingStudents == "" || $visitingStudents == '' || $visitingStudents == "NONE") $visitingStudents = $studentName;
        else $visitingStudents = $visitingStudents . ";" . $studentName;
        $query = "UPDATE `$email` SET visitingStudents=\"$visitingStudents\",slotsUsed='$slotsUsed' WHERE id='$desiredDay'";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:87::Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function updateCurrentData($user, $connect) {
    $sql = "SELECT * FROM `$user`";
    $data = null;
    if(!$data = mysqli_query($connect, $sql)) {
      die("scripts/schedule.php:96::error: " . mysqli_error($connect));
    }
    mysqli_data_seek($data, getdate()['wday']-1);
    $parsedData = mysqli_fetch_assoc($data);
    return $parsedData;
  }

  function getRawData($user, $connect) {
    $sql = "SELECT * FROM `$user`";
    $data = null;
    if(!$data = mysqli_query($connect, $sql)) {
      die("scripts/schedule.php:107::error: " . mysqli_error($connect));
    }
    return $data;
  }

  function addStudentToHomeroom($name, $teacherName, $connect) {
    $teacherTable = getTeacherTable($teacherName, $connect);
    $teacherData = getTableData($teacherTable, 0, $connect);
    $flexStudents = $teacherData["flexStudents"];
    if(strpos($flexStudents, $name) !== false) {
      // do nothing, user already added
    } else {
      if($flexStudents != null) {
        $flexStudentsArr = explode(";", $flexStudents);
        array_push($flexStudentsArr, $name);
        $flexStudents = implode(";", $flexStudentsArr);
      } else {
        $flexStudents = $name;
      }
      $query = "UPDATE `$teacherTable` SET flexStudents=\"$flexStudents\"";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }
    }
  }

  function updateSignup($going, $teacher, $dayID, $user, $connect) {
    if($going) {
      $targetTeacher = getTeacherTable($teacher, $connect);
      $teacherData = getTableData($targetTeacher, $dayID, $connect);
      $curData = getTableData($user, $dayID, $connect);
      $locked = filter_var($curData["locked"], FILTER_VALIDATE_BOOLEAN);
      $blockedList = explode(";", $teacherData["blockedStudents"]);
      $isBlocked = studentAlreadyInList($curData["name"], $blockedList);
      if(!$locked && !$isBlocked && (teacherIsAvailable($targetTeacher, $dayID, $connect) || $teacher == $curData["room"])) {
        $curTeacher = $curData["teacher"];
        if($curTeacher != $curData["room"] && $curTeacher != $teacher) {
          $curTeacherTable = getTeacherTable($curTeacher, $connect);
          $prevTeacher = getTableData($curTeacherTable, $dayID, $connect);
          $visitingStudents = $prevTeacher["visitingStudents"];
          if(strpos($visitingStudents, $curData["name"]) !== false) {
            if($slotsUsed < 0) $slotsUsed = 0;
            $visitingStudentsArray = explode(";", $visitingStudents);
            $newArray = [];
            foreach($visitingStudentsArray as $s) {
              if($s != $curData["name"]) array_push($newArray, $s);
            }
            $visitingStudents = implode(";", $newArray);
            $slotsUsed = sizeof($newArray);
            if($slotsUsed <= 0) {
              $slotsUsed = 0;
              $visitingStudents = "NONE";
            }
            $query = "UPDATE `$curTeacherTable` SET visitingStudents=\"$visitingStudents\",slotsUsed='$slotsUsed' WHERE id='$dayID'";
            if(!mysqli_query($connect, $query)) {
              echo "Query failed: " . mysqli_error($connect);
            }
          }
        }

        $query = "UPDATE `$user` SET teacher=\"$teacher\" WHERE id='$dayID'";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:118::Query failed: " . mysqli_error($connect);
        }
        $teacherData = getTableData($targetTeacher, $dayID, $connect);
        $curData = updateCurrentData($user, $connect);
        $name = $curData["name"];
        $room = $curData["room"];
        if($teacher != $curData["room"]) addStudentToVisitList($teacherData, $name, $dayID, $connect, $room);
      }
    }
  }

  function updateKickedStudents($tokick, $parsedData, $dayOfWeek, $connect) {
    foreach($tokick as $studentName) {
      $studentName = trim($studentName);
      if($studentName != '' && $studentName != 'NONE') {
        $studentTable = getStudentTable($studentName, $connect);
        if($studentTable != null) {
          $studentData = getTableData($studentTable, $dayOfWeek, $connect);
          if($studentData["teacher"] == $parsedData["name"]) {
            $room = $studentData["room"];
            $query = "UPDATE `$studentTable` SET teacher=\"$room\" WHERE id='$dayOfWeek'";
            if(!mysqli_query($connect, $query)) {
              echo "scripts/schedule.php:140::Query failed: " . mysqli_error($connect);
            }
          }
        }
      }
    }
  }

  function flipAvailability($swap, $data, $dayID, $user, $connect) {
    if($swap) {
      mysqli_data_seek($data, $dayID);
      $parsedData = mysqli_fetch_array($data);
      $available = !filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
      $slots = ($available)? $parsedData["slots"]:'0';
      $query = "UPDATE `$user` SET available='$available',slots='$slots' WHERE id='$dayID'";
      if(!mysqli_query($connect, $query)) {
        echo "scripts/schedule.php:155::Query failed: " . mysqli_error($connect);
      }

      $visitingStudentsStr = $parsedData["visitingStudents"];
      $visitingStudents = explode(";", $visitingStudentsStr);
      foreach($visitingStudents as $studentName) {
        $studentTable = getStudentTable($studentName, $connect);
        if($studentTable != null) {
          $studentData = getTableData($studentTable, $dayID, $connect);
          if($studentData["teacher"] == $parsedData["name"]) {
            $room = $studentData["room"];
            $query = "UPDATE `$studentTable` SET teacher=\"$room\" WHERE id='$dayID'";
            if(!mysqli_query($connect, $query)) {
              echo "scripts/schedule.php:168::Query failed: " . mysqli_error($connect);
            }
          }
        }
      }
    }
  }

  function availabilityUpdates($day, $parsedData, $connect) {
    $user = $parsedData['email'];
    $available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
		$slots = filter_var($parsedData["slots"], FILTER_VALIDATE_INT);
		if(!$available && $slots > 0) {
			$slots = 0;
			$query = "UPDATE `$user` SET slots='$slots' WHERE id='$day'";
			if(!mysqli_query($connect, $query)) {
				echo "Query failed: " . mysqli_error($connect);
			}
		}

    if($available == false) {
      $query = "UPDATE `$user` SET visitingStudents='NONE',slotsUsed='0' WHERE id='$day'";
      if(!mysqli_query($connect, $query)) {
        echo "scripts/schedule.php:181::Query failed: " . mysqli_error($connect);
      }
    } else {
      if($parsedData["visitingStudents"] != null && $parsedData["visitingStudents"] != "NONE" && $parsedData["visitingStudents"] != "") {
        $visitingStudents = explode(";", $parsedData["visitingStudents"]);
        $newVisitingStudents = [];
        $count = 0;
        foreach($visitingStudents as $studentName) {
          $studentTable = getStudentTable($studentName, $connect);
          if($studentTable != null) {
            $studentData = getTableData($studentTable, $day, $connect);
            if($studentData["teacher"] != "undecided" && $studentData["teacher"] == $parsedData["name"]) {
              $newVisitingStudents[$count] = $studentData["name"];
              $count += 1;
            }
          }
        }

        while(sizeof($newVisitingStudents) > $parsedData["slots"]) {
          $student = array_pop($newVisitingStudents);
          $studentTable = getStudentTable($student, $connect);
          $studentData = getTableData($student, $day, $connect);
          $teacher = $studentData["room"];
          $query = "UPDATE `$studentTable` SET teacher=\"$teacher\" WHERE id='$day'";
          if(!mysqli_query($connect, $query)) {
            echo "scripts/schedule.php:206::Query failed: " . mysqli_error($connect);
          }
        }
        $newVisitingStudentsStr = implode(";", $newVisitingStudents);
        $slotsUsed = sizeof($newVisitingStudents);
        $query = "UPDATE `$user` SET visitingStudents=\"$newVisitingStudentsStr\",slotsUsed='$slotsUsed' WHERE id='$day'";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:212::Query failed: " . mysqli_error($connect);
        }
      } else if($parsedData["visitingStudents"] == "") {
        $query = "UPDATE `$user` SET visitingStudents='NONE',slotsUsed='0' WHERE id='$day'";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:217::Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function createNewUserIfNonexistent($user, $connect) {
    strtolower($user);
    $query = "SELECT name FROM `$user`";
    $result = mysqli_query($connect, $query);

    if(empty($result)) {
      $query = "CREATE TABLE IF NOT EXISTS `$user` (
        id INT(10),
        day VARCHAR(30),
        name VARCHAR(60),
        email VARCHAR(50),
        type VARCHAR(30),
        room VARCHAR(60),
        teacher VARCHAR(60),
        locked BOOLEAN
      )";
      if(mysqli_query($connect, $query) !== false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function updateSlots($slots, $data, $connect) {
    for($day = 0; $day < 5; $day++) {
      mysqli_data_seek($data, $day);
      $parsedData = mysqli_fetch_assoc($data);
      $slotAmt = $slots[$day];
      if($slotAmt < 0) $slotAmt = 0;
      $available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
      if(!$available && $slotAmt > 0) $slotAmt = 0;
      $user = $parsedData["email"];
      $query = "UPDATE `$user` SET slots='$slotAmt' WHERE id='$day'";
      if(!mysqli_query($connect, $query)) {
        echo "scripts/schedule.php:256::Query failed: " . mysqli_error($connect);
      }
      if($parsedData["visitingStudents"] != null && $parsedData["visitingStudents"] != "NONE" && $parsedData["visitingStudents"] != "") {
        $visitingStudents = explode(";", $parsedData["visitingStudents"]);
        while(sizeof($visitingStudents) > $slotAmt) {
          $student = array_pop($visitingStudents);
          $studentTable = getStudentTable($student, $connect);
          $studentData = getTableData($studentTable, $day, $connect);
          $teacher = $studentData["teacher"];
          if($teacher == $parsedData["name"]) {
            $teacher = $studentData["room"];
            $query = "UPDATE `$studentTable` SET teacher=\"$teacher\" WHERE id='$day'";
            if(!mysqli_query($connect, $query)) {
              echo "scripts/schedule.php:269::Query failed: " . mysqli_error($connect);
            }
          }
        }
        $visitingStudentsStr = implode(";", $visitingStudents);
        $query = "UPDATE `$user` SET visitingStudents=\"$visitingStudentsStr\" WHERE id='$day'";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:276::Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function addDefaultStudentData($user, $name, $connect) {
    for($i = 0; $i < 5; $i++) {
      $curDay = "";
      if($i == 0) $curDay = "Monday";
      else if($i == 1) $curDay = "Tuesday";
      else if($i == 2) $curDay = "Wednesday";
      else if($i == 3) $curDay = "Thursday";
      else if($i == 4) $curDay = "Friday";
      $query = "INSERT INTO `$user` (id, day, name, email, type, locked)
      VALUES ('$i', '$curDay', \"$name\", \"$user\", 'student', 0);";
      if(!mysqli_query($connect, $query)) {
        echo "scripts/schedule.php:293::Query failed: " . mysqli_error($connect);
      }
    }
  }

  function studentTableIsEmpty($user, $connect) {
    $query = "SELECT name FROM `$user` WHERE id='0'";
    $result = mysqli_query($connect, $query);
    if(empty($result)) {
      return true;
    } else {
      $array = mysqli_fetch_assoc($result);
      if(empty($array["name"])) {
        return true;
      } else {
        return false;
      }
    }
  }

  function studentRoomIsEmpty($user, $connect) {
    $parsedData = updateCurrentData($user, $connect);
    if($parsedData["room"] == null || $parsedData["room"] == 'null' || $parsedData["room"] == '') return true;
    return false;
  }

  function createTeacherTable($user, $name, $roomNum, $slots, $connect) {
    strtolower($user);
    $query = "CREATE TABLE `$user` (
      id INT(10),
      day VARCHAR(30),
      name VARCHAR(60),
      email VARCHAR(50),
      type VARCHAR(30),
      room INT(10),
      slots INT(10),
      slotsUsed INT(10),
      available BOOLEAN,
      flexStudents TEXT,
      visitingStudents TEXT,
      blockedStudents TEXT
    )";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:334::Query failed: " . mysqli_error($connect);
    } else {
      for($id = 0; $id < 5; $id++) {
        $day = "";
        if($id == 0) $day = "Monday";
        else if($id == 1) $day = "Tuesday";
        else if($id == 2) $day = "Wednesday";
        else if($id == 3) $day = "Thursday";
        else if($id == 4) $day = "Friday";
        $query = "INSERT INTO `$user` (id, day, name, email, type, room, slots, slotsUsed, available, visitingStudents, blockedStudents)
        VALUES ('$id', '$day', \"$name\", \"$user\", 'teacher', '$roomNum', '$slots', '0', 1, 'NONE', 'NONE')";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:346::Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function createFloaterTable($user, $name, $schedule, $connect) {
    strtolower($user);
    $query = "CREATE TABLE `$user` (
      id INT(10),
      day VARCHAR(30),
      name VARCHAR(60),
      email VARCHAR(50),
      type VARCHAR(30),
      room INT(10),
      teacherCovering VARCHAR(60),
      visitingStudents TEXT,
      blockedStudents TEXT,
      available BOOLEAN,
      slots INT(10),
      slotsUsed INT(10)
    )";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:334::Query failed: " . mysqli_error($connect);
    } else {
      for($id = 0; $id < 5; $id++) {
        $day = "";
        $roomNum = 0;
        $teacherCovering = $schedule[$id];
        if($schedule[$id] != 'NONE') {
          $teacherTable = getTeacherTable($schedule[$id], $connect);
          $teacherData = getTableData($teacherTable, 0, $connect);
          $roomNum = $teacherData['room'];
        }

        if($id == 0) $day = "Monday";
        else if($id == 1) $day = "Tuesday";
        else if($id == 2) $day = "Wednesday";
        else if($id == 3) $day = "Thursday";
        else if($id == 4) $day = "Friday";
        $query = "INSERT INTO `$user` (id, day, name, email, type, room, teacherCovering, visitingStudents, blockedStudents, available, slots, slotsUsed)
        VALUES ('$id', '$day', \"$name\", \"$user\", 'floater', '$roomNum', \"$teacherCovering\", \"NONE\", 'NONE', 1, 5, 0)";
        if(!mysqli_query($connect, $query)) {
          echo "scripts/schedule.php:346::Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function lastAccessedDay($connect) {
    $query = "SELECT Day FROM `Previous Access Date` WHERE id=0";
    if(!$result = mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:414::Query failed: " . mysqli_error($connect);
    } else {
      $array = mysqli_fetch_assoc($result);
      return filter_var($array["Day"], FILTER_VALIDATE_INT);
    }
  }

  function resetTables($connect, $day) {
    $query = "SHOW TABLES FROM franklin_flexSystem";
    if(!$result = mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:424::Query failed: " . mysqli_error($connect);
    }
    while($tables = mysqli_fetch_array($result)) {
      foreach($tables as $t) {
        $data = getTableData($t, 0, $connect);
        if($data != null) {
          if($data["type"] == 'teacher') {
            $query = "UPDATE `$t` SET visitingStudents='NONE',slotsUsed=0,available=1,blockedStudents='NONE'";
          } else if($data["type"] == 'student') {
            $query = "UPDATE `$t` SET teacher=room, locked=0";
          }
          if(!mysqli_query($connect, $query)) {
            echo "scripts/schedule.php:436::Query failed: " . mysqli_error($connect);
          }
        }
      }
    }
    $query = "UPDATE `Previous Access Date` SET Day=$day";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:443::Query failed: " . mysqli_error($connect);
    }
  }

  function setFlexroom($userName, $roomName, $connect) {
    $userTable = getStudentTable($userName, $connect);
    $teacherTable = getTeacherTable($roomName, $connect);
    $query = "UPDATE `$userTable` SET room=\"$roomName\",teacher=\"$roomName\"";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:492::Query failed: " . mysqli_error($connect);
    } else {
      addStudentToHomeroom($userName, $roomName, $connect);
    }
  }

  function removeTable($userTable, $connect) {
    $query = "DROP TABLE `$userTable`";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:501::Query failed: " . mysqli_error($connect);
    }
  }

  function removeWrongStudentsFromHomeroom($userTable, $connect) {
    $newStudentList = array();
    for($i = 0; $i < 5; $i++) {
      $data = getTableData($userTable, $i, $connect);
      $homeroomList = explode(";", $data["flexStudents"]);
      foreach($homeroomList as $studentName) {
        if($studentName != null && $studentName != "" && $studentName != "NONE") {
          $studentExists = studentAlreadyInList($studentName, $newStudentList);
          if($studentExists) continue;
          else {
            array_push($newStudentList, $studentName);
            $studentTable = getStudentTable($studentName, $connect);
            $studentData = getTableData($studentTable, $i, $connect);
            if($studentTable == null || $studentData == null || $studentData["room"] != $data["name"]) {
              array_pop($newStudentList);
              continue;
            }
          }
        }
      }
    }
    $flexStudentsStr = implode(";", $newStudentList);
    $query = "UPDATE `$userTable` SET flexStudents=\"$flexStudentsStr\"";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:523::Query failed: " . mysqli_error($connect);
    }
  }

  function studentAlreadyInList($name, $list) {
    foreach($list as $studentName) {
      if($name == $studentName) return true;
    }
    return false;
  }

  function blockStudent($user, $studentName, $connect) {
    $data = getTableData($user, 4, $connect);
    $blockedStr = $data["blockedStudents"];
    if($blockedStr == "" || $blockedStr == "NONE" || $blockedStr == null) $blockedList = array();
    else $blockedList = explode(";", $data["blockedStudents"]);
    if(studentAlreadyInList($studentName, $blockedList)) {
      $listCopy = $blockedList;
      $blockedList = array();
      foreach($listCopy as $n) {
        if($n == 'NONE' || $n == $studentName) continue;
        else {
          array_push($blockedList, $n);
        }
      }
    } else {
      array_push($blockedList, $studentName);
      for($i = 0; $i < 5; $i++) { // getdate()['wday']-1
        $data = getTableData($user, $i, $connect);
        $visitingList = explode(";", $data["visitingStudents"]);
        if(studentAlreadyInList($studentName, $visitingList)) {
          $toKick = array($studentName);
          updateKickedStudents($toKick, $data, $i, $connect);
        }
      }
    }
    $blockedStr = implode(";", $blockedList);
    $query = "UPDATE `$user` SET blockedStudents=\"$blockedStr\"";
    if(!mysqli_query($connect, $query)) {
      echo "scripts/schedule.php:540::Query failed: " . mysqli_error($connect);
    }
  }

  function lockStudent($user, $studentName, $connect) {
    $studentTable = getStudentTable($studentName, $connect);
    if($studentTable != null) {
      for($currentDay = getdate()['wday'] - 1; $currentDay < 5; $currentDay++) {
        $studentData = getTableData($studentTable, $currentDay, $connect);
        if($studentData != null) {
          $targetTeacher = $studentData["teacher"];
          $teacherTable = getTeacherTable($targetTeacher, $connect);
          $teacherData = getTableData($teacherTable, $currentDay, $connect);
          if($teacherData != null) {
            $visitList = explode(";", $teacherData["visitingStudents"]);
            if(studentAlreadyInList($studentName, $visitList)) {
              $toKick = array($studentName);
              $data = getTableData($user, $currentDay, $connect);
              updateKickedStudents($toKick, $data, $i, $connect);
            }
          }
          $lock = filter_var($studentData["locked"], FILTER_VALIDATE_INT);
          if($lock == 0) $lock = 1;
          else $lock = 0;
          $room = $studentData["room"];
          $query = "UPDATE `$studentTable` SET locked=$lock,teacher=\"$room\" WHERE id='$currentDay'";
          if(!mysqli_query($connect, $query)) {
            echo "scripts/schedule.php:574::Query failed: " . mysqli_error($connect);
          }
          $query = "UPDATE `$studentTable` SET locked=$lock";
          if(!mysqli_query($connect, $query)) {
            echo "scripts/schedule.php:567::Query failed: " . mysqli_error($connect);
          }
        }
      }
    }
  }

  function sortByLastName($a){
    $tmp = $a;
    foreach($tmp as $k => $v){
        $tmp[$k] = substr($v,strrpos($v, ' ')+1);
    }
    asort($tmp);
    $ret = array();
    foreach($tmp as $k => $v){
        $ret[$k] = $a[$k];
    }
    return $ret;
  }

  function userIsAdmin($user, $connect) {
    $userData = getTableData($user, 0, $connect);
    if($userData["type"] == 'admin') return true;
    return false;
  }
?>
