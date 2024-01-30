-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Erstellungszeit: 04. Jan 2024 um 13:43
-- Server-Version: 10.4.27-MariaDB
-- PHP-Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `alumni_dev`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alumni_data`
--

CREATE TABLE `alumni_data` (
  `id` int(11) NOT NULL,
  `name` varchar(500) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `studies` varchar(1000) DEFAULT NULL,
  `job` varchar(500) DEFAULT NULL,
  `company` varchar(500) DEFAULT NULL,
  `date_registered` int(20) DEFAULT NULL,
  `transfer_privacy` int(1) DEFAULT NULL,
  `date_transfer_privacy_agreed` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alumni_sessions`
--

CREATE TABLE `alumni_sessions` (
  `userid` int(20) DEFAULT NULL,
  `session_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alumni_users`
--

CREATE TABLE `alumni_users` (
  `username` varchar(500) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `password` varchar(500) DEFAULT NULL,
  `2fa` int(1) DEFAULT NULL,
  `secret` varchar(50) DEFAULT NULL,
  `userid` int(20) NOT NULL,
  `last_login` int(50) DEFAULT NULL,
  `login_tries` int(2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Ersten User erstellen
--
INSERT INTO `alumni_users` (`username`, `email`, `password`, `2fa`, `secret`, `userid`, `last_login`, `login_tries`) VALUES ('admin', 'admin@example.org', '$2y$10$7rGATLyH0QPU4WgGSphYResfMmKRp/.Y9G291uW.aw/21lVGx7vGW', '0', NULL, '20', NULL, '0');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `alumni_data`
--
ALTER TABLE `alumni_data`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `alumni_users`
--
ALTER TABLE `alumni_users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `alumni_data`
--
ALTER TABLE `alumni_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `alumni_users`
--
ALTER TABLE `alumni_users`
  MODIFY `userid` int(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
