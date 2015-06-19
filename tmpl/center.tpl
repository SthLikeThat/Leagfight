	<div class="avatar">
		%nick% <br />
		<img src="images/avatars/%avatar%.png" class="avatarimg"/>
		<div class="leftavatar" id="leftavatar" onclick="addSomeStats('add')" >
			%characteristics%
		</div>
		
		<div id="addSomeStats" >
			<form name="characteristics" id="characteristics">
				<table>
				<tr><td> Скидка: </td> <td> %discount%% </td> <td colspan="2" > <a href="#" style="width:100%;" onclick="addSomeStats('show')" > Назад </a> </td></tr>
				
				<tr><td> <img src="image_char/image/strengh.png" height="20"/> </td>
				<td> <a href="#" onclick="delInputValue('strengh','max')"> <img src="images/delAll.png" height="20"/> </a> <a href="#" onclick="delInputValue('strengh',10)"><img src="images/del10.png" height="20"/></a> <a href="#" onclick="delInputValue('strengh',1)"> <img src="images/del1.png" height="20"/></a> </td>
				<td> <input type="text" name="strengh" id="strengh" value="0" /> </td> 
				<td>  <a href="#" onclick="addInputValue('strengh',1)"> <img src="images/pump1.png" height="20"/> </a> <a href="#" onclick="addInputValue('strengh',10)"> <img src="images/pump10.png" height="20"/> </a> <a href="#" onclick="addInputValue('strengh','max')"> <img src="images/pumpAll.png" height="20"/> </a></td></tr>
				
				<tr><td> <img src="image_char/image/defence.png" height="20"/> </td>
				<td> <a href="#" onclick="delInputValue('defence','max')"> <img src="images/delAll.png" height="20"/> </a> <a href="#" onclick="delInputValue('defence',10)"> <img src="images/del10.png" height="20"/> </a> <a href="#" onclick="delInputValue('defence',1)"> <img src="images/del1.png" height="20"/> </a> </td>
				<td> <input type="text" name="defence" id="defence" value="0"/> </td> 
				<td> <a href="#" onclick="addInputValue('defence',1)"> <img src="images/pump1.png" height="20"/> </a> <a href="#" onclick="addInputValue('defence',10)"> <img src="images/pump10.png" height="20"/> </a> <a href="#" onclick="addInputValue('defence','max')"> <img src="images/pumpAll.png" height="20"/> </a> </td></tr>
				
				<tr><td> <img src="image_char/image/agility.png" height="20"/> </td>
				<td> <a href="#" onclick="delInputValue('agility','max')"> <img src="images/delAll.png" height="20"/> </a> <a href="#" onclick="delInputValue('agility',10)"> <img src="images/del10.png" height="20"/> </a> <a href="#" onclick="delInputValue('agility',1)"><img src="images/del1.png" height="20"/> </a> </td>
				<td> <input type="text" name="agility" id="agility" value="0"/> </td>
				<td> <a href="#" onclick="addInputValue('agility',1)"> <img src="images/pump1.png" height="20"/> </a> <a href="#" onclick="addInputValue('agility',10)"> <img src="images/pump10.png" height="20"/> </a> <a href="#" onclick="addInputValue('agility','max')"> <img src="images/pumpAll.png" height="20"/> </a></td></tr>
				
				<tr><td> <img src="image_char/image/physique.png" height="20"/> </td>
				<td> <a href="#" onclick="delInputValue('physique','max')"> <img src="images/delAll.png" height="20"/> </a> <a href="#" onclick="delInputValue('physique',10)"> <img src="images/del10.png" height="20"/> </a> <a href="#" onclick="delInputValue('physique',1)"> <img src="images/del1.png" height="20"/> </a> </td>
				<td> <input type="text" name="physique" id="physique" value="0"/> </td> 
				<td> <a href="#" onclick="addInputValue('physique',1)"> <img src="images/pump1.png" height="20"/> </a> <a href="#" onclick="addInputValue('physique',10)"> <img src="images/pump10.png" height="20"/> </a> <a href="#" onclick="addInputValue('physique','max')"> <img src="images/pumpAll.png" height="20"/> </a> </td></tr>
				
				<tr><td> <img src="image_char/image/mastery.png" height="20"/> </td>
				<td> <a href="#" onclick="delInputValue('mastery','max')"> <img src="images/delAll.png" height="20"/> </a> <a href="#" onclick="delInputValue('mastery',10)"> <img src="images/del10.png" height="20"/> </a> <a href="#" onclick="delInputValue('mastery',1)"> <img src="images/del1.png" height="20"/> </a> </td>
				<td> <input type="text" name="mastery" id="mastery" value="0"/> </td>
				<td> <a href="#" onclick="addInputValue('mastery',1)"> <img src="images/pump1.png" height="20"/> </a> <a href="#" onclick="addInputValue('mastery',10)"> <img src="images/pump10.png" height="20"/> </a> <a href="#" onclick="addInputValue('mastery','max')"> <img src="images/pumpAll.png" height="20"/> </a> </td></tr>
				
				
				<tr> <td> <img src="images/coinBlack.png" height="20"/> </td> <td> <div id="pumpPrice"> 0 </div> </td><td colspan="2" ><a href="#" style="width:100%;" onclick="sendChar('yes')"> Тренироваться </a>  </td></tr>
				</table>
			</form>
		</div>
	</div>
	
	<div class="clothing" id="clothing">
		%equipment%
	</div>
	
	<div class="inventory" id="inventory">
		%inventory%
	</div>
	
	<div class="inventory" id="invPotions">
		%invPotions%
	</div>
    <div class="damageInformation">
        %damageInformation%
    </div>
	
	
	