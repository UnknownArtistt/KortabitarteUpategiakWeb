-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-12-2024 a las 08:46:10
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdweb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produktuak`
--

CREATE TABLE `produktuak` (
  `id` int(11) NOT NULL,
  `izena` varchar(30) NOT NULL,
  `deskripzioa` varchar(150) NOT NULL,
  `salneurria` float NOT NULL,
  `pic` varchar(60) NOT NULL,
  `stock` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `produktuak`
--

INSERT INTO `produktuak` (`id`, `izena`, `deskripzioa`, `salneurria`, `pic`, `stock`) VALUES
(2, 'produktu 2', 'produktu 2ren deskripzioa test', 12, 'generikoa.jpg', 7),
(4, 'produktu 3', 'hirugarrena da, etiketa barik', 8, 'producto3.jpg', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` mediumint(9) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `izena` varchar(30) NOT NULL,
  `abizena` varchar(60) NOT NULL,
  `hiria` varchar(60) NOT NULL,
  `lurraldea` varchar(60) NOT NULL,
  `herrialdea` varchar(30) NOT NULL,
  `postakodea` int(5) NOT NULL,
  `telefonoa` int(9) NOT NULL,
  `irudia` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `izena`, `abizena`, `hiria`, `lurraldea`, `herrialdea`, `postakodea`, `telefonoa`, `irudia`) VALUES
(1, 'admin@bdweb', '$2a$12$t338czPUltRZTTXrEl6F8OVispTCnJw/MNL/70ESwQ/6uX91Kz3xS', 'admin', '', 'eibar', 'gipuzkoa', 'euskal herria', 20600, 121212121, 'Soy_Admin.png'),
(2, 'izaskun@uni.eus', '5f4dcc3b5aa765d61d8327deb882cf99', 'izaskun', 'ffff', 'ss', 'ddd', 'España', 48280, 121232343, 'usuario.jpg'),
(6, 'herrero.julen@uni.eus', '$2y$10$Z9KXhVcZ6px0jDzq2VsOXOEdFP/NwYbKOBwJw3CdVP4xrqKEsp53C', 'Julen', 'Herrero', 'Osintxu Hiria', 'Osintxu', 'Osintxu', 11111, 666777888, '675039bfb14f0_abysswatchers2.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `produktuak`
--
ALTER TABLE `produktuak`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `produktuak`
--
ALTER TABLE `produktuak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
