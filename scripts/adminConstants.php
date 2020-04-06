<?php

  $connect = mysqli_connect("localhost", "franklin_flexsys", "PASSWORD", "franklin_flexSystem") or die("Connection to database failed: " . mysqli_connect_error());
  $signUpTimeout = strtotime("12:13:00") % (24*60*60);
  $adminLoginHash = '$2y$10$ELelPeMdP0upEvz51P8C3uu7eK597v8p8.N0PSWelOttpGoe8PRUm'; // generate key somewhere else!!!

?>
