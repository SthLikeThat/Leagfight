<?php
require_once "modules_class.php";

class userStatistic extends Modules {

	protected $db;
	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	protected function getCenter() {
			$sr["fightStatistic"] = $this->getFightStatistic();
			$sr["damageStatistic"] = $this->getDamageStatistic();
			$sr["arenaBotsStatistic"] = $this->getArenaBotsStatistic();
			$sr["shopStatistic"] = $this->getShopStatistic();
			$sr["clanStatistic"] = $this->getClanStatistic();
			$sr["messageStatistic"] = $this->getMessageStatistic();
			$sr["advStatistic"] = $this->getAdvStatistic();
			$sr["charStatistic"] = $this->getCharStatistic();
		return $this->getReplaceTemplate($sr, "allStatistic");
	}
	
	private function getFightStatistic(){
		$statistic = unserialize($this->user["userStatistic"]);
		$fights = $statistic["wins"] + $statistic["lose"] + $statistic["draw"];
		$table = "<div class = 'botStatistic'><table><td colspan='2'>Статистка сражений</td>";
		$table .= "<tr><td> Сражений </td> <td> $fights</td></tr>";
		$table .= "<tr><td> Побед </td> <td>".$statistic["wins"]."</td></tr>";
		$table .= "<tr><td> Поражений </td> <td>".$statistic["lose"]."</td></tr>";
		$table .= "<tr><td> Ничьих </td> <td>".$statistic["draw"]."</td></tr>";
		$table .= "<tr><td> Украдено </td> <td>".$statistic["goldStolen"]."</td></tr>";
		$table .= "<tr><td> Потеряно </td> <td>".$statistic["goldLost"]."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getDamageStatistic(){
		$statistic = unserialize($this->user["damageStatistic"]);
		$table = "<div class = 'botStatistic'><table><td colspan='2'>Статистика ударов</td>";
		$table .= "<tr><td> Попаданий </td> <td>".$statistic["hits"]."</td></tr>";
		$table .= "<tr><td> Критов </td> <td>".$statistic["crits"]."</td></tr>";
		$table .= "<tr><td> Промахов </td> <td>".$statistic["misses"]."</td></tr>";
		$table .= "<tr><td> Уворотов </td> <td>".$statistic["dodges"]."</td></tr>";
		$table .= "<tr><td> Блоков </td> <td>".$statistic["Blocks"]."</td></tr>";
		$table .= "<tr><td> Вторых ударов </td> <td>".$statistic["secondHits"]."</td></tr>";
		$table .= "<tr><td> Нанесено урона </td> <td>".$statistic["damageDealt"]."</td></tr>";
		$table .= "<tr><td> Критический урон </td> <td>".$statistic["critDamage"]."</td></tr>";
		$table .= "<tr><td> Получено урона </td> <td>".$statistic["damageReceived"]."</td></tr>";
		$table .= "<tr><td> Заблокировано урона </td> <td>".$statistic["damageBlocked"]."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getArenaBotsStatistic(){
		$statistic = unserialize($this->user["allBots"]);
		$fights = $statistic["wins"] + $statistic["lose"] + $statistic["draw"];
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Статистика турниров</td>";
		$table .= "<tr><td> Сражений </td> <td> $fights</td></tr>";
		$table .= "<tr><td> Побед </td> <td>".$statistic["wins"]."</td></tr>";
		$table .= "<tr><td> Поражений </td> <td>".$statistic["lose"]."</td></tr>";
		$table .= "<tr><td> Ничьих </td> <td>".$statistic["draw"]."</td></tr>";
		$table .= "<tr><td> Украдено </td> <td>".$statistic["stoleGold"]."</td></tr>";
		$table .= "<tr><td> Потеряно </td> <td>".$statistic["lostGold"]."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getShopStatistic(){
		$statistic = unserialize($this->user["shopStatistic"]);
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Статистика покупок</td>";
		$table .= "<tr><td> Золота потрачено </td> <td>".$statistic["spentGold"]."</td></tr>";
		$table .= "<tr><td> Жемчужин потрачено </td> <td>".$statistic["spentAnother"]."</td></tr>";
		$table .= "<tr><td> Снаряжения куплено </td> <td>".$statistic["equipment"]."</td></tr>";
		$table .= "<tr><td> Зелий куплено </td> <td>".$statistic["potions"]."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getClanStatistic(){
		$mda = "?";
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Клановая статистика</td>";
		$table .= "<tr><td> Золота внесено </td> <td>".$mda."</td></tr>";
		$table .= "<tr><td> Жемчуга внесено </td> <td>".$mda."</td></tr>";
		$table .= "<tr><td> Участий в штурмах </td> <td>".$mda."</td></tr>";
		$table .= "<tr><td> Участий в походах </td> <td>".$mda."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getMessageStatistic(){
		$mda = "?";
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Статистика сообщений</td>";
		$table .= "<tr><td>Сообщений отправлено </td> <td>".$mda."</td></tr>";
		$table .= "<tr><td>Сообщений получено </td> <td>".$mda."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getAdvStatistic(){
		$mda = "?";
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Статистика объявлений</td>";
		$table .= "<tr><td> Объявлений дано </td> <td>".$mda."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
	
	private function getCharStatistic(){
		$mda = "?";
		$table = "<div class = 'botStatistic'><table><td colspan='2' >Статистика характеристик</td>";
		$table .= "<tr><td> Золота потрачено </td> <td>".$mda."</td></tr>";
		$table .= "</table></div>";
		return $table;
	}
}
?>