-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 10, 2021 at 09:38 PM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ludicamente`
--

-- --------------------------------------------------------

--
-- Table structure for table `ciclope_actividad`
--

DROP TABLE IF EXISTS `ciclope_actividad`;
CREATE TABLE `ciclope_actividad` (
  `id` int(11) NOT NULL,
  `juego` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `imagen_activo` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `texto_activo` text CHARACTER SET utf8 COLLATE utf8_bin,
  `texto_inactivo` text CHARACTER SET utf8 COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ciclope_juego`
--

DROP TABLE IF EXISTS `ciclope_juego`;
CREATE TABLE `ciclope_juego` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `hash` varchar(20) NOT NULL,
  `texto_espera` text CHARACTER SET utf8 COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ciclope_participante`
--

DROP TABLE IF EXISTS `ciclope_participante`;
CREATE TABLE `ciclope_participante` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `participa` tinyint(4) NOT NULL,
  `juego` int(11) NOT NULL,
  `activo` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `login_dttm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `username` varchar(70) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pass_hash` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ciclope_actividad`
--
ALTER TABLE `ciclope_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actividad_juego` (`juego`);

--
-- Indexes for table `ciclope_juego`
--
ALTER TABLE `ciclope_juego`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ciclope_participante`
--
ALTER TABLE `ciclope_participante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participante_juego` (`juego`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_usuario` (`usuario`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ciclope_actividad`
--
ALTER TABLE `ciclope_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ciclope_juego`
--
ALTER TABLE `ciclope_juego`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ciclope_participante`
--
ALTER TABLE `ciclope_participante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ciclope_actividad`
--
ALTER TABLE `ciclope_actividad`
  ADD CONSTRAINT `actividad_juego` FOREIGN KEY (`juego`) REFERENCES `ciclope_juego` (`id`);

--
-- Constraints for table `ciclope_participante`
--
ALTER TABLE `ciclope_participante`
  ADD CONSTRAINT `participante_juego` FOREIGN KEY (`juego`) REFERENCES `ciclope_juego` (`id`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
