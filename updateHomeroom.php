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
    <select id="teacherSelect">
      <?php
        include "scripts/schedule.php";

        $connect = mysqli_connect("localhost", "techmeds_FlexSystem", "Tennessee18!", "techmeds_FlexSystem") or die("Connection to database failed: " . mysqli_connect_error());
        $query = "SHOW TABLES FROM techmeds_FlexSystem";
        if(!$result = mysqli_query($connect, $query)) {
          echo "Failed to obtain tables..." . mysqli_error($connect);
        } else {
          $whileCount = 0;
          $forCount = 0;
          while($tables = mysqli_fetch_array($result)) {
            $forCount = 0;
            $whileCount++;
            foreach($tables as $t) {
              $forCount++;
              $data = getTableData($t, 0, $connect);
              if($data["type"] != null && $data["type"] == "teacher") {
                $name = $data["name"];
                echo "<option>$name</option>";
              }
              echo $forCount . "For";
            }
            echo $whileCount . "While";
          }
        }
      ?>
    </select>
    <button id="submit" onclick="setHomeroom()">Submit</button>
    <script>
    function setHomeroom(){
      var select = document.getElementById("teacherSelect")
      var teachername = select.options[select.selectedIndex].text
      var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Email"] + "&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&signedup=0&room=" + teachername;
      window.location.href = "schedule.php?" + extension;
    }
    </script>
  </div>
</body>
</html>
