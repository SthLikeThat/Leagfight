<div class="topItem">
	<div class="lvlAndExp headerInformation" data-title="%currentExp%/%needExp%"><div class="lvl"><img src="image_char/image/lvl.png" height="17">%lvl%</div><div id="percentExp" style="width:%exp%%;" > </div> </div>
	<div class="resource"><img src="images/coinBlack.png" height="25" class="imgResource"/><div id="gold">%gold%</div></div>
</div>

<div class="topItem">
	<div class="lvlAndExp headerInformation" data-title="%currentHp%/%maxHp%"><div class="lvl"><img src="image_char/image/hp.png" height="17"></div> <div id="percentHp" style="width:%hp%%;"> </div> </div>
	<div class="resource"><img src="images/diamond.png" height="25" class="imgResource"/><div id="another">%another%</div></div>
</div>

<div class="topItem">
	<div class="lvlAndExp"> <div id="sleep" onclick="goSleep(0)"> <img src="images/sleep.png" height="17" /> </div> 
	<div id="rangeSleep"> <input type="range" min="1" max="12" id="range" name="range" value="6" oninput="showHours()" > </div> </div>
	<div id="sleepHours">   </div>
	<div id="sleepGo" onclick="goSleep(1)"> <img src="images/go.png" height="17" /> </div>
	<div class="resource"><img src="images/euro.png" height="25" class="imgResource"/><div id="donat">%donat%</div></div>
</div>


<div class="topItem">
		<div class="minor">
		<div class="minorResource"><img src="images/tournament_icon.png" height="18"/>%tournament_icon%</div>
		<div class="minorResource">Ресурс</div>
		<div class="minorResource">Ресурс</div>
	</div>
</div>

<div class="topItem">
	<div class="minor">
		<div class="minorResource">Ресурс</div>
		<div class="minorResource">Ресурс</div>
		<div class="minorResource">Ресурс</div>
	</div>
</div>

<div class="topItem">
	<div class="counter">
		<div class="counter1">	<div id="timeToAttack"> Время до нападения </div>
		<div id="timerHeader" class="timerHeader"> 
			<div id="timerMinHeader" class="timerAllHeader" >%timerMin%</div><div class="timerAllHeader">:</div>
			<div id="timerSecHeader" class="timerAllHeader">%timerSec%</div>	
		</div> 
	</div>
	
	<div class="counter1">	<div id="timeDef"> Время защиты </div> </div>
	
	<div class="counter1">	<div id="timeJob"> Время работы </div>
		<div id="jobHeader" class="widthTimerHeader"> 
			<div id="jobHourHeader" class="timerAllHeader" ></div>
			<div id="mda">%mda%</div>
		</div> 
	</div>
</div>