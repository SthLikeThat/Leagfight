<div id="riverPlace">
	%riverBlocks%
</div>
<ul id="riverMenu">
	<li>Выберите куда установить сети.</li>
	<li>Предел сетей за раз: %maxNetworks%</li>
	<li>Сетей: <div id="fishnetCount">%networks%</div></li>
	<li style="margin-top:10px;">
		<input type="number" id="netsToBuy" min="0" max="10" style="width:35px;" value="0" onchange="getPriceNetworks()">
		<a href="#" onclick="buyNetwork()">Купить </a>
	</li>
	<li id="priceRiverBlock"><div id="priceRiver">0</div> <img src="images/coinBlack.png" height="20"></li>
	<li style="margin-top:10px;">
		<a href="#" onclick="readyNetworks()"> Готово </a>
	</li>
</div>