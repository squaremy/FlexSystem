<?php
  $connect = mysqli_connect("localhost", "franklin_flexsys", "PASSWORD", "franklin_flexSystem") or die('Connection failed: ' . msqli_connect_error());
  $json = file_get_contents("../configs/teacherlist.json");
  $jsonData = json_decode($json, true);

  foreach($jsonData['teachers'] as $i => $object) {
    foreach($jsonData['teachers'][$i] as $key => $data) {
      $key = strtolower($key);
      $sql = "CREATE TABLE IF NOT EXISTS `$key` (
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

      if(!mysqli_query($connect, $sql)) {
        die("Couldn't create table: " . mysqli_error($connect));
      } else {
        echo "Successully created teacher table!";
        echo "<br />";
      }
    }
  }
  echo "<br />";

  $json2 = file_get_contents("../configs/studentlist.json");
  $jsonData2 = json_decode($json2, true);

  foreach($jsonData2['students'] as $i => $object) {
    foreach($jsonData2['students'][$i] as $key => $data) {
      $key = strtolower($key);
      $sql = "CREATE TABLE IF NOT EXISTS `$key` (
        id INT(10),
        day VARCHAR(30),
        name VARCHAR(60),
        email VARCHAR(50),
        type VARCHAR(30),
        room VARCHAR(60),
        teacher VARCHAR(60)
      )";

      if(!mysqli_query($connect, $sql)) {
        die("Couldn't create table: " . mysqli_error($connect));
      } else {
        echo "Successfully created student table!<br />";
      }
    }
  }

  if(!mysqli_close($connect)) {
    die("Couldn't close sql connection: " . mysqli_error($connect));
  } else {
    echo "Closed sql connection successfully!<br />";
  }
?>

<?php
  $connect = mysqli_connect("localhost", "franklin_flexsys", "PASSWORD", "franklin_flexSystem") or die("Connection to database failed: " . mysqli_connect_error());
  $json = file_get_contents("../configs/GOAL_CONFIG.json");
  $jsonData = json_decode($json, true);

  foreach($jsonData as $email => $data) {
    $name = $jsonData[$email]['name'];
    $type = $jsonData[$email]['type'];
    if($jsonData[$email]['type'] == 'teacher') {
      foreach($jsonData[$email]['schedule'] as $i => $val) {
        $day = $jsonData[$email]['schedule'][$i]['day'];
        $room = $jsonData[$email]['room'];
        $available = $jsonData[$email]['schedule'][$i]['available'];
        $flexStudents = implode(";", $jsonData[$email]['schedule'][$i]['flexstudents']);
        $visitingStudents = implode(";", $jsonData[$email]['schedule'][$i]['visitingstudents']);
        $sql = "INSERT INTO `$email` (id, day, name, email, type, room, slots, slotsUsed, available, flexStudents, visitingStudents)
        VALUES ('$i', '$day', '$name', '$email', '$type', '$room', '0', '0', '$available', '$flexStudents', '$visitingStudents')";
        if(!mysqli_query($connect, $sql)) {
          echo "Could not insert data... " . mysqli_error($connect) . "<br />";
        } else {
          echo "Successfully inserted teacher data!<br />";
        }
      }
    } else {
      foreach($jsonData[$email]['schedule'] as $i => $val) {
        $day = $jsonData[$email]['schedule'][$i]['day'];
        $teacher = $jsonData[$email]['schedule'][$i]['teacher'];
        $room = $jsonData[$email]['flex room'];
        $sql = "INSERT INTO `$email` (id, day, name, email, type, room, teacher)
        VALUES ('$i', '$day', '$name', '$email', '$type', '$room', '$teacher');";
        if(!mysqli_query($connect, $sql)) {
          echo "Could not insert data... " . mysqli_error($connect) . "<br />";
        } else {
          echo "Successully inserted student data!<br />";
        }
      }
    }
  }
  echo "<br />";
  if(!mysqli_close($connect)) {
    die("Couldn't close sql connection: " . mysqli_error($connect));
  } else {
    echo "Closed sql connection successfully!";
  }
 ?>
