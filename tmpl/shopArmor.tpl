	<div class="leftSort">
		<form name="shop" id="shop">
		<input type="radio" id="c1" name="typeSort" value="0" checked > <label for="c1"><span></span>Вся</label>
		<input type="radio" id="c2" name="typeSort" value="1"  > <label for="c2"><span></span>Лёгкая</label>
		<input type="radio" id="c3" name="typeSort" value="2"  > <label for="c3"><span></span>Средняя</label>
		<input type="radio" id="c4" name="typeSort" value="3"  > <label for="c4"><span></span>Тяжелая</label><br />
	</div>
	<div class="rightSort">
		<input type="checkbox" %checked% onclick="change(this)" /> Отобразить
		<a href="#" onclick="sortThisShit(%type%)" >Сортировать</a>
	</div>
	</div>
</form>
<ul>%armor%</ul>