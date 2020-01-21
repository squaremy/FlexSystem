<?php

  $connect = mysqli_connect("localhost", "franklin_flexsys", "PASSWORD", "franklin_flexSystem") or die("Connection to database failed: " . mysqli_connect_error());
  $signUpTimeout = strtotime("12:10:00");

?>
