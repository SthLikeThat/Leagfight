<input type="search" onchange="searchThis('value','')" name="searchInput" id="searchInput" value="%value%"/>

<div id="clansSearch"> 
		<div class="searchTopClan">
			<div class="leagueClanSearchTop" ><a href="#" onclick="searchThis('sortClan','&clanSort=league&clanDesc=down')" > Лига </a></div> 
			<div class="nickSearch"><a href="#" onclick="searchThis('sortClan','&clanSort=name&clanDesc=down')" > Название </a></div> 
			<div class="lvlSearch"><a href="#" onclick="searchThis('sortClan','&clanSort=tag&clanDesc=down')" > Тег </a></div>
			<div class="clanSearch"><a href="#" onclick="searchThis('sortClan','&clanSort=middle&clanDesc=down')" > Ср. показатель </a></div> 
			<div class="powerSearch"><a href="#" onclick="searchThis('sortClan','&clanSort=total&clanDesc=down')" > Могущество </a></div>
	</div>
	<ul>
		%searchResultClans%
	</ul>
</div>
<div id="clientsSearch"> 
	<div class="searchTop">
			<div class="leagueSearchTop" ><a href="#" onclick="searchThis('sortUser','&sort=league&desc=down')" > Лига </a></div> 
			<div class="nickSearch"><a href="#" onclick="searchThis('sortUser','&sort=login&desc=down')" > Ник </a></div> 
			<div class="lvlSearch"><a href="#" onclick="searchThis('sortUser','&sort=lvl&desc=down')" > Уровень </a></div>
			<div class="clanSearch"><a href="#" onclick="searchThis('sortUser','&sort=clan&desc=down')" > Клан </a></div> 
			<div class="powerSearch"><a href="#" onclick="searchThis('sortUser','&sort=power&desc=down')" > Могущество </a></div>
	</div>
	<ul>
		%searchResultClients%
	</ul>
</div>
