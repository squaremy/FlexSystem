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
          if($data != null && $data["name"] == $name) return $t;
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
        if($data["name"] == $teacherName) return $t;
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
    if($teacherTableData["name"] != $roomLastName) {
      $visitingStudents = $teacherTableData["visitingStudents"];
      if(strpos($visitingStudents, $studentName) !== false) {
        // echo "Student already visiting!";
      } else {
        if($visitingStudents == "NONE") $visitingStudents = $studentName;
        else $visitingStudents = $visitingStudents . ";" . $studentName;
      }
      $query = "UPDATE `$email` SET visitingStudents='$visitingStudents' WHERE id='$desiredDay'";
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

  function updateSignup($going, $teacher, $dayID, $user, $connect) {
    if($going) {
      $targetTeacher = getTeacherTable($teacher, $connect);
      if(teacherIsAvailable($targetTeacher, $dayID, $connect)) {
        $query = "UPDATE `$user` SET teacher='$targetTeacher' WHERE id='$dayID'";
        if(!mysqli_query($connect, $query)) {
          echo "Query failed: " . mysqli_error($connect);
        }
        $curData = updateCurrentData($user, $connect);
        $name = $curData["name"];
        $room = $curData["room"];
        addStudentToVisitList($targetTeacher, $name, $dayID, $connect, $room);
      }
    }
  }

  function updateKickedStudents($tokick, $parsedData, $connect) {
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

  function flipAvailability($swap, $data, $dayID, $user, $connect) {
    if($swap) {
      mysqli_data_seek($data, $dayID);
      $parsedData = mysqli_fetch_array($data);
      $available = !filter_var($parsedData["available"], FILTER_VALIDATE_BOOLEAN);
      $query = "UPDATE `$user` SET available='$available' WHERE id='$dayID'";
      if(!mysqli_query($connect, $query)) {
        echo "Query failed: " . mysqli_error($connect);
      }

      $flexStudentsStr = $parsedData["flexStudents"];
      $flexStudents = explode(";", $flexStudentsStr);
      foreach($flexStudents as $studentName) {
        $studentTable = getStudentTable($studentName, $connect);
        $goingTo = "undecided";
        if($studentTable != null) {
          $query = "UPDATE `$studentTable` SET teacher='$goingTo' WHERE id='$dayID'";
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
            $studentData = getStudentData($studentTable, $day, $connect);
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

  function createNewUserIfNonexistent($user, $name, $connect) {
    $query = "CREATE TABLE IF NOT EXISTS `$user` (
      id INT(10),
      day VARCHAR(30),
      name VARCHAR(60),
      email VARCHAR(50),
      type VARCHAR(30),
      room VARCHAR(60),
      teacher VARCHAR(60)
    )";
    if(!mysqli_query($connect, $query)) {
      echo "Failed to create new user table";
    } else {
      $teacherLastName = $_POST["roomInput"]; // TODO: add input for last name
      $teacherTable = getTeacherTableLessEffective($teacherLastName, $connect);
      $teacherData = getTableData($teacherTable, 0, $connect);
      $room = $teacherData["name"];
      for($i = 0; $i < 5; $i++) {
        $query = "INSERT INTO `$user` (id, day, name, email, type, room, teacher) VALUES ('$i', '$days[$i]', '$name', '$user', 'student', '$room', '$room')";
      }
    }
  }
?>