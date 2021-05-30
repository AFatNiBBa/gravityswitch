
-- [/!\]:  Questi commenti
-- [/!\]:  Sostituisci "\) ENGINE[^;]*;" con ");"
-- [/!\]:  Imposta "mappe.stile" come 'json'

-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 21, 2021 alle 21:01
-- Versione del server: 10.4.14-MariaDB
-- Versione PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `amicizie`
--

CREATE TABLE `amicizie` (
  `a` varchar(13) NOT NULL,
  `b` varchar(13) NOT NULL,
  `creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `attivo` bit(1) NOT NULL DEFAULT b'0'
);

-- --------------------------------------------------------

--
-- Struttura della tabella `blocchi`
--

CREATE TABLE `blocchi` (
  `mappa` int(11) NOT NULL,
  `vettore` int(11) NOT NULL,
  `num` int(11) NOT NULL
);

--
-- Dump dei dati per la tabella `blocchi`
--

INSERT INTO `blocchi` (`mappa`, `vettore`, `num`) VALUES
(1, 3, 0),
(1, 4, 1),
(1, 5, 2),
(2, 8, 0),
(2, 9, 2),
(2, 10, 1),
(2, 11, 3),
(2, 12, 5),
(2, 13, 4),
(3, 16, 1),
(3, 17, 4),
(3, 18, 0),
(3, 19, 2),
(3, 20, 5),
(3, 21, 3),
(3, 22, 7),
(3, 23, 11),
(3, 24, 8),
(3, 25, 6),
(3, 26, 10),
(3, 27, 9),
(3, 28, 12),
(3, 29, 13),
(3, 30, 14),
(4, 33, 0),
(4, 34, 1),
(4, 35, 2),
(4, 36, 5),
(4, 37, 3),
(4, 38, 4),
(4, 39, 6),
(4, 40, 7),
(4, 41, 8),
(4, 42, 9),
(5, 45, 0),
(5, 46, 3),
(5, 47, 2),
(5, 48, 1),
(5, 49, 5),
(5, 50, 4),
(5, 51, 6),
(5, 52, 7),
(5, 53, 8),
(5, 54, 9),
(5, 55, 11),
(5, 56, 10),
(5, 57, 12),
(5, 58, 13),
(6, 61, 0),
(6, 62, 2),
(6, 63, 1),
(6, 64, 5),
(6, 65, 4),
(6, 66, 3),
(6, 67, 6),
(6, 68, 7),
(6, 69, 8),
(6, 70, 9),
(6, 71, 10),
(6, 72, 11),
(6, 73, 14),
(6, 74, 12),
(6, 75, 13),
(7, 78, 1),
(7, 79, 0),
(7, 80, 2),
(7, 81, 5),
(7, 82, 4),
(7, 83, 3),
(7, 84, 6),
(7, 85, 8),
(7, 86, 7),
(7, 87, 9),
(7, 88, 10),
(7, 89, 11),
(7, 90, 12),
(7, 91, 13),
(7, 92, 16),
(7, 93, 17),
(7, 94, 18),
(7, 95, 15),
(7, 96, 14),
(7, 97, 19),
(7, 98, 22),
(7, 99, 20),
(7, 100, 21);

-- --------------------------------------------------------

--
-- Struttura della tabella `mappe`
--

CREATE TABLE `mappe` (
  `id` int(11) NOT NULL,
  `creatore` varchar(13) DEFAULT NULL,
  `giocatore` int(11) DEFAULT NULL,
  `traguardo` int(11) NOT NULL,
  `raggio` int(11) DEFAULT NULL,
  `stile` json NOT NULL
) ;

--
-- Dump dei dati per la tabella `mappe`
--

INSERT INTO `mappe` (`id`, `creatore`, `raggio`, `giocatore`, `traguardo`, `stile`) VALUES
(1, NULL, 15, 1, 2, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(2, NULL, 15, 6, 7, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(3, NULL, 15, 14, 15, '{\"rnd\":null,\"colors\":{\"end\":4036310,\"win\":5163440,\"base\":12945088,\"lose\":13724009,\"touch\":13208952,\"lights\":16777215,\"player\":{\"body\":14474410,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(4, NULL, 15, 31, 32, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(5, NULL, 15, 43, 44, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(6, NULL, 15, 59, 60, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}'),
(7, NULL, 15, 76, 77, '{\"rnd\":null,\"colors\":{\"end\":1193046,\"win\":255,\"lose\":16711680,\"base\":16763904,\"touch\":65280,\"lights\":16777215,\"player\":{\"body\":15658734,\"edge\":0}},\"lights\":[[-15,-15,-15],[15,15,15]]}');

-- --------------------------------------------------------

--
-- Struttura della tabella `partite`
--

CREATE TABLE `partite` (
  `id` int(11) NOT NULL,
  `utente` varchar(13) NOT NULL,
  `mappa` int(11) NOT NULL,
  `salti` int(11) NOT NULL,
  `morti` int(11) NOT NULL,
  `tempo` float NOT NULL,
  `creazione` timestamp NOT NULL DEFAULT current_timestamp()
);

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` varchar(13) NOT NULL,
  `nick` varchar(30) NOT NULL,
  `email` varchar(64) NOT NULL,
  `pass` varchar(60) NOT NULL,
  `creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `attivo` bit(1) NOT NULL DEFAULT b'0'
);

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `nick`, `email`, `pass`, `creazione`, `attivo`) VALUES
('1', 'AFatNiBBa', 'seanalunni@gmail.com', '$2y$10$GdN6wvMe9AMUIhk7ts9lF.VXu9Tb6QqZauT5K3FkbC6RHN4Bg1UEu', '2021-05-19 16:53:13', b'1');

-- --------------------------------------------------------

--
-- Struttura della tabella `vettori`
--

CREATE TABLE `vettori` (
  `id` int(11) NOT NULL,
  `x` float NOT NULL,
  `y` float NOT NULL,
  `z` float NOT NULL
);

--
-- Dump dei dati per la tabella `vettori`
--

INSERT INTO `vettori` (`id`, `x`, `y`, `z`) VALUES
(1, 0, 0, 0),
(2, -1, 4, 2),
(3, 2, 0, 0),
(4, 1, 4, 3),
(5, 1, 5, 0),
(6, 0, 0, 0),
(7, -1, 4, 2),
(8, 4, 0, 0),
(9, 3, 5, 6),
(10, 3, 0, 7),
(11, 0, 4, 6),
(12, -2, 4, -1),
(13, 1, 4, -2),
(14, 0, 0, 0),
(15, 5, 5, 5),
(16, 9, 0, -12),
(17, -9, -7, -11),
(18, 10, 0, 0),
(19, 9, -5, -11),
(20, -9, -6, 7),
(21, -10, -4, -11),
(22, -9, 3, 2),
(23, -3, -2, -8),
(24, -2, 3, 3),
(25, -9, 4, 6),
(26, -3, -3, -6),
(27, -3, 3, -7),
(28, -3, 2, 3),
(29, -3, 6, 2),
(30, 6, 5, 2),
(31, -12, 9, -13),
(32, 1, 4, -1),
(33, -1, 9, -13),
(34, -2, -11, -13),
(35, -8, -10, -13),
(36, 4, -10, -2),
(37, -7, -10, -5),
(38, 5, -10, -6),
(39, -4, -10, -3),
(40, -3, -10, 10),
(41, -3, 5, 9),
(42, -3, 4, -2),
(43, 14, -1, 0),
(44, 5, -1, 13),
(45, 14, 8, 0),
(46, 14, 4, 3),
(47, 14, 3, 7),
(48, 14, 7, 8),
(49, 4, 12, 4),
(50, 14, 13, 4),
(51, 5, 12, 2),
(52, 5, 7, 3),
(53, 5, 8, -8),
(54, 5, -14, -7),
(55, 10, 5, -7),
(56, 11, -13, -7),
(57, 10, 4, 14),
(58, 10, -2, 13),
(59, 2, 0, -2),
(60, 4, -4, -8),
(61, 14, 0, -2),
(62, 13, 2, 10),
(63, 13, 0, 11),
(64, -8, 7, -3),
(65, -8, 1, -4),
(66, -9, 1, 10),
(67, -8, 6, -5),
(68, 9, 6, -4),
(69, 8, 6, 11),
(70, 8, -11, 10),
(71, 8, -10, 0),
(72, 8, 10, 1),
(73, 3, -4, -13),
(74, 8, 9, -14),
(75, 8, -5, -13),
(76, -4, -5, 7),
(77, 2, -13, -4),
(78, 0, -5, 5),
(79, -4, -5, 4),
(80, -1, 9, 5),
(81, -11, 8, -5),
(82, -12, 8, 1),
(83, -1, 8, 0),
(84, -11, -8, -4),
(85, -11, 5, -7),
(86, -11, -7, -8),
(87, -11, 4, -13),
(88, -11, -12, -12),
(89, -11, -11, 2),
(90, -11, -7, 1),
(91, -11, -8, 12),
(92, 11, -8, 6),
(93, 10, 7, 6),
(94, 10, 6, 1),
(95, 7, -8, 5),
(96, 8, -8, 11),
(97, 10, 13, 2),
(98, 10, -13, -5),
(99, 10, 12, 14),
(100, 10, -14, 13);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `amicizie`
--
ALTER TABLE `amicizie`
  ADD PRIMARY KEY (`a`,`b`),
  ADD KEY `b` (`b`);

--
-- Indici per le tabelle `blocchi`
--
ALTER TABLE `blocchi`
  ADD PRIMARY KEY (`mappa`,`vettore`),
  ADD KEY `vettore` (`vettore`);

--
-- Indici per le tabelle `mappe`
--
ALTER TABLE `mappe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creatore` (`creatore`),
  ADD KEY `giocatore` (`giocatore`),
  ADD KEY `traguardo` (`traguardo`);

--
-- Indici per le tabelle `partite`
--
ALTER TABLE `partite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappa` (`mappa`),
  ADD KEY `utente` (`utente`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `vettori`
--
ALTER TABLE `vettori`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `mappe`
--
ALTER TABLE `mappe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `partite`
--
ALTER TABLE `partite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `vettori`
--
ALTER TABLE `vettori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `amicizie`
--
ALTER TABLE `amicizie`
  ADD CONSTRAINT `amicizie_ibfk_1` FOREIGN KEY (`a`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `amicizie_ibfk_2` FOREIGN KEY (`b`) REFERENCES `utenti` (`id`);

--
-- Limiti per la tabella `blocchi`
--
ALTER TABLE `blocchi`
  ADD CONSTRAINT `blocchi_ibfk_1` FOREIGN KEY (`mappa`) REFERENCES `mappe` (`id`),
  ADD CONSTRAINT `blocchi_ibfk_2` FOREIGN KEY (`vettore`) REFERENCES `vettori` (`id`);

--
-- Limiti per la tabella `mappe`
--
ALTER TABLE `mappe`
  ADD CONSTRAINT `mappe_ibfk_1` FOREIGN KEY (`creatore`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `mappe_ibfk_2` FOREIGN KEY (`giocatore`) REFERENCES `vettori` (`id`),
  ADD CONSTRAINT `mappe_ibfk_3` FOREIGN KEY (`traguardo`) REFERENCES `vettori` (`id`);

--
-- Limiti per la tabella `partite`
--
ALTER TABLE `partite`
  ADD CONSTRAINT `partite_ibfk_1` FOREIGN KEY (`mappa`) REFERENCES `mappe` (`id`),
  ADD CONSTRAINT `partite_ibfk_2` FOREIGN KEY (`utente`) REFERENCES `utenti` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
