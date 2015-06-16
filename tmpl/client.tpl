	<div class="avatar" >
		<div id="avatar">
			%nick% [ %lvl% ]
		</div>
		<a href="#" onclick="writeMessage()" id="writeMessage">Написать сообщение</a>
			<img src="images/avatars/%avatar%.png" class="avatarArena"/>
			<div class="leftavatar">
			%characteristics%
			</div>
	</div>
	<ul id="ulClient">
	<li id="message" style='display:none;'>
		<ul>
			<li><input type="text" id="title_%id%"/><a href="#" onclick="sendMessage('%id%',0)">Отправить</a></li>
			<li><textarea rows="6" cols="45" id="textMessage_%id%"></textarea></li>
		</ul>
	</li>
	
	<li id="description">
		%description%
	</li>
	