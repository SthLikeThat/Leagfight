-- phpMyAdmin SQL Dump
-- version 4.0.10.10
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 04 2015 г., 21:44
-- Версия сервера: 5.6.26-log
-- Версия PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `LeagFight`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id_account` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `user_hash` varchar(32) DEFAULT NULL,
  `confirmed` varchar(32) DEFAULT NULL,
  `avatar` varchar(45) NOT NULL,
  PRIMARY KEY (`id_account`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `confirmed_UNIQUE` (`confirmed`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`id_account`, `login`, `email`, `password`, `user_hash`, `confirmed`, `avatar`) VALUES
(1, 'e33', 'opirus6229078@mail.ru', '3348ed5798b40d7719efa8b9bf0ba915', 'edba32bf85740dbda0666c490fbc2bf3', '1', '1');

--
-- Триггеры `accounts`
--
DROP TRIGGER IF EXISTS `accounts_AFTER_INSERT`;
DELIMITER //
CREATE TRIGGER `accounts_AFTER_INSERT` AFTER INSERT ON `accounts`
 FOR EACH ROW BEGIN
INSERT INTO `user_house` Set id_user = NEW.id_account;
INSERT INTO `user_inventory` Set id_user = NEW.id_account;
INSERT INTO `user_potions` Set id_user = NEW.id_account;
INSERT INTO `user_timers` Set id_user = NEW.id_account;
INSERT INTO `user_information` Set id_user = NEW.id_account;
INSERT INTO `user_resources` Set id_user = NEW.id_account;
INSERT INTO `user_equipment` Set id_user = NEW.id_account;
INSERT INTO `user_settings` Set id_user = NEW.id_account;
INSERT INTO `user_statistic` Set id_user = NEW.id_account;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `accounts_BEFORE_DELETE`;
DELIMITER //
CREATE TRIGGER `accounts_BEFORE_DELETE` BEFORE DELETE ON `accounts`
 FOR EACH ROW BEGIN
DELETE FROM `user_house` WHERE id_user = OLD.id_account;
DELETE FROM `user_inventory` WHERE id_user = OLD.id_account;
DELETE FROM `user_potions` WHERE id_user = OLD.id_account;
DELETE FROM `user_timers` WHERE id_user = OLD.id_account;
DELETE FROM `user_information` WHERE id_user = OLD.id_account;
DELETE FROM `user_resources` WHERE id_user = OLD.id_account;
DELETE FROM `user_equipment` WHERE id_user = OLD.id_account;
DELETE FROM `user_settings` WHERE id_user = OLD.id_account;
DELETE FROM `user_statistic` WHERE id_user = OLD.id_account;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `advertisings`
--

CREATE TABLE IF NOT EXISTS `advertisings` (
  `id_advertising` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `time` datetime(6) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `image` varchar(32) DEFAULT NULL,
  `id_section` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id_advertising`),
  KEY `advertising_to_user_idx` (`id_user`),
  KEY `advertising_to_section_idx` (`id_section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `advertising_sections`
--

CREATE TABLE IF NOT EXISTS `advertising_sections` (
  `id` tinyint(1) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `arena_bots`
--

CREATE TABLE IF NOT EXISTS `arena_bots` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `avatar` varchar(45) DEFAULT NULL,
  `lvl` tinyint(3) unsigned NOT NULL,
  `characteristics` tinytext,
  `equipment` varchar(255) DEFAULT NULL,
  `specialAbilities` tinyint(2) unsigned NOT NULL,
  `prize` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lvl_INDEX` (`lvl`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `arena_bots`
--

INSERT INTO `arena_bots` (`id`, `name`, `text`, `avatar`, `lvl`, `characteristics`, `equipment`, `specialAbilities`, `prize`) VALUES
(1, 'Бездомный', '<p>Один из видов заработка для бездомных - сражения на арене. Ведь власти платят неплохие деньги за развлечение толпы. Обычно не оснащены бронёй, а из оружия то, что найдут на улице. Ну или украдут.</p>', 'homeless', 1, 'a:10:{s:10:"strenghMin";i:5;s:10:"strenghMax";i:10;s:10:"defenceMin";i:4;s:10:"defenceMax";i:8;s:10:"agilityMin";i:6;s:10:"agilityMax";i:12;s:11:"physiqueMin";i:2;s:11:"physiqueMax";i:5;s:10:"masteryMin";i:2;s:10:"masteryMax";i:6;}', 'a:6:{s:13:"primaryWeapon";i:21;s:15:"secondaryWeapon";i:21;s:5:"armor";i:501;s:6:"helmet";i:0;s:8:"leggings";i:0;s:7:"bracers";i:0;}', 0, 'a:4:{s:4:"Gold";i:0;s:7:"Another";i:0;s:3:"exp";i:2;s:15:"tournament_icon";i:1;}'),
(2, 'Рядовой Лиги Лени', '<p>Мы не знаем как они сюда забредают и зачем. Возможно, надеются, что их противник окажется еще более ленивым и даже не явится на сражение. Неповоротливые, но отрастили тяжелое пузо, которое могут использовать как щит.', 'soldierLeagueOfLaziness', 1, 'a:10:{s:10:"strenghMin";i:3;s:10:"strenghMax";i:7;s:10:"defenceMin";i:8;s:10:"defenceMax";i:13;s:10:"agilityMin";i:1;s:10:"agilityMax";i:2;s:11:"physiqueMin";i:7;s:11:"physiqueMax";i:9;s:10:"masteryMin";i:2;s:10:"masteryMax";i:4;}', 'a:6:{s:13:"primaryWeapon";i:21;s:15:"secondaryWeapon";i:505;s:5:"armor";i:501;s:6:"helmet";i:502;s:8:"leggings";i:504;s:7:"bracers";i:0;}', 0, 'a:4:{s:4:"Gold";i:0;s:7:"Another";i:0;s:3:"exp";i:2;s:15:"tournament_icon";i:1;}'),
(3, 'Приспособленец', '<p>Эта прослойка общества пытается выжить из последних сил. Для них выделили отдельный район города, где они и варятся в собственном соку. Убийства в их районе случаются довольно таки часто, поэтому драться они умеют.</p>', 'trimmer', 2, 'a:10:{s:10:"strenghMin";i:4;s:10:"strenghMax";i:9;s:4:"lose";i:0;s:10:"defenceMin";i:6;s:10:"agilityMin";i:7;s:10:"agilityMax";i:11;s:11:"physiqueMin";i:3;s:11:"physiqueMax";i:6;s:10:"masteryMin";i:6;s:10:"masteryMax";i:8;}', 'a:6:{s:13:"primaryWeapon";i:6;s:15:"secondaryWeapon";i:21;s:5:"armor";i:501;s:6:"helmet";i:0;s:8:"leggings";i:504;s:7:"bracers";i:0;}', 0, 'a:4:{s:4:"Gold";i:50;s:7:"Another";i:0;s:3:"exp";i:2;s:15:"tournament_icon";i:1;}');

-- --------------------------------------------------------

--
-- Структура таблицы `available_works`
--

CREATE TABLE IF NOT EXISTS `available_works` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `requirement` varchar(255) DEFAULT NULL,
  `resources` varchar(255) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
  `id_clan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `tag` varchar(7) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(45) DEFAULT 'clan_default_image',
  `minLvl` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`id_clan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `clans_and_rates`
--

CREATE TABLE IF NOT EXISTS `clans_and_rates` (
  `id_relation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_clan` int(10) unsigned NOT NULL,
  `id_rate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_relation`),
  KEY `clan_to_relation_rates_idx` (`id_clan`),
  KEY `rate_to_raletion_rates_idx` (`id_rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `clan_rates`
--

CREATE TABLE IF NOT EXISTS `clan_rates` (
  `id_rate` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `treasury` tinyint(1) DEFAULT '0',
  `platz` tinyint(1) DEFAULT '0',
  `workshop` tinyint(1) DEFAULT '0',
  `diplomacy` tinyint(1) DEFAULT '0',
  `academy` tinyint(1) DEFAULT '0',
  `campaign` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clan_treasury`
--

CREATE TABLE IF NOT EXISTS `clan_treasury` (
  `id_clan` int(10) unsigned NOT NULL,
  `Gold` int(10) unsigned NOT NULL DEFAULT '0',
  `Another` int(10) unsigned NOT NULL DEFAULT '0',
  `Donat` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_clan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clan_treasury_logging`
--

CREATE TABLE IF NOT EXISTS `clan_treasury_logging` (
  `id_clan` int(10) unsigned NOT NULL,
  `login` varchar(45) DEFAULT NULL,
  `time` datetime(6) DEFAULT NULL,
  `Gold` int(10) unsigned NOT NULL DEFAULT '0',
  `Another` int(10) unsigned NOT NULL DEFAULT '0',
  `Donat` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_clan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clan_workshop`
--

CREATE TABLE IF NOT EXISTS `clan_workshop` (
  `id_clan` int(10) unsigned NOT NULL,
  `barracks` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `desk` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_clan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clan_workshop_information`
--

CREATE TABLE IF NOT EXISTS `clan_workshop_information` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL,
  `startPrice` int(10) unsigned NOT NULL,
  `value` tinyint(3) unsigned NOT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `maxSize` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `clan_workshop_information`
--

INSERT INTO `clan_workshop_information` (`id`, `name`, `title`, `text`, `image`, `startPrice`, `value`, `prefix`, `maxSize`) VALUES
(1, 'barracks', 'Казарма', 'Служит для содержания воинов. Увеличивает максимальное количество членов клана.', 'barracks', 5000, 2, 'чел.', 25),
(2, 'table', 'Круглый стол', 'Служит для обсуждения важных событий в жизни клана. Увеличивает максимальное количество званий.', 'table', 3500, 1, 'шт.', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_agresoor` int(10) unsigned NOT NULL,
  `id_defender` int(10) unsigned NOT NULL,
  `winner` int(10) unsigned NOT NULL,
  `type_fight` enum('user','bot') DEFAULT NULL,
  `time` datetime(6) DEFAULT NULL,
  `prize` varchar(255) DEFAULT NULL,
  `agressor_damage` varchar(255) DEFAULT NULL,
  `defender_damage` varchar(255) DEFAULT NULL,
  `avatar` varchar(45) DEFAULT NULL,
  `agressor` text,
  `defender` text,
  PRIMARY KEY (`id_log`),
  KEY `agressor_to_users_idx` (`id_agresoor`),
  KEY `defender_to_users_idx` (`id_defender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `time` datetime(6) DEFAULT NULL,
  `type` enum('l','m') DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `textMessage` text,
  `extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `mail_from_to_users_idx` (`from`),
  KEY `to_mail_to_users_idx` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_armor`
--

CREATE TABLE IF NOT EXISTS `shop_armor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thing` tinyint(1) unsigned NOT NULL,
  `type_defence` enum('1','2','3') DEFAULT NULL,
  `required_lvl` tinyint(3) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `armor` decimal(5,2) unsigned NOT NULL,
  `bonus_strengh` mediumint(6) NOT NULL,
  `bonus_defence` mediumint(6) NOT NULL,
  `bonus_agility` mediumint(6) NOT NULL,
  `bonus_physique` mediumint(6) NOT NULL,
  `bonus_mastery` mediumint(6) NOT NULL,
  `price` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=509 ;

--
-- Дамп данных таблицы `shop_armor`
--

INSERT INTO `shop_armor` (`id`, `thing`, `type_defence`, `required_lvl`, `name`, `armor`, `bonus_strengh`, `bonus_defence`, `bonus_agility`, `bonus_physique`, `bonus_mastery`, `price`) VALUES
(501, 2, '1', 1, 'Кожанка', '0.20', 0, 5, 0, 0, 0, 200),
(502, 3, '1', 2, 'Кепка', '0.10', 0, 0, 0, 0, 0, 150),
(503, 5, '1', 3, 'Сыромятные наручи', '0.05', 0, 0, 0, 0, 5, 215),
(504, 4, '1', 1, 'Шорты', '0.10', 0, 3, 0, 0, 0, 225),
(505, 6, '1', 4, 'Крышка', '0.20', 0, 4, 0, 0, 0, 340),
(506, 2, '2', 6, 'Средняя куртка', '0.30', 0, 10, -10, 0, 0, 500),
(508, 2, '3', 7, 'Тяжелая броня', '0.50', 0, 25, -70, 10, 0, 1500);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_something`
--

CREATE TABLE IF NOT EXISTS `shop_something` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `thing` tinyint(1) unsigned NOT NULL,
  `required_lvl` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `type_effect` varchar(45) DEFAULT NULL,
  `value_effect` varchar(45) DEFAULT NULL,
  `price` int(10) unsigned NOT NULL,
  `valuta` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1002 ;

--
-- Дамп данных таблицы `shop_something`
--

INSERT INTO `shop_something` (`id`, `thing`, `required_lvl`, `title`, `name`, `type_effect`, `value_effect`, `price`, `valuta`) VALUES
(1001, 7, 1, 'Бутерброд', 'sandwich', 'healPercent', '5', 200, 'coinBlack');

-- --------------------------------------------------------

--
-- Структура таблицы `shop_weapon`
--

CREATE TABLE IF NOT EXISTS `shop_weapon` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `thing` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `type` enum('1','2','3') NOT NULL,
  `type_damage` enum('1','2','3') DEFAULT NULL,
  `required_lvl` tinyint(3) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `damage` decimal(5,2) unsigned NOT NULL,
  `crit` decimal(5,2) unsigned NOT NULL,
  `bonus_strengh` mediumint(6) NOT NULL DEFAULT '0',
  `bonus_defence` mediumint(6) NOT NULL DEFAULT '0',
  `bonus_agility` mediumint(6) NOT NULL DEFAULT '0',
  `bonus_physique` mediumint(6) NOT NULL DEFAULT '0',
  `bonus_mastery` mediumint(6) NOT NULL DEFAULT '0',
  `price` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Дамп данных таблицы `shop_weapon`
--

INSERT INTO `shop_weapon` (`id`, `thing`, `type`, `type_damage`, `required_lvl`, `name`, `damage`, `crit`, `bonus_strengh`, `bonus_defence`, `bonus_agility`, `bonus_physique`, `bonus_mastery`, `price`) VALUES
(21, 1, '1', '1', 1, 'Палка', '0.20', '1.50', 3, 0, 0, 0, 0, 90),
(2, 1, '1', '1', 4, 'Вилка', '0.45', '1.70', 7, 0, 0, 0, 3, 232),
(3, 1, '1', '1', 7, 'Отвёртка', '0.50', '1.70', 13, 0, 0, 0, 5, 100),
(4, 1, '1', '1', 10, 'Зонт', '0.95', '1.75', 20, 0, 0, 0, 10, 100),
(5, 1, '1', '1', 13, 'Шпага', '1.00', '2.00', 30, 0, 10, 0, 10, 100),
(6, 1, '1', '2', 2, 'Линейка', '0.25', '1.50', 3, 0, 0, 0, 3, 122),
(7, 1, '1', '2', 7, 'Нож', '0.65', '1.50', 5, 0, 0, 0, 5, 100),
(8, 1, '1', '2', 12, 'Мясницкий нож', '1.00', '1.75', 25, 0, 0, 0, 15, 100),
(9, 1, '1', '2', 17, 'Меч', '1.30', '2.00', 35, 0, 0, 0, 35, 100),
(10, 1, '1', '3', 3, 'Сковорода', '0.40', '1.50', 7, 0, 0, 0, 4, 155),
(11, 1, '1', '3', 5, 'Скалка', '0.45', '1.60', 10, 0, 0, 0, 5, 350),
(12, 1, '1', '3', 7, 'Молоток', '0.70', '1.80', 15, 0, 0, 0, 7, 100),
(13, 1, '1', '3', 9, 'Гаечный ключ', '0.90', '1.80', 20, 0, 0, 0, 10, 100),
(14, 1, '1', '3', 11, 'Лом', '1.00', '1.85', 25, 0, 0, 0, 15, 100),
(15, 1, '2', '2', 8, 'Топор', '0.75', '1.85', 15, 0, 0, 0, 15, 100),
(16, 1, '2', '2', 14, 'Секира', '1.20', '2.00', 35, 0, 0, 0, 25, 100),
(17, 1, '2', '3', 9, 'Кувалда', '1.00', '1.85', 20, 0, 0, 0, 10, 100),
(18, 1, '2', '3', 15, 'Молот', '1.20', '2.00', 45, 0, 0, 0, 20, 100),
(19, 1, '3', '1', 5, 'Вилы', '0.55', '1.50', 15, 0, 0, 0, 5, 300),
(20, 1, '3', '3', 7, 'Посох', '0.65', '1.60', 18, 0, 0, 0, 13, 100);

-- --------------------------------------------------------

--
-- Структура таблицы `users_and_clans`
--

CREATE TABLE IF NOT EXISTS `users_and_clans` (
  `id_relation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_clan` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_relation`),
  KEY `user_to_relation_idx` (`id_user`),
  KEY `clan_to_realtion_idx` (`id_clan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users_and_rates`
--

CREATE TABLE IF NOT EXISTS `users_and_rates` (
  `id_relation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_rate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_relation`,`id_user`,`id_rate`),
  KEY `user_to_relation_rate_idx` (`id_user`),
  KEY `rate_to_relation_rate_idx` (`id_rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `user_equipment`
--

CREATE TABLE IF NOT EXISTS `user_equipment` (
  `id_user` int(10) unsigned NOT NULL,
  `primaryWeapon` varchar(32) DEFAULT '0',
  `secondaryWeapon` varchar(32) DEFAULT '0',
  `helmet` varchar(32) DEFAULT '0',
  `armor` varchar(32) DEFAULT '0',
  `bracers` varchar(32) DEFAULT '0',
  `leggings` varchar(32) DEFAULT '0',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_equipment`
--

INSERT INTO `user_equipment` (`id_user`, `primaryWeapon`, `secondaryWeapon`, `helmet`, `armor`, `bracers`, `leggings`) VALUES
(1, 'teIYOKrr', 'spHNk zo', 'b00rI9Yd', 'oO2dLx06', 'cZBghJgM', 'n1AQjRjP');

-- --------------------------------------------------------

--
-- Структура таблицы `user_house`
--

CREATE TABLE IF NOT EXISTS `user_house` (
  `id_user` int(10) unsigned NOT NULL,
  `bed` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `safe` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `warehouse` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_house`
--

INSERT INTO `user_house` (`id_user`, `bed`, `safe`, `warehouse`) VALUES
(1, 6, 5, 7);

-- --------------------------------------------------------

--
-- Структура таблицы `user_house_information`
--

CREATE TABLE IF NOT EXISTS `user_house_information` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL,
  `startPrice` int(10) unsigned NOT NULL,
  `value` tinyint(3) unsigned NOT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `maxSize` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `user_house_information`
--

INSERT INTO `user_house_information` (`id`, `name`, `title`, `text`, `image`, `startPrice`, `value`, `prefix`, `maxSize`) VALUES
(1, 'bed', 'Кровать', 'Служит для отдыха. Её уровень влияет на % регенерации хп час.', 'bed', 500, 1, '%', 25),
(2, 'safe', 'Сейф', 'Сохраняет часть вашего золота при проигрыше.', 'safe', 1000, 5, '%', 10),
(3, 'warehouse', 'Склад', 'Служит для хранения ваших вещей. Увеличивает размер инвентаря. ', 'warehouse', 1250, 2, ' шт.', 11);

-- --------------------------------------------------------

--
-- Структура таблицы `user_information`
--

CREATE TABLE IF NOT EXISTS `user_information` (
  `id_user` int(10) unsigned NOT NULL,
  `Strengh` mediumint(6) unsigned NOT NULL DEFAULT '10',
  `Defence` mediumint(6) unsigned NOT NULL DEFAULT '10',
  `Agility` mediumint(6) unsigned NOT NULL DEFAULT '10',
  `Physique` mediumint(6) unsigned NOT NULL DEFAULT '10',
  `Mastery` mediumint(6) unsigned NOT NULL DEFAULT '10',
  `lvl` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `current_Exp` int(10) unsigned NOT NULL DEFAULT '0',
  `need_Exp` int(10) unsigned NOT NULL DEFAULT '25',
  `current_Hp` int(10) unsigned NOT NULL DEFAULT '1000',
  `max_Hp` int(10) unsigned NOT NULL DEFAULT '1000',
  `job_info` varchar(255) DEFAULT NULL,
  `power` int(11) NOT NULL,
  PRIMARY KEY (`id_user`),
  KEY `lvl_INDEX` (`lvl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_information`
--

INSERT INTO `user_information` (`id_user`, `Strengh`, `Defence`, `Agility`, `Physique`, `Mastery`, `lvl`, `current_Exp`, `need_Exp`, `current_Hp`, `max_Hp`, `job_info`, `power`) VALUES
(1, 135, 123, 111, 104, 148, 7, 67, 119, 18508, 18508, NULL, 1130);

-- --------------------------------------------------------

--
-- Структура таблицы `user_inventory`
--

CREATE TABLE IF NOT EXISTS `user_inventory` (
  `id_user` int(10) unsigned NOT NULL,
  `slot1` varchar(255) DEFAULT '0',
  `slot2` varchar(255) DEFAULT '0',
  `slot3` varchar(255) DEFAULT '0',
  `slot4` varchar(255) DEFAULT '0',
  `slot5` varchar(255) DEFAULT '999',
  `slot6` varchar(255) DEFAULT '999',
  `slot7` varchar(255) DEFAULT '999',
  `slot8` varchar(255) DEFAULT '999',
  `slot9` varchar(255) DEFAULT '999',
  `slot10` varchar(255) DEFAULT '999',
  `slot11` varchar(255) DEFAULT '999',
  `slot12` varchar(255) DEFAULT '999',
  `slot13` varchar(255) DEFAULT '999',
  `slot14` varchar(255) DEFAULT '999',
  `slot15` varchar(255) DEFAULT '999',
  `slot16` varchar(255) DEFAULT '999',
  `slot17` varchar(255) DEFAULT '999',
  `slot18` varchar(255) DEFAULT '999',
  `slot19` varchar(255) DEFAULT '999',
  `slot20` varchar(255) DEFAULT '999',
  `slot21` varchar(255) DEFAULT '999',
  `slot22` varchar(255) DEFAULT '999',
  `slot23` varchar(255) DEFAULT '999',
  `slot24` varchar(255) DEFAULT '999',
  `slot25` varchar(255) DEFAULT '999',
  `slot26` varchar(255) DEFAULT '999',
  `slot27` varchar(255) DEFAULT '999',
  `slot28` varchar(255) DEFAULT '999',
  `slot29` varchar(255) DEFAULT '999',
  `slot30` varchar(255) DEFAULT '999',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_inventory`
--

INSERT INTO `user_inventory` (`id_user`, `slot1`, `slot2`, `slot3`, `slot4`, `slot5`, `slot6`, `slot7`, `slot8`, `slot9`, `slot10`, `slot11`, `slot12`, `slot13`, `slot14`, `slot15`, `slot16`, `slot17`, `slot18`, `slot19`, `slot20`, `slot21`, `slot22`, `slot23`, `slot24`, `slot25`, `slot26`, `slot27`, `slot28`, `slot29`, `slot30`) VALUES
(1, '{"hash":"b00rI9Yd","id":"502","armor":"5"}', '{"hash":"n1AQjRjP","id":"504","armor":"5"}', '{"hash":"spHNk zo","id":"505","armor":"2"}', '{"hash":"cZBghJgM","id":"503","armor":0}', '{"hash":"oO2dLx06","id":"506","armor":0}', '{"hash":"z0bzF7Q4","id":"508","armor":"5"}', '{"hash":"0zbwcKNU","id":"3","crit":"5","damage":"5"}', '{"hash":"teIYOKrr","id":"4","crit":"5","damage":"5"}', '{"hash":"wPcmdy2r","id":"4","crit":0,"damage":0}', '{"hash":"7MnY8syG","id":17,"crit":0,"damage":0}', '0', '0', '0', '0', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999', '999');

-- --------------------------------------------------------

--
-- Структура таблицы `user_potions`
--

CREATE TABLE IF NOT EXISTS `user_potions` (
  `id_user` int(10) unsigned NOT NULL,
  `potion1` varchar(255) DEFAULT '0',
  `potion2` varchar(255) DEFAULT '0',
  `potion3` varchar(255) DEFAULT '999',
  `potion4` varchar(255) DEFAULT '999',
  `potion5` varchar(255) DEFAULT '999',
  `potion6` varchar(255) DEFAULT '999',
  `potion7` varchar(255) DEFAULT '999',
  `potion8` varchar(255) DEFAULT '999',
  `potion9` varchar(255) DEFAULT '999',
  `potion10` varchar(255) DEFAULT '999',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_potions`
--

INSERT INTO `user_potions` (`id_user`, `potion1`, `potion2`, `potion3`, `potion4`, `potion5`, `potion6`, `potion7`, `potion8`, `potion9`, `potion10`) VALUES
(1, '{"id":1001,"image":"sandwich","count":8}', '0', '0', '0', '0', '0', '0', '999', '999', '999');

-- --------------------------------------------------------

--
-- Структура таблицы `user_resources`
--

CREATE TABLE IF NOT EXISTS `user_resources` (
  `id_user` int(10) unsigned NOT NULL,
  `Gold` int(10) unsigned NOT NULL DEFAULT '100',
  `Another` int(10) unsigned NOT NULL DEFAULT '0',
  `Donat` int(10) unsigned NOT NULL DEFAULT '0',
  `tournament_icon` int(10) unsigned NOT NULL DEFAULT '0',
  `network` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_resources`
--

INSERT INTO `user_resources` (`id_user`, `Gold`, `Another`, `Donat`, `tournament_icon`, `network`) VALUES
(1, 89420, 4534, 15, 32, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user_settings`
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `id_user` int(10) unsigned NOT NULL,
  `minLvl` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `maxLvl` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `mess_to_attacker` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `viewAllShop` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_settings`
--

INSERT INTO `user_settings` (`id_user`, `minLvl`, `maxLvl`, `mess_to_attacker`, `description`, `viewAllShop`) VALUES
(1, 5, 7, 'Нужно бооольше опыта!        ', '			Тут короче инфа такая, много, ооооочень много инфы.\n\nПосмотрим как оно себя вести будет.\n		', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user_statistic`
--

CREATE TABLE IF NOT EXISTS `user_statistic` (
  `id_user` int(10) unsigned NOT NULL,
  `user_statistic` text,
  `damage_statistic` text,
  `shop_statistic` text,
  `arena_bot_1` text,
  `arena_bot_2` text,
  `arena_bot_3` text,
  `arena_bot_4` text,
  `arena_bot_5` text,
  `all_bots` text,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_statistic`
--

INSERT INTO `user_statistic` (`id_user`, `user_statistic`, `damage_statistic`, `shop_statistic`, `arena_bot_1`, `arena_bot_2`, `arena_bot_3`, `arena_bot_4`, `arena_bot_5`, `all_bots`) VALUES
(1, 'a:5:{s:4:"wins";i:227;s:4:"lose";i:21;s:4:"draw";i:3;s:10:"goldStolen";d:139489;s:8:"goldLost";d:140559;}', 'a:10:{s:11:"damageDealt";d:30458;s:10:"critDamage";d:18226;s:4:"hits";i:359;s:5:"crits";i:145;s:6:"dodges";i:344;s:6:"misses";i:277;s:14:"damageReceived";d:3024;s:13:"damageBlocked";d:157.29239999999999;s:10:"secondHits";i:27;s:6:"Blocks";i:65;}', '{"Gold":1800,"Another":0,"equipment":1,"potions":9}', 'a:2:{i:0;a:6:{s:2:"id";i:1;s:4:"wins";i:18;s:4:"lose";i:7;s:4:"draw";i:5;s:9:"stoleGold";d:20;s:8:"lostGold";i:0;}i:1;a:6:{s:2:"id";i:2;s:4:"wins";i:17;s:4:"lose";i:4;s:4:"draw";i:4;s:9:"stoleGold";d:2301;s:8:"lostGold";i:0;}}', 'a:3:{i:0;a:6:{s:2:"id";i:3;s:4:"wins";i:56;s:4:"lose";i:2;s:4:"draw";i:2;s:9:"stoleGold";d:2047970;s:8:"lostGold";i:0;}i:1;a:6:{s:2:"id";i:4;s:4:"wins";i:13;s:4:"lose";i:0;s:4:"draw";i:0;s:9:"stoleGold";d:2301;s:8:"lostGold";i:0;}i:2;a:1:{s:4:"wins";i:1;}}', NULL, NULL, NULL, 'a:5:{s:4:"wins";i:120;s:4:"lose";i:22;s:4:"draw";i:0;s:9:"stoleGold";d:2050271;s:8:"lostGold";i:0;}');

-- --------------------------------------------------------

--
-- Структура таблицы `user_timers`
--

CREATE TABLE IF NOT EXISTS `user_timers` (
  `id_user` int(10) unsigned NOT NULL,
  `jobEnd` datetime(6) DEFAULT NULL,
  `last_Regen` datetime(6) DEFAULT NULL,
  `last_Advertising` datetime(6) DEFAULT NULL,
  `last_attack` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_timers`
--

INSERT INTO `user_timers` (`id_user`, `jobEnd`, `last_Regen`, `last_Advertising`, `last_attack`) VALUES
(1, NULL, NULL, NULL, NULL);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `advertisings`
--
ALTER TABLE `advertisings`
  ADD CONSTRAINT `advertising_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `advertising_to_section` FOREIGN KEY (`id_section`) REFERENCES `advertising_sections` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `clans_and_rates`
--
ALTER TABLE `clans_and_rates`
  ADD CONSTRAINT `clan_to_relation_rates` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `rate_to_raletion_rates` FOREIGN KEY (`id_rate`) REFERENCES `clan_rates` (`id_rate`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `clan_treasury`
--
ALTER TABLE `clan_treasury`
  ADD CONSTRAINT `treasury_to_clan` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `clan_treasury_logging`
--
ALTER TABLE `clan_treasury_logging`
  ADD CONSTRAINT `treasury_logging_to_clan` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `clan_workshop`
--
ALTER TABLE `clan_workshop`
  ADD CONSTRAINT `workshop_to_clan` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `agressor_to_users` FOREIGN KEY (`id_agresoor`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `defender_to_users` FOREIGN KEY (`id_defender`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `mail`
--
ALTER TABLE `mail`
  ADD CONSTRAINT `from_mail_to_users` FOREIGN KEY (`from`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `to_mail_to_users` FOREIGN KEY (`to`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `users_and_clans`
--
ALTER TABLE `users_and_clans`
  ADD CONSTRAINT `clan_to_relation_clans` FOREIGN KEY (`id_clan`) REFERENCES `clans` (`id_clan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_to_relation_clans` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `users_and_rates`
--
ALTER TABLE `users_and_rates`
  ADD CONSTRAINT `rate_to_relation_rate` FOREIGN KEY (`id_rate`) REFERENCES `clan_rates` (`id_rate`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_to_relation_rate` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_equipment`
--
ALTER TABLE `user_equipment`
  ADD CONSTRAINT `equipment_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_house`
--
ALTER TABLE `user_house`
  ADD CONSTRAINT `house_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_information`
--
ALTER TABLE `user_information`
  ADD CONSTRAINT `user_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_inventory`
--
ALTER TABLE `user_inventory`
  ADD CONSTRAINT `inventory_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_potions`
--
ALTER TABLE `user_potions`
  ADD CONSTRAINT `potions_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_resources`
--
ALTER TABLE `user_resources`
  ADD CONSTRAINT `resources_to_user` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `settings_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_statistic`
--
ALTER TABLE `user_statistic`
  ADD CONSTRAINT `statistic_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_timers`
--
ALTER TABLE `user_timers`
  ADD CONSTRAINT `timers_to_account` FOREIGN KEY (`id_user`) REFERENCES `accounts` (`id_account`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
