-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Lug 29, 2016 alle 16:11
-- Versione del server: 10.1.10-MariaDB
-- Versione PHP: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testrest_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Username` varchar(25) NOT NULL,
  `Nome` varchar(25) NOT NULL,
  `Cognome` varchar(30) NOT NULL,
  `Birthday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `Username`, `Nome`, `Cognome`, `Birthday`) VALUES
(23, 'MrRobot85', 'Alessio', 'Moriconi', '1985-08-27'),
(24, 'PR1987', 'Paola', 'Rossi', '1987-02-20'),
(25, 'Frollo67', 'Francesco', 'Lollo', '1967-02-23'),
(26, 'Corso89', 'Vito', 'Corsini', '1989-01-31'),
(27, 'Poster78', 'Piero', 'Gallo', '1978-04-23'),
(34, 'Amnesia92', 'Anna', 'Verdi', '1992-04-23'),
(35, 'Lorenzino87', 'Lorenzo', 'Siino', '1987-06-12'),
(36, 'LexLutor90', 'Alessandro', 'Rettori', '1990-06-05'),
(37, 'Superman56', 'Giuseppe', 'Marini', '1956-06-23'),
(38, 'KosaKKo78', 'Vladimiro', 'Artemisi', '1978-10-23');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
