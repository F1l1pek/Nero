-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 11. dub 2024, 22:49
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
-- Struktura tabulky `obednavky`
--

CREATE TABLE `obednavky` (
  `ID_O` int(11) NOT NULL,
  `datum` date NOT NULL,
  `ID_U` int(11) NOT NULL,
  `stav` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `obednavky`
--

INSERT INTO `obednavky` (`ID_O`, `datum`, `ID_U`, `stav`) VALUES
(92, '2024-04-11', 1, 'zaplaceno'),
(93, '2024-04-11', 1, 'zaplaceno'),
(94, '2024-04-11', 1, 'zaplaceno'),
(95, '2024-04-11', 1, 'zaplaceno'),
(96, '2024-04-11', 1, 'zaplaceno'),
(97, '2024-04-11', 1, 'zaplaceno'),
(98, '2024-04-11', 1, 'zaplaceno'),
(99, '2024-04-11', 1, 'zaplaceno'),
(100, '2024-04-11', 1, 'zaplaceno'),
(101, '2024-04-11', 1, 'zaplaceno'),
(102, '2024-04-11', 1, 'zaplaceno'),
(103, '2024-04-11', 1, 'zaplaceno'),
(104, '2024-04-11', 1, 'zaplaceno'),
(105, '2024-04-11', 1, 'zaplaceno'),
(106, '2024-04-11', 1, 'zaplaceno'),
(107, '2024-04-11', 1, 'zaplaceno'),
(108, '2024-04-11', 1, 'zaplaceno'),
(109, '2024-04-11', 1, 'zaplaceno'),
(110, '2024-04-11', 1, 'zaplaceno'),
(111, '2024-04-11', 1, 'zaplaceno'),
(112, '2024-04-11', 1, 'zaplaceno');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `obednavky`
--
ALTER TABLE `obednavky`
  ADD PRIMARY KEY (`ID_O`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `obednavky`
--
ALTER TABLE `obednavky`
  MODIFY `ID_O` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
