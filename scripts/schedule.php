<?php
  function getStudentTable($name, $connect) {
    if($name != 'NONE') {
      $names = explode(" ", $name);
      $lastName = strtolower($names[1]);
      $nameToSearch = $lastName . strtolower(substr($names[0], 0, 1));
      $query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$nameToSearch%'";
      if(!$result = mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
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
      echo "Query failed: " . mysqli_error($connect);
    }
    $toReturn = mysqli_fetch_assoc($data);
    return $toReturn;
  }

  function teacherIsAvailable($table, $desiredDay, $connect) {
    $query = "SELECT available FROM `$table` WHERE id='$desiredDay'";
    if(!$data = mysqli_query($connect, $query)) {
      echo "Query failed: " . mysqli_error($connect);
    }
    $readableData = mysqli_fetch_array($data);
    $result = filter_var($readableData["available"], FILTER_VALIDATE_BOOLEAN);
    return $result;
  }

  function getTeacherTable($teacherName, $connect) {
    $names = explode(" ", $teacherName);
    $teacherLastName = strtolower($names[1]);
    $toLookFor = $teacherLastName . strtolower(substr($names[0], 0, 1));
    $query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$toLookFor%'";
    if(!$result = mysqli_query($connect, $query)) {
      echo "Query failed: " . mysqli_error($connect);
    }
    while($tables = mysqli_fetch_array($result)) {
      foreach($tables as $t) {
        $data = getTableData($t, 0, $connect);
        if($data != null && $data["name"] == $teacherName && $data["type"] == 'teacher') return $t;
      }
    }
    return null;
  }

  function getTeacherTableLessEffective($teacherLastName, $connect) {
    $teacherLastName = strtolower($teacherLastName);
    $query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$teacherLastName%'";
    if(!$result = mysqli_query($connect, $query)) {
      echo "Query failed: " . mysqli_error($connect);
    }
    while($tables = mysqli_fetch_array($result)) {
      foreach($tables as $t) {
        $data = getTableData($t, 0, $connect);
        $lastName = strtolower(explode(" ", $data["name"])[1]);
        if($teacherLastName == $lastName) return $t;
      }
    }
    return null;
  }

  function addStudentToVisitList($teacherTableData, $studentName, $desiredDay, $connect, $room) {
    $roomNames = explode(" ", $room);
    $roomLastName = strtolower($roomNames[1]);
    $email = $teacherTableData["email"];
    $slotsUsed = $teacherTableData["slotsUsed"] + 1;
    if($teacherTableData["name"] != $roomLastName) {
      $visitingStudents = $teacherTableData["visitingStudents"];
      if(strpos($visitingStudents, $studentName) !== false) {
      } else {
        if($visitingStudents == "NONE") $visitingStudents = $studentName;
        else $visitingStudents = $visitingStudents . ";" . $studentName;
      }
      $query = "UPDATE `$email` SET visitingStudents='$visitingStudents',slotsUsed='$slotsUsed' WHERE id='$desiredDay'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }
    }
  }

  function updateCurrentData($user, $connect) {
    $sql = "SELECT * FROM `$user`";
    $data = null;
    if(!$data = mysqli_query($connect, $sql)) {
      die("error: " . mysqli_error($connect));
    }
    mysqli_data_seek($data, getdate()['wday']-1);
    $parsedData = mysqli_fetch_assoc($data);
    return $parsedData;
  }

  function getRawData($user, $connect) {
    $sql = "SELECT * FROM `$user`";
    $data = null;
    if(!$data = mysqli_query($connect, $sql)) {
      die("error: " . mysqli_error($connect));
    }
    return $data;
  }

  function updateSignup($going, $teacher, $dayID, $user, $connect) {
    if($going) {
      $targetTeacher = getTeacherTable($teacher, $connect);
      if(teacherIsAvailable($targetTeacher, $dayID, $connect)) {
        $query = "UPDATE `$user` SET teacher='$teacher' WHERE id='$dayID'";
        if(!mysqli_query($connect, $query)) {
          echo "Query failed: " . mysqli_error($connect);
        }
        $teacherData = getTableData($targetTeacher, 0, $connect);
        $curData = updateCurrentData($user, $connect);
        $name = $curData["name"];
        $room = $curData["room"];
        addStudentToVisitList($teacherData, $name, $dayID, $connect, $room);
      }
    }
  }

  function updateKickedStudents($tokick, $parsedData, $connect) {
    foreach($tokick as $studentName) {
      if($studentName != '' && $studentName != 'NONE') {
        $studentTable = getStudentTable($studentName, $connect);
        if($studentTable != null) {
          $dayOfWeek = getdate()['wday']-1;
          $studentData = getTableData($studentTable, $dayOfWeek, $connect);
          if($studentData["teacher"] == $parsedData["name"]) {
            $room = $studentData["room"];
            $query = "UPDATE `$studentTable` SET teacher='$room' WHERE day='$dayOfWeek'";
            if(!mysqli_query($connect, $query)) {
              echo "Query failed: " . mysqli_error($connect);
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
      $query = "UPDATE `$user` SET available='$available' WHERE id='$dayID'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }

      $visitingStudentsStr = $parsedData["visitingStudents"];
      $visitingStudents = explode(";", $visitingStudentsStr);
      foreach($visitingStudents as $studentName) {
        $studentTable = getStudentTable($studentName, $connect);
        if($studentTable != null) {
          $studentData = getTableData($studentTable, $dayID, $connect);
          if($studentData["teacher"] == $parsedData["name"]) {
            $room = $studentData["room"];
            $query = "UPDATE `$studentTable` SET teacher='$room' WHERE id='$dayID'";
            if(!mysqli_query($connect, $query)) {
              echo "Query failed: " . mysqli_error($connect);
            }
          }
        }
      }
    }
  }

  function availabilityUpdates($day, $parsedData, $user, $connect) {
    $available = filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
    if($available == false) {
      $query = "UPDATE `$user` SET visitingStudents='NONE' WHERE id='$day'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }
    } else {
      if($parsedData["visitingStudents"] != "NONE" && $parsedData["visitingStudents"] != "") {
        $visitingStudents = explode(";", $parsedData["visitingStudents"]);
        $newVisitingStudents = [];
        $count = 0;
        foreach($visitingStudents as $studentName) {
          $studentTable = getStudentTable($studentName, $connect);
          if($studentTable != null) {
            $studentData = getTableData($studentTable, $day, $connect);
            if($studentData["teacher"] != "undecided" && $studentData["teacher"] == $name) {
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
      } else if($parsedData["visitingStudents"] == "") {
        $query = "UPDATE `$user` SET visitingStudents='NONE' WHERE id='$day'";
        if(!mysqli_query($connect, $query)) {
          echo "Query failed: " . mysqli_error($connect);
        }
      }
    }
  }

  function createNewUserIfNonexistent($user, $connect) {
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
        teacher VARCHAR(60)
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
      $parsedData = mysqli_data_seek($data, $day);
      $slotAmt = $slots[$day];
      $user = $parsedData["email"];
      $query = "UPDATE `$user` SET slots='$slotAmt' WHERE id='$day'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }
      $visitingStudents = explode(";", $parsedData["visitingStudents"]);
      while(sizeof($visitingStudents) > $slotAmt) {
        $student = array_pop($visitingStudents);
        $studentTable = getStudentTable($student, $connect);
        $studentData = getTableData($studentTable, $day, $connect);
        $teacher = $studentData["teacher"];
        if($teacher == $parsedData["name"]) {
          $teacher = $studentData["room"];
          $query = "UPDATE `$studentTable` SET teacher='$teacher' WHERE id='$day'";
          if(!mysqli_query($connect, $query)) {
            echo "Query failed: " . mysqli_error($connect);
          }
        }
      }
      $visitingStudentsStr = implode(";", $visitingStudents);
      $query = "UPDATE `$user` SET visitingStudents='$visitingStudentsStr' WHERE id='$day'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
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
      $query = "INSERT INTO `$user` (id, day, name, email, type)
      VALUES ('$i', '$curDay', '$name', '$user', 'student');";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
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

  function createTeacherTable($user, $name, $roomNum, $flexStudents, $slots, $connect) {
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
      flexStudents VARCHAR(65535),
      visitingStudents VARCHAR(65535)
    )";
    if(!mysqli_query($connect, $query)) {
      echo "Query failed: " . mysqli_error($connect);
    } else {
      for($id = 0; $id < 5; $id++) {
        $day = "";
        if($id == 0) $day = "Monday";
        else if($id == 1) $day = "Tuesday";
        else if($id == 2) $day = "Wednesday";
        else if($id == 3) $day = "Thursday";
        else if($id == 4) $day = "Friday";
        $query = "INSERT INTO `$user` (id, day, name, email, type, room, slots, slotsUsed, available, flexStudents, visitingStudents)
        VALUES ('$id', '$day', '$name', '$user', 'teacher', '$roomNum', '$slots', '0', 1, '$flexStudents', 'NONE')";
        if(!mysqli_query($connect, $query)) {
          echo "Query failed: " . mysqli_error($connect);
        }
      }
    }
  }
?>
