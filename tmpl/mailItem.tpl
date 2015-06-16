<li class="mail %onOff% %styleBackground%">
	<div class="messageDiv" id="message%idSender%" style="padding-bottom:%padding%px;%styleBackground%" onclick="loadAllMessages(%idSender%)">
	<img src="images/avatars/%avatar%.png" height="50" />
		<div class="nick"> %sender% </div> <div class="title"> %title% </div> <div class="time"> %time% </div>
		<div class="textMessage"> %textMessage% </div>
		</div>
		<div class="%loadMore%">
			<a href="#" onclick="variation(%type%,%id%)">Ответить</a>
		</div>
		<div id="AnswerThis_%id%" class="AnswerThis">
			<form name="answer" id="answer_%who%"> 
			<table>
			<tr> <td colspan="2" ><input type="text" name="title" id="title_%who%" /> </td> </tr>
			<tr><td colspan="2" ><textarea name="textMessage" id="textMessage_%who%" cols="40" rows="3" ></textarea></td></tr>
			<tr> <td> <a href="#" onclick="reloadBlock('?view=mail&type=0','ul_mail')" class="leftA" > Скрыть </a></td> <td> <a href="#" onclick="sendMessage(%who%,%id%)" class="rightA"> Отправить </a></td> </tr>
			</table>
			</form>
		</div>
</li>
