<?php
$sort = $_REQUEST["text"];
$type = $_REQUEST["type"];
$table = array("Стол", "квадратный", "деревянный", "низкий");
$chair = array("Стул", "квадратный", "деревянный", "низкий", "мягкий");
$comp = array("Компьютер", "квадратный", "металлический", "низкий", "твёрдый");
$banana = array("Банан", "мягкий", "желтый", "длинный");
$lamp = array("Лампа", "длинный", "черный");
$pipe = array("Палка", "длинный", "деревянный", "твёрдый");
$all = array($table, $chair, $comp, $banana, $lamp, $pipe);

if($type == 1){
$text = "<table class='staticTable'>";
	for($i = 0; $i < count($all); $i++){
		if($all[$i][0] == $sort){
			for($x = 1; $x < count($all[$i]); $x++){
				$text .= "<tr><td>".$all[$i][$x]."</td></tr>";
			}
			break;
			
		}
	}
	$text.= "</table>";
	echo $text;
}

if($type == 0){
	$text1 = $sort." ";
	$i = 0;
	$text =  preg_replace('/ {2,}/',' ',$text1);
	for($i = 0; $i < strlen($text); $i++){
		if (!preg_match("/([a-zа-я0-9]*)/i", $text[$i])) $text[$i] = " ";
	}
	$i = 0;
	do{
		$pos2 = strpos($text, " ");
		$word = substr($text, 0, $pos2);
		$mass[$i] = $word;
		
		$text = substr($text, $pos2+1);
		$i++;
	} while($pos2 <= strlen($text));
	if(strlen($text) > 1){
		$text = substr($text,0, -1);
		$mass[$i] = $text;
	}
	$a = 0;
	$count = 0;
	for($i = 0; $i < count($all); $i++){ 								//Вещь
		for($x = 1; $x < count($all[$i]); $x++){						//Характеристика вещи
			for($y = 0; $y <= count($mass); $y++){						//Перебор характеристик введённых
				if($all[$i][$x] == $mass[$y]){
					$count++;
					$result[$a] = array($count, $all[$i][0]);
					$char .= $mass[$y].", ";
					break;
				}
			}
		}
		if($count > 0){
			$char = substr($char, 0, -2);
			$result[$a][2] = $char;
			$a++;
			$count = 0;
			$char = "";
		}
	}
	//Сортируем по количеству найденных совпадений
	for($y=0;$y < count($result);$y++){
		for($i=0;$i <= count($result);$i++){
			if($result[$i][0] < $result[$i+1][0]){
				$temp = $result[$i];
				$result[$i] = $result[$i+1];
				$result[$i+1] = $temp;
			}
		}
	}

	$resultText = '<table rules="all" cellpadding="11" border="1" style="width: 600px;" ><tr><td>Совпадений</td><td>Название</td><td>Хар-ки</td></tr>';
	for($i=0;$i<count($result);$i++){
		$mda = '"'.$result[$i][1].'"';
		$resultText .= "<tr><td>".$result[$i][0]."</td><td class='showArr' onclick='findThis(".$mda.",1)'>".$result[$i][1]."</td><td>".$result[$i][2]."</td></tr>";
	}
	$resultText .= "</table>";
	echo $resultText;
	print_r($mass);
}
?>