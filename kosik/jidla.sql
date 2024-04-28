-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 02. dub 2024, 10:05
-- Verze serveru: 10.4.28-MariaDB
-- Verze PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `nero`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `jidla`
--

CREATE TABLE `jidla` (
  `ID_jidla` int(11) NOT NULL,
  `název` text NOT NULL,
  `typ` text NOT NULL,
  `cena` int(11) NOT NULL,
  `cena_s` int(11) NOT NULL,
  `cena_f` int(11) NOT NULL,
  `popis` text NOT NULL,
  `img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `jidla`
--

INSERT INTO `jidla` (`ID_jidla`, `název`, `typ`, `cena`, `cena_s`, `cena_f`, `popis`, `img`) VALUES
(1, 'Bageta Posh Cheddar ', 'Bagety', 59, 56, 53, 'Irský Chreddar, rajče, červená  cibule, polníček, majonéza', 'IMG_20230109_202339_377.jpg'),
(2, 'Bageta B.L.T.', 'Bagety', 59, 56, 53, 'slanina, rajče, ledový salát, majonéza', 'IMG_20230909_080527_963.jpg'),
(3, 'Kaiserka s vaječnou omeletou', 'Kaiserky', 35, 32, 29, 'vaječná omeleta , rajče, salát, domácí dresink', 'IMG_20230428_092643_363.jpg'),
(4, 'Chlebíček Caprese', 'Chlebíčky', 45, 42, 39, 'Mozzarella, rajče, salát, bazalkové pesto , vícezrný chléb', 'IMG_20230428_092643_363.jpg'),
(5, 'Nero Chlebíček', 'Chlebíčky', 33, 30, 27, 'bramborový salát, salám, šunka, vařené vejce, kyselá okurka, kapie, veka', 'IMG_20230428_092643_363.jpg');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `jidla`
--
ALTER TABLE `jidla`
  ADD PRIMARY KEY (`ID_jidla`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `jidla`
--
ALTER TABLE `jidla`
  MODIFY `ID_jidla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
