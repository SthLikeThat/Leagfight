	<div class="avatar" >
		<div id="avatar">
			<form name="arena" id="arena">
				<table>
					<tr><td><input type="text" name="minLvl" id="minLvlarena" value="%minLvl%"/></td> <td><input type="text" name="maxLvl" id="maxLvlarena" value="%maxLvl%" /></td></tr>
					<tr><td><div class="attackUser"><a href="#" onclick="changeBorderSearch()"><img src="images/next.png" height="20" /> </a></div></td> <td><div class="attackUser"><a href="#" onclick="attackUser('arenaUser', '%avatar%', %id%)"> <img src="images/attack.png" height="20" /> </a></div> </td></tr>
				</table>
			</form>
			<div id="timer"> <div id="timerMin" >%timerMin%</div>:<div id="timerSec" >%timerSec%</div> </div>
		
		</div>
		%nick% [ %lvl% ]<br />
		<div class="arenaDown">
			<img src="images/avatars/%avatar%.png" class="avatarArena"/>
			<div class="leftavatar">
				%characteristics%
			</div>
		</div>
	</div>
	
	<div class="avatar" style='margin-left:1px; width:25%;padding:3px;' >
		<div class = "textBot">
			%messAttacker%
		</div>
			%userStatistic%
	</div>
	
	<div class="avatar" style='margin-left:1px;' >
		<div id="avatar">
			<form name="arenaBot" id="arenaBot">
				<table>
					<tr><td><input type="text" disabled id="minLvlarena" value="%currentLvlBot%"/></td><td><div class="nextBot"><a href="#" onclick="changeBorderSearchBot()"><img src="images/next.png" height="15" /> </a></div></td> <td><input type="text" disabled id="maxLvlarena" value="%nextLvlBot%" /></td></tr>
					<tr><td colspan ='3'><div class="attackUser"><a href="#" onclick="attackUser('arenaBot',%avatarBotRand%, %idBot%)" style='padding-right:40%; margin-left:15%;'> <img src="images/attack.png" height="20" /> </a></div> </td></tr>
				</table>
			</form>
			<div id="timer"> <div id="timerMin" >%timerMin%</div>:<div id="timerSec" >%timerSec%</div> </div>
		
			%nameBot% [ %lvlBot% ]<br />
				<div class="arenaDown">
					<img src="images/avatars/arena_bots/%avatarBot%.png" class="avatarArena"/>
					<div class="leftavatar">
						%characteristicsBot%
					</div>
				</div>
		</div>
	</div>
	
	<div class="avatar" style='margin-left:1px; width:25%;padding:3px;' >
		<div class = "textBot">
			%textBot%
		</div>
			%botStatistic%
	</div>
	