
function readJSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json['students'];
			displayStudents(data);
			if (typeof(data) === 'undefined'){
				console.log("No data");
				return;
			}
			//console.log(data);
			teacherlist = data;
			//console.log("Heres the variable array");
			//console.log(teacherlist);
		  })
}
readJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/classlist.json');

function displayStudents(students) {
  var table = document.getElementById("studenttable");
	table.style.width="90%";
	table.style.left="5%";
	for(var i = 0; i < students.length; i++){
		var row = table.insertRow(-1);
		var cell = row.insertCell(-1);
		cell.style.padding = "10px 10px 10px 10px";
		cell.id = students[i];
		if(students[i] == null) {
			cell.style.width = "20%";
			cell.style.display = "none";
		} else cell.innerHTML = students[i];
		// for(var j = 0; j < 4; j++) {
		// 	var cell = row.insertCell(-1);
		// 	cell.style.padding = "10px 10px 10px 10px";
		// 	cell.id = students[i+j];
		// 	// cell.onclick = function(){teacherclick(this);};
		// 	if(students[i+j] == null){
		// 		cell.style.width = "20%";
		// 		cell.style.display = "none";
		// 	}else{
		// 		cell.innerHTML = students[i+j];
		// }
		// }
	}
}
