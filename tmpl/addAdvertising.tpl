<div class="allAdvertisings">
	<form name="addAdv" action="lib/allFunctions.php" id="addAdv" method="post" enctype="multipart/form-data">
	
		<li>%time%<br/>
		<input type="text" name="title" id="title" placeholder="Заголовок" maxlength="25" required/>
		<select id="section" name="section" form="addAdv" required>
		  <option disabled selected value="none">Тип</option>
		  <option value="buy">Куплю</option>
		  <option value="swap">Обменяю</option>
		  <option value="set">Набор в клан</option>
		  <option value="admin">Админу</option>
		</select></li>
		<li><textarea name="text" id="text" placeholder="Описание" rows="5" cols="75" maxlength="255" required ></textarea></li>
		
		<li><input type="file" accept="image/jpeg,image/png,image/jpg" name="userfile" id="userfile"/>
		<input type="radio" checked name="WhatIMustDo" value="addAdv" style="display:none;"/>
		<input type="submit" value="Отправить" /> </li>
	</form>
</div>