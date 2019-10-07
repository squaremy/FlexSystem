function checkIfLoggedIn(){
  if(sessionStorage.getItem('myUserEntity') == null){
    console.log("NO USER-- Redirect to SignIn.html");
    sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
    window.location.href='signin.html';
  } else {
    var userEntity = {};
    userEntity = JSON.parse(sessionStorage.getItem('myUserEntity'));
    console.log(userEntity);
  }
}

function logout()
{
  sessionStorage.clear();

  var auth2 = gapi.auth2.getAuthInstance();
  auth2.signOut().then(function() {
    console.log("User logged out.");
  });

  if(window.location.href != "signin.html"){
    sessionStorage.setItem('prevPage', JSON.stringify(window.location.href));
  }else{
    sessionStorage.setItem('prevPage', JSON.stringify("index.html"));
  }
  window.location.href='signin.html';
}

checkIfLoggedIn();
