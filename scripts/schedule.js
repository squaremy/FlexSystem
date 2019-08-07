
var isteacher = false;

function readaJSON(path, useremail){
	fetch(path).then(response => response.json()).then(json => {
			let data2 = json[useremail];
			var name = document.getElementById("searchtxt");
			name.innerHTML = data2['name'];
			displayWeek(data2);
			if (typeof(data2) === 'undefined'){
				console.log("No data");
				return;
			}
      else if (data2['type'] == 'teacher') {
        displayStudents(data2);
      }

			//console.log(data);
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}

// TEST CODE
readaJSON("https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json", "styslingert@franklinacademy.org");
// TEST CODE

function displayWeek(data) {
  var table = document.getElementById("weektable");
  for(var j = 0; j < 2; j++) {
    var row = table.insertRow(-1);
    for(var i = 0; i < data['schedule'].length; i++) {
      var cell = row.insertCell(-1);
      cell.style.padding = "2px 2px 2px 2px";
      cell.id = data['schedule'][i]['day'];
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
				if(data['type'] == "teacher") {
					isteacher = true;
					cell.innerHTML = (data['schedule'][i]['available'])? "AVAILABLE":"BLOCKED";
				} else {
					cell.innerHTML = data['schedule'][i]['teacher'];
				}
        // cell.innerHTML = schedule[i]['day'];
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
				console.log("No data");
				return;
			}
			console.log(data);
			checktype(data);
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}

function displayStudents(data) {
  var flextable = document.getElementById("flexstudents");
	var visittable = document.getElementById("visitingstudents");
	var students = data['schedule'][0]['flexstudents'];
	for(var i = -1; i < students.length; i++){
		var row = flextable.insertRow(-1);
		for(var j = 0; j < 3; j++) {
			var cell = row.insertCell(-1);
			if(i > -1) {
				flextable.style.display = "table";
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
				flextable.style.display = "block";
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
	flextable.style.display = "block";
	var visit = data['schedule'][0]['visitingstudents'];
	for(var i = -1; i < visit.length; i++) {
		var row = visittable.insertRow(-1);
		for(var j = 0; j < 3; j++) {
			var cell = row.insertCell(-1);
			cell.style.padding = "5px 5px 5px 5px";
			cell.id = visit[i];
			if(i > -1) {
				visittable.style.display = "table";
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
				visittable.style.display = "block";
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
		//displayStudentWeek();
	}
	//if(isteacher==true){
		readaJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/GOAL_CONFIG.json',userEntity["Email"]);
	//}

	var name = document.getElementById("searchtxt");
	name.innerHTML = userEntity["Name"];
}

// read2JSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/teacherlist.json');

function swapAvailability(){

}
