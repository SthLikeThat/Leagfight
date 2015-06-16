<div class='detail1 photoDetail' data-title='$typeName'><img src='image_char/image/$type.png' alt='$typeName'  height='45'></div> 
 <div class='detail2 photoDetail' data-title='$typedamageName'><img src='image_char/image/$typedamage.png' alt='$typedamageName' height='45'></div>
 <div class='detail2 photoDetail' data-title='Уровень'><img src='image_char/image/lvl.png ' alt='Уровень' height='20'> <br/>".$weapon["requiredlvl"]." </div>
 <div class='detail2 photoDetail'data-title='Урон' ><img src='image_char/image/damage.png ' alt='Урон' height='20'> <br/>".$weapon["damage"]."</div> 
 <div class='detail2 photoDetail' data-Title='Крит'><img src='image_char/image/crit.png ' alt='Крит' height='20' > <br/> ".$weapon["crit"]."</div>";
 
			if($weapon["bonusstr"]) echo "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />".$weapon["bonusstr"]."</div>";
			if($weapon["bonusdef"]) echo "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>".$weapon["bonusdef"]."</div>";
			if($weapon["bonusag"]) echo "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>".$weapon["bonusag"]."</div>";
			if($weapon["bonusph"]) echo "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>".$weapon["bonusph"]."</div>";
			if($weapon["bonusms"]) echo "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>".$weapon["bonusms"]."</div>";
			exit;