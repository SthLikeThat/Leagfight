<form name="options" id="options">
	<div class="options">
		<textarea name="messAttacker" id="messAttacker" rows="5" cols="35" placeholder="Сообщение нападающему" >
			%messAttacker%
		</textarea>
	</div>
	<div class="options"> 
		<textarea name="description" id="description" rows="5" cols="35" placeholder="Информация о персонаже" >
			%description%
		</textarea>
	</div>
	<a href="#" onclick="changeSettings()"> Сохранить </a>
	<div id="myGames">
		<div><input type="number" id="cellsMemoryPuzzle" min="2" max="10" value="6" style="width:35px;"><a href="#" onclick="newMemoryPuzzle()"> Memory Puzzle </a></div>
		<div><input type="number" id="SimonButtons" min="3" max="6" value="4" style="width:35px;"><a href="#" onclick="newSimon()"> Simon </a></div>
		<div><a href="#" onclick="newSokoban()"> Sokoban </a></div>
	</div>
	<div class="footerCorrect"></div>
</form>