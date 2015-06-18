<div class="allInformation">
	<div class="smbEquip">
		<div class="equipItem" onclick="showDetailsLog('agressor|primaryWeapon', %id%)" >
		<img src="images/cloth/%agrPrimaryWeapon%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('agressor|secondaryWeapon', %id%)">
		<img src="images/cloth/%agrSecondaryWeapon%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('agressor|helmet', %id%)">
		<img src="images/cloth/%agrHelmet%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('agressor|armor', %id%)">
		<img src="images/cloth/%agrArmor%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('agressor|leggings', %id%)">
		<img src="images/cloth/%agrLeggings%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('agressor|bracers', %id%)">	
		<img src="images/cloth/%agrBracers%.png" height="60" />	</div>
	</div>
	%fightLogDamageAgr%
	<div class="agressorBlock %agrClass%" >
		<div class="warriorNick"><a href='?view=client&id=%idAgressor%'> %agressorNick% </a> </div>
		<img src="images/avatars/%agressorAvatar%.png" height="120" />
		%agressorCharacteristics%
		<img src="image_char/image/power.png" height="20"/> %powerAgr%
	</div>
	
	
	
	<div class="defenderBlock %defClass%" >
		<div class="warriorNick"> <a href='?view=client&id=%idDefender%'> %defenderNick% </a> </div>
		<img src="images/avatars/%defenderAvatar%.png" height="120" />
		%defenderCharacteristics%
		<img src="image_char/image/power.png" height="20"/> %powerDef%
	</div>
	%fightLogDamageDef% 
	<div class="smbEquip">
		<div class="equipItem" onclick="showDetailsLog('defender|primaryWeapon', %id%)">	
		<img src="images/cloth/%defPrimaryWeapon%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('defender|secondaryWeapon', %id%)">	
		<img src="images/cloth/%defSecondaryWeapon%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('defender|helmet', %id%)">	
		<img src="images/cloth/%defHelmet%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('defender|armor', %id%)">
		<img src="images/cloth/%defArmor%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('defender|leggings', %id%)">	
		<img src="images/cloth/%defLeggings%.png" height="60" />	</div>
		
		<div class="equipItem" onclick="showDetailsLog('defender|bracers', %id%)">	
		<img src="images/cloth/%defBracers%.png" height="60" />	</div>
		
	</div>
</div>

<div class="prize winner">
%prize%
</div>