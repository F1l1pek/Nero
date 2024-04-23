-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 09. dub 2024, 13:59
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
-- Struktura tabulky `svatby`
--

CREATE TABLE `svatby` (
  `id` int(11) NOT NULL,
  `nazev` varchar(20) NOT NULL,
  `cena` int(11) NOT NULL,
  `popis` varchar(500) NOT NULL,
  `obrazek` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `svatby`
--

INSERT INTO `svatby` (`id`, `nazev`, `cena`, `popis`, `obrazek`) VALUES
(1, 'Nevim', 69420, 'popis svatby', 'received_2791426547596365.jpeg'),
(2, 'idk', 42069, 'nějaký zajímavý popis', 'WhatsApp Image 2024-03-16 at 20.37.07.jpeg'),
(3, 'nazev2', 64209, 'popis svatby 3', 'WhatsApp Image 2024-03-16 at 20.48.33.jpeg');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `svatby`
--
ALTER TABLE `svatby`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `svatby`
--
ALTER TABLE `svatby`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
