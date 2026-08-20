-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-08-2026 a las 18:45:06
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
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casa`
--

CREATE TABLE `casa` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratista`
--

CREATE TABLE `contratista` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destino`
--

CREATE TABLE `destino` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `manzana`
--

CREATE TABLE `manzana` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `ID` int(10) NOT NULL,
  `Codigo` int(10) DEFAULT NULL,
  `Descripcion` varchar(100) DEFAULT NULL,
  `Unidad` varchar(30) DEFAULT NULL,
  `Saldo` int(10) UNSIGNED DEFAULT 0,
  `Min_Almacen` int(11) NOT NULL DEFAULT 0,
  `Max_Casa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_registro_entradas`
--

CREATE TABLE `material_registro_entradas` (
  `ID` int(10) NOT NULL,
  `MaterialID` int(10) NOT NULL,
  `Registro_EntradasID` int(10) NOT NULL,
  `Cantidad` int(10) DEFAULT NULL,
  `DestinoID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_registro_salidas`
--

CREATE TABLE `material_registro_salidas` (
  `ID` int(10) NOT NULL,
  `MaterialID` int(10) NOT NULL,
  `Registro_SalidasID` int(10) NOT NULL,
  `Cantidad` int(10) DEFAULT NULL,
  `CasaID` int(10) NOT NULL,
  `ManzanaID` int(10) NOT NULL,
  `DestinoID` int(10) NOT NULL,
  `AreaID` int(10) NOT NULL,
  `RubroID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_entradas`
--

CREATE TABLE `registro_entradas` (
  `ID` int(10) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `UsuarioID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_salidas`
--

CREATE TABLE `registro_salidas` (
  `ID` int(10) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `UsuarioID` int(10) NOT NULL,
  `ContratistaID` int(10) NOT NULL,
  `ProyectoID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubro`
--

CREATE TABLE `rubro` (
  `ID` int(10) NOT NULL,
  `Descripcion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID` int(10) NOT NULL,
  `Identificacion` int(10) NOT NULL,
  `Nombre` varchar(30) DEFAULT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  `Clave` varchar(10) DEFAULT NULL,
  `RolID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `casa`
--
ALTER TABLE `casa`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `contratista`
--
ALTER TABLE `contratista`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `destino`
--
ALTER TABLE `destino`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `manzana`
--
ALTER TABLE `manzana`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `material_registro_entradas`
--
ALTER TABLE `material_registro_entradas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FKMaterial_R356246` (`MaterialID`),
  ADD KEY `FKMaterial_R660572` (`Registro_EntradasID`),
  ADD KEY `DestinoID` (`DestinoID`);

--
-- Indices de la tabla `material_registro_salidas`
--
ALTER TABLE `material_registro_salidas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FKMaterial_R456168` (`MaterialID`),
  ADD KEY `FKMaterial_R116924` (`Registro_SalidasID`),
  ADD KEY `FKMaterial_R349492` (`DestinoID`),
  ADD KEY `FKMaterial_R249940` (`AreaID`),
  ADD KEY `FKMaterial_R690450` (`ManzanaID`),
  ADD KEY `FKMaterial_R269180` (`CasaID`),
  ADD KEY `RubroID` (`RubroID`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `registro_entradas`
--
ALTER TABLE `registro_entradas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FKRegistro_E578564` (`UsuarioID`);

--
-- Indices de la tabla `registro_salidas`
--
ALTER TABLE `registro_salidas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FKRegistro_S59635` (`UsuarioID`),
  ADD KEY `FKRegistro_S536051` (`ContratistaID`),
  ADD KEY `FKRegistro_S45085` (`ProyectoID`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `rubro`
--
ALTER TABLE `rubro`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FKUsuario36461` (`RolID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `casa`
--
ALTER TABLE `casa`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratista`
--
ALTER TABLE `contratista`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `destino`
--
ALTER TABLE `destino`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `manzana`
--
ALTER TABLE `manzana`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material_registro_entradas`
--
ALTER TABLE `material_registro_entradas`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material_registro_salidas`
--
ALTER TABLE `material_registro_salidas`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_entradas`
--
ALTER TABLE `registro_entradas`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_salidas`
--
ALTER TABLE `registro_salidas`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rubro`
--
ALTER TABLE `rubro`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `material_registro_entradas`
--
ALTER TABLE `material_registro_entradas`
  ADD CONSTRAINT `FKMaterial_R356246` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  ADD CONSTRAINT `FKMaterial_R660572` FOREIGN KEY (`Registro_EntradasID`) REFERENCES `registro_entradas` (`ID`),
  ADD CONSTRAINT `material_registro_entradas_ibfk_1` FOREIGN KEY (`DestinoID`) REFERENCES `destino` (`ID`);

--
-- Filtros para la tabla `material_registro_salidas`
--
ALTER TABLE `material_registro_salidas`
  ADD CONSTRAINT `FKMaterial_R116924` FOREIGN KEY (`Registro_SalidasID`) REFERENCES `registro_salidas` (`ID`),
  ADD CONSTRAINT `FKMaterial_R249940` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`),
  ADD CONSTRAINT `FKMaterial_R269180` FOREIGN KEY (`CasaID`) REFERENCES `casa` (`ID`),
  ADD CONSTRAINT `FKMaterial_R349492` FOREIGN KEY (`DestinoID`) REFERENCES `destino` (`ID`),
  ADD CONSTRAINT `FKMaterial_R456168` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  ADD CONSTRAINT `FKMaterial_R690450` FOREIGN KEY (`ManzanaID`) REFERENCES `manzana` (`ID`),
  ADD CONSTRAINT `material_registro_salidas_ibfk_1` FOREIGN KEY (`RubroID`) REFERENCES `rubro` (`ID`);

--
-- Filtros para la tabla `registro_entradas`
--
ALTER TABLE `registro_entradas`
  ADD CONSTRAINT `FKRegistro_E578564` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`);

--
-- Filtros para la tabla `registro_salidas`
--
ALTER TABLE `registro_salidas`
  ADD CONSTRAINT `FKRegistro_S45085` FOREIGN KEY (`ProyectoID`) REFERENCES `proyecto` (`ID`),
  ADD CONSTRAINT `FKRegistro_S536051` FOREIGN KEY (`ContratistaID`) REFERENCES `contratista` (`ID`),
  ADD CONSTRAINT `FKRegistro_S59635` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FKUsuario36461` FOREIGN KEY (`RolID`) REFERENCES `rol` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
