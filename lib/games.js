var globalSimon = new Array();
var chance = new Boolean(true);
var canEdit = new Boolean(true);
var canClickSimon = new Boolean(false);

$(document).ready(function(){
	var urlArray = parseUrlQuery();
	//memoryPuzzle
	if(urlArray["view"] == "memoryPuzzle"){
		$('#memoryPuzzle').click(function (event){
			if(document.getElementById("trailOn").checked) 
				var trail = 1;
			else	
				var trail = 0;
			var ready = $(".ready");
			var tr = $("tr");
			if(ready.length == tr.length * tr.length)
				memoryPuzzleWinn(tr.length);
			tag = event.target||event.srcElement;
			if(tag.id != "memoryPuzzle" && tag.id != "trailOn" && canEdit){
				var opened = $(".opened");
				var trails = $(".trail");
				if(trails.length == 2){
					for( var i = 0; i < trails.length; i++)
						$("#"+ trails[i].id).removeClass('trail');
				}
				if(opened.length > 1){
					$("#"+ opened[0].id).removeClass('opened');
					$("#"+ opened[0].id).html(" ");
					$("#"+ opened[1].id).removeClass('opened');
					$("#"+ opened[1].id).html(" ");
				}
				if(!$("#" + tag.id).hasClass("ready")){
					$.ajax({
						type: 'POST',
						url: 'lib/games/gameFunctions.php',
						data: {"WhatIMustDo":"getNumberPuzzle", "tag":tag.id, "id":urlArray["id"]},
						success: function(data) {
							$("#" + tag.id).html(data);
							$("#" + tag.id).addClass("opened");
							var opened = $(".opened");
							if(opened.length == 2){
								$.ajax({
									type: 'POST',
									url: 'lib/games/gameFunctions.php',
									data:  {"WhatIMustDo":"checkNumbersPuzzle", "tag1":opened[1].id, "tag2":opened[0].id, "id":urlArray["id"]},
									success: function(data) {
										if(data == "true"){
											$("#"+ opened[0].id).addClass('ready');
											$("#"+ opened[1].id).addClass('ready');
											$("#"+ opened[0].id).removeClass('opened');
											$("#"+ opened[1].id).removeClass('opened');
											setTimeout(function(){
												$("#"+ opened[0].id).html("");
												$("#"+ opened[1].id).html("");
											}, 500);
										}
										if(data == "false"){
											setTimeout(function(){
												$("#"+ opened[0].id).removeClass('opened');
												$("#"+ opened[1].id).removeClass('opened');
												$("#"+ opened[0].id).html("");
												$("#"+ opened[1].id).html("");
												if(trail == 1){
													$("#"+ opened[0].id).addClass('trail');
													$("#"+ opened[1].id).addClass('trail');
												}
											}, 500);
										}
									}
								});
							}	
						}
					});
				}
			}
		});
	}
	
	//Simon
	if(urlArray["view"] == "Simon"){
		var i = 0;
			$('.SimonButton').click(function (event){
				if(canClickSimon){
					var lvl = Number(document.getElementById("lvlSimon").innerHTML) + 2;
					tag = event.target||event.srcElement;
					clickedSimon[clickedSimon.length] = Number((tag.id).substr(-1));
					console.log(clickedSimon);
					if(clickedSimon[i] != globalSimon[i]){
						$("#lvlSimon").html(1);
						console.log(clickedSimon[i] +" != "+globalSimon[i]);
						console.log("END");
						message("Вы проиграли");
						i = 0;
					}
					i++;
					if(i == lvl){
						if(arraysEqual(clickedSimon,globalSimon)){
							document.getElementById("lvlSimon").innerHTML++;
							console.log("NEXT LVL");
							message("Следующий уровень!");
							i = 0;
						}
						else{
							$("#lvlSimon").html(1);
							console.log("END");
							message("Вы проиграли");
							i = 0;
						}
					}
				}
			});
	}
	if(urlArray["view"] == "Sokoban"){
		
		document.onkeyup = function (e){
			e = e || window.event;
			var user = $(".user");
			var id = user[0].id;
			var position = id.indexOf('_');
			var raw = id.substr(0,position);
			var cell = id.substr(position+1);
			if (e.keyCode === 38) {	//вверх
				var newRaw = Number(raw) - 1;
				if(!$("#" + newRaw + "_" + cell).hasClass("bottom") && !$("#" + raw + "_" + cell).hasClass("top") && raw != "1"){
					$("#" + newRaw + "_" + cell).addClass("user");
					$("#" + raw + "_" + cell).removeClass("user");
					$("#" + raw + "_" + cell).addClass("trailSokoban");
				}
			}
			if (e.keyCode === 40) {	//вниз
				var newRaw = Number(raw) + 1;
				if(!$("#" + newRaw + "_" + cell).hasClass("top") && !$("#" + raw + "_" + cell).hasClass("bottom") && raw != "33"){
					$("#" + newRaw + "_" + cell).addClass("user");
					$("#" + raw + "_" + cell).removeClass("user");
					$("#" + raw + "_" + cell).addClass("trailSokoban");
				}
			}
			if (e.keyCode === 37) {	//влево
				var newCell = Number(cell) - 1;
				if(!$("#" + raw + "_" + newCell).hasClass("right") && !$("#" + raw + "_" + cell).hasClass("left") && cell != "1"){
					$("#" + raw + "_" + newCell).addClass("user");
					$("#" + raw + "_" + cell).removeClass("user");
					$("#" + raw + "_" + cell).addClass("trailSokoban");
				}
			}
			if (e.keyCode === 39) {	//вправо
				var newCell = Number(cell) + 1;
				if(!$("#" + raw + "_" + newCell).hasClass("left") && !$("#" + raw + "_" + cell).hasClass("right") && cell != "33"){
					$("#" + raw + "_" + newCell).addClass("user");
					$("#" + raw + "_" + cell).removeClass("user");
					$("#" + raw + "_" + cell).addClass("trailSokoban");
				}
			}
			user = $(".user");
			exit = $(".exit");
			if(user[0].id == exit[0].id){
				message("Вы победили!");
			}
		}
			
	}
});


function memoryPuzzleWinn(tr){
	tr = Number(tr);
	var min = 1;
	var max = 3;
	var colorType = Math.floor(Math.random() * (max - min + 1)) + min;
	var modificator = Number((255/(tr * tr )).toFixed());
	var count = 0;
	var i = 0;
	var j = 0;
	if(colorType == 1){
		var colorGreen = 0;
		var colorRed = 144;
		var colorBlue = 144;
	}
	if(colorType == 2){
		var colorGreen = 144;
		var colorRed = 0;
		var colorBlue = 144;
	}
	if(colorType == 3){
		var colorGreen = 144;
		var colorRed = 144;
		var colorBlue = 0;
	}
	var interval = setInterval(function(){
		document.getElementById(i + "_" + j).style.backgroundColor = "rgb("+ colorRed +","+ colorGreen +","+ colorBlue +")";
		if(colorType == 1)	colorGreen += modificator;
		if(colorType == 2)	colorRed += modificator;
		if(colorType == 3)	colorBlue += modificator;
		console.log(colorGreen);
		if(colorGreen > 255) colorGreen = 255;
		j++;
		if(j == tr){
			i++;
			j = 0;
		}
		count++;
		if(count == tr * tr){
			clearInterval(interval);
			message("ПОБЕДА!");
		}
	}, 50);
}

function newMemoryPuzzle(){
	var cells = document.getElementById("cellsMemoryPuzzle").value;
	$.ajax({
		type: 'POST',
		url: 'lib/games/gameFunctions.php',
		data: {"WhatIMustDo":"newMemoryPuzzle", "cells":cells},
		success: function(data){
			document.location.href = "?view=memoryPuzzle&id="+data;
		}
	});
}

function newSimon(){
	var buttons = document.getElementById("SimonButtons").value;
	document.location.href = "?view=Simon&buttons=" + buttons;
}

function startSimon(){
	canClickSimon = false;
	var lvl = Number(document.getElementById("lvlSimon").innerHTML) + 2;
	if(globalSimon.length > 0 && lvl > 1){
		if(!arraysEqual(clickedSimon,globalSimon)){
			$("#lvlSimon").html(1);
			console.log("END");
			message("Вы проиграли");
			i = 0;
			lvl = 3;
		}
	}
	globalSimon = new Array();
	clickedSimon = new Array();
	var buttons = $(".SimonButton");

	var min = 1;
	var max = buttons.length;
	var number = Math.floor(Math.random() * (max - min + 1)) + min;
	for( i = 0; i < lvl; i++){
		var lastNumber = number;
		while(number == lastNumber){
			number = Math.floor(Math.random() * (max - min + 1)) + min;
		}
		globalSimon[globalSimon.length] = number;
	}
	i = 0;
	var interval = setInterval(function(){
		$("#simon_" + globalSimon[i]).addClass("hoveredSimon");
		setTimeout(function(){
			$("#simon_" + globalSimon[i]).removeClass("hoveredSimon");
			i++;
			if( i == globalSimon.length){
				clearInterval(interval);
				console.log(globalSimon);
				canClickSimon = true;
			}
		},500);
		
	}, 1000);
}

function giveChanceMemory(id){
	var ready = $(".ready");
	if(chance && ready.length == 0){
		canEdit = false;
		$.ajax({
			type: 'POST',
			url: 'lib/games/gameFunctions.php',
			data: {"WhatIMustDo":"giveChanceMemory", "id":id},
			success: function(data) {
				var result =  JSON.parse(data);
				for( var i = 0; i < result.length; i++){
					cell = JSON.parse(result[i]);
					for( var j = 0; j < cell.length; j++){
						$("#"+ i + "_" + j).html(cell[j]["num"]);
					}
				}
				setTimeout(function(){
				for( var i = 0; i < result.length; i++){
					for( var j = 0; j < result.length; j++){
						$("#"+ i + "_" + j).html(" ");
					}
				}
				canEdit = true;
				},5000);
				chance = false;
				
			 }
		});
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

function newSokoban(){
	document.location.href = "?view=Sokoban";
}