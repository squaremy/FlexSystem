<?php
// This PHP script must be in "SOME_PATH/jsonFile/index.php"
$file = 'temp.json';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    file_put_contents($file, $_POST["jsonTxt"]);
}
else if($_SERVER['REQUEST_METHOD'] === 'GET'){
    echo file_get_contents($file);
    $array_data = json_decode($file, true);
    $extra = array(
      'name'    => 'test'
    );
    $array_data[] = array_push($array_data, $extra);
    $final_data = json_encode($array_data);
    file_put_contents('temp.json', $final_data);
}
?>

<?php
  $connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!") or die('Database Not Connected. Please Fix the Issue! ' . mysqli_error());
  mysqli_select_db($connect, "techmeds_FlexSystem");

  $jsonContents = file_get_contents("../configs/GOAL_CONFIG.json");

  $jsonArray = json_decode($jsonContents, true);

  echo $jsonArray;

  $query;

  for($i = 0; $i < count($jsonArray); $i++) {
    $name = $jsonArray[$i]['name'];
    $type = $jsonArray[$i]['type'];
    if($type == 'teacher') {
      $room = $jsonArray[$i]['room'];
      $schedule = $jsonArray[$i]['schedule'];
      $query = "INSERT INTO teacherData(name, type, room, schedule) VALUES('$name', '$type', '$room', '$schedule')";
    } else {
      $flexRoom = $jsonArray[$i]['flex room'];
      $schedule = $jsonArray[$i]['schedule'];
      $query = "INSERT INTO teacherData(name, type, flexRoom, schedule) VALUES('$name', '$type', '$flexRoom', '$schedule')";
    }
  }


  if(!mysqli_query($connect, $query)) {
    die('Error : Query Not Executed. Please Fix the Issue! ' . mysqli_error());
  } else {
    echo "Data Inserted Successully!!!";
  }
 ?>
