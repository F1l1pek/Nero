-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 02. dub 2024, 13:55
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
-- Struktura tabulky `košik`
--

CREATE TABLE `košik` (
  `ID_O` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `ID_J` int(11) NOT NULL,
  `mnozstvi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `košik`
--

INSERT INTO `košik` (`ID_O`, `ID_U`, `ID_J`, `mnozstvi`) VALUES
(1, 1, 2, 7),
(2, 1, 5, 6),
(3, 1, 4, 12),
(4, 1, 2, 8),
(25, 1, 4, 8);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `košik`
--
ALTER TABLE `košik`
  ADD PRIMARY KEY (`ID_O`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `košik`
--
ALTER TABLE `košik`
  MODIFY `ID_O` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
