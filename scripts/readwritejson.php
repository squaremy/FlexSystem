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
      $sql = "CREATE TABLE `$key` (
        name VARCHAR(60),
        day VARCHAR(30),
        email VARCHAR(50),
        room INT(10),
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
  if(!mysqli_close($connect)) {
    die("Couldn't close sql connection: " . mysqli_error($connect));
  } else {
    echo "Closed sql connection successfully!";
  }
?>
