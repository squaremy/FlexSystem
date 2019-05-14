//var teacherlist = document.getElementbyId("teachertable");
//	let newRow = teacherlist.insertRow(-1);
//	newRow.innerHTML = "working";


function searchForTeacher() {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'https://github.com/squaremy/FlexSystem/tree/master/configs/teachers.json');
	xhr.responseType = 'json';
	xhr.send();
	xhr.onload = function() {
		var list = xhr.response;
		displayTeachers(list["teachers"]);
	}
}

function displayTeachers(teachers) {
	document.getElementById("searchtxt").innerHTML = teachers;
}

searchForTeacher();
