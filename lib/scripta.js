$(document).ready(function(){
	var urlArray = parseUrlQuery();
	if(urlArray["view"] === undefined)
		id = "menu_user";
	if(urlArray["view"] === "town" && urlArray["type"] === "clan")
		id = "menu_clan";
	else if(urlArray["view"] !== undefined && urlArray["type"] !== "clan")
		id = "menu_" + urlArray["view"];
	$("#" + id).addClass("currentMenuItem");
	
	if(urlArray["view"] === "battleField"){
		 $("[data-info]").mousemove(function (eventObject) {

        $data_info = $(this).attr("data_info");
        
        $("#tooltip").text($data_info)
                     .css({ 
                         "top" : eventObject.pageY + 5,
                        "left" : eventObject.pageX + 5
                     })
                     .show();

    }).mouseout(function () {

        $("#tooltip").hide()
                     .text("")
                     .css({
                         "top" : 0,
                        "left" : 0
                     });
    });
	
	
		var timer = Number(document.getElementById("timer_battle").innerHTML);
		var mda = setInterval(function(){
			timer--;
			if(timer <= 0){
				clearTimeout(mda);
				$('#timer_battle').html("0");
				$.ajax({
						type: 'POST',
						url: 'lib/massBattleFunctions.php',
						data: {"WhatIMustDo": "updateBattle", "id": urlArray["id"]},
						success: function(data) {
							window.location.reload();
						}
					});
			}
			$('#timer_battle').html(timer);
		}, 1000);
		var information = document.querySelector("#information");
		data = information.dataset;
		if(data.instock == "true"){
			var coordinates = $("#battle_information").attr("data-coordinates");
			var course = Number($("#battle_information").attr("data-course"));
			var position = coordinates.indexOf('_');
			var i_coor = Number(coordinates.substring(0, position));
			var j_coor =  Number(coordinates.substring(position + 1));
			
			var min_i = i_coor - course;
			if(min_i < 1) min_i = 1;
			var max_i = i_coor + course;
			if(max_i > 10) max_i = 10;
			
			var min_j = j_coor - course;
			if(min_j < 1) min_j = 1;
			var max_j = j_coor + course;
			if(max_j > 20) max_j = 20;
			
			if(data.league == data.turn){
				if(data.move == "0"){
					for( var i = min_i; i <= max_i; i++){
						for( var j = min_j; j <= max_j; j++){
							if( i + "_" + j != coordinates && $("#" + i + "_" + j).hasClass("field") && document.getElementById(i + "_" + j).innerHTML == "")
								$("#" + i + "_" + j).addClass("canGo");
						}
					}
				}
			}
			$('#battleTaible').click(function (event){
				tag = event.target||event.srcElement;
				if($("#" + tag.id).hasClass("canGo")){
					$.ajax({
						type: 'POST',
						url: 'lib/massBattleFunctions.php',
						data: {"WhatIMustDo": "goCoordinates", "coordinates": tag.id, "rowStand": i_coor , "columnStand": j_coor, "id": urlArray["id"]},
						success: function(data) {
							if(data === "OK")
								window.location.reload();
							else
								$("#mess3").html(data);
						}
					});
				}
				
				if($("#" + tag.id).hasClass("canAttack") && document.getElementById(tag.id).innerHTML != ""){
					$.ajax({
						type: 'POST',
						url: 'lib/massBattleFunctions.php',
						data: {"WhatIMustDo": "attackCoor", "coordinates": tag.id, "rowStand": i_coor , "columnStand": j_coor, "id": urlArray["id"]},
						success: function(data) {
								$("#mess3").html(data);
						}
					});
				}
			});
		}
		if(data.instock == "false")
			setSpawn(data.league);
		if (data.instock == "ready"){
			$('#battleTaible').click(function (event){
				tag = event.target||event.srcElement;
				if(data.league == "grey"){
					var first = 1;
					var second = 20;
				}
				if(data.league == "black"){
					var first = 1;
					var second = 2;
				}
				if(data.league == "white"){
					var first = 19;
					var second = 20;
				}
				var position = tag.id.indexOf('_');
				var i = tag.id.substring(0, position);
				var j = tag.id.substring(position + 1);
				if( $("#" + tag.id).hasClass("field  canSpawn") && j >= first && j <= second && i <= 10 && i >= 1){
					$.ajax({
						type: 'POST',
						url: 'lib/massBattleFunctions.php',
						data: { "WhatIMustDo":"spawnBattle", "row":j , "column":i, "id":urlArray["id"]},
						success: function(data){
							window.location.reload();
						}
					});
				}
			});
		}
	}
});

function isLocalStorageAvailable() {
	//return false;
	try {
		return 'localStorage' in window && window['localStorage'] !== null;
	} 
	catch (e) {
		return false;
	}
}

function arraysEqual(a, b) {
  if (a === b) return true;
  if (a == null || b == null) return false;
  if (a.length != b.length) return false;

  for (var i = 0; i < a.length; ++i) {
    if (a[i] !== b[i]) return false;
  }
  return true;
}

function setSpawn(league){
	var information = document.querySelector("#information");
	information.setAttribute("data-instock", "ready");
	var elements = $(".field");
	console.log(league);
	if(league == "grey"){
		var first = 1;
		var second = 20;
	}
	if(league == "black"){
		var first = 1;
		var second = 2;
	}
	if(league == "white"){
		var first = 19;
		var second = 20;
	}
	for( var i = 1; i <= 10; i++){
		for( var j = first; j <= second; j++){
			if(document.getElementById(i + "_" + j).innerHTML == "")
				$("#" + i + "_" + j).addClass("canSpawn");
		}
	}
}

function parseUrlQuery() {
    var data = {};
    if(location.search) {
        var pair = (location.search.substr(1)).split('&');
        for(var i = 0; i < pair.length; i ++) {
            var param = pair[i].split('=');
            data[param[0]] = param[1];
        }
    }
    return data;
}

function message(text){
		document.getElementById('mess4').style.backgroundColor = 'rgba(0, 0, 0, .5)';
		document.getElementById('mess4').style.border = 'thin solid rgb(200, 200, 200)';
	$('#mess4').html(text);
	setTimeout(function(){
		document.getElementById('mess4').style.backgroundColor = 'rgba(0, 0, 0, 0)';
		document.getElementById('mess4').style.border = '0';
		$('#mess4').html(" ");
	}, 6000);
}

function exituser(){
	$('#exituser').click(function () {
		$.ajax({
			type: 'GET',
			url: 'lib/exit.php',
			success: function(response) { 

			}
		 });
	});
}

function sendreg() {
 var msg   = $('#reg').serialize(); 
    $.ajax({
        type: 'POST',
        url: 'lib/registration.php',
        data: msg,
        success: function(data) {
            if(data == "OK"){
                document.location.href = "auth.html";
            }
            else {
                $("#messErrors").html(data);
            }
        }
    });
} 

function sendauth() {
	  var email = document.getElementById("email").value;
	  var password = document.getElementById("password").value;
       $.ajax({
          type: 'POST',
          url: 'lib/auth.php',
          data: {"WhatIMustDo": "checkAuth", "email": email, "password": password},
          success: function(data) {
			if(data == "OK"){
				document.location.href = "index.php";
			}
			else{
				$("#mess2").html(data); 
			}
          }
        });
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

function buyThing(id){
	$.ajax({
          type: 'POST',
          url: 'lib/inventoryFunctions.php',
          data: {'iden':id , 'WhatIMustDo':'buyThing'},
          success: function(data) {
			if(data.substr(0,2) == "OK"){
				$("#gold").html(data.substr(2)); 
				message("Куплено!");
			}
			else message(data);
             
          }
        });

}

function buyPotion(id){
$.ajax({
          type: 'POST',
          url: 'lib/inventoryFunctions.php',
          data: {'iden':id , 'WhatIMustDo':'buyPotion'},
          success: function(data) {
		  if(data == "!"){
				message("Не хватает денег.");
			}
			if(data == "?"){
				message("Не хватает места в инвентаре.");
			}
			else if(data != "!"){
				$("#gold").html(data); 
			}
		}
        });
}

function showDetails(onOff, block, show){
	if(onOff == 1 && show == 1){
		document.getElementById("delete_" + block).style.display = 'block';
		document.getElementById("putoff_" + block).style.display = 'block';
	}
	if( onOff == 0 && show == 1){
		document.getElementById("delete_" + block).style.display = 'block';
		document.getElementById("puton_" + block).style.display = 'block';	
	}
}

function showDetails2(id, show, hash){
	if(show == 0 || id == 0)
		return false;
	if(isLocalStorageAvailable()){
		if(localStorage[hash]){
			showDetailsFromStorage(hash);
			console.log("Достаём из localStorage");
		}
		else{
			$.ajax({
				type: 'POST',
				url: 'lib/inventoryFunctions.php',
				data: {'iden':id ,'WhatIMustDo':'showDetails', "inStorage" : true},
				success: function(data) {
					localStorage[hash] = data;
					showDetailsFromStorage(hash);
					console.log("Запрос на вещь из инвентаря...");
				}
			});
		}
	}
	else{
		$.ajax({
			type: 'POST',
			url: 'lib/invventoryFunctions.php',
			data: {'iden':id ,'WhatIMustDo':'showDetails', "inStorage" : false},
			success: function(data) {
				$("#mess3").html(data);
				console.log("Придётся напрягать сервер(");
			}
		});
	}
}

function showDetailsFromStorage(hash){
	var armorView = "<div class='detail1 photoDetail' data-title='%typeName%'> <img src='image_char/image/%type%.png' alt='%typeName%'  height='45'> </div> ";
	armorView += "	<div class='detail2 photoDetail' data-title='Уровень'><img src='image_char/image/lvl.png ' alt='Уровень' height='20'> <br/>%requiredlvl% </div>	";
	armorView += "<div class='detail2 photoDetail'data-title='Броня (%armorLvl%)' >	<img src='image_char/image/armor.png ' alt='Урон' height='20'> <br/>%armor% </div> "

	var weaponView = "<div class='detail1 photoDetail' data-title='%typeName%'> <img src='image_char/image/%type%.png' alt='%typeName%'  height='45'> </div> ";
	weaponView += " <div class='detail2 photoDetail' data-title='%typedamageName%'>	<img src='image_char/image/%typedamage%.png' alt='%typedamageName%' height='45'> </div>";
	weaponView += " <div class='detail2 photoDetail' data-title='Уровень'>	<img src='image_char/image/lvl.png ' alt='Уровень' height='20'> <br/>%requiredlvl% </div>";
	weaponView += " <div class='detail2 photoDetail'data-title='Урон (%damageLvl%)' > <img src='image_char/image/damage.png ' alt='Урон' height='20'> <br/>%damage% </div> ";
	weaponView += " <div class='detail2 photoDetail' data-Title='Крит (%critLvl%)'>	 <img src='image_char/image/crit.png ' alt='Крит' height='20' >  <br/>%crit% </div>";
	var information = JSON.parse(localStorage[hash]);
	
	if(Number(information["id"]) > 500)
		view = armorView;
	if(Number(information["id"]) < 500)
		view = weaponView;
	
	if(information["strengh"]) view += "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />" + information["strengh"] + "</div>";
	if(information["defence"]) view += "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>"+ information["defence"] +"</div>";
	if(information["agility"]) view += "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>"+ information["agility"] +"</div>";
	if(information["physique"]) view += "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>" + information["physique"] + "</div>";
	if(information["mastery"]) view += "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>"+ information["mastery"] + "</div>";
	
	for (var key in information) {
		var val = "%" + key + "%";
		view = view.replace(val , information[key]);
	} 
	$("#mess3").html(view);
}

function showDetailsSmith(slot){
	$.ajax({
		type: 'POST',
		url: 'lib/inventoryFunctions.php',
		data: {'slot':slot ,'WhatIMustDo':'showDetailsSmith'},
		success: function(data) {
			$("#informationSmith").html(data);
		}
	});
}

function getMenuSmith(slot){
	$.ajax({
		type: 'POST',
		url: 'lib/inventoryFunctions.php',
		data: {'slot':slot ,'WhatIMustDo':'getMenuSmith'},
		success: function(data) {
			$("#smithBlock").html(data);
		}
	});

}

function showPotions(slot){
	if(slot != '0' && slot != '999'){
		document.getElementById("use_" + slot).style.display = 'block';
		document.getElementById("delete_" + slot).style.display = 'block';
	}
}

function lostFocusPotions(slot){
	document.getElementById("use_" + slot).style.display = 'none';
	document.getElementById("delete_" + slot).style.display = 'none';
}

function lostFocus( block){
	document.getElementById("puton_" + block).style.display = 'none';
	document.getElementById("putoff_" + block).style.display = 'none';
	document.getElementById("delete_"+ block).style.display = 'none';
}

function putOnThisThing(slot){
$.ajax({
			type: 'POST',
			url: 'lib/inventoryFunctions.php',
			data: {'slot':slot, 'WhatIMustDo':'putOnThisThing' },
			success: function(data) {
				if(data == "OK"){
					document.location.href = "index.php";
				}
				if(data != "OK")
					message(data);
			}
        });
}

function deleteThis(slot, type, hash){
$.ajax({
		type: 'POST',
		url: 'lib/inventoryFunctions.php',
		data: {'slot':slot, 'type':type, 'WhatIMustDo':'wantDelete'},
		success: function(data) {
			document.getElementById("alert").style.display = "block";
			document.getElementById("alertWindow").innerHTML = data;
			if(isLocalStorageAvailable && localStorage[hash])
				localStorage.removeItem(hash);
        }
    });
}

function shureDelete(slot, type){
	$.ajax({
		type: 'POST',
		url: 'lib/inventoryFunctions.php',
		data: {'slot':slot, 'type':type, 'WhatIMustDo':'deleteThis'},
		success: function(data) {
			location.reload();
        }
    });
}

function notAlert(){
	document.getElementById("alert").style.display = "none";
	document.getElementById("alertWindow").innerHTML = "";
}

function useIt(name){
$.ajax({
	type: 'POST',
	url: 'lib/inventoryFunctions.php',
	data: {'name':name, 'WhatIMustDo':'useIt'},
		success: function(data) {
			document.location.href = "index.php";
		}
	});
}

function takeOffFocus(id,type){
	if(id != "log" && type != "bracers" && type != "leggings" && type != "helmet" && type != "armor" && type != "primaryWeapon" && type != "secondaryWeapon"){
		document.getElementById(id).style.display = 'block';
	}
}

function takeOffFocusLost(id){
document.getElementById(id).style.display = 'none';
}

function putOffThisThing(slot){
	$.ajax({
		type: 'POST',
		url: 'lib/inventoryFunctions.php',
		data: {'slot':slot , 'WhatIMustDo':'putOffThisThing'},
		success: function(data) {
			document.location.href = "index.php";
		}
	});
}

function attackUser(type, avatar, id){
	if(id == 0){
		message("На кого нападать? Нет никого.");
		return false;
	}
	$.ajax({
        type: 'POST',
        url: 'lib/attackFunctions.php',
        data: {'id':id, 'WhatIMustDo':'attackUser', "avatar":avatar, "type":type },
        success: function(data) {
            $("#mess3").html(data);
			/*if(data == "Location:?view=arena"){
				document.location.href = "?view=arena";
			}
			if(data == "friend"){
				document.location.href = "?view=arena";
				message("Нельзя нападать на своих!");
			}
			else{
				document.location.href = "?view=fightLog&id=" + data;
			}*/
        }
    });
}

function sortThisShit(type){
	var msg   = $('#shop').serialize(); 
	var typeSort = $('input[name="typeSort"]:checked').val();
	var typedamageSort = $('input[name="typedamageSort"]:checked').val();
	var url = window.location.href;
	if(type == 1){
		var position = url.indexOf('&typedamageSort');
		url = url.substring(0, position-1) + typeSort + url.substring(position);
		url = url.substring(0, url.length-1) + typedamageSort;
	}
	if(type == 0){
		url = url.substring(0, url.length-1) + typeSort;
	}
	document.location.href = "" + url;
}

function change(obj) {
	 $.ajax({
          type: 'POST',
          url: 'lib/allFunctions.php',
          data: {'WhatIMustDo':'viewAllShop'},
          success: function(data) {
			document.location.href = "" + data;
          }
        });
}

function changeBorderSearch(){
	var minLvl = document.getElementById('minLvlarena').value;
	var maxLvl = document.getElementById('maxLvlarena').value;
        $.ajax({
          type: 'POST',
          url: 'lib/allFunctions.php',
          data: {'minLvl':minLvl, 'maxLvl':maxLvl, 'WhatIMustDo':'changeBorderSearch'},
          success: function(data) {
			if(data == "OK")document.location.href = "?view=arena";
			else $("#mess3").html(data);
          }
        });
}

function reloadBlock(address,block){
	$("#" + block).load(address + " #" + block);
}

function addSomeStats(value){
	var statsDisplay = document.getElementById('leftavatar');
	var statsAdd = document.getElementById('addSomeStats');
	if(value == 'add'){
		statsAdd.style.display = 'block';
		statsDisplay.style.display = 'none';
	}
	if(value == 'show'){
		statsAdd.style.display = 'none';
		statsDisplay.style.display = 'block';
	}
}

function sendChar(pump,field,max){
	var strengh = document.getElementById('strengh').value;
	var defence = document.getElementById('defence').value;
	var agility = document.getElementById('agility').value;
	var physique = document.getElementById('physique').value;
	var mastery = document.getElementById('mastery').value;
	$.ajax({
          type: 'POST',
          url: 'lib/allFunctions.php',
          data: {'strengh':strengh, 'defence':defence, 'agility':agility, 'physique':physique, 'mastery':mastery, 'pump':pump, 'total':field, 'WhatIMustDo': 'sendChar'},
          success: function(data) {
		   var position = data.indexOf('&');
			$("#pumpPrice").html(data.substr(0,position));
			var newVal = parseInt(data.substr(position + 1));
			if(max == 'max') document.getElementById(field).value = newVal;
			if(pump == 'yes'){
				document.location.href = "index.php";
			}
          }
        });
}

function addInputValue(field,val){
	var currentVal = document.getElementById(field).value;
	var newVal =  parseInt(currentVal) + parseInt(val);
	if(val == 'max') sendChar('no',field,'max');
	if(val != 'max'){
		document.getElementById(field).value = newVal;
		sendChar('no');
	}
}

function delInputValue(field,val){
	var currentVal = document.getElementById(field).value;
	var newVal =  parseInt(currentVal) - parseInt(val);
	if(newVal < 0)	{ newVal = 0;}
	if(val == 'max')	{ newVal = 0;}
	document.getElementById(field).value = newVal;
	sendChar('no');
}

function checkThis(){
	if(window.location.href == "http://zadanie/?view=arena"){
		var min = document.getElementById('timerMin');
		var sec = document.getElementById('timerSec');
		
		if(sec.innerHTML == 0 && min.innerHTML == 0){
			
		}
		else{
			document.getElementById("timer").style.display = 'block';
			document.getElementById("arena").style.display = 'none';
			setTimeout(timery,1000);
		}
	}
}

function checkThisHeader(){
	var min = document.getElementById('timerMinHeader');
	var sec = document.getElementById('timerSecHeader');
	
	if(sec.innerHTML == 0 && min.innerHTML == 0){
		document.getElementById("timerHeader").style.display = 'none';
		document.getElementById("timeToAttack").style.display = 'block';
	}
	else{
		document.getElementById("timerHeader").style.display = 'block';
		document.getElementById("timeToAttack").style.display = 'none';
		setTimeout(timeryHeader,1000);
	}
}

function timeryHeader(){
	var min = document.getElementById('timerMinHeader');
	var sec = document.getElementById('timerSecHeader');
	
	sec.innerHTML--;
	if(sec.innerHTML < 0 ){
		sec.innerHTML = 59;
		min.innerHTML--;
	}
	if(min.innerHTML <= 0 && sec.innerHTML <= 0 ){
		setTimeout(function(){},984);
	}
	else{
		setTimeout(timeryHeader,984);
	}
}

function timery(){
	var min = document.getElementById('timerMin');
	var sec = document.getElementById('timerSec');
	
	sec.innerHTML--;
	
	if(sec.innerHTML < 0 ){
		sec.innerHTML = 59;
		min.innerHTML--;
	}
	if(min.innerHTML <= 0 && sec.innerHTML <= 0 ){
		reloadBlock("/?view=arena","avatar");
		setTimeout(function(){},984);
	}
	else{
		setTimeout(timery,984);
	}
}

function variation(type,id){
	if(type == 1){
		document.getElementById("AnswerThis_" + id).style.display = 'block';
	}
}

function sendMessage(id, user){
	var title = document.getElementById("title_" + id).value; 
	var textMessage = document.getElementById("textMessage_" + id).value; 
	$.ajax({
		type: 'POST',
		url: 'lib/allFunctions.php',
		data: {'title':title, 'textMessage':textMessage, 'WhatIMustDo':'sendMessage', 'idAddressee':id},
		success: function(data) {
			$("#mess3").html(data);
			if(data == 'OK'){
				if(user != 0)
					reloadBlock("?view=mail&type=0","AnswerThis_" + user);
				if(user == 0){
					document.getElementById('message').style.display = "none";
					document.getElementById("title_" + id).value = "";
					document.getElementById("textMessage_" + id).value = "";
				}
			}
		}
	});
}

function loadAllMessages(id){
if(id != 0){
	$.ajax({
          type: 'POST',
          url: 'lib/allFunctions.php',
          data: {'idSender':id, 'WhatIMustDo': 'loadAllMessages'},
          success: function(data) {
			$("#messages").html(data);
			$("#form").html('<form><textarea name="textMessage" id="textMessage" cols="45" rows="2" onkeydown ="sendMessageEnter('+ id +')" ></textarea>	</form>');
			var obj = document.getElementById("moreMessages");
			obj.scrollTop = obj.scrollHeight;		
			document.getElementById('form').style.display = 'block';
			document.getElementById('controlMail').style.display = 'block';
          }
        });
	}
}

function sendMessageEnter(id){
var textMessage   = document.getElementById("textMessage").value; 
	document.onkeyup = function (e){
		e = e || window.event;
		if (e.keyCode === 13 && textMessage != "") {
			document.getElementById("textMessage").value = "";
			$.ajax({
				type: 'POST',
				url: 'lib/allFunctions.php',
				data: {'textMessage':textMessage, 'WhatIMustDo':'sendMessageEnter', 'idSender':id},
				success: function(data) {
					loadAllMessages(id);
					var date = new Date();
					var time = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
					$($("#message" + id).find(".textMessage")).html(textMessage);
					$($("#message" + id).find(".time")).html(time);
				}
			});
		}
		else return false;
	}
}

function searchThis(type,sort){
	if(type == 'value'){
		document.onkeyup = function (e){
			e = e || window.event;
			if (e.keyCode === 13) {
				var searchInput   = document.getElementById("searchInput").value; 
					document.location.href = "?view=search&value=" + searchInput +"&sort=login&desc=down&clanSort=name&clanDesc=down";
			}		
		}
	}
	if(type == 'sortUser'){
		var url = window.location.href;
		var positionClan = url.indexOf('&clanSort');
		var clanUrl = url.substr(positionClan);
		var position = url.indexOf('&sort');
		var newUrl = url.substr(0,position);
		newUrl += sort;
		if(url == newUrl + clanUrl){
			position = url.indexOf('&desc');
			newUrl = url.substr(0, position + 6);
			newUrl += 'up';
		}
		newUrl += clanUrl;
		document.location.href = newUrl;
	}
	if(type == 'sortClan'){
		var url = window.location.href;
		var positionClan = url.indexOf('&clanSort');
		var userUrl = url.substr(0,positionClan);
		var position = url.indexOf('&clanSort');
		newUrl = sort;
		if(url == userUrl + newUrl){
			position = newUrl.indexOf('&clanDesc');
			newUrl = newUrl.substr(0, position + 10);
			newUrl += 'up';
		}
		newUrl = userUrl + newUrl;
		document.location.href = newUrl;
	}
}

function changeSettings(){
	var messAttacker   = document.getElementById("messAttacker").value; 
	var description   = document.getElementById("description").value; 
			$.ajax({
				  type: 'POST',
				  url: 'lib/allFunctions.php',
				  data: {"WhatIMustDo":"changeSettings", "messAttacker":messAttacker, "description":description},
				  success: function(data) {
					document.location.href = "?view=options";
				  }
				});
	}

function upHouse(name){
	$.ajax({
		type: 'POST',
		url: 'lib/allFunctions.php',
		data: {"WhatIMustDo":"upHouse", "name":name},
		success: function(data) {
			if(data == "house")
				document.location.reload();
			if(data == "workShop")
				clanMenu("workshop");
			if(data != "house" && data != "workShop")
				message(data);
		 }
		 
	});
}

function goSleep(value){
	var sleep = document.getElementById("range").value;
	if(value == 0){
		document.getElementById("sleepHours").style.display = "block";
		document.getElementById("sleepGo").style.display = "block";
		$("#sleepHours").html(sleep + "ч");
		var rangeSleep = document.getElementById("rangeSleep");
		if(rangeSleep.style.display = "none")
			rangeSleep.style.display = "block";
		else
			rangeSleep.style.display = "none";
	}
	if(value == 1){
		$.ajax({
			type: 'POST',
			url: 'lib/allFunctions.php',
			data: {"WhatIMustDo":"goSleep", "values":sleep},
			success: function(data) {
				document.location.href = "index.php";
			 }
			 
		});
	}
}

function showHours(){
	var sleep = document.getElementById("range").value;
	$("#sleepHours").html(sleep + "ч");
}

function jobTime(){
	var timerJob = document.getElementById("mda");
	if(timerJob.innerHTML > 0){
		document.getElementById("timeJob").style.display = "none";
		setInterval(function(){
			timerJob.innerHTML--;
			var time = timerJob.innerHTML;
			var hours = time/3600;
			hours = hours.toFixed( 5 ).slice( 0, -6 );
			var minutes = (time - hours * 3600)/60;
			minutes = minutes.toFixed( 5 ).slice( 0, -6 );
			var seconds = time - hours * 3600 - minutes * 60;
			var resultTime = hours +":" + minutes + ":" + seconds;
			if(time <= 0){
				document.getElementById("timeJob").style.display = "block";
				document.getElementById("jobHourHeader").style.display = "none";
				document.location.href = "index.php";
			}
			$("#jobHourHeader").html(resultTime);
		},1000);
	}
}

function createClan(){
	var name = document.getElementById("name").value;
	var tag = document.getElementById("tag").value;
	$.ajax({
			type: 'POST',
			url: 'lib/allFunctions.php',
			data: {"WhatIMustDo":"createClan", "name":name, "tag":tag},
			success: function(data) {
				if(data == "Ok")
					document.location.href="?view=town&type=clan";
				else
					message(data);
			 }
			 
		});
}

function clanMenu(type){
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"clanMenu", "type":type},
			success: function(data) {
					$("#itemClan").html(data);
			 }
		});
}

function depositTreasury(){
	var gold = document.getElementById("goldTreasury").value;
	var another = document.getElementById("anotherTreasury").value;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"depositTreasury", "gold":gold, "another":another},
			success: function(data) {
				var position = data.indexOf('&');
				$("#gold").html(data.substr(0, position));
				$("#another").html(data.substr(position + 1));
				clanMenu("treasury");
			 }
		});
}

function newTitle(){
	var name = document.getElementById("nameTitle").value;
	if(document.getElementById("treasuryCheck").checked) var treasury = 1;
	else	var treasury = 0;
	if(document.getElementById("platzCheck").checked) var platz = 1;
	else	var platz = 0;
	if(document.getElementById("workshopCheck").checked)var workshop = 1;
	else	var workshop = 0;
	if(document.getElementById("diplomacyCheck").checked)  var diplomacy = 1;
	else	var diplomacy = 0;
	if(document.getElementById("academyCheck").checked)  var academy = 1;
	else	var academy = 0;
	if(document.getElementById("hikesCheck").checked)  var hike = 1;
	else	var hike = 0;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"newTitle","name":name, "treasury":treasury, "platz":platz, "workshop":workshop, "diplomacy":diplomacy, "academy":academy, "hike":hike},
			success: function(data) {
				if(data != ""){
					clanMenu('settings');
					$("#mess3").html(data);
				}
				else clanMenu('settings');
			 }
		});
}

function enterClan(id){
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"enterClan", "id":id},
			success: function(data) {
				if(data != "") message(data);
			 }
		});
}

function decisionPlatz(id, decision){
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"decisionPlatz", "decision":decision, "id":id},
			success: function(data) {
				clanMenu("platz");
				if(data != "") message(data);
			 }
		});
}

function newRate(){
	var minLvl = document.getElementById("minLvl").value;
	var maxLvl = document.getElementById("maxLvl").value;
	var goldRate = document.getElementById("goldRate").value;
	var anotherRate = document.getElementById("anotherRate").value;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"newRate", "minLvl":minLvl, "maxLvl":maxLvl, "goldRate":goldRate, "anotherRate":anotherRate},
			success: function(data) {
				clanMenu("rate");
				if(data != "") message(data);
			 }
		});
}

function loadForEdit(id){
	var mass = new Array();
	var a = 0;
	var elems = $('li');
	for( var i = 0; i < elems.length; i++){
		if(elems[i].className == 'canEdit'){
			mass[a] = elems[i];
			a++;
		}
	}
	for(i = 0; i < mass.length; i++){
		mass[i].style.backgroundColor = "rgba(0, 0, 0, 0)";
	}
	var selectedLi = document.getElementById(id);
	selectedLi.style.backgroundColor = 'rgb(174,242,174)';
	document.getElementById("goldRate").value = document.getElementById(id + "_gold").innerHTML;
	document.getElementById("anotherRate").value = document.getElementById(id + "_another").innerHTML;
	var lvl = document.getElementById(id + "_lvlRate").innerHTML;
	var position = lvl.indexOf('-');
	document.getElementById("minLvl").value = lvl.substr(0,position - 1);
	document.getElementById("maxLvl").value = lvl.substr(position + 2);
}

function editRate(){
	var minLvl = document.getElementById("minLvl").value;
	var maxLvl = document.getElementById("maxLvl").value;
	var goldRate = document.getElementById("goldRate").value;
	var anotherRate = document.getElementById("anotherRate").value;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"editRate", "minLvl":minLvl, "maxLvl":maxLvl, "goldRate":goldRate, "anotherRate":anotherRate},
			success: function(data) {
				clanMenu("rate");
				if(data != "") message(data);
			 }
		});
}

function delRate(){
	var minLvl = document.getElementById("minLvl").value;
	var maxLvl = document.getElementById("maxLvl").value;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"delRate", "minLvl":minLvl, "maxLvl":maxLvl},
			success: function(data) {
				clanMenu("rate");
				if(data != "") message(data);
			 }
		});
}

function editTitle(id){
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"editTitle", "id":id},
			success: function(data) {
				var position = data.indexOf('|');
				$("#title_" + id).html(data.substr(0,position));
				$("#control_" + id).html(data.substr(position + 1));
			 }
		});
}

function saveNewTitle(id){
	var title = document.getElementById("newTitle_" + id).value;
	$.ajax({
			type: 'POST',
			url: 'lib/clanFunctions.php',
			data: {"WhatIMustDo":"saveNewTitle", "id":id, "title":title},
			success: function(data) {
				clanMenu("users");
				if(data != "") message(data);
			 }
		});
}

function changeBorderSearchBot(){
	$.ajax({
			type: 'POST',
			url: 'lib/allFunctions.php',
			data: {"WhatIMustDo":"changeBorderSearchBot"},
			success: function(data) {
			if(data == "OK") document.location.href = "?view=arena";
			else	message(data);
			 }
		});
}

function saveTitles(){
	var description = document.getElementById("description").value;
	var minLvl = document.getElementById("minLvl").value;
	var titles = $(".changedTitle");
	var mas = new Array();
	for(var i = 0; i < titles.length; i++){
		mas[i] = ($(titles[i]).attr('id'));
	}
	$.ajax({
		type: 'POST',
		url: 'lib/clanFunctions.php',
		data: {"WhatIMustDo":"saveTitles", "description":description, "minLvl":minLvl, "titles": mas},
		success: function(data) {
			clanMenu('settings');
			if(data != "") message(data);
		 }
	});
}

function titleChanged(y, x){
	document.getElementById(y).style.display = "block";
	document.getElementById("modeTitles").innerHTML = "Edit";
	var titles = $(".deleteTitle");
	for(var i = 2; i < titles.length; i++)
		titles[i].style.display = "none";
	if ( $("#" + y + "_" + x).hasClass("changedTitle") )
		$("#" + y + "_" + x).removeClass('changedTitle');
	else
		$("#" + y + "_" + x).addClass('changedTitle');
}

function writeMessage(){
	if(document.getElementById("message").style.display == "none")
		document.getElementById("message").style.display = "block";
	else
		document.getElementById("message").style.display = "none";
}

function rollBackTitle(i){
	var all = $(".changedTitle");
	for(var x = 1; x <= 6; x++){
		if ( $("#" + i + "_" + x).hasClass("changedTitle") )
			$("#" + i + "_" + x).click();
	}
	document.getElementById(i).style.display = "none";
}

function changeModeTitles(){
	var mda = document.getElementById("modeTitles").innerHTML;
	var titles = $(".editingTitle");
		for(var i = 2; i < titles.length; i++)
			titles[i].style.display = "none";
	if(mda == "Edit"){
		document.getElementById("modeTitles").innerHTML = "Delt";
		var titles = $(".deleteTitle");
		for(var i = 2; i < titles.length; i++)
			titles[i].style.display = "block";
	}
	if(mda == "Delt"){
		document.getElementById("modeTitles").innerHTML = "Edit";
		var titles = $(".deleteTitle");
		for(var i = 2; i < titles.length; i++)
			titles[i].style.display = "none";
	}
}

function deleteTitle(i){
	$.ajax({
		type: 'POST',
		url: 'lib/clanFunctions.php',
		data: {"WhatIMustDo":"deleteTitle", "id":i},
		success: function(data) {
			clanMenu('settings');
			if(data != "") message(data);
		 }
	});
}

function upLvlArmor(type, value, price, currentLvl){
	if(type == "up")
		var armorLvl = Number(document.getElementById("armorLvl").innerHTML) + 1;
	if(type == "del")
		var armorLvl = Number(document.getElementById("armorLvl").innerHTML) - 1;
	if(armorLvl > currentLvl)
		$("#armorLvl").addClass('changed');
	if(armorLvl == currentLvl)
		$("#armorLvl").removeClass('changed');
	
	var values = new Array();
	var prices = new Array();
	values[0] = Number(value);
	prices[0] = Number(price) / 10;
	for( var i = 1; i <= 5; i++){
		var modificator = 1;
		for(var x = 1; x <= i; x++)
			modificator += 0.05;
		values[i] = (values[0] * modificator).toFixed(2);
		prices[i] = (prices[i-1] * 1.9).toFixed(0);
	}
	if(armorLvl <= 5 && armorLvl >= currentLvl){
		document.getElementById("armorLvl").innerHTML = armorLvl;
		document.getElementById("armorDefence").innerHTML = values[armorLvl];
		var resultPrice = 0;
		for(i = currentLvl; i <= armorLvl; i++)
			resultPrice += Number(prices[i]);
		if(armorLvl > currentLvl)
			document.getElementById("price").innerHTML = resultPrice.toFixed(0);
		else 
			document.getElementById("price").innerHTML = 0;
	}
}

function upCharsWeapon(upDown, type, price, baseDmg, baseLvlDmg, baseCrit, baseLvlCrit){
	var valuesDamage = new Array();
	var valuesCrit = new Array();
	var pricesCrit = new Array();
	var pricesDamage = new Array();
	valuesDamage[0] = Number(baseDmg);
	valuesCrit[0] = Number(baseCrit);
	pricesDamage[0] = (Number(price) / 10).toFixed();
	pricesCrit[0] = (Number(price) / 10).toFixed();
	for( var i = 1; i <= 5; i++){
		var modificator = 1;
		for(var x = 1; x <= i; x++)
			modificator += 0.05;
		valuesDamage[i] = (valuesDamage[0] * modificator).toFixed(2);
		valuesCrit[i] = (valuesCrit[0] * modificator).toFixed(2);
		pricesCrit[i] = (pricesCrit[i-1] * 1.5).toFixed();
		pricesDamage[i] = (pricesDamage[i-1] * 1.75).toFixed();
	}
	
	if(type == "damage"){
		if(upDown == "up"){
			var damageLvl = Number(document.getElementById("damageLvl").innerHTML) + 1;
			if(damageLvl > 5) damageLvl = 5;
		}
		if(upDown == "down")
			var damageLvl = Number(document.getElementById("damageLvl").innerHTML) - 1;
		if(damageLvl > baseLvlDmg)
			$("#damageLvl").addClass('changed');
		if(damageLvl == baseLvlDmg)
			$("#damageLvl").removeClass('changed');
		var critLvl = Number(document.getElementById("critLvl").innerHTML);
	
		if(damageLvl >= baseLvlDmg && damageLvl <= 5){
			document.getElementById("damageLvl").innerHTML = damageLvl;
			document.getElementById("weaponDamage").innerHTML = valuesDamage[damageLvl];
		}
	}
	
	if(type == "crit"){
		if(upDown == "up"){
			var critLvl = Number(document.getElementById("critLvl").innerHTML) + 1;
			if(critLvl > 5) critLvl = 5;
		}
		if(upDown == "down")
			var critLvl = Number(document.getElementById("critLvl").innerHTML) - 1;
		if(critLvl > baseLvlCrit)
			$("#critLvl").addClass('changed');
		if(critLvl == baseLvlCrit)
			$("#critLvl").removeClass('changed');
		var damageLvl = Number(document.getElementById("damageLvl").innerHTML);
		
		if(critLvl >= baseLvlCrit && critLvl <= 5){
			document.getElementById("critLvl").innerHTML = critLvl;
			document.getElementById("weaponCrit").innerHTML = valuesCrit[critLvl];
		}
	}
	
	var totalPrice = 0;
	if($("#damageLvl").hasClass('changed') && damageLvl != baseLvlDmg && damageLvl <= 5){
		for(i = baseLvlDmg; i <= damageLvl; i++)
			totalPrice += Number(pricesDamage[i]);
	}
	if($("#critLvl").hasClass('changed') && critLvl != baseLvlCrit && critLvl <= 5){
		for(i = baseLvlCrit; i <= critLvl; i++)
			totalPrice += Number(pricesCrit[i]);
	}
	document.getElementById("price").innerHTML = totalPrice;
	if(critLvl == baseLvlCrit && damageLvl == baseLvlDmg)
		document.getElementById("price").innerHTML = 0;
}

function setChanges_localStorage(hash, changes){
	if(isLocalStorageAvailable && localStorage[hash]){
		var item = JSON.parse(localStorage[hash]);
		for (var key in changes) {
			item[key] = changes[key];
		}
		localStorage[hash] = JSON.stringify(item);
	}
}

function upWeapon(slot){
	if($("#damageLvl").hasClass('changed') || $("#critLvl").hasClass('changed')){
		var damageLvl = Number(document.getElementById("damageLvl").innerHTML);
		var critLvl = Number(document.getElementById("critLvl").innerHTML);
		if(damageLvl >= 0 && damageLvl <= 5 && critLvl >= 0 && critLvl <=5){
			$.ajax({
				type: 'POST',
				url: 'lib/inventoryFunctions.php',
				data: {"WhatIMustDo":"upSmith", "type":"weapon", "slot":slot, "damageLvl":damageLvl, "critLvl":critLvl},
				success: function(data){
					$("#mess3").html(data);
					if(data.substr(0, 2) == "OK"){
						if(isLocalStorageAvailable){
							var position = data.indexOf("}");
							var changes =  JSON.parse(data.substr(2, data.length - 10));
							var hash = data.substr(position + 1);
							setChanges_localStorage(hash, changes);
						}
						location.reload();
					}
					else
						message(data);
				 }
			});
		}
	}
}

function upArmor(slot){
	if($("#armorLvl").hasClass('changed')){
		var armorLvl = Number(document.getElementById("armorLvl").innerHTML);
		if(armorLvl > 0 && armorLvl <= 5){
			$.ajax({
				type: 'POST',
				url: 'lib/inventoryFunctions.php',
				data: {"WhatIMustDo":"upSmith", "type":"armor", "slot":slot, "armorLvl":armorLvl},
				success: function(data){
				console.log(data);
					if(data.substr(0, 2) == "OK"){
						if(isLocalStorageAvailable){
							var position = data.indexOf("}");
							var changes =  JSON.parse(data.substr(2, data.length - 10));
							console.log(changes);
							var hash = data.substr(position + 1);
							setChanges_localStorage(hash, changes);
						}
						location.reload();	
					}
					else
						message(data);
				 }
			});
		}
		else location.reload();
	}
}

function showDetailsLog(thing, id){
		$.ajax({
			type: 'POST',
			url: 'lib/functions_without_user.php',
			data: {"WhatIMustDo":"showDetailsLog", "thing":thing, "id":id},
			success: function(data){
				if(data != "")
					$("#mess3").html(data);
			 }
		});
}

function wantDeleteClanMember(id){
	$.ajax({
		type: 'POST',
		url: 'lib/clanFunctions.php',
		data: {"WhatIMustDo":"wantDeleteClanMember", "id":id},
		success: function(data){
			document.getElementById("alert").style.display = "block";
			document.getElementById("alertWindow").innerHTML = data;
		 }
	});
}

function deleteClanMember(id){
	document.getElementById("alert").style.display = "none";
	document.getElementById("alertWindow").innerHTML = "";
	$.ajax({
		type: 'POST',
		url: 'lib/clanFunctions.php',
		data: {"WhatIMustDo":"deleteClanMember", "id":id},
		success: function(data){
			clanMenu("users");
			if(data != "")
				message(data);
		 }
	});
}

function goWork(type){
	$.ajax({
		type: 'POST',
		url: 'lib/allFunctions.php',
		data: {"WhatIMustDo":"wantToWork", "type":type},
		success: function(data){
			if(data == "!")
				message("Вы сейчас заняты делом!");
			else
				$("#ul_town").html(data);
		}
	});
}

function chooseRiverBlock(id){
	var choosed = $(".choosedRiverBlock");
	var count = Number(document.getElementById("fishnetCount").innerHTML);
	if($("#" + id).hasClass("choosedRiverBlock")){
		$("#" + id).removeClass("choosedRiverBlock");
		$("#fishnetCount").html(count + 1);
	}
	else if(choosed.length < 2 && count > 0 ){
		$("#" + id).addClass("choosedRiverBlock");
		$("#fishnetCount").html(count - 1);
	}
}

function readyNetworks(){
	var choosed = $(".choosedRiverBlock");
	if(choosed.length > 0){
		$.ajax({
			type: 'POST',
			url: 'lib/allFunctions.php',
			data: {"WhatIMustDo":"goRiver", "nets": choosed.length},
			success: function(data){
				window.location.reload();
			}
		});
	}
}

function getPriceNetworks(){
	var count = document.getElementById("netsToBuy").value;
	$("#priceRiver").html(count * 200);
}

function buyNetwork(){
	var count = Number(document.getElementById("netsToBuy").value);
	$.ajax({
		type: 'POST',
		url: 'lib/allFunctions.php',
		data: {"WhatIMustDo":"buyFishnet", "nets": count},
		success: function(data){
			if(data != "")
				message(data);
			else{
				$("#fishnetCount").html(Number(document.getElementById("fishnetCount").innerHTML) + count);
				document.getElementById("netsToBuy").value = 0;
				var currentGold = Number(document.getElementById("gold").innerHTML);
				document.getElementById("gold").innerHTML = currentGold - count * 200;
			}
		}
	});
}

function workIt(id){
	var number = document.getElementById("work_range_" + id).value;
	$.ajax({
		type: 'POST',
		url: 'lib/allFunctions.php',
		data: {"WhatIMustDo":"workIt", "id": id, "number":number},
		success: function(data){
			
		}
	});
}

function newBattle(){
	$.ajax({
		type: 'POST',
		url: 'lib/massBattleFunctions.php',
		data: { "WhatIMustDo":"newBattle" },
		success: function(data){
			//$("#mess3").html(data);
			document.location.href = "?view=battleField&id=" + data;
		}
	});
}

function chooseThisBattle(id){
	var battles = $(".trHover");
	for(var i = 0; i < battles.length; i++){
		if($("#" + battles[i].id).hasClass("choosedBattle"))
			$("#" + battles[i].id).removeClass("choosedBattle");
	}
	$("#battle_" + id).addClass("choosedBattle");
}

function IchoseBattle(){
	var battle = $(".choosedBattle");
	id = battle[0].id.substring(7);
	$.ajax({
		type: 'POST',
		url: 'lib/massBattleFunctions.php',
		data: { "WhatIMustDo":"connectBattle", "id":id },
		success: function(data){
			document.location.href = "?view=battleField&id=" + data;
		}
	});
}

function chooseSkill(i){
	var skills = $(".battle_skill");
	for(var j = 1; j < skills.length; j++){
		if($("#" + skills[j].id).hasClass("choosedSkill"))
			$("#" + skills[j].id).removeClass("choosedSkill");
	}
	$("#skill_" + i).addClass("choosedSkill");
}

function showInfoSkill(i){ 
	var data = $("#skill_" + i).attr("data-text");
	$("#skillInfo").html(data);
}

function showInformationBattle(eventObject){
	data = eventObject.dataset;  
	var coor = $("#" + eventObject.id).position();	
	$("#tooltip").html(data.info)
                     .css({ 
                         "top" : coor.top + 5,
                        "left" : coor.left + 5
                     })
                     .show();
}

function hideInformation(eventObject){
	/* $("#tooltip").hide()
		 .text("")
		 .css({
			 "top" : 0,
			"left" : 0
		 });*/
}

function battleEvent(type){
	
	if(type == "attack"){
		
		var information = document.querySelector("#information");
		var battle_information = document.querySelector("#battle_information");
		data = information.dataset;
		data_information = battle_information.dataset;
		
		var coordinates = data_information.coordinates;
		var attackRange = Number(data_information.attackrange);
		var position = coordinates.indexOf('_');
		var i_coor = Number(coordinates.substring(0, position));
		var j_coor =  Number(coordinates.substring(position + 1));
		
		console.log(attackRange);
		var min_i = i_coor - attackRange;
		if(min_i < 1) min_i = 1;
		var max_i = i_coor + attackRange;
		if(max_i > 10) max_i = 10;
		
		var min_j = j_coor - attackRange;
		if(min_j < 1) min_j = 1;
		var max_j = j_coor + attackRange;
		if(max_j > 20) max_j = 20;
		
		//if(data.league == data.turn){
			//if(data.attack == "0"){
				for( var i = min_i; i <= max_i; i++){
					for( var j = min_j; j <= max_j; j++){
						if( i + "_" + j != coordinates && $("#" + i + "_" + j).hasClass("field"))
							$("#" + i + "_" + j).addClass("canAttack");
					}
				}
			//}
		//}
	}
}