function linkSchedulePage() {
  var schedulebutton = document.getElementById("schedulebutton");
  schedulebutton.href = "schedule.php?user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=0";
}

linkSchedulePage();
