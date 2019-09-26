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
  $content = mysqli_connect("localhost", "root") or die('Database Not Connected. Please Fix the Issue! ' . mysqli_error());
  mysqli_select_db("techmeds_FlexSystem", $connect);

  $jsonContents = json_get_contents("../configs/GOAL_CONFIG.json");

  $jsonArray = json_decode($jsonContents, true);

  $query;

  for($i = 0; $i < $jsonArray.length; $i++) {
    $name = $jsonArray[i]['name'];
    $type = $jsonArray[i]['type'];
    if($type == 'teacher') {
      $room = $jsonArray[i]['room'];
      $schedule = $jsonArray[i]['schedule'];
      $query = "INSERT INTO teacherData(name, type, room, schedule) VALUES('$name', '$type', '$room', '$schedule')";
    } else {
      $flexRoom = $jsonArray[i]['flex room'];
      $schedule = $jsonArray[i]['schedule'];
      $query = "INSERT INTO teacherData(name, type, flexRoom, schedule) VALUES('$name', '$type', '$flexRoom', '$schedule')";
    }
  }


  if(!mysqli_query($query,$connect)) {
    die('Error : Query Not Executed. Please Fix the Issue! ' . mysqli_error());
  } else {
    echo "Data Inserted Successully!!!";
  }
 ?>
