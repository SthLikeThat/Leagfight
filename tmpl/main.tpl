<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<title>Главная страницы</title>
	<link href="css/style.css" rel="stylesheet">
	<script src="lib/jquery-2.1.3.min.js"></script>
	<script src="lib/scripta.js"></script>
	<script src="lib/games.js"></script>
</head>

<body onload="checkThisHeader();checkThis();jobTime();">

<div class="wrapper" >
	
	<header class="header">
		%header%
	</header>
		
	<div class="middle" id="middle">

		<div class="container">
				%center%
		</div>

		<aside class="left-sidebar">
			<ul>%menu%</ul>
			<div id="bottomMenu">
			<ul>
				<li> <div class="photo" id="menu_options" data-title="Настройки"><a href="/?view=options" class="menuUrl"><img src="images/menu_items/options.png" alt="" height="25"></a></div> </li>
				<li> <div class="photo" data-title="Выход"><a href="lib/exit.php" class="menuUrl"><img src="images/menu_items/exit.png" alt="" height="25"></a></div> </li>
			</ul>
			</div>
		</aside>

	</div>
</div>
<footer class="footer" id="footer">
	<div id="mess3"></div>
</footer>
	<div id="mess4"></div>
	<div id="alert" onclick="notAlert()">
		&times;
		<div id="alertWindow">
		</div>
	</div>
</body>
</html>