-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Ned 07. dub 2024, 17:43
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
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `ID_user` int(11) NOT NULL,
  `jmeno` varchar(50) NOT NULL,
  `prijmeni` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `heslo` varchar(100) NOT NULL,
  `dat_nar` date NOT NULL,
  `tel_cislo` int(9) DEFAULT NULL,
  `typ_uzivatele` varchar(20) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`ID_user`, `jmeno`, `prijmeni`, `email`, `heslo`, `dat_nar`, `tel_cislo`, `typ_uzivatele`, `reset_token`) VALUES
(15, 'Filip', 'Górka', 'F.gorka43@gmail.com', '$2y$10$mjRxbMpZjx9QP1pPF8A/C.JyFYj/dqfi5VLvGVrPmoKXiHwrpE.MG', '2006-01-01', 732340000, 'admin', NULL),
(16, 'Matyáš', 'Dětský', 'm.detsky.st@spseiostrava.cz', '$2y$10$eCwHqCAapwM0wGRyrh6wbOrz8u0gP4E40QsOG6kZ0N8SroSJxC5Z.', '2000-01-01', 0, 'velkoodberatel', NULL),
(17, 'Jakub', 'Kichner', 'j.kichner.st@spseiostrava.cz', '$2y$10$a8JlN1bIdDbJUK999Pc2NuLaEkGGK89U9GTFU/fbFkE/7SbSoLIhG', '2000-01-01', 0, 'běžný', NULL),
(18, 'Ondřej', 'Dron', 'dron@gmail.com', '$2y$10$l8Y6RGesBFFhFXwqEUXUuOC8S6OW9NCNU1xujnu.YxPg7TwAr8Jt6', '2024-04-01', 0, 'běžný', NULL),
(19, 'ADMIN', 'ADMINE', 'admin@gmail.com', '$2y$10$Ck1RmY5PLxbGL22yEQdJ.uVyAxyqfenLSioawaQB3hHt.X5aERhPK', '2024-04-01', 732340000, 'admin', NULL);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `ID_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
