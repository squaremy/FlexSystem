<!DOCTYPE html>
<html>
<head>
  <title>FAHS Flex</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="apple-touch-icon" href="/faflexappicon.png">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" href="favicon.ico">
</head>
<body>
  <div id="newUserPopup">
    <input type="text" id="name" placeholder="Name">
    <input type="text" id="email" placeholder="Email">
    <input type="number" id="roomNum" placeholder="Room Number">
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

      var extension = "user=" + user + "&name=" + name + "&signedup=3&flexStudents=" + flexStudents + "&roomNum=" + roomNum;
      window.location.href = "schedule.php?" + extension;
    }
    </script>
  </div>
</body>
</html>
