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
    if($data != null && $data["type"] == "floater") {
      $query = "ALTER TABLE `$t` ADD available BOOLEAN";
      if(!mysqli_query($connect, $query)) {
        echo "Query 3 failed: " . mysqli_error($connect);
      }

      $query = "ALTER TABLE `$t` ADD slots INT(10)";
      if(!mysqli_query($connect, $query)) {
        echo "Query 3 failed: " . mysqli_error($connect);
      }

      $query = "ALTER TABLE `$t` ADD slotsUsed INT(10)";
      if(!mysqli_query($connect, $query)) {
        echo "Query 3 failed: " . mysqli_error($connect);
      }

      $query = "UPDATE `$t` SET available=1,slots=5,slotsUsed=0";
      if(!mysqli_query($connect, $query)) {
        echo "Query 3 failed: " . mysqli_error($connect);
      }
    }
  }
}
?>
