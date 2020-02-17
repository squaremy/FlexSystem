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

    $cookie = $_COOKIE["toRun"];
    $dataCookie = $_COOKIE["data"];
    switch($cookie) {
      case "resetTables":
        resetTables($connect, getdate()['wday']);
        break;
      case "signupTimeout":
        $signUpTimeout = strtotime($dataCookie) % (24*60*60);
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
  <h3 id="nametxt">Admin Constants</h3>
  <br>
<table>
  <tr><input id="time" type="time" step="1"></input></tr>
  <tr><button onclick="setSignupTimeout()">Set Signup Timeout</button></tr>
</table>
    <script>
      function resetTables() {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
        document.cookie = "toRun=resetTables;expires=" + d.toUTCString() + ";path=/";
        location.reload(true);
      }
      function setSignupTimeout() {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
        document.cookie = "toRun=signupTimeout;expires=" + d.toUTCString() + ";path=/";
        document.cookie = "data=" + document.getElementById("time").value; + ";expires=" + d.toUTCString() + ";path=/";
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
