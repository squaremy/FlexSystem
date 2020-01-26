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
  <div id="newUserPopup">
    <button id="resetTables" onclick="resetTables()">Reset All Tables</button>
    <h3 id="nametxt"><br>Change User Details</h3><br>

    <?php
      include "scripts/schedule.php";
      include "scripts/adminConstants.php";

        echo "<td><select id=$i>";
        $query = "SHOW TABLES FROM franklin_flexSystem";
        if(!$result = mysqli_query($connect, $query)) {
          echo "Failed to obtain tables..." . mysqli_error($connect);
        } else {
          while($tables = mysqli_fetch_array($result)) {
            $data = getTableData($tables[0], 0, $connect);
            if($data["type"] != null) {
              $name = $data["name"];
              echo "<option id='$name'>$name</option>";
            }
          }
        }
        echo "</select>";
    ?>
  <br><br><button>Remove</button><br><br>
      <?php
      echo "<td><select id=$i>";
          $query = "SHOW TABLES FROM franklin_flexSystem";
          if(!$result = mysqli_query($connect, $query)) {
            echo "Failed to obtain tables..." . mysqli_error($connect);
          } else {
            while($tables = mysqli_fetch_array($result)) {
              $data = getTableData($tables[0], 0, $connect);
              if($data["type"] != null && $data["type"] == "teacher") {
                $name = $data["name"];
                echo "<option id='$name'>$name</option>";
              }
            }
          echo "</select>";
        }
      ?>
      <button>Set Homeroom</button><br><br>
  <h3 id="nametxt">Admin Constants</h3>
  <br>
<table>
  <tr><input type="time"></input></tr>
  <tr><button onclick="#">Set Signup Timeout</button></tr>
</table>
    <!-- <button id="submit" onclick="submit()">Submit</button> -->
    <script>
    document.getElementById("email").value = JSON.parse(sessionStorage.getItem("myUserEntity"))["Email"];
    document.getElementById("name").value = JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"];

    function submit(){
      // var flexStudents = document.getElementById("flexStudents").value;
      var roomNum = document.getElementById("roomNum").value;
      var user = document.getElementById("email").value;
      var name = document.getElementById("name").value;
      var slots = document.getElementById("slots").value;
      var floater = document.getElementById("floater").checked;


      var extension = "user=" + user + "&name=" + name + "&signedup=3&roomNum=" + roomNum + "&slots=" + slots;
      window.location.href = "schedule.php?" + extension;
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
