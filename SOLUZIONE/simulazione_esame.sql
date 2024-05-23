-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 23, 2024 alle 12:03
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simulazione_esame`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `nome` varchar(32) NOT NULL,
  `cognome` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `privilegi_admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `admin`
--

INSERT INTO `admin` (`ID`, `username`, `password`, `nome`, `cognome`, `email`, `privilegi_admin`) VALUES
(1, '.admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin', 'admin', 'admin.admin@admin.admin', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `bicicletta`
--

DROP TABLE IF EXISTS `bicicletta`;
CREATE TABLE `bicicletta` (
  `ID` int(11) NOT NULL,
  `codice` varchar(16) NOT NULL,
  `id_stazione` int(11) DEFAULT NULL,
  `manutenzione` tinyint(1) NOT NULL,
  `GPS` varchar(32) NOT NULL,
  `RFID` varchar(32) NOT NULL,
  `kmEffettuati` int(64) NOT NULL,
  `id_posizione` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `bicicletta`
--

INSERT INTO `bicicletta` (`ID`, `codice`, `id_stazione`, `manutenzione`, `GPS`, `RFID`, `kmEffettuati`, `id_posizione`) VALUES
(17, 'B000001', 4, 1, 'GPS12342134', 'RFID12342134', 220, 13),
(18, 'B000002', 9, 0, 'GPS21345', 'RFID2345', 150, 18),
(19, 'B000003', 4, 0, 'GPS324213', 'RFID2435', 80, 13),
(20, 'B000004', 4, 0, 'GPS1234', 'RFID23456', 64, 13);

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `ID` int(11) NOT NULL,
  `nome` varchar(32) NOT NULL,
  `cognome` varchar(32) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `id_indirizzo` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `numeroCartaCredito` varchar(19) NOT NULL,
  `numeroTessera` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cliente`
--

INSERT INTO `cliente` (`ID`, `nome`, `cognome`, `username`, `password`, `id_indirizzo`, `email`, `numeroCartaCredito`, `numeroTessera`) VALUES
(3, 'ajeje', 'brazorf', 'a_b', 'af26ae04a962399d2758055d4f09570dcd519ae725c8a28ba6c61e6b57550c75', 10, 'aje_braz@mail.com', '9786-1324-7564-3546', '0000000'),
(13, 'Amedeo', 'Fumagalli', 'ame_fuma', '4d0782767987d11e8aaa1f07a5be55eae043c714e02d872ada52875a9b611be7', 11, 'ame.fuma@mail.com', '0909-1212-5454-8989', '0000001'),
(29, 'Asd', 'Asd', 'asd_asd', '688787d8ff144c502c7f5cffaafe2cc588d86079f9de88304c26b0cb99ce91c6', 13, 'asd@asd.asd', '1234-5678-9012-3456', '0000002'),
(34, 'Pietro', 'Brandovardi', 'brandu_', 'd07ee7e529af02ace472e74ef4be1bd92f3604f6c3a5b11602aad4496161ecb3', 9, 'brandovardipietro@outlook.it', '1231-2312-3123-1231', '0000003');

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzo`
--

DROP TABLE IF EXISTS `indirizzo`;
CREATE TABLE `indirizzo` (
  `ID` int(11) NOT NULL,
  `regione` varchar(64) NOT NULL,
  `provincia` varchar(64) NOT NULL,
  `comune` varchar(64) NOT NULL,
  `cap` int(11) NOT NULL,
  `via` varchar(64) NOT NULL,
  `numeroCivico` int(11) NOT NULL,
  `latitudine` double NOT NULL,
  `longitudine` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `indirizzo`
--

INSERT INTO `indirizzo` (`ID`, `regione`, `provincia`, `comune`, `cap`, `via`, `numeroCivico`, `latitudine`, `longitudine`) VALUES
(9, 'Lombardia', 'CO', 'Cantù', 22063, 'ettore brambilla', 34, 45.7430842, 9.1314225),
(10, 'Lombardia', 'MI', 'Milano', 20121, 'Via Montenapoleone', 1, 45.469, 9.19),
(11, 'Lazio', 'RM', 'Roma', 184, 'Via dei Fori Imperiali', 3, 41.892, 12.485),
(12, 'Toscana', 'FI', 'Firenze', 50122, 'Via de Calzaiuoli', 5, 43.771, 11.256),
(13, 'Veneto', 'VE', 'Venezia', 30124, 'San Marco', 45, 45.434, 12.339),
(14, 'Campania', 'NA', 'Napoli', 80133, 'Via Toledo', 23, 40.842, 14.247),
(15, 'Piemonte', 'TO', 'Torino', 10121, 'Via Roma', 17, 45.07, 7.686),
(16, 'Sicilia', 'PA', 'Palermo', 90133, 'Via Maqueda', 8, 38.115, 13.361),
(17, 'Emilia-Romagna', 'BO', 'Bologna', 40125, 'Via Zamboni', 33, 44.496, 11.352),
(18, 'Liguria', 'GE', 'Genova', 16121, 'Via XX Settembre', 20, 44.41, 8.932),
(19, 'Puglia', 'BA', 'Bari', 70122, 'Corso Vittorio Emanuele II', 34, 41.125, 16.869),
(20, 'Campania', 'NA', 'Napoli', 80133, 'Via Toledo', 12, 40.8416069, 14.2487433),
(21, 'Emilia-Romagna', 'FE', 'Ferrara', 44121, 'Corso della Giovecca', 1, 44.8379809, 11.6205086),
(22, 'Puglia', 'BA', 'Bari', 70121, 'Via Martin Luther King', 9, 41.1005719, 16.8588472);

-- --------------------------------------------------------

--
-- Struttura della tabella `operazione`
--

DROP TABLE IF EXISTS `operazione`;
CREATE TABLE `operazione` (
  `ID` int(11) NOT NULL,
  `idCliente` int(11) NOT NULL,
  `idBicicletta` int(11) NOT NULL,
  `idStazione` int(11) NOT NULL,
  `tipo` enum('noleggio','riconsegna') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `tariffa` float DEFAULT NULL,
  `kmEffettuati` int(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `operazione`
--

INSERT INTO `operazione` (`ID`, `idCliente`, `idBicicletta`, `idStazione`, `tipo`, `timestamp`, `tariffa`, `kmEffettuati`) VALUES
(51, 13, 17, 5, 'noleggio', '2023-05-01 06:00:00', NULL, NULL),
(52, 13, 17, 7, 'riconsegna', '2023-05-01 07:00:00', 5, 15),
(53, 29, 18, 3, 'noleggio', '2023-05-02 08:30:00', NULL, NULL),
(54, 29, 18, 6, 'riconsegna', '2023-05-02 09:45:00', 6.5, 20),
(55, 3, 17, 4, 'noleggio', '2023-05-03 05:15:00', NULL, NULL),
(56, 3, 17, 6, 'riconsegna', '2023-05-03 06:30:00', 7.25, 10),
(57, 34, 19, 4, 'noleggio', '2023-05-04 07:00:00', NULL, NULL),
(58, 34, 19, 7, 'riconsegna', '2023-05-04 08:00:00', 4.75, 12),
(59, 34, 20, 5, 'noleggio', '2023-05-05 04:45:00', NULL, NULL),
(60, 34, 20, 3, 'riconsegna', '2023-05-05 05:30:00', 5.5, 8);

-- --------------------------------------------------------

--
-- Struttura della tabella `stazione`
--

DROP TABLE IF EXISTS `stazione`;
CREATE TABLE `stazione` (
  `ID` int(11) NOT NULL,
  `codice` int(11) NOT NULL,
  `id_indirizzo` int(11) NOT NULL,
  `numero_slot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `stazione`
--

INSERT INTO `stazione` (`ID`, `codice`, `id_indirizzo`, `numero_slot`) VALUES
(3, 1003, 12, 12),
(4, 1004, 13, 8),
(5, 1005, 20, 20),
(6, 1006, 15, 25),
(7, 1007, 16, 10),
(9, 1009, 18, 14),
(10, 1010, 19, 16),
(12, 1011, 21, 40),
(13, 1012, 22, 26);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indici per le tabelle `bicicletta`
--
ALTER TABLE `bicicletta`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `codice` (`codice`),
  ADD UNIQUE KEY `GPS` (`GPS`),
  ADD UNIQUE KEY `RFID` (`RFID`),
  ADD KEY `id_stazione` (`id_stazione`),
  ADD KEY `id_posizione` (`id_posizione`);

--
-- Indici per le tabelle `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`,`numeroTessera`),
  ADD KEY `id_indirizzo` (`id_indirizzo`);

--
-- Indici per le tabelle `indirizzo`
--
ALTER TABLE `indirizzo`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `operazione`
--
ALTER TABLE `operazione`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idCliente` (`idCliente`,`idBicicletta`),
  ADD KEY `idBicicletta` (`idBicicletta`),
  ADD KEY `idStazionePartenza` (`idStazione`);

--
-- Indici per le tabelle `stazione`
--
ALTER TABLE `stazione`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `codice` (`codice`),
  ADD KEY `id_indirizzo` (`id_indirizzo`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `bicicletta`
--
ALTER TABLE `bicicletta`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `cliente`
--
ALTER TABLE `cliente`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT per la tabella `indirizzo`
--
ALTER TABLE `indirizzo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT per la tabella `operazione`
--
ALTER TABLE `operazione`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT per la tabella `stazione`
--
ALTER TABLE `stazione`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `bicicletta`
--
ALTER TABLE `bicicletta`
  ADD CONSTRAINT `bicicletta_ibfk_1` FOREIGN KEY (`id_stazione`) REFERENCES `stazione` (`ID`),
  ADD CONSTRAINT `bicicletta_ibfk_2` FOREIGN KEY (`id_posizione`) REFERENCES `indirizzo` (`ID`);

--
-- Limiti per la tabella `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_indirizzo`) REFERENCES `indirizzo` (`ID`);

--
-- Limiti per la tabella `operazione`
--
ALTER TABLE `operazione`
  ADD CONSTRAINT `operazione_ibfk_2` FOREIGN KEY (`idBicicletta`) REFERENCES `bicicletta` (`ID`),
  ADD CONSTRAINT `operazione_ibfk_3` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`ID`),
  ADD CONSTRAINT `operazione_ibfk_4` FOREIGN KEY (`idStazione`) REFERENCES `stazione` (`ID`);

--
-- Limiti per la tabella `stazione`
--
ALTER TABLE `stazione`
  ADD CONSTRAINT `stazione_ibfk_1` FOREIGN KEY (`id_indirizzo`) REFERENCES `indirizzo` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
