-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 11. dub 2024, 22:23
-- Verze serveru: 10.4.27-MariaDB
-- Verze PHP: 8.2.0

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
-- Struktura tabulky `obrazky`
--

CREATE TABLE `obrazky` (
  `id` int(11) NOT NULL,
  `obrazek` varchar(50) NOT NULL,
  `nazev` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `obrazky`
--

INSERT INTO `obrazky` (`id`, `obrazek`, `nazev`) VALUES
(1, '20191211-IMG_4585.jpg', 'nevim'),
(2, '20200925-IMG_5714.jpg', 'idk'),
(3, '20230322_120001.jpg', 'jmeno');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `obrazky`
--
ALTER TABLE `obrazky`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `obrazky`
--
ALTER TABLE `obrazky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
