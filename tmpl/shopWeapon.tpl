
	<div class="leftSort">
		<form name="shop" id="shop">
		<input type="radio" id="c1" name="typeSort" value="0" checked > <label for="c1"><span></span>Все</label>
		<input type="radio" id="c2" name="typeSort" value="1"  > <label for="c2"><span></span>Одноручные</label>
		<input type="radio" id="c3" name="typeSort" value="2"  > <label for="c3"><span></span>Двуручные</label>
		<input type="radio" id="c4" name="typeSort" value="3"  > <label for="c4"><span></span>Древковые</label><br />

		<input type="radio" id="r1" name="typedamageSort" value="0" checked ><label for="r1"><span></span>Все</label>
		<input type="radio" id="r2" name="typedamageSort" value="1" > <label for="r2"><span></span>Колющее</label>
		<input type="radio" id="r3" name="typedamageSort" value="2" > <label for="r3"><span></span>Режущее</label>
		<input type="radio" id="r4" name="typedamageSort" value="3" > <label for="r4"><span></span>Дробящее</label><br />
	</div>
	
	<div class="rightSort">
		<input type="checkbox" %checked% onclick="change(this)" /> Отобразить
		<a href="#" onclick="sortThisShit(%type%)" >Сортировать</a>
	</div>
</div>
</form>
<ul id="elems">%weapon%</ul>