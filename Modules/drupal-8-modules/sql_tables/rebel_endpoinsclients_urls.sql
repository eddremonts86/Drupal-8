-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 22-03-2017 a las 13:44:12
-- Versión del servidor: 5.7.17-0ubuntu0.16.04.1
-- Versión de PHP: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `drupal_dominios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `domcl_rebel_endpoinsclients_urls`
--

CREATE TABLE `rebel_endpoinsclients_urls` (
  `urlid` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `domcl_rebel_endpoinsclients_urls`
--

INSERT INTO `rebel_endpoinsclients_urls` (`urlid`, `type`, `url`) VALUES
(26, 'alluser', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(27, 'node', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(28, 'onerevisions', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(29, 'allnodes', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(30, 'revisions', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(31, 'allalias', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(32, 'aliasbyid', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(33, 'alltaxonomy', 'dominios.br:81/rpendp/getnode.json?nid=2754'),
(34, 'allctypes', 'dominios.br:81/rpendp/getnode.json?nid=2754');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `domcl_rebel_endpoinsclients_urls`
--
ALTER TABLE `rebel_endpoinsclients_urls`
  ADD PRIMARY KEY (`urlid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `domcl_rebel_endpoinsclients_urls`
--
ALTER TABLE `rebel_endpoinsclients_urls`
  MODIFY `urlid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
