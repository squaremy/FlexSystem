<?php
  function get_data(){
    $connect = mysqli_connect("localhost", "root", "", "testing");
    $query = "SELECT * FROM tbl_employee";
    
  }

  // $content = mysql_connect("localhost", "root","") or die('Database Not Connected. Please Fix the Issue! ' . mysql_error());
  // mysql_select_db("../database/json.db", $connect);
  //
  // $jsonContents = json_get_contents("../configs/data.json");
  //
  // $jsonArray = json_decode($jsonContents, true);
  //
  // $query;
  //
  // for(int i = 0; i < $jsonArray.length; i++) {
  //   $name = $jsonArray[i]['name'];
  //   $type = $jsonArray[i]['type'];
  //   if($type == 'teacher') {
  //     $room = $jsonArray[i]['room'];
  //     $schedule = $jsonArray[i]['schedule'];
  //     $query = "INSERT INTO teacherData(name, type, room, schedule) VALUES('$name', '$type', '$room', '$schedule')";
  //   } else {
  //     $flexRoom = $jsonArray[i]['flex room'];
  //     $schedule = $jsonArray[i]['schedule'];
  //     $query = "INSERT INTO teacherData(name, type, flexRoom, schedule) VALUES('$name', '$type', '$flexRoom', '$schedule')";
  //   }
  // }
  //
  //
  // if(!mysql_query($query,$connect)) {
  //   die('Error : Query Not Executed. Please Fix the Issue! ' . mysql_error());
  // } else {
  //   echo "Data Inserted Successully!!!";
  // }
 ?>
