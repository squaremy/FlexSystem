var tempvar = [];

function updatedata(){
	readJSON2('https://raw.githubusercontent.com/squaremy/FlexSystem/master/data.json');
	document.getElementById("data").innerHTML = tempvar;
}
function loaddata(){
	readJSON2('https://raw.githubusercontent.com/squaremy/FlexSystem/master/data.json');
	document.getElementById("data").innerHTML = tempvar;
}

loaddata();