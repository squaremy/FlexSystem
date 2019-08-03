function onSignIn(googleUser)
{
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId());
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail());

  var myUserEntity = {};
  myUserEntity.Id = profile.getId();
  myUserEntity.Name = profile.getName();

  //Store the entity object in sessionStorage where it will be accessible from all pages of your site.
  sessionStorage.setItem('myUserEntity',JSON.stringify(myUserEntity));
	window.location.href = JSON.parse(sessionStorage.getItem('prevPage'));
  alert(profile.getName());
}

function checkIfLoggedIn(){
//Use userdata to check login state.
/*
var userdata = readJSON('https://raw.githubusercontent.com/squaremy/FlexSystem/master/configs/userdata.json');
console.log("userdata");
console.log(userdata);
if(userdata['user']){
  window.location.href = "signin.html";
}*/
var userid;
  try{
    userid = sessionStorage.getItem('myUserEntity');
    if(userid != null){
      console.log("Signed in");
      var profile = googleUser.getBasicProfile();
      console.log('ID: ' + profile.getId());
      console.log('Name: ' + profile.getName());
      console.log('Image URL: ' + profile.getImageUrl());
      console.log('Email: ' + profile.getEmail());
      var userEntity = {};
      userEntity = JSON.parse(sessionStorage.getItem('myUserEntity'));
    }else{
      console.log("Not signed in");
      if(window.location.href != "signin.html"){
        sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
      }else{
        sessionStorage.setItem('prevPage', JSON.stringify("index.html"));
      }
        //sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
        window.location.href='signin.html';
    }
  }
  catch(err){
    console.log("Not signed in");
    if(window.location.href != "signin.html"){
      sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
    }else{
      sessionStorage.setItem('prevPage', JSON.stringify("index.html"));
    }
      //sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
      window.location.href='signin.html';
  }
  /*
  if(sessionStorage.getItem('myUserEntity') == null){
    console.log("NO USER-- Redirect to SignIn.html");
    //Redirect to login page, no user entity available in sessionStorage
    if(window.location.href != "signin.html"){
      sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
    }else{
      sessionStorage.setItem('prevPage', JSON.stringify("index.html"));
    }
		//sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
    window.location.href='signin.html';
  } else {
    //User already logged in
    var userEntity = {};
    userEntity = JSON.parse(sessionStorage.getItem('myUserEntity'));
  }*/
}
function logout()
{
  //Don't forget to clear sessionStorage when user logs out
  sessionStorage.clear();
}

checkIfLoggedIn();
