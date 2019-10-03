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
    $query = "SHOW TABLES FROM techmeds_FlexSystem LIKE '$teacherLastName%'";
    if(!$result = mysqli_query($connect, $query)) {
      echo "Query failed: " . mysqli_error($connect);
    }
    while($tables = mysqli_fetch_array($result)) {
      foreach($tables as $t) {
        $data = getTableData($t, 0, $connect);
      }
      return $teacherTable;
    }
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
?>
