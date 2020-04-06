<html>
<head>
  <title>FAHS Flex</title>
	<link rel="stylesheet" href="./css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<!-- <meta name="apple-mobile-web-app-capable" content="yes"> -->
	<link rel="icon" href="favicon.ico">
  <meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>
  <div class="topnav">
		<a href="index.php"><img id="logo" src="faflexlogo.svg"></a>
		<a id="schedulebutton" class="disable-select">My Schedule</a>
		<a id="signupbutton" href="index.php" class="disable-select">Sign Up</a>
	</div>
  <?php
    include "scripts/adminConstants.php";
    include "scripts/schedule.php";

    $authenticated = false;

    $user = $_COOKIE["user"];
    $pass = $_COOKIE["pass"];
    if(userIsAdmin($user, $connect)) {
      if($pass != null && password_verify($pass, $adminLoginHash)) $authenticated = true;
      else {
        echo '<script>
                var pwd = prompt("Enter the password below: ");
                var d = new Date();
                d.setTime(d.getTime() + (30 * 60 * 1000));
                document.cookie = "pass=" + pwd + ";expires=" + d.toUTCString() + ";path=/";
                location.reload(true);
              </script>';
      }
    }
  ?>
  <?php
    if($authenticated) {
      $cookie = $_COOKIE["toRun"];
      $dataCookie = $_COOKIE["data"];
      switch($cookie) {
        case "resetTables":
          resetTables($connect, getdate()['wday']);
          break;
        case "removeTable":
          removeTable($dataCookie, $connect);
          break;
        case "setFlexroom":
          $array = explode(",", $dataCookie);
          $user = trim($array[0]);
          $room = trim($array[1]);
          setFlexroom($user, $room, $connect);
          break;
      }
      setcookie("toRun", null, time()+3600);
      setcookie("data", null, time()+3600);
    } else {
      echo "<strong>You do not have permission!</strong>";
    }
  ?>
  <div id="newUserPopup">
    <button id="resetTables" onclick="resetTables()">Reset All Tables</button>
    <h3 id="nametxt"><br>Change User Details</h3><br>

    <?php
        echo "<select id='user'>";
        $query = "SHOW TABLES FROM franklin_flexSystem";
        if(!$result = mysqli_query($connect, $query)) {
          echo "Failed to obtain tables..." . mysqli_error($connect);
        } else {
          while($tables = mysqli_fetch_array($result)) {
            $data = getTableData($tables[0], 0, $connect);
            if($data["type"] != null) {
              $name = $data["name"];
              $email = $data["email"];
              echo "<option id='$email'>$name</option>";
            }
          }
        }
        echo "</select>";
    ?>
  <button onclick="removeTable()">Remove</button><br><br>
      <?php
      echo "<select id='homeroom'>";
          $query = "SHOW TABLES FROM franklin_flexSystem";
          if(!$result = mysqli_query($connect, $query)) {
            echo "Failed to obtain tables..." . mysqli_error($connect);
          } else {
            while($tables = mysqli_fetch_array($result)) {
              $data = getTableData($tables[0], 0, $connect);
              if($data["type"] != null && $data["type"] == "teacher") {
                $name = $data["name"];
                $email = $data["email"];
                echo "<option id='$email'>$name</option>";
              }
            }
          echo "</select>";
        }
      ?>
      <button onclick="setFlexroom()">Set Flexroom</button><br><br>
  <br>
    <script>
      function resetTables() {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
        document.cookie = "toRun=resetTables;expires=" + d.toUTCString() + ";path=/";
        location.reload(true);
      }
      function removeTable() {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
        document.cookie = "toRun=removeTable;expires=" + d.toUTCString() + ";path=/";
        document.cookie = "data=" + document.getElementById("user").options[document.getElementById("user").selectedIndex].id + ";expires=" + d.toUTCString() + ";path=/";
        location.reload(true);
      }
      function setFlexroom() {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
        document.cookie = "toRun=setFlexroom;expires=" + d.toUTCString() + ";path=/";
        document.cookie = "data=" + document.getElementById("user").options[document.getElementById("user").selectedIndex].text + "," + document.getElementById("homeroom").options[document.getElementById("homeroom").selectedIndex].text + ";expires=" + d.toUTCString() + ";path=/";
        location.reload(true);
      }
    </script>
    <a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout()">Sign out</a>
    <script type="text/javascript" src="scripts/signin.js"></script>
    <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
  </div>
  <div id="footerSpace"></div>
	<footer>
		<div class="foot">
			&copy; 2019 Jordan Martin and Grant Gupton
			<br>
			Class of 2020
		</div>
	</footer>
</body>
</html>
