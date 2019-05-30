//var teacherlist = document.getElementbyId("teachertable");
//	let newRow = teacherlist.insertRow(-1);
//	newRow.innerHTML = "working";
function readJSON(path) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', path, true);
    xhr.responseType = 'json';
    xhr.onload = function(e) {
      if (this.status == 200) {
          var file = new File([this.response], 'temp');
          var fileReader = new FileReader();
          fileReader.addEventListener('load', function(){
               //do stuff with fileReader.result
							 console.log(fileReader.result);
          });
          fileReader.readAsText(file);
      }
    }
    xhr.send();
}
readJSON('localhost:///home/jeremy/Desktop/Sit4e/data.json');

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
	var table = document.getElementById("teachertable")
	var row = table.insertRow(-1);
	var cell1 = row.insertCell(0);
	var timevar = time;
	cell1.innerHTML = teachername;
}

//searchForTeacher();
