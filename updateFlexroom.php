<html>
<head>
  <title>FAHS Flex</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<!-- <meta name="apple-mobile-web-app-capable" content="yes"> -->
	<link rel="icon" href="favicon.ico">
  <meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
  <script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>
  <div class="topnav">
		<a href="index.html"><img id="logo" src="faflexlogo.svg"></a>
		<a id="schedulebutton" class="disable-select">My Schedule</a>
		<a id="signupbutton" href="index.php" class="disable-select">Sign Up</a>
	</div>
  <p id="searchtxt">Select Your Homeroom Teacher</p>
  <div id="newUserPopup">
    <select id="teacherSelect">
      <?php
        include "scripts/schedule.php";
        include "scripts/adminConstants.php";

        $query = "SHOW TABLES FROM franklin_flexSystem";
        if(!$result = mysqli_query($connect, $query)) {
          echo "Failed to obtain tables..." . mysqli_error($connect);
        } else {
          while($tables = mysqli_fetch_array($result)) {
            $data = getTableData($tables[0], 0, $connect);
            if($data["type"] != null && $data["type"] == "teacher") {
              $name = $data["name"];
              echo "<option>$name</option>";
            }
          }
        }
      ?>
    </select>
    <button id="submit" onclick="setHomeroom()">Submit</button>
    <script>
    function setHomeroom(){
      var select = document.getElementById("teacherSelect")
      var teachername = select.options[select.selectedIndex].text
      var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Email"] + "&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&signedup=2&room=" + teachername;
      window.location.href = "schedule.php?" + extension;
    }
    </script>
  </div>
  <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
  <a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout();">Sign out</a>
  <script type="text/javascript" src="scripts/signin.js"></script>
</body>
<div id="footerSpace"></div>
<footer>
  <div class="foot">
    &copy; 2019 Jordan Martin and Grant Gupton
    <br/>
    Class of 2020
  </div>
</footer>
</html>
