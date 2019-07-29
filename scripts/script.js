//var teacherlist = document.getElementbyId("teachertable");
//	let newRow = teacherlist.insertRow(-1);
//	newRow.innerHTML = "working";
var teacherlist = [];
var table;

function readJSON(path){
	fetch(path).then(response => response.json()).then(json => {
			let data = json['teachers'];
			displayTeachers(data);
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
readJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/data.json');

function displayTeachers(teachers) {
	//document.getElementById("searchtxt").innerHTML = teachers;
	var table = document.getElementById("teachertable");
	table.style.width="90%";
	table.style.left="5%";
	for(var i = 0; i < teachers.length; i=i+4){
		var row = table.insertRow(-1);
		for(var j = 0; j < 4; j++) {
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
	}
}

function recreateTeachers(teachers) {
	//document.getElementById("searchtxt").innerHTML = teachers;
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

function searchFilter() { //deprecated
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

var filteredteacherlist = [];
function searchFilter1() {
    var input, filter, table, li, a, i, txtValue;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    table = document.getElementById("teachertable");
		var x = 0;
		//console.log(teacherlist);
		//console.log(teacherlist.length);
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
			//console.log(teacherlist);
			//console.log(teacherlist.length);
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
		//console.log(x);
		recreateTeachers(filteredteacherlist);
		//console.log(filteredteacherlist);
}
//searchForTeacher();

function teacherclick(temp){
	var teachername = temp.id;
	window.location.href="signup.php?name=" + teachername;
	//var str = row + " ," + col;
	// alert("clicked cell at: " + this.cellIndex + ", " + this.parentNode.rowIndex);
	//console.log(str);
}
