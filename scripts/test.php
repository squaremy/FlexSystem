<?php
include "adminConstants.php";
include "schedule.php";

$query = "SHOW TABLES FROM franklin_flexSystem";
if(!$result = mysqli_query($connect, $query)) {
  echo "Query failed: " . mysqli_error($connect);
}
while($tables = mysqli_fetch_array($result)) {
  foreach($tables as $t) {
    $data = getTableData($t, 0, $connect);
    if($data != null && $data["type"] == "student") {
      $query = "UPDATE `$t` SET locked=0";
      if(!mysqli_query($connect, $query)) {
        echo "Query 1 failed: " . mysqli_error($connect);
      }
    } else if($data != null && $data["type"] == "teacher") {
      $query = "UPDATE `$t` SET blockedStudents='NONE'";
      if(!mysqli_query($connect, $query)) {
        echo "Query 2 failed: " . mysqli_error($connect);
      }
    } else if($data != null && $data["type"] == "floater") {
      $query = "UPDATE `$t` SET blockedStudents='NONE'";
      if(!mysqli_query($connect, $query)) {
        echo "Query 3 failed: " . mysqli_error($connect);
      }
    }
  }
}


?>
