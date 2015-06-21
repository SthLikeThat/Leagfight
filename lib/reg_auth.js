function checkEmail(){
	emailEmpty = document.getElementById("emailEmpty");
	emailLong = document.getElementById("emailLong");
	if(emailEmpty){
		var email = document.getElementById("email").value;
		if(email.length > 7)
			emailEmpty.style.display = "none";
	}
	if(emailLong){
		var email = document.getElementById("email").value;
		if(email.length < 255)
			emailLong.style.display = "none";
	}
}

function checkLogin(){
	loginShort = document.getElementById("loginShort");
	loginLong = document.getElementById("loginLong");
	if(loginShort){
		var login = document.getElementById("login").value;
		if(login.length > 2)
			loginShort.style.display = "none";
	}
	if(loginLong){
		var login = document.getElementById("login").value;
		if(login.length < 16)
			loginLong.style.display = "none";
	}
}

function checkPass(){
	passwordShort = document.getElementById("passwordShort");
	if(passwordShort){
		var password = document.getElementById("password").value;
		if(password.length > 6)
			passwordShort.style.display = "none";
	}
}

function checkrePass(){
	passwordMage = document.getElementById("passwordMage");
	if(passwordMage){
		var password = document.getElementById("password").value;
		var repassword = document.getElementById("repassword").value;
		console.log(password + " - " + repassword);
		if(password == repassword)
			passwordMage.style.display = "none";
	}
}

function sendreg() {
	var email = document.getElementById("email").value;
	var login = document.getElementById("login").value;
	var password = document.getElementById("password").value;
	var repassword = document.getElementById("repassword").value;
	var errors = [];
	var text = "";
	
	if(email.length < 6)
		errors[errors.length] = {"id":"emailEmpty", "text":"Заполните поле E-mail. "};
	if(email.length > 255)
		errors[errors.length] = {"id":"emailLong","text":"Слишком длинный E-mail. "};
	if(login.length < 2)
		errors[errors.length] = {"id":"loginShort","text":"Длина логина должна быть не менее 3 символов. "};
	if(login.length > 16)
		errors[errors.length] = {"id":"loginLong","text":"Длина логина должна быть не более 16 символов. "};
	if(password.length < 6)
		errors[errors.length] = {"id":"passwordShort","text":"Длина пароля должна быть не менее 7 символов. "};
	if(repassword != password)
		errors[errors.length] = {"id":"passwordMage","text":"Пароли не совпадают. "};
		
	if(errors.length > 0){
		errors.forEach(function(error){
			text += "<div class='errorBlock' id=" + error["id"] +"><img src='css/validation.png' height=15>" + error["text"] + "</div>";
		});
		$("#messErrors").slideDown("fast").html(text);
	}
	else{
		$.ajax({
			type: 'POST',
			url: 'lib/registration.php',
			data: {"email":email, "login":login, "password":password},
			success: function(data) {
				if(data == "OK"){
					document.location.href = "auth.html";
				}
				else {
					$("#messErrors").slideDown("fast").html(data);
				}
			}
		});
	}
} 

function sendauth() {
	var email = document.getElementById("email").value;
	var password = document.getElementById("password").value;
	var errors = [];
	var text = "";
	
	if(email.length < 6)
		errors[errors.length] = {"id":"emailEmpty", "text":"Заполните поле E-mail. "};
	if(email.length > 255)
		errors[errors.length] = {"id":"emailLong","text":"Слишком длинный E-mail. "};
	if(password.length < 6)
		errors[errors.length] = {"id":"passwordShort","text":"Длина пароля должна быть не менее 7 символов. "};
	
	if(errors.length > 0){
		errors.forEach(function(error){
			text += "<div class='errorBlock' id=" + error["id"] +"><img src='css/validation.png' height=15>" + error["text"] + "</div>";
		});
		$("#messErrors").slideDown("fast").html(text);
	}
	else{
		$.ajax({
			type: 'POST',
			url: 'lib/auth.php',
			data: {"WhatIMustDo": "checkAuth", "email": email, "password": password},
			success: function(data) {
				if(data == "OK")
					document.location.href = "index.php";
				else
					$("#messErrors").html(data); 
			}
		});
	}
}

function loginf(){
	$("#mess").html('Длина: 3 - 16 ');
}
function passwordf(){
	$("#mess").html('Длина: 6 - &#8734; <br/> Должен содержать хотя-бы 1 букву и 1 цифру. ');
}


function datef(){
	$("#mess2").html('Необязательно для заполнения');
}
function lossd(){
	m$("#mess2").html('');
}