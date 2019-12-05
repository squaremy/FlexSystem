<!DOCTYPE html>
<html>
<head>
  <title>FAHS Flex</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" href="favicon.ico">
  <meta name="google-signin-client_id" content="483422839968-llldr1bas7hurg44av8h9bh8dpqgtq98.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>
  <div id="newUserPopup">
    <input type="text" id="name" placeholder="Name">
    <input type="text" id="email" placeholder="Email">
    <input type="number" id="roomNum" placeholder="Room Number">
    <input type="number" id="slots" placeholder="# Visiting Students Slots">
    <p id="flexStudentsDisplay">In this text box, input your flex students and separate each with a semi-colon as shown here: FirstName LastName;FirstName LastName</p>
    <input type="text" id="flexStudents" placeholder="Your Flex Students">
    <button id="submit" onclick="submit()">Submit</button>
    <script>
    document.getElementById("email").value = JSON.parse(sessionStorage.getItem("myUserEntity"))["Email"];
    document.getElementById("name").value = JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"];

    function submit(){
      var flexStudents = document.getElementById("flexStudents").value;
      var roomNum = document.getElementById("roomNum").value;
      var user = document.getElementById("email").value;
      var name = document.getElementById("name").value;
      var slots = document.getElementById("slots").value;

      var extension = "user=" + user + "&name=" + name + "&signedup=3&flexStudents=" + flexStudents + "&roomNum=" + roomNum + "&slots=" + slots;
      window.location.href = "schedule.php?" + extension;
    }
    </script>
  </div>
  <script type="text/javascript" src="scripts/signin.js"></script>
  <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="askForLogin" data-theme="dark" style="visibility: hidden;"></div>
  <a href="#" style="position: absolute; top:80px; right: 10px;" onclick="logout();">Sign out</a>
</body>
</html>
