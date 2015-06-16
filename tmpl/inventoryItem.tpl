<li class="invItem">
		<div class="inventoryItems" onmouseover='showDetails(%onOff%, %slot%, %show%)' onmouseout="lostFocus(%slot%) " >
			<img src="images/cloth/mini/%id%.png"  class="inventoryItems" onclick="showDetails2(%slot%, %show%, '%hash%')"/>
			<div id="puton_%slot%" class="displayPutOn" onclick="putOnThisThing(%slot%)" > Надеть </div>
			<div id="putoff_%slot%" class="displayPutOn" onclick="putOffThisThing(%slot%)" > Снять </div>
			<div id="delete_%slot%" class="delete"  onclick="deleteThis(%slot%, %type%, '%hash%')"> &times; </div> 
		</div>
</li>