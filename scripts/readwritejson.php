<!-- <?php
// // This PHP script must be in "SOME_PATH/jsonFile/index.php"
// $file = 'temp.json';
// if($_SERVER['REQUEST_METHOD'] === 'POST'){
//     file_put_contents($file, $_POST["jsonTxt"]);
// }
// else if($_SERVER['REQUEST_METHOD'] === 'GET'){
//     echo file_get_contents($file);
//     $array_data = json_decode($file, true);
//     $extra = array(
//       'name'    => 'test'
//     );
//     $array_data[] = array_push($array_data, $extra);
//     $final_data = json_encode($array_data);
//     file_put_contents('temp.json', $final_data);
// }
?> -->

<!-- <?php
  // $connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!") or die('Database Not Connected. Please Fix the Issue! ' . mysqli_error($connect));
  // mysqli_select_db($connect, "techmeds_FlexSystem");
  //
  // $jsonContents = file_get_contents("../configs/GOAL_CONFIG.json");
  //
  // $jsonArray = json_decode($jsonContents, true);
  //
  // $query = null;
  //
  // foreach($jsonArray as $key => $val) {
  //   echo $key;
  //   $name = $jsonArray[$key]['name'];
  //   $type = $jsonArray[$key]['type'];
  //   echo $type;
  //
  //   if($type == 'teacher') {
  //     $room = $jsonArray[$key]['room'];
  //     $available = $jsonArray[$key]['schedule'][0]['available'];
  //     $query = "INSERT INTO teacherData(name, type, room, available) VALUES('$name', '$type', '$room', '$available')";
  //     // foreach($jsonArray[$key]['schedule'][0] as $k => $v) {
  //     //   $flexStudents = null;
  //     //   $visitingStudents = null;
  //     //   if($k == 'flexstudents') $flexStudents = $v;
  //     //   else if($k == 'visitingstudents') $visitingStudents = $v;
  //     //   $query = "INSERT INTO teacherData(name, type, room, available, flexstudents, visitingstudents) VALUES('$name', '$type', '$room', '$available', '$flexStudents', '$visitingStudents')";
  //     // }
  //   } else {
  //     $flexRoom = $jsonArray[$key]['flex room'];
  //     foreach($jsonArray[$key]['schedule'][0] as $k => $v) {
  //       $teacher = null;
  //       if($k == 'teacher') $teacher = $v;
  //       $query = "INSERT INTO studentData(name, type, flexRoom, teacher) VALUES('$name', '$type', '$flexRoom', '$teacher')";
  //     }
  //   }
  //
  //   if(!mysqli_query($connect, $query)) {
  //     die('Error : Query Not Executed. Please Fix the Issue! ' . mysqli_error($connect));
  //   } else {
  //     echo "Data Inserted Successully!!!";
  //   }
  // }
 ?> -->

<?php
  $connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!", "techmeds_FlexSystem") or die('Connection failed: ' . msqli_connect_error());
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
  $connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!", "techmeds_FlexSystem") or die("Connection to database failed: " . mysqli_connect_error());
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
        $sql = "INSERT INTO `$email` (id, day, name, email, type, room, available, flexStudents, visitingStudents)
        VALUES ('$i', '$day', '$name', '$email', '$type', '$room', '$available', '$flexStudents', '$visitingStudents')";
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
