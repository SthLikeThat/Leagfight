<ul>
	<li><textarea cols="55" rows="8" placeholder="Описание клана" id="description" style="resize:vertical;">%description%</textarea></li>
	<li>Минимальный уровень входа: <input type="text" id="minLvl" value="%minLvl%"></li>

</ul>

<ul id="tableUl">
	<li><table>
		<tr>
			<td>Название</td>
			<td>Казна</td>
			<td>Плац</td>
			<td>Мастерская</td>
			<td>Дипломатия</td>
			<td>Академии</td>
			<td>Походы</td>
			<td id='modeTitles' onclick='changeModeTitles()'>Edit</td>
		</tr>
		%table%
		<tr id="newTitle">
			<td><input type="text" id="nameTitle" /></td>
			<td><input type="checkbox" id="treasuryCheck" /></td>
			<td><input type="checkbox" id="platzCheck" /></td>
			<td><input type="checkbox" id="workshopCheck" /></td>
			<td><input type="checkbox" id="diplomacyCheck" /></td>
			<td><input type="checkbox" id="academyCheck" /></td>
			<td><input type="checkbox" id="hikesCheck" /></td>
		</tr>
	</table></li>
	<li id="buttonsTitles"><a href="#" onclick="newTitle()">Добавить</a><a href="#" onclick="saveTitles()">Сохранить</a></li>
</ul>