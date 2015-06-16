<li class="invItem">
		<div class="inventoryItems" onmouseover='showPotions("%slot%")' onmouseout="lostFocusPotions('%slot%')">
			<img src="images/cloth/mini/%slot%.png"  class="inventoryItems" />
			<div class="countPotions">%count%</div>
			<div id="delete_%slot%" class="delete" onclick="deleteThis('%slot%', %type%)"> &times; </div> 
			<div id="use_%slot%" class="displayPutOn" onclick="useIt('%slot%')"> Use </div>
		</div>
</li>