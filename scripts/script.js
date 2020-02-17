
var teacherlist = [];
var table;

function addTeachersToList() {
	table = document.getElementById("teachertablehidden");
	for(var i = 0, row; row = table.rows[i]; i++) {
		for(var j = 0, cell; cell = row.cells[j]; j++) {
			teacherlist[(4*i)+j] = cell.id;
		}
	}
	console.log(teacherlist);
}

// function readteacherJSON(path){
// 	fetch(path).then(response => response.json()).then(json => {
// 			let data = json['teachers'];
// 			displayTeachers(data);
// 			if (typeof(data) === 'undefined'){
// 				return;
// 			}
// 			teacherlist = data;
// 		  })
// }
//
// function readJSON(path){
// 	fetch(path).then(response => response.json()).then(json => {
// 			let data = json['teachers'];
// 			if (typeof(data) === 'undefined'){
// 				return;
// 			}
// 			return data;
// 		  })
// }

// readteacherJSON('./configs/data.json');
//
// function displayTeachers(teachers) {
// 	var table = document.getElementById("teachertable");
// 	for(var i = 0; i < teachers.length; i=i+4){
// 		var row = table.insertRow(-1);
// 		for(var j = 0; j < 4; j++) {
// 			var cell = row.insertCell(-1);
// 			cell.style.padding = "10px 10px 10px 10px";
// 			cell.id = teachers[i+j];
// 			cell.onclick = function(){teacherclick(this);};
// 			if(teachers[i+j] == null){
// 				cell.style.width = "20%";
// 				cell.style.display = "none";
// 			}else{
// 				cell.innerHTML = teachers[i+j];
// 		}
// 		}
// 	}
// }

function recreateTeachers(teachers) {
	table = document.getElementById("teachertable");
	table.innerHTML = "";
	table.style.width="90%";
	table.style.left="5%";
	var rownum = 0;
	var cellnum = "";
	for(var i = 0; i < teachers.length; i=i+4){
		var row = table.insertRow(-1);
		for(var j = 0; j < 4; j++) {
			cellnum = "";
			var cell = row.insertCell(-1);
			cell.style.padding = "10px 10px 10px 10px";
			cell.id = teachers[i+j];
			cell.onclick = function(){teacherclick(this);};
			if(teachers[i+j] == null){
				cell.style.width = "20%";
				cell.style.display = "none";
			}else{
				cell.innerHTML = teachers[i+j];
		}
		}
		rownum++;
	}
}

function searchFilter() { // deprecated
  var input, filter, table, tr, td, i, txtValue, newTeacherlist = [];
  input = document.getElementById("searchbar");
  filter = input.value.toUpperCase();
	table = document.getElementById("teachertable");
	tr = table.getElementsByTagName("tr");
	addTeachersToList();
	recreateTeachers(teacherlist);
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td");
		for (var cell = 0; cell < td.length; cell++) {
			if(td[cell].id.toUpperCase().indexOf(filter) > -1) {
				newTeacherlist.push(td[cell].id);
				recreateTeachers(newTeacherlist);
			}
		}
  }
}

var filteredteacherlist = [];
function searchFilter1() {
		addTeachersToList();
    var input, filter, table, li, a, i, txtValue;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    table = document.getElementById("teachertable");
		var x = 0;
		for(i = 0; i < filteredteacherlist.length; i++){
			filteredteacherlist.splice(i, 1);
		}
    for (i = 0; i < teacherlist.length; i++) {
				if (teacherlist[i].toUpperCase().indexOf(filter) > -1) {
            filteredteacherlist[x] = teacherlist[i];
						x++;
        } else {
            //Not included
        }
    }
		while(x < filteredteacherlist.length){
			var x = 0;
			for(i = 0; i < filteredteacherlist.length; i++){
				filteredteacherlist.splice(i, 1);
			}
			for (i = 0; i < teacherlist.length; i++) {
					if (teacherlist[i].toUpperCase().indexOf(filter) > -1) {
							filteredteacherlist[x] = teacherlist[i];
							x++;
					} else {
							//Not included
					}
			}
		}
		recreateTeachers(filteredteacherlist);
}

function teacherclick(teachername){
	window.location.href="signup.php?name=" + teachername.id + "&user=" + JSON.parse(sessionStorage.getItem("myUserEntity"))['Email'];
}

function randomHexCode() {
	return Math.round(Math.random() * Math.pow(2,32)).toString(16) + Math.round(Math.random() * Math.pow(2,32)).toString(16);
}

// function readJSON2(path){
// 	fetch(path).then(response => response.json()).then(json => {
// 			let data = json['datapoint'];
// 			if (typeof(data) === 'undefined'){
// 				return;
// 			}
// 			tempvar = data;
// 		  })
// }
