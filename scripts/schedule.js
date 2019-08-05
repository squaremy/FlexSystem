
var isteacher = false;

function readaJSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data2 = json['styslingert@franklinacademy.org'];
			displayWeek(data2['schedule']);
			if (typeof(data2) === 'undefined'){
				console.log("No data");
				return;
			}
      else if (data2['type'] == 'teacher') {
				isteacher = true;
        displayStudents(data2['schedule'][0]['students']);
      }

			//console.log(data);
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}
readaJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json');

function displayWeek(schedule) {
  console.log(schedule);
  console.log(schedule[0]['available']);
  console.log(schedule[0]['day']);
  console.log(schedule.length);
  var table = document.getElementById("weektable");
  for(var j = 0; j < 2; j++) {
    var row = table.insertRow(-1);
    for(var i = 0; i < schedule.length; i++) {
      var cell = row.insertCell(-1);
      cell.style.padding = "2px 2px 2px 2px";
      cell.id = schedule[i]['day'];
      if(j == 1) {
        // var checkbox = document.createElement("INPUT");
        // checkbox.type = "checkbox";
        // checkbox.checked = schedule[i]['checked'];
        // checkbox.value = schedule[i]['day'];
        // if(checkbox.value == "Monday") checkbox.name = 'mon';
        // else if(checkbox.value == "Tuesday") checkbox.name = 'tue';
        // else if(checkbox.value == "Wednesday") checkbox.name = 'wed';
        // else if(checkbox.value == "Thursday") checkbox.name = 'thu';
        // else if(checkbox.value == "Friday") checkbox.name = 'fri';
        // cell.appendChild(checkbox);
				if(isteacher) {
					cell.innerHTML = (schedule[i]['available'])? "AVAILABLE":"BLOCKED";
				} else {
					cell.innerHTML = schedule[i]['teacher'];
				}
        // cell.innerHTML = schedule[i]['day'];
      } else {
        cell.innerHTML = schedule[i]['day'];
      }
    }
  }
}

function read2JSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json;
			if (typeof(data) === 'undefined'){
				console.log("No data");
				return;
			}
			console.log(data);
			checktype(data);
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}

function displayStudents(students) {
  var table = document.getElementById("studenttable");
	for(var i = 0; i < students.length; i++){
		var row = table.insertRow(-1);
		var cell = row.insertCell(-1);
		cell.style.padding = "5px 5px 5px 5px";
		cell.id = students[i];
		if(students[i] == null) {
			cell.style.width = "20%";
			cell.style.display = "none";
		} else cell.innerHTML = students[i];
	}
}

var teacherlist = [];
function checktype(teacherelist){ //Check if student or teacher
	//for teachers in teacherelist['teachers']
	teacherlist = teacherelist;
	var teacherlength = teacherelist['teachers'][0];
	var count = Object.keys(teacherlength).length;
	isteacher = false;
	for(var k in teacherlength){ //Gets all teacher emails
		//console.log(k);
		var userEntity = {};
		userEntity = JSON.parse(sessionStorage.getItem('myUserEntity'));
		if(userEntity["Email"] == k){
			console.log(userEntity["Email"] + " is a teacher");
			isteacher = true;
		}
	}
	if(isteacher==false){
		console.log(userEntity["Email"] + " is a student");
	}
	var name = document.getElementById("searchtxt");
	name.innerHTML = userEntity["Name"];
}

// read2JSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/teacherlist.json');
