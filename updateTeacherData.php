<!DOCTYPE html>
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
		<a id="signupbutton" href="index.html" class="disable-select">Sign Up</a>
	</div>
  <div id="newUserPopup">
    <h3 id="nametxt">Name</h3>
    <input type="text" id="name" placeholder="Name">
    <h3 id="emailtxt">Email</h3>
    <input type="text" id="email" placeholder="Email">
    <h3 id="roomNumtxt">Room Number</h3>
    <input type="number" id="roomNum" placeholder="Room Number">
    <h3 id="slotstxt">Slots Available To Visiting Students</h3>
    <input type="number" id="slots" placeholder="Slots">
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

      var extension = "user=" + user + "&name=" + name + "&signedup=3&roomNum=" + roomNum + "&slots=" + slots;
      window.location.href = "schedule.php?" + extension;
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
