
function readJSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json['schedule'];
			displayWeek(data['week']);
			if (typeof(data) === 'undefined'){
				console.log("No data");
				return;
			}
      else if (data['type'] == 'teacher') {
        displayStudents(data['students']);
      }
			//console.log(data);
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}
readJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/classlist.json');

function displayWeek(schedule) {
  var table = document.getElementById("weektable");
  for(var j = 0; j < 2; j++) {
    for(var i = 0; i < schedule[j].length; i++) {
      var row = table.insertRow(-1);
      var cell = row.insertCell(-1);
      if(j == 1) {
        var checkbox = document.createElement("INPUT");
        checkbox.type = "checkbox";
        checkbox.checked = schedule[j][i];
        cell.appendChild(checkbox);
      } else {
        cell.innerHTML = schedule[j][i];
      }
    }
  }
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
