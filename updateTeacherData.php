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
    <h3 id="nametxt">Name</h3>
    <input type="text" id="name" placeholder="Name">
    <h3 id="emailtxt">Email</h3>
    <input type="text" id="email" placeholder="Email">
    <h3 id="floatertxt">Floater?</h3>
    <input type="checkbox" id="floater" onclick="showExtraFields()">
    <h3 id="roomNumtxt">Room Number</h3>
    <input type="number" id="roomNum" placeholder="Room Number">
    <h3 id="slotstxt">Slots Available To Visiting Students</h3>
    <input type="number" id="slots" placeholder="Slots">
    <h3 id="floaterSchedule" style="display: none;">Floater Schedule</h3>
    <table id="floaterScheduleTable" style="display: none;">
      <tr>
      <?php
        include "scripts/schedule.php";
        include "scripts/adminConstants.php";

        for($i = 0; $i < 5; $i++) {
          echo "<td><select id=$i><option id='NONE'>N/A</option>";
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
          }
          echo "</select></td>";
        }
      ?>
      </tr>
    </table>
    <br/>

    <button id="submit" onclick="submit()">Submit</button>
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
      var monSelect = document.getElementById("0");
      var tueSelect = document.getElementById("1");
      var wedSelect = document.getElementById("2");
      var thuSelect = document.getElementById("3");
      var friSelect = document.getElementById("4");

      var extension = "user=" + user + "&name=" + name + "&signedup=3" + "&floater=" + floater;
      if(floater) {
        extension += "&mon=" + monSelect.options[monSelect.selectedIndex].id + "&tue="
                      + tueSelect.options[tueSelect.selectedIndex].id + "&wed=" + wedSelect.options[wedSelect.selectedIndex].id
                      + "&thu=" + thuSelect.options[thuSelect.selectedIndex].id + "&fri=" + friSelect.options[friSelect.selectedIndex].id;
      } else {
        extension += "&roomNum=" + roomNum + "&slots=" + slots;
      }
      var d = new Date();
      d.setTime(d.getTime() + (60 * 24 * 60 * 60 * 1000));
      document.cookie = "updated=false;expires=" + d.toUTCString() + ";path=/";
      window.location.href = "schedule.php?" + extension;
    }

    function showExtraFields() {
      var checkbox = document.getElementById("floater");
      if(checkbox.checked) {
        document.getElementById("floaterSchedule").style.display = "";
        document.getElementById("floaterScheduleTable").style.display = "";
        document.getElementById("roomNum").style.display = "none";
        document.getElementById("roomNumtxt").style.display = "none";
        document.getElementById("slotstxt").style.display = "none";
        document.getElementById("slots").style.display = "none";
      } else {
        document.getElementById("floaterSchedule").style.display = "none";
        document.getElementById("floaterScheduleTable").style.display = "none";
        document.getElementById("roomNum").style.display = "";
        document.getElementById("roomNumtxt").style.display = "";
        document.getElementById("slotstxt").style.display = "";
        document.getElementById("slots").style.display = "";
      }
    }
    </script>
  </div>
  <script type="text/javascript" src="scripts/signin.js"></script>
  <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
  <a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout();">Sign out</a>
  <div id="footerSpace"></div>
	<footer>
		<div class="foot">
			&copy; 2019 Jordan Martin and Grant Gupton
			<br/>
			Class of 2020
		</div>
	</footer>
</body>
</html>
