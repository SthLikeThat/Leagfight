<?php
	mb_internal_encoding("UTF-8");
	require_once "lib/database_class.php";
	$db = new DataBase();
	$view = $_GET["view"];
	switch ($view) {
		case "": 
			require_once "lib/frontpagecontent_class.php";
			$content = new FrontPageContent($db);
			break;
		case "confirmRegistration":
			require_once "lib/confirmRegistration_class.php";
			$content = new Confirm($db);
			break;
		case "client":
			require_once "lib/clientContent_class.php";
			$content = new clientContent($db);
			break;
		case "shop":
			require_once "lib/shopContent_class.php";
			$content = new shopContent($db);
			break;
		case "arena":
			require_once "lib/arenaContent_class.php";
			$content = new arenaContent($db);
			break;
		case "fightLog":
			require_once "lib/fightLogContent_class.php";
			$content = new fightLogContent($db);
			break;
		case "mailMenu":
			require_once "lib/mailMenuContent_class.php";
			$content = new mailMenuContent($db);
			break;
		case "mail":
			require_once "lib/mailContent_class.php";
			$content = new mailContent($db);
			break;
		case "search":
			require_once "lib/searchContent_class.php";
			$content = new searchContent($db);
			break;
		case "options":
			require_once "lib/optionsContent_class.php";
			$content = new optionsContent($db);
			break;
		case "town":
			require_once "lib/townContent_class.php";
			$content = new townContent($db);
			break;
		case "clan":
			require_once "lib/clanContent_class.php";
			$content = new clanContent($db);
			break;
		case "statistic":
			require_once "lib/userStatistic.php";
			$content = new userStatistic($db);
			break;
		case "memoryPuzzle":
			require_once "lib/games/memPuzz_Content.php";
			$content = new memPuzz_Content($db);
			break;
		case "Simon":
			require_once "lib/games/simonContent.php";
			$content = new simonContent($db);
			break;
		case "Sokoban":
			require_once "lib/games/sokobanContent.php";
			$content = new sokobanContent($db);
			break;
		case "availableWorks":
			require_once "lib/availableWorksContent_class.php";
			$content = new availableWorks($db);
			break;
		case "battleField":
			require_once "lib/massBattleContent_class.php";
			$content = new massBattle($db);
			break;
		default: 
		require_once "lib/notfoundcontent_class.php";
		$content = new NotFoundContent($db);
	}
	echo $content->getContent();
?>