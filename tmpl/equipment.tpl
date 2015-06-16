		<div class="leftcloth">
			<div class="clothimg" onmouseover="takeOffFocus('primaryWeapon','%primaryWeapon%')" onmouseout="takeOffFocusLost('primaryWeapon')"> 
			<img src="images/cloth/mini/%primaryWeapon%.png" height="60" onclick="showDetails2('%slotPrim%', 1, '%hashPrim%')"/>
			<div class="displayPutOn" id="primaryWeapon" onclick="putOffThisThing('primaryWeapon')"> Снять </div> </div>
			
			<div class="clothimg" onmouseover="takeOffFocus('secondaryWeapon','%secondaryWeapon%')" onmouseout="takeOffFocusLost('secondaryWeapon')">
			<img src="images/cloth/mini/%secondaryWeapon%.png" height="60" onclick="showDetails2('%slotSec%', 1, '%hashSec%')"/>
			<div class="displayPutOn" id="secondaryWeapon" onclick="putOffThisThing('secondaryWeapon')"> Снять </div> </div>
			
			<div class="clothimg" onmouseover="takeOffFocus('armor','%armor%')" onmouseout="takeOffFocusLost('armor')">
			<img src="images/cloth/mini/%armor%.png" height="60" onclick="showDetails2('%slotArmor%', 1, '%hashArmor%')"/>
			<div class="displayPutOn" id="armor" onclick="putOffThisThing('armor')"> Снять </div> </div>
		</div>
		
		<div class="rightcloth">
			<div class="clothimg" onmouseover="takeOffFocus('helmet','%helmet%')" onmouseout="takeOffFocusLost('helmet')">
			<img src="images/cloth/mini/%helmet%.png" height="60" onclick="showDetails2('%slotHelmet%', 1, '%hashHelmet%')"/>
			<div class="displayPutOn" id="helmet" onclick="putOffThisThing('helmet')"> Снять </div> </div>
			
			<div class="clothimg" onmouseover="takeOffFocus('leggings','%leggings%')" onmouseout="takeOffFocusLost('leggings')">
			<img src="images/cloth/mini/%leggings%.png" height="60" onclick="showDetails2('%slotLeggings%', 1, '%hashLeggings%')"/>
			<div class="displayPutOn" id="leggings" onclick="putOffThisThing('leggings')"> Снять </div> </div>
			
			<div class="clothimg" onmouseover="takeOffFocus('bracers','%bracers%')" onmouseout="takeOffFocusLost('bracers')">
			<img src="images/cloth/mini/%bracers%.png" height="60" onclick="showDetails2('%slotBracers%', 1, '%hashBracers%')"/>
			<div class="displayPutOn" id="bracers" onclick="putOffThisThing('bracers')"> Снять </div> </div>
		</div>