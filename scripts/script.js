//var teacherlist = document.getElementbyId("teachertable");
//	let newRow = teacherlist.insertRow(-1);
//	newRow.innerHTML = "working";
function readJSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json['teachers'];
			displayTeachers(data);
			if (typeof(data) === 'undefined'){
				console.log("No data");
				return;
			}
			console.log(data);
		  })
}
readJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/data.json');

console.log("Working here");
function searchForTeacher() {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'file:///home/jeremy/Desktop/Sit4e/data.json', true);
	xhr.responseType = 'json';

	xhr.onload = function() {
		console.log("working here lolololololol");
		if(this.status == 200){
			var file = new File([this.response], 'temp');
			var fileReader = new FileReader();
			fileReader.addEventListener('load', function(){

				displayTeachers(fileReader.result);
			});
			fileReader.readAsText(file);
			//displayTeachers(list["teachers"]);
		}


	};
}

function displayTeachers(teachers) {
	//document.getElementById("searchtxt").innerHTML = teachers;
	var table = document.getElementById("teachertable");
	table.style.width="90%";
	table.style.left="5%";
	for(var i = 0; i < teachers.length; i=i+4){
		var row = table.insertRow(-1);
		for(var j = 0; j < 4; j++) {
			var cell = row.insertCell(-1);
			if(teachers[i+j] == null){
				cell.style.width = "20%"
				cell.style.display = "none";
			}else{
				cell.innerHTML = teachers[i+j];
		}
		}
	}
}

//searchForTeacher();
