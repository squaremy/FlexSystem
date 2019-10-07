
var isteacher = false;
var myDat;

function readaJSON(path, useremail){
	fetch(path).then(response => response.json()).then(json => {
			let data2 = json[useremail];
			myDat = data2;
			var name = document.getElementById("searchtxt");
			name.innerHTML = data2['name'];
			displayWeek(data2);
			if (typeof(data2) === 'undefined'){
				return;
			}
      else if (data2['type'] == 'teacher' || isteacher) {
        displayStudents(data2);
      }
		  })
}

function displayWeek(data) {
  var table = document.getElementById("weektable");
  for(var j = 0; j < 2; j++) {
    var row = table.insertRow(-1);
    for(var i = 0; i < data['schedule'].length; i++) {
      var cell = row.insertCell(-1);
      cell.style.padding = "2px 2px 2px 2px";
      cell.id = data['schedule'][i]['day'];
      if(j == 1) {
				if(data['type'] == "teacher") {
					isteacher = true;
					cell.innerHTML = (data['schedule'][i]['available'])? "AVAILABLE":"BLOCKED";
				} else {
					cell.innerHTML = data['schedule'][i]['teacher'];
				}
      } else {
        cell.innerHTML = data['schedule'][i]['day'];
      }
    }
  }
}

function read2JSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json;
			if (typeof(data) === 'undefined'){
				return;
			}
			checktype(data);
		  })
}

function displayStudents(data) {
  var flextable = document.getElementById("flexstudents");
	var visittable = document.getElementById("visitingstudents");
	var d = new Date();
	if(d.getDay() != 0 && d.getDay() != 6) {
		var students = data['schedule'][d.getDay()-1]['flexstudents'];
		for(var i = -1; i < students.length; i++){
			var row = flextable.insertRow(-1);
			for(var j = 0; j < 3; j++) {
				var cell = row.insertCell(-1);
				if(i > -1) {
					flextable.style.border = "1px";
					if(j == 1) {
						cell.style.padding = "5px 5px 5px 5px";
						cell.id = students[i];
						if(students[i] == null) {
							cell.style.width = "20%";
							cell.style.display = "none";
						} else cell.innerHTML = students[i];
					} else if(j == 0) {
						var checkbox = document.createElement("INPUT");
						checkbox.name = students[i];
						checkbox.type = "checkbox";
						cell.appendChild(checkbox);
					} else {
						cell.innerHTML = "GOING TO";
					}
				} else {
					if(j == 0) {
						cell.innerHTML = "Kick?";
					} else if(j == 1) {
						cell.innerHTML = "My Students";
					} else {
						cell.innerHTML = "Going To";
					}
				}
			}
		}
		var visit = data['schedule'][d.getDay()-1]['visitingstudents'];
		for(var i = -1; i < visit.length; i++) {
			var row = visittable.insertRow(-1);
			for(var j = 0; j < 3; j++) {
				var cell = row.insertCell(-1);
				cell.style.padding = "5px 5px 5px 5px";
				cell.id = visit[i];
				if(i > -1) {
					visittable.style.border = "1px";
					if(visit[i] == null) {
						cell.style.width = "20%";
						cell.style.display = "none";
					} else {
						if(j == 1) cell.innerHTML = visit[i];
						else if(j == 0) {
							var checkbox = document.createElement("INPUT");
							checkbox.type = "checkbox";
							checkbox.name = visit[i];
							cell.appendChild(checkbox);
						} else cell.innerHTML = "COMING FROM";
					}
				} else {
					if(j == 0) {
						cell.innerHTML = "Kick?";
					} else if(j == 1) {
						cell.innerHTML = "Visiting Students";
					} else {
						cell.innerHTML = "Coming From";
					}
				}
			}
		}
	}
}

var teacherlist = [];
function checktype(teacherelist){
	teacherlist = teacherelist;
	var teacherlength = teacherelist['teachers'][0];
	var count = Object.keys(teacherlength).length;
	isteacher = false;
	for(var k in teacherlength){
		var userEntity = {};
		userEntity = JSON.parse(sessionStorage.getItem('myUserEntity'));
		if(userEntity["Email"] == k){
			isteacher = true;
			}
	}
	if(isteacher==false){
	}

	var name = document.getElementById("searchtxt");
	name.innerHTML = userEntity["Name"];
	readaJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json',userEntity["Email"]);
}

function loadUser() {
	read2JSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/teacherlist.json');
}

function loadData(path, useremail) {
	fetch(path).then(response => response.json()).then(json => {
			myDat = json[useremail];
		  })
}

function swapAvailability(dayOfWeek) {
	var day = "";
	switch(dayOfWeek) {
		case 0:
			day = "&mon=1&tue=0&wed=0&thu=0&fri=0";
			break;
		case 1:
			day = "&mon=0&tue=1&wed=0&thu=0&fri=0";
			break;
		case 2:
			day = "&mon=0&tue=0&wed=1&thu=0&fri=0";
			break;
		case 3:
			day = "&mon=0&tue=0&wed=0&thu=1&fri=0";
			break;
		case 4:
			day = "&mon=0&tue=0&wed=0&thu=0&fri=1";
			break;
	}
	var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&signedup=1" + day;
	window.location.href = 'schedule.php?' + extension;
}

function confirmsignup() {
	loadData('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json', JSON.parse(sessionStorage.getItem('myUserEntity'))['Email']);
	var mon = document.getElementById("monchk");
  var tue = document.getElementById("tuechk");
  var wed = document.getElementById("wedchk");
  var thu = document.getElementById("thuchk");
  var fri = document.getElementById("frichk");
	var extension = "user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'] + "&teacher=" + document.getElementById("searchtxt").innerHTML + "&name=" + JSON.parse(sessionStorage.getItem("myUserEntity"))["Name"] + "&signedup=1";

	if(myDat['type'] == "student") {
	  extension += "&mon=" + mon.checked + "&tue=" + tue.checked + "&wed=" + wed.checked + "&thu=" + thu.checked + "&fri=" + fri.checked;
		window.location.href = 'schedule.php?' + extension;
	}
}
