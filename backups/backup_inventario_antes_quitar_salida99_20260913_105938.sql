-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: inventario
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ajuste_inventario`
--

DROP TABLE IF EXISTS `ajuste_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajuste_inventario` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `MaterialID` int(10) NOT NULL,
  `UsuarioID` int(10) NOT NULL,
  `Tipo` varchar(20) NOT NULL,
  `CantidadAnterior` int(10) NOT NULL,
  `CantidadNueva` int(10) NOT NULL,
  `CantidadAjuste` int(11) NOT NULL,
  `CostoUnitario` decimal(12,2) DEFAULT NULL,
  `Motivo` varchar(255) NOT NULL,
  `AperturaUnica` int(10) GENERATED ALWAYS AS (case when `Tipo` = 'APERTURA' then `MaterialID` else NULL end) STORED,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_apertura_material` (`AperturaUnica`),
  KEY `idx_material` (`MaterialID`),
  KEY `idx_fecha` (`Fecha`,`Hora`),
  KEY `ai_usuario_fk` (`UsuarioID`),
  CONSTRAINT `ai_material_fk` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  CONSTRAINT `ai_usuario_fk` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `ajuste_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `almacen`
--

DROP TABLE IF EXISTS `almacen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `almacen` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) NOT NULL,
  `Tipo` varchar(30) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `almacen`
--

LOCK TABLES `almacen` WRITE;
/*!40000 ALTER TABLE `almacen` DISABLE KEYS */;
INSERT INTO `almacen` VALUES (1,'Almacén Principal','principal');
/*!40000 ALTER TABLE `almacen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'Etapa 1');
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Entidad` varchar(40) NOT NULL,
  `EntidadID` int(10) NOT NULL,
  `Accion` varchar(30) NOT NULL,
  `UsuarioID` int(10) NOT NULL,
  `Fecha` datetime NOT NULL,
  `DatosAntes` text DEFAULT NULL,
  `DatosDespues` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `idx_entidad` (`Entidad`,`EntidadID`),
  KEY `UsuarioID` (`UsuarioID`),
  CONSTRAINT `auditoria_usuario_fk` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=313 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
INSERT INTO `auditoria` VALUES (261,'Usuario',24,'EDITAR',1,'2026-09-01 11:46:32','{\"Identificacion\":1070605738,\"Nombre\":\"Caterine Fernanda\",\"Apellido\":\"Jovel Rincon\",\"RolID\":2}','{\"Identificacion\":\"1070605738\",\"Nombre\":\"Caterine Fernanda\",\"Apellido\":\"Jovel Rincon\",\"RolID\":\"1\"}'),(262,'Ubicacion',6939,'CREAR',1,'2026-09-01 12:21:01',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":null,\"Nombre\":\"Etapa 2\",\"Tipo\":\"Etapa\"}'),(263,'Ubicacion',6939,'ELIMINAR',1,'2026-09-01 12:21:36','{\"ProyectoID\":1,\"PadreID\":null,\"Nombre\":\"Etapa 2\",\"Tipo\":\"Etapa\"}',NULL),(264,'Ubicacion',6943,'GENERAR_MASIVO',1,'2026-09-01 12:24:40',NULL,'{\"ProyectoID\":1,\"PadreID\":null,\"ContenedorNuevo\":\"ETAPA 2\",\"Niveles\":[{\"tipo\":\"Manzana\",\"cantidad\":2,\"patron\":\"Manzana {N:01}\"},{\"tipo\":\"Casa\",\"cantidad\":10,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":22}'),(265,'Proveedor',5,'CREAR',1,'2026-09-08 07:07:41',NULL,'{\"Descripcion\":\"Bob\"}'),(266,'Proveedor',5,'EDITAR',1,'2026-09-08 07:07:58','{\"Descripcion\":\"Bob\"}','{\"Descripcion\":\"Proveedor prueba\"}'),(267,'RegistroEntradas',80,'CREAR',1,'2026-09-08 07:08:13',NULL,'{\"Fecha\":\"2026-09-08\",\"Hora\":\"07:08:13\",\"Materiales\":1}'),(268,'Ubicacion',36,'CREAR',1,'2026-09-08 09:19:18',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":null,\"Nombre\":\"ETAPA 1\",\"Tipo\":\"Etapa\"}'),(269,'AjusteInventario',28,'CREAR',1,'2026-09-08 09:22:50',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"DANO\",\"CantidadAnterior\":20,\"CantidadNueva\":19,\"CantidadAjuste\":-1,\"Motivo\":\"Vencimiento\",\"CostoUnitario\":\"48500.00\"}'),(270,'NotificacionDestinatario',2,'EDITAR',1,'2026-09-08 09:28:05','{\"Nombre\":\"Administración\",\"Correo\":\"diegolopez1797@gmail.com\"}','{\"Nombre\":\"Administración\",\"Correo\":\"diegolopez1797@gmail.com\"}'),(271,'NotificacionDestinatario',3,'CREAR',1,'2026-09-08 09:28:26',NULL,'{\"Nombre\":\"x\",\"Correo\":\"123@gmail.com\"}'),(272,'Ubicacion',37,'CREAR',1,'2026-09-08 09:33:50',NULL,'{\"ProyectoID\":\"3\",\"PadreID\":null,\"Nombre\":\"Piso 1\",\"Tipo\":\"Piso\"}'),(273,'RegistroEntradas',81,'CREAR',1,'2026-09-08 10:23:41',NULL,'{\"Fecha\":\"2026-09-08\",\"Hora\":\"10:23:41\",\"Materiales\":1}'),(274,'RegistroSalidas',71,'CREAR',1,'2026-09-08 10:24:31',NULL,'{\"Fecha\":\"2026-09-08\",\"Hora\":\"10:24:31\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"2\",\"UbicacionID\":36}]}'),(275,'AjusteInventario',29,'CREAR',1,'2026-09-08 10:24:53',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"PERDIDA\",\"CantidadAnterior\":18,\"CantidadNueva\":17,\"CantidadAjuste\":-1,\"Motivo\":\"mmmm\",\"CostoUnitario\":\"51075.00\"}'),(276,'AjusteInventario',30,'CREAR',1,'2026-09-08 10:25:26',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"DANO\",\"CantidadAnterior\":17,\"CantidadNueva\":15,\"CantidadAjuste\":-2,\"Motivo\":\"nnn\",\"CostoUnitario\":\"51075.00\"}'),(277,'AjusteInventario',31,'CREAR',1,'2026-09-08 10:26:01',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"CONTEO\",\"CantidadAnterior\":15,\"CantidadNueva\":14,\"CantidadAjuste\":-1,\"Motivo\":\"otro\",\"CostoUnitario\":\"51075.00\"}'),(278,'AjusteInventario',32,'CREAR',1,'2026-09-08 10:26:25',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"CONTEO\",\"CantidadAnterior\":14,\"CantidadNueva\":16,\"CantidadAjuste\":2,\"Motivo\":\"lll\",\"CostoUnitario\":null}'),(279,'AjusteInventario',33,'CREAR',1,'2026-09-08 10:27:34',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"APERTURA\",\"CantidadAnterior\":16,\"CantidadNueva\":16,\"CantidadAjuste\":0,\"Motivo\":\"20\",\"CostoUnitario\":\"51075.00\"}'),(280,'RegistroSalidas',72,'CREAR',1,'2026-09-08 10:30:37',NULL,'{\"Fecha\":\"2026-09-08\",\"Hora\":\"10:30:37\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":2,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":36},{\"MaterialID\":1,\"Cantidad\":\"2\",\"UbicacionID\":20}]}'),(281,'Ubicacion',40,'DESACTIVAR',1,'2026-09-08 11:30:30','{\"Nombre\":\"Casa 6\",\"Activo\":1}','{\"Activo\":0}'),(282,'RegistroEntradas',83,'CREAR',1,'2026-09-10 10:06:51',NULL,'{\"Fecha\":\"2026-09-10\",\"Hora\":\"10:06:51\",\"Materiales\":1}'),(283,'Ubicacion',43,'CREAR',1,'2026-09-10 10:11:45',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"25\",\"Nombre\":\"Casa 11\",\"Tipo\":\"Casa\"}'),(284,'RegistroSalidas',76,'CREAR',1,'2026-09-10 10:13:32',NULL,'{\"Fecha\":\"2026-09-10\",\"Hora\":\"10:13:32\",\"ContratistaID\":1,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":43}]}'),(285,'Ubicacion',44,'CREAR',1,'2026-09-10 10:15:44',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":null,\"Nombre\":\"Etapa 5\",\"Tipo\":\"Etapa\"}'),(286,'Ubicacion',45,'CREAR',1,'2026-09-10 10:16:29',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"44\",\"Nombre\":\"Manzana M9\",\"Tipo\":\"Manzana\"}'),(287,'Ubicacion',46,'CREAR',1,'2026-09-10 10:17:01',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"45\",\"Nombre\":\"Casa 1\",\"Tipo\":\"Casa\"}'),(288,'Ubicacion',47,'CREAR',1,'2026-09-10 10:17:37',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"45\",\"Nombre\":\"Casa 2\",\"Tipo\":\"Casa\"}'),(289,'Ubicacion',60,'CREAR',1,'2026-09-10 10:30:57',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":null,\"Nombre\":\"ETAPA 4\",\"Tipo\":\"Etapa\"}'),(290,'Ubicacion',60,'GENERAR_MASIVO',1,'2026-09-10 10:32:36',NULL,'{\"ProyectoID\":2,\"PadreID\":60,\"ContenedorNuevo\":\"\",\"Niveles\":[{\"tipo\":\"Manzana\",\"cantidad\":4,\"patron\":\"Manzana {N:01}\"},{\"tipo\":\"Casa\",\"cantidad\":62,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":252}'),(291,'Ubicacion',315,'GENERAR_MASIVO',1,'2026-09-10 10:33:50',NULL,'{\"ProyectoID\":2,\"PadreID\":null,\"ContenedorNuevo\":\"ETAPA 3\",\"Niveles\":[{\"tipo\":\"Manzana\",\"cantidad\":10,\"patron\":\"Manzana {N:01}\"},{\"tipo\":\"Casa\",\"cantidad\":62,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":630}'),(292,'Ubicacion',946,'GENERAR_MASIVO',1,'2026-09-10 10:44:39',NULL,'{\"ProyectoID\":2,\"PadreID\":null,\"ContenedorNuevo\":\"Etapa 2\",\"Niveles\":[{\"tipo\":\"Manzana\",\"cantidad\":4,\"patron\":\"Manzana {N:01}\"},{\"tipo\":\"Casa\",\"cantidad\":42,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":172}'),(293,'RegistroSalidas',77,'CREAR',1,'2026-09-10 10:46:30',NULL,'{\"Fecha\":\"2026-09-10\",\"Hora\":\"10:46:30\",\"ContratistaID\":1,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":955}]}'),(294,'AjusteInventario',34,'CREAR',1,'2026-09-10 10:48:21',NULL,'{\"MaterialID\":\"2\",\"Tipo\":\"DANO\",\"CantidadAnterior\":2,\"CantidadNueva\":1,\"CantidadAjuste\":-1,\"Motivo\":\"POR QUE SE MOJO\",\"CostoUnitario\":null}'),(295,'Ubicacion',1119,'CREAR',1,'2026-09-10 10:53:57',NULL,'{\"ProyectoID\":\"3\",\"PadreID\":\"39\",\"Nombre\":\"urbanismo\",\"Tipo\":\"Urbanismo\"}'),(296,'Ubicacion',1120,'GENERAR_MASIVO',1,'2026-09-10 10:57:25',NULL,'{\"ProyectoID\":1,\"PadreID\":null,\"ContenedorNuevo\":\"Etapa 1\",\"Niveles\":[{\"tipo\":\"Manzana\",\"cantidad\":3,\"patron\":\"Manzana {N:01}\"},{\"tipo\":\"Casa\",\"cantidad\":42,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":129}'),(297,'Ubicacion',1250,'CREAR',1,'2026-09-10 10:59:17',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"24\",\"Nombre\":\"Manzana M911\",\"Tipo\":\"Manzana\"}'),(298,'Ubicacion',1250,'GENERAR_MASIVO',1,'2026-09-10 11:00:36',NULL,'{\"ProyectoID\":2,\"PadreID\":1250,\"ContenedorNuevo\":\"\",\"Niveles\":[{\"tipo\":\"Casa\",\"cantidad\":16,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":16}'),(299,'Ubicacion',24,'EDITAR',1,'2026-09-10 11:01:14','{\"Nombre\":\"ETAPA 6\",\"Tipo\":\"ETAPA\",\"PadreID\":null}','{\"Nombre\":\"ETAPA 6\",\"Tipo\":\"Etapa\",\"PadreID\":null}'),(300,'Ubicacion',1250,'DESACTIVAR',1,'2026-09-10 11:01:43','{\"Nombre\":\"Manzana M911\",\"Activo\":1}','{\"Activo\":0}'),(301,'Ubicacion',1267,'GENERAR_MASIVO',1,'2026-09-10 11:08:19',NULL,'{\"ProyectoID\":2,\"PadreID\":24,\"ContenedorNuevo\":\"Manzana M11\",\"Niveles\":[{\"tipo\":\"Casa\",\"cantidad\":16,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":16}'),(302,'Ubicacion',1284,'CREAR',1,'2026-09-10 11:11:57',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"24\",\"Nombre\":\"Manzana M12\",\"Tipo\":\"Manzana\"}'),(303,'Ubicacion',1284,'GENERAR_MASIVO',1,'2026-09-10 11:13:16',NULL,'{\"ProyectoID\":2,\"PadreID\":1284,\"ContenedorNuevo\":\"\",\"Niveles\":[{\"tipo\":\"Casa\",\"cantidad\":33,\"patron\":\"Casa {N:01}\"}],\"TotalGenerado\":33}'),(304,'Ubicacion',1322,'CREAR',1,'2026-09-10 12:04:38',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":null,\"Nombre\":\"Torre 1\",\"Tipo\":\"Torre\"}'),(305,'Ubicacion',1322,'GENERAR_MASIVO',1,'2026-09-10 12:07:08',NULL,'{\"ProyectoID\":2,\"PadreID\":1322,\"ContenedorNuevo\":\"\",\"Niveles\":[{\"tipo\":\"Piso\",\"cantidad\":30,\"patron\":\"Piso {N:01}\"},{\"tipo\":\"Apartamento\",\"cantidad\":8,\"patron\":\"Apto {N:0002}\"}],\"TotalGenerado\":270}'),(306,'Ubicacion',1353,'ELIMINAR',1,'2026-09-10 12:08:17','{\"ProyectoID\":2,\"PadreID\":1323,\"Nombre\":\"Apto 01\",\"Tipo\":\"Apartamento\"}',NULL),(307,'Ubicacion',1354,'ELIMINAR',1,'2026-09-10 12:08:42','{\"ProyectoID\":2,\"PadreID\":1323,\"Nombre\":\"Apto 02\",\"Tipo\":\"Apartamento\"}',NULL),(308,'RegistroSalidas',78,'CREAR',1,'2026-09-10 12:09:27',NULL,'{\"Fecha\":\"2026-09-10\",\"Hora\":\"12:09:26\",\"ContratistaID\":1,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":1583}]}'),(309,'RolPermiso',2,'EDITAR',1,'2026-09-10 12:12:39','{\"Permisos\":[\"catalogo.ver\",\"entrada.registrar\",\"salida.registrar\",\"solicitud.entregar\",\"dashboard.ver\",\"inventario.ajustar\"]}','{\"Permisos\":[\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"solicitud.entregar\",\"dashboard.ver\",\"inventario.ajustar\"]}'),(310,'Ubicacion',1593,'CREAR',2,'2026-09-10 12:13:36',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":null,\"Nombre\":\"Etapa 1\",\"Tipo\":\"Etapa\"}'),(311,'Ubicacion',1594,'CREAR',2,'2026-09-10 12:14:01',NULL,'{\"ProyectoID\":\"2\",\"PadreID\":\"1593\",\"Nombre\":\"Manzana M20\",\"Tipo\":\"Manzana\"}'),(312,'RegistroSalidas',99,'CREAR',1,'2026-09-13 10:34:32',NULL,'{\"Fecha\":\"2026-09-13\",\"Hora\":\"10:34:32\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"130\",\"UbicacionID\":50}]}');
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `casa`
--

DROP TABLE IF EXISTS `casa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `casa` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `casa`
--

LOCK TABLES `casa` WRITE;
/*!40000 ALTER TABLE `casa` DISABLE KEYS */;
INSERT INTO `casa` VALUES (1,'1');
/*!40000 ALTER TABLE `casa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contratista`
--

DROP TABLE IF EXISTS `contratista`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contratista` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratista`
--

LOCK TABLES `contratista` WRITE;
/*!40000 ALTER TABLE `contratista` DISABLE KEYS */;
INSERT INTO `contratista` VALUES (1,'Amin Narvaez'),(2,'Hermanos Murcia'),(3,'Smartools'),(4,'Sendos'),(5,'Grabiel cardona'),(6,'Administración');
/*!40000 ALTER TABLE `contratista` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destino`
--

DROP TABLE IF EXISTS `destino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `destino` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destino`
--

LOCK TABLES `destino` WRITE;
/*!40000 ALTER TABLE `destino` DISABLE KEYS */;
INSERT INTO `destino` VALUES (1,'Urbanismo'),(2,'Cimentación'),(3,'Industrializado'),(4,'Consumibles'),(5,'Acabados'),(6,'Cubierta'),(7,'Tuberías'),(8,'Vías, andenes y sardineles'),(9,'Accesorios');
/*!40000 ALTER TABLE `destino` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manzana`
--

DROP TABLE IF EXISTS `manzana`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manzana` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manzana`
--

LOCK TABLES `manzana` WRITE;
/*!40000 ALTER TABLE `manzana` DISABLE KEYS */;
INSERT INTO `manzana` VALUES (1,'M10'),(2,'M11');
/*!40000 ALTER TABLE `manzana` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Codigo` int(10) DEFAULT NULL,
  `Descripcion` varchar(100) DEFAULT NULL,
  `Unidad` varchar(30) DEFAULT NULL,
  `Saldo` int(10) unsigned DEFAULT 0,
  `Min_Almacen` int(11) NOT NULL DEFAULT 0,
  `Max_Casa` int(11) NOT NULL DEFAULT 0,
  `CostoPromedio` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,1,'Cemento gris uso general * 50kg','bulto',9,10,3,NULL),(2,2,'Cemento blanco * 40 kg','bulto',7,2,0,NULL),(3,3,'Yeso extra * 25 kg','bulto',4,2,0,NULL),(4,4,'Sardinel en cemento de 80 cms','UND',0,0,0,NULL),(5,5,'Malla electrosoldada 4mm','UND',939,0,0,NULL),(6,6,'Malla electrosoldada 4 mm en rollo * 18 mts','UND',118,0,0,NULL),(7,7,'Malla electrosoldada 5 mm','UND',178,0,0,NULL),(8,8,'Malla electrosoldada 6 mm','UND',91,0,0,NULL),(9,9,'Malla electrosoldada 6.5 mm','UND',381,0,0,NULL),(10,10,'Malla electrosoldada 8 mm','UND',180,0,0,NULL),(11,11,'Alambre negro liso','kg',390,0,0,NULL),(12,12,'varilla corrugada de 1/4\" * 6 mt','UND',5573,0,0,NULL),(13,13,'varilla corrugada de 1/4\" en chipa','kg',0,0,0,NULL),(14,14,'varilla corrugada de 3/8\" x 6mt','UND',244,0,0,NULL),(15,15,'varilla corrugada de 3/8\" x 12mt','UND',2072,0,0,NULL),(16,16,'varilla corrugada de 1/2\" x 6mt','UND',1316,0,0,NULL),(17,17,'varilla corrugada de 1/2\" x 12mt','UND',0,0,0,NULL),(18,20,'varilla corrugada de 5/8\" x 6mt lisa','UND',818,0,0,NULL),(19,23,'Fleje de 1/4\" de 13cm*18cm','UND',6200,0,0,NULL),(20,24,'Gancho tipo C de 3/8\" de 10*5*5','UND',206,0,0,NULL),(21,25,'Fleje de 3/8\" de 13cm*16cm','UND',8100,0,0,NULL),(22,26,'Gancho tipo C de 1/4 20x8x8','UND',6880,0,0,NULL),(23,27,'Fleje de 1/4\" de 15cm*20cm','UND',5014,0,0,NULL),(24,28,'Fleje de 1/4\" de 15cm*15cm','UND',1248,0,0,NULL),(25,29,'Gancho tipo C de 1/4 10x8x8','UND',6790,0,0,NULL),(26,30,'Silleta plástica SU25 mm','UND',0,0,0,NULL),(27,31,'Silleta plástica CP30 mm','UND',712,0,0,NULL),(28,32,'Silleta plástica CP50 mm','UND',6900,0,0,NULL),(29,33,'Silleta plástica CP65 mm','UND',0,0,0,NULL),(30,34,'Disco separador MC100 mm','UND',9500,0,0,NULL),(31,35,'Disco separador MC120 mm','UND',14400,0,0,NULL),(32,36,'Tuberia conduit de 1/2\" * 3mt','UND',300,0,0,NULL),(33,37,'Medio polín * 3mt','UND',300,0,0,NULL),(34,42,'Estaca en madera de 25cm','UND',390,0,0,NULL),(35,43,'Estaca en madera de 50cm','UND',400,0,0,NULL),(36,44,'Estaca en madera de 75cm','UND',450,0,0,NULL),(37,45,'Estaca en madera de 1mt','UND',100,0,0,NULL),(38,46,'Guadua * 6mt','UND',0,0,0,NULL),(39,48,'puntilla para madera de 2\"','LB',17,0,0,NULL),(40,49,'puntilla para madera de 2 1/2\"','LB',26,0,0,NULL),(41,50,'puntilla para madera de 3\"','LB',57,0,0,NULL),(42,51,'puntilla en acero de 2 1/2\"','LB',26,0,0,NULL),(43,52,'Broca SDS plus de 3/8\" * 6\"','UND',68,0,0,NULL),(44,53,'Broca SDS plus de 1/2\" * 6\"','UND',23,0,0,NULL),(45,54,'Broca SDS plus de 1/2\" * 12\"','UND',35,0,0,NULL),(46,55,'Chazo plastico de 3/8\"','UND',500,0,0,NULL),(47,56,'Disco diamantado continuo de 4 1/2\"','UND',30,0,0,NULL),(48,57,'Disco diamantado segmentado de 9\"','UND',0,0,0,NULL),(49,58,'Disco de copa diamantada doble de 7\" para pulir','UND',11,0,0,NULL),(50,59,'Disco de corte de hierro de 4 1/2\"','UND',3,0,0,NULL),(51,60,'Disco de corte de hierro de 7\"','UND',34,0,0,NULL),(52,62,'Disco de corte de hierro de 14\" - tronzadora','UND',48,0,0,NULL),(53,63,'Brocha de 2 1/2\"','UND',43,0,0,NULL),(54,64,'Brocha de 3\"','UND',44,0,0,NULL),(55,65,'Brocha de 4\"','UND',35,0,0,NULL),(56,66,'Rodillo de Felpa de 9\"','UND',103,0,0,NULL),(57,67,'Lija #150','UND',31,0,0,NULL),(58,68,'Espátula metálica de 3\" mango madera/plastico','UND',10,0,0,NULL),(59,69,'Espátula metálica de 4\" mango madera/plastico','UND',46,0,0,NULL),(60,70,'Espátula metálica de 5\" mango madera/plastico','UND',26,0,0,NULL),(61,72,'Pintura vinilo gris Tipo 1','cuñete',2,0,0,NULL),(62,73,'Pintura vinilo Blanco Tipo 2','cuñete',25,0,0,NULL),(63,80,'Aerosol color rojo','UND',2,0,0,NULL),(64,81,'Aerosol color negro','UND',0,0,0,NULL),(65,83,'Sellante poliuretano topex gris * 300ml','UND',30,0,0,NULL),(66,86,'Illbruck SP523 * blanco 300ml','UND',0,0,0,NULL),(67,92,'Aquacero - impermeabilizante acrilico','cuñete',0,0,0,NULL),(68,94,'Cinta de enmascarar de 1\"','UND',61,0,0,NULL),(69,95,'Mineral bayer Rojo','caja',33,0,0,NULL),(70,97,'Curaseal pf blanco - curador de concreto * 200kg (tambor)','Tambor * 55gls',1,0,0,NULL),(71,100,'Vulken 45 SSl * 5gl','cuñete',2,0,0,NULL),(72,101,'Verticoat No. 2 *30kg','bulto',2,0,0,NULL),(73,104,'Sikadur - 32 primer * 1 KG','UND',0,0,0,NULL),(74,106,'Cal Hidratada * 10KG','bulto',17,0,0,NULL),(75,107,'Plastico negro calibre 6 * 4mts ancho','Rollo',1,0,0,NULL),(76,108,'Lona verde para cerramiento * 2mts alto','Rollo',6,0,0,NULL),(77,110,'Cinta peligro * 500 mts','Rollo',23,0,0,NULL),(78,113,'Cepillo de alambre - para limpieza','UND',10,0,0,NULL),(79,118,'Pala Draga (Hoyadora)','UND',2,0,0,NULL),(80,119,'Pica','UND',2,0,0,NULL),(81,120,'Palín cuadrado','UND',0,0,0,NULL),(82,121,'Barretón','UND',0,0,0,NULL),(83,122,'Cepillo carretero - Escobillón','UND',0,0,0,NULL),(84,123,'Carretilla','UND',3,0,0,NULL),(85,124,'Rastrillo metálico','UND',4,0,0,NULL),(86,126,'Combo sanitario linea institucional','UND',0,0,0,NULL),(87,127,'Lavamanos blanco linea institucional (sin pedestal)','UND',0,0,0,NULL),(88,128,'Enchape cerámico eco plus blanco 20*20 * 2m2','caja',0,0,0,NULL),(89,129,'Enchape cerámico piso pared natal blanco 25*35','caja',100,0,0,NULL),(90,130,'Enchape cerámico blanco 30*30','caja',20,0,0,NULL),(91,131,'Enchape cerámico Stone café 45*45','caja',83,0,0,NULL),(92,132,'Enchape cerámico Slate White EP 51*51','caja',90,0,0,NULL),(93,133,'Pegante cerámico * 25 kg','bulto',9,0,0,NULL),(94,134,'Boquilla blanca * 2 kg','UND',0,0,0,NULL),(95,139,'Hojas de Segueta','UND',74,0,0,NULL),(96,140,'Cinta teflón industrial','UND',71,0,0,NULL),(97,141,'Tuberia pvc presiòn de 1/2\" * 6 mts - RDE 9','UND',1028,0,0,NULL),(98,142,'Tuberia pvc presiòn de 3/4\" * 6 mts - RDE 11','UND',521,0,0,NULL),(99,143,'Tuberia pvc presiòn de 1\" * 6 mts - RDE 13.5','UND',500,0,0,NULL),(100,144,'Codo pvc presión 1/2\"','UND',1916,0,0,NULL),(101,145,'Codo pvc presión 3/4\"','UND',4488,0,0,NULL),(102,146,'Codo pvc presión 1\"','UND',196,0,0,NULL),(103,147,'Unión pvc presión 1/2\"','UND',709,0,0,NULL),(104,148,'Unión pvc presión 3/4\"','UND',5972,0,0,NULL),(105,149,'Unión pvc presión 1\"','UND',15,0,0,NULL),(106,150,'Unión universal pvc presión 1/2\"','UND',215,0,0,NULL),(107,151,'Unión universal pvc presión 1\"','UND',98,0,0,NULL),(108,152,'Tee pvc presión 1/2\"','UND',3080,0,0,NULL),(109,153,'Tee pvc presión 3/4\"','UND',2184,0,0,NULL),(110,154,'Tee pvc presión 1\"','UND',550,0,0,NULL),(111,155,'Tapón pvc presión liso de 1/2\"','UND',454,0,0,NULL),(112,156,'Tapón pvc presión roscado de 1/2\"','UND',0,0,0,NULL),(113,157,'Tapón pvc presión liso de 1\"','UND',0,0,0,NULL),(114,158,'Adaptador pvc presión hembra de 1/2\"','UND',4788,0,0,NULL),(115,159,'Adaptador pvc presión macho de 1/2\"','UND',3475,0,0,NULL),(116,160,'Adaptador pvc presión macho de 3/4\"','UND',900,0,0,NULL),(117,161,'Adaptador pvc presión hembra de 1\"','UND',770,0,0,NULL),(118,162,'Adaptador pvc presión macho de 1\"','UND',220,0,0,NULL),(119,163,'Semicodo pvc presión 1/2\"','UND',671,0,0,NULL),(120,164,'reducción pvc presión 3/4\" x 1/2\" (buje)','UND',2162,0,0,NULL),(121,165,'reducción pvc presión 1\" x 1/2\" (buje)','UND',313,0,0,NULL),(122,166,'reducción pvc presión 1\" x 3/4\" (buje)','UND',125,0,0,NULL),(123,167,'Cheque horizontal de 1/2\" - grival','UND',151,0,0,NULL),(124,168,'Cheque horizontal de 1\" - grival','UND',61,0,0,NULL),(125,169,'Llave de paso pvc presión lisa de 1/2\" - plástica','UND',179,0,0,NULL),(126,170,'Llave de paso pvc presión lisa de 3/4\" - plastica','UND',73,0,0,NULL),(127,171,'Llave de paso pvc presión lisa de 1\" - plástica','UND',280,0,0,NULL),(128,172,'Llave terminal de 1/2\"','UND',258,0,0,NULL),(129,173,'Codo galvanizado de 1/2\"','UND',340,0,0,NULL),(130,174,'Micromedidor de Agua potable de 1/2\"','UND',80,0,0,NULL),(131,175,'Registro ducha completo','UND',71,0,0,NULL),(132,176,'Lavadero prefabricado en concreto 0.50*0.50*0.80','UND',3,0,0,NULL),(133,178,'soldadura pvc Gerfor verde * 1/4 gl','UND',718,0,0,NULL),(134,179,'Limpiador pvc Gerfor * 1/4gl','UND',696,0,0,NULL),(135,180,'Tanque Almacenamiento * 500 lts (polinter)','UND',57,0,0,NULL),(136,181,'Flotador llenado tanque almacenamiento','UND',57,0,0,NULL),(137,182,'Acople para sanitario','UND',446,0,0,NULL),(138,183,'Acople para Lavamanos','UND',134,0,0,NULL),(139,184,'Acople para Lavaplatos','UND',135,0,0,NULL),(140,185,'Arbol de entrada (llenado) tanque sanitario - Repuesto','UND',24,0,0,NULL),(141,186,'Arbol de salida - tanque sanitario - Repuesto','UND',12,0,0,NULL),(142,187,'Canastilla para lavaplatos de 4\"*3\"','UND',117,0,0,NULL),(143,188,'Sifón ajustable tipo acordeón - lavaplatos','UND',16,0,0,NULL),(144,189,'Biscocho / cajilla en cemento para medidor','UND',59,0,0,NULL),(145,190,'Tapa empo metálica - para medidor','UND',94,0,0,NULL),(146,191,'Tubo sanitario de 1 1/2\" * 6 mt','UND',77,0,0,NULL),(147,192,'Tubo sanitario de 3\" * 6 mt','UND',206,0,0,NULL),(148,193,'Tubo sanitario de 4\" * 6 mt','UND',560,0,0,NULL),(149,194,'Tubo sanitario de 4\" * 6 mt - Reventilación (naranja)','UND',49,0,0,NULL),(150,195,'Tubo sanitario de 6\" * 6 mt','UND',36,0,0,NULL),(151,196,'Unión pvc sanitaria de 1 1/2\"','UND',700,0,0,NULL),(152,197,'Unión pvc sanitaria de 2\"','UND',75,0,0,NULL),(153,198,'Unión pvc sanitaria de 3\"','UND',78,0,0,NULL),(154,199,'Unión pvc sanitaria de 4\"','UND',850,0,0,NULL),(155,200,'Unión pvc sanitaria de 6\"','UND',1,0,0,NULL),(156,201,'Unión pvc sanitaria de 8\"','UND',1,0,0,NULL),(157,202,'Codo pvc sanitario de 1 1/2\"','UND',553,0,0,NULL),(158,203,'Codo pvc sanitario de 2\"','UND',139,0,0,NULL),(159,204,'Codo pvc sanitario de 3','UND',336,0,0,NULL),(160,205,'Codo pvc sanitario de 4\"','UND',332,0,0,NULL),(161,206,'Semicodo pvc sanitario de 1\" 1/2\"','UND',223,0,0,NULL),(162,207,'Semicodo pvc sanitario de 2\"','UND',489,0,0,NULL),(163,208,'Semicodo pvc sanitario de 3','UND',303,0,0,NULL),(164,209,'Semicodo pvc sanitario de 4\"','UND',187,0,0,NULL),(165,210,'Sifón pvc sanitario 180° C*C 1 1/2\"','UND',91,0,0,NULL),(166,211,'Sifón pvc sanitario 135° C*E 3\"','UND',302,0,0,NULL),(167,212,'Sifón pvc sanitario 135° C*E 4\"','UND',55,0,0,NULL),(168,213,'Tapa prueba sanitaria de 2\"','UND',107,0,0,NULL),(169,214,'Tapa prueba sanitaria de 3\"','UND',434,0,0,NULL),(170,215,'Tapa prueba sanitaria de 4\"','UND',529,0,0,NULL),(171,216,'Tapa prueba sanitaria de 6\"','UND',26,0,0,NULL),(172,217,'Adaptador de limpieza sanitario de 4','UND',210,0,0,NULL),(173,218,'Rejilla plástica de 2\"','UND',17,0,0,NULL),(174,219,'Rejilla plástica con sosco de 4\" * 3\"','UND',461,0,0,NULL),(175,220,'Rejilla plástica con sosco de 5\" * 4\"','UND',88,0,0,NULL),(176,221,'Buje pvc sanitario de 3\" * 1 1/2\"','UND',300,0,0,NULL),(177,222,'Buje pvc sanitario de 4\" * 2\"','UND',44,0,0,NULL),(178,223,'Buje pvc sanitario de 4\" * 3\"','UND',105,0,0,NULL),(179,224,'Tee pvc sanitaria de 1 1/2\"','UND',147,0,0,NULL),(180,225,'Tee pvc sanitaria de 4\"','UND',546,0,0,NULL),(181,226,'Tee pvc sanitaria de 6\"','UND',15,0,0,NULL),(182,227,'Tee pvc sanitaria reducida de 4\" * 3\"','UND',94,0,0,NULL),(183,228,'Tee pvc sanitaria reducida de 6\" * 4\"','UND',3,0,0,NULL),(184,229,'Yee pvc sanitaria de 4\"','UND',100,0,0,NULL),(185,230,'Yee pvc sanitaria reducida de 4\" * 2\"','UND',125,0,0,NULL),(186,231,'Yee pvc sanitaria reducida de 4\" * 3\"','UND',129,0,0,NULL),(187,232,'Yee pvc sanitaria reducida de 6\" * 4\"','UND',178,0,0,NULL),(188,233,'Teja de zinc * 3mts','UND',200,0,0,NULL),(189,234,'Amarre para teja de zinc','UND',1000,0,0,NULL),(190,235,'Tuberia rectangular Cr 3\" * 1 1/2\" * 6 mts calibre 1.1mm (calibre 18)','UND',20,0,0,NULL),(191,243,'Caballete de 2 mts largo X 0,60 mt ancho','UND',87,0,0,NULL),(192,244,'Soldadura Electrica 6013 * 3/32','kg',55,0,0,NULL),(193,245,'Tornillo fijador de ala','UND',497,0,0,NULL),(194,254,'Emulsión asfaltica ED-9','cuñete',3,0,0,NULL),(195,255,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2.5mm (con soplete) * 10m2','Rollo',18,0,0,NULL),(196,256,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2mm (aplicación en frio)','Rollo',5,0,0,NULL),(197,257,'Gárgola prefabricada en cemento','UND',8,0,0,NULL),(198,258,'Flanche en lámina galvanizada de 2.0mt * 25cm desarrollo - calibre 26','UND',32,0,0,NULL),(199,259,'Flanche en lámina galvanizada de 2.40mt * 25cm desarrollo - calibre 26','UND',32,0,0,NULL),(200,260,'Cinta Asfáltica tapa goteras de 10cms * 10mts','UND',9,0,0,NULL),(201,261,'Cinta Asfáltica tapa goteras de 15cms * 10mts','UND',4,0,0,NULL),(202,262,'Cinta Asfáltica tapa goteras de 20cms * 10mts','UND',3,0,0,NULL),(203,263,'Juego de parrillas y quemadores para estufa','UND',89,0,0,NULL),(204,264,'Griferia cuello de ganso - para cocina','UND',31,0,0,NULL),(205,265,'Manijas para mueble de cocina','UND',176,0,0,NULL),(206,266,'Chapa de poma metálica y redonda - para alcoba','UND',10,0,0,NULL),(207,267,'Chapa de poma metálica y redonda - para baño','UND',10,0,0,NULL),(208,268,'Lubricante Gerfor * Tarro 500ml','UND',46,0,0,NULL),(209,269,'Silla fix adhesivo sellante * tarro 310ml','UND',25,0,0,NULL),(210,270,'Geotextil No tejido NT 1600 - rollo de 3.5mt*160ml','Rollo',3,0,0,NULL),(211,271,'Rejilla metálica para sumidero de 0.50mt * 0.80mt marco y contramarco','UND',7,0,0,NULL),(212,273,'Tubería PVC para alcantarillado Novafort 6\" S-4 x 6mt','UND',278,0,0,NULL),(213,274,'Tubería PVC para alcantarillado Novafort 8\" S-8 x 6mt','UND',278,0,0,NULL),(214,275,'Tubería PVC para alcantarillado Novafort 10\" S-8 x 6mt','UND',69,0,0,NULL),(215,276,'Tubería PVC para alcantarillado Novafort 12\" S-8 x 6mt','UND',39,0,0,NULL),(216,277,'Tubería PVC para alcantarillado Novafort 14\" S-8 x 6mt','UND',0,0,0,NULL),(217,279,'Tubería en cemento para alcantarillado diametro 18\" * 1mt','UND',0,0,0,NULL),(218,281,'Tubería en cemento para alcantarillado diámetro 36\" * 1mt','UND',0,0,0,NULL),(219,282,'Tapa antirrobo para pozo de inspección','UND',50,0,0,NULL),(220,283,'Silla Tee pvc sanitario de 6\" * 4\"','UND',15,0,0,NULL),(221,284,'Silla Yee pvc sanitario 8\" * 6\"','UND',280,0,0,NULL),(222,285,'Silla Yee pvc sanitario 10\" * 6\"','UND',9,0,0,NULL),(223,286,'Silla Yee pvc sanitario 12\" * 6\" (315*160)','UND',22,0,0,NULL),(224,287,'Silla Yee pvc sanitario 16\" * 6\" (400*160)','UND',40,0,0,NULL),(225,288,'Sifón pvc sanitario 180° C*C 2\"','UND',6,0,0,NULL),(226,289,'Codo pvc presión 1 1/2\"','UND',0,0,0,NULL),(227,290,'Semicodo pvc presión 1 1/2\"','UND',0,0,0,NULL),(228,291,'Tapón pvc presión liso de 1 1/2\"','UND',0,0,0,NULL),(229,293,'Tapón pvc presión roscado de 1 1/2\"','UND',0,0,0,NULL),(230,294,'Adaptador pvc presión hembra de 2\"','UND',9,0,0,NULL),(231,297,'Tubería campana pvc presión de 3\" * 6 mts - RDE 21','UND',165,0,0,NULL),(232,298,'Tubería campana pvc presión de 4\" * 6 mts - RDE 21','UND',2,0,0,NULL),(233,299,'Tubería campana pvc presión de 6\" * 6 mts - RDE 21','UND',36,0,0,NULL),(234,300,'Tubería campana pvc presión de 8\" * 6 mts - RDE 21','UND',9,0,0,NULL),(235,301,'Collarín pvc presión de 3\" x 1/2\"','UND',403,0,0,NULL),(236,303,'Collarín pvc presión de 4\" x 1/2\"','UND',23,0,0,NULL),(237,305,'Codo pvc presión 90° 4\"','UND',16,0,0,NULL),(238,306,'Codo pvc presión 90° 6\"','UND',15,0,0,NULL),(239,307,'Codo pvc presión gran radio 45° 6','UND',2,0,0,NULL),(240,308,'Semicodo pvc presión 45° 3\"','UND',6,0,0,NULL),(241,310,'Tee pvc presión 90° 3\"','UND',1,0,0,NULL),(242,313,'Tee pvc presión 4\"','UND',9,0,0,NULL),(243,314,'Tee pvc presión 4\" x 4\" x 3\"','UND',6,0,0,NULL),(244,315,'Tee pvc presión 6\"','UND',3,0,0,NULL),(245,316,'Tee H.D presión 6\" x 3\"','UND',2,0,0,NULL),(246,320,'Tapón pvc presión 3\"','UND',12,0,0,NULL),(247,321,'Tapón pvc presión 4\"','UND',5,0,0,NULL),(248,322,'Tapón H.D presión 8\"','UND',23,0,0,NULL),(249,323,'Reducción pvc presión 1 1/2\" x 3/4\" (buje)','UND',0,0,0,NULL),(250,324,'Reducción pvc presión 2\" x 1\" (buje)','UND',20,0,0,NULL),(251,325,'Reducción pvc presión 2\" x 1 1/2\" (buje)','UND',17,0,0,NULL),(252,326,'Reducción pvc presión 4\" x 2\" (buje)','UND',1,0,0,NULL),(253,328,'Reducción pvc presión 6\" x 4\" (buje)','UND',1,0,0,NULL),(254,331,'Unión Z UM presión 4\"','UND',0,0,0,NULL),(255,332,'Unión pasante pvc presión 3\"','UND',35,0,0,NULL),(256,333,'Unión pasante pvc presión 4\"','UND',52,0,0,NULL),(257,334,'Unión pasante pvc presión 6\"','UND',23,0,0,NULL),(258,335,'Unión pasante pvc presión 8\"','UND',10,0,0,NULL),(259,337,'Brida unión de 3\"','UND',3,0,0,NULL),(260,338,'Brida unión de 4\"','UND',6,0,0,NULL),(261,339,'Brida unión de 6\"','UND',4,0,0,NULL),(262,340,'Brida unión de 8\"','UND',3,0,0,NULL),(263,341,'Tapa chorote','UND',17,0,0,NULL),(264,342,'Cruceta H.D 4\" x 3\"','UND',7,0,0,NULL),(265,343,'Cruceta H.D 4\" x 4\"','UND',3,0,0,NULL),(266,344,'Cruceta H.D 6\" x 3\"','UND',11,0,0,NULL),(267,346,'Válvula de compuerta sello elástico H.D 3\"','UND',11,0,0,NULL),(268,347,'Válvula de compuerta sello elástico H.D 4\"','UND',3,0,0,NULL),(269,348,'Válvula de compuerta sello elástico H.D 6\"','UND',0,0,0,NULL),(270,349,'Válvula AQT compuerta elástica 3\" RDE 21','UND',3,0,0,NULL),(271,351,'Válvula ventosa de 1\"','UND',0,0,0,NULL),(272,352,'Válvula ventosa de 2\"','UND',6,0,0,NULL),(273,353,'Hidrante H.D 3\"','UND',6,0,0,NULL),(274,354,'imprermeabilizante color seal','cuñete',10,0,0,NULL),(275,355,'Combo sanitario constructor blanco corona','unidad',39,0,0,NULL),(276,356,'Tubo presion de 10\"','unidad',2,1,0,NULL),(277,357,'Dymonic FC (300cc)  gris','tarro',76,5,2,NULL),(278,358,'Tee UM 3x3x2 presion','unidad',1,1,0,NULL),(279,359,'Tee UM 3x2x3 presion','unidad',2,1,0,NULL),(280,360,'Tee UM 4x3x4 presion','unidad',9,1,0,NULL),(281,361,'Union pasante cxc 2\"','unidad',2,1,0,NULL),(282,362,'Eucoplus 1000','bolsa',12,1,0,NULL),(283,363,'Concrete color ladrillo x 30 kilos','bolsa',20,1,0,NULL),(284,364,'Concrete color gris x 30 kilos','bolsa',16,1,0,NULL),(285,365,'Tubo presion RDE 13.5','unidad',500,5,1,NULL),(286,366,'Union presion simple de 3\"','unidad',22,2,3,NULL),(287,367,'Yee sanitario 3\"x2\"','unidad',21,2,3,NULL),(288,368,'Buje sanitario 2\"x1\"1/2','unidad',21,2,3,NULL),(289,369,'Polin x 3 mts 5x5','unidad',54,2,3,NULL),(290,370,'Tabla burra x 30 cm','unidad',0,2,3,NULL),(291,371,'Cercos  5x10','unidad',24,2,3,NULL),(292,372,'Cemento gris fortecem','Bulto',47,10,5,NULL),(293,373,'Sellasil soporte 1/4','Rollo',4,1,1,NULL);
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_almacen`
--

DROP TABLE IF EXISTS `material_almacen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_almacen` (
  `MaterialID` int(10) NOT NULL,
  `AlmacenID` int(10) NOT NULL,
  `Saldo` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`MaterialID`,`AlmacenID`),
  KEY `idx_almacen` (`AlmacenID`),
  CONSTRAINT `ma_almacen_fk` FOREIGN KEY (`AlmacenID`) REFERENCES `almacen` (`ID`),
  CONSTRAINT `ma_material_fk` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_almacen`
--

LOCK TABLES `material_almacen` WRITE;
/*!40000 ALTER TABLE `material_almacen` DISABLE KEYS */;
INSERT INTO `material_almacen` VALUES (1,1,9),(2,1,7),(3,1,4),(4,1,0),(5,1,939),(6,1,118),(7,1,178),(8,1,91),(9,1,381),(10,1,180),(11,1,390),(12,1,5573),(13,1,0),(14,1,244),(15,1,2072),(16,1,1316),(17,1,0),(18,1,818),(19,1,6200),(20,1,206),(21,1,8100),(22,1,6880),(23,1,5014),(24,1,1248),(25,1,6790),(26,1,0),(27,1,712),(28,1,6900),(29,1,0),(30,1,9500),(31,1,14400),(32,1,300),(33,1,300),(34,1,390),(35,1,400),(36,1,450),(37,1,100),(38,1,0),(39,1,17),(40,1,26),(41,1,57),(42,1,26),(43,1,68),(44,1,23),(45,1,35),(46,1,500),(47,1,30),(48,1,0),(49,1,11),(50,1,3),(51,1,34),(52,1,48),(53,1,43),(54,1,44),(55,1,35),(56,1,103),(57,1,31),(58,1,10),(59,1,46),(60,1,26),(61,1,2),(62,1,25),(63,1,2),(64,1,0),(65,1,30),(66,1,0),(67,1,0),(68,1,61),(69,1,33),(70,1,1),(71,1,2),(72,1,2),(73,1,0),(74,1,17),(75,1,1),(76,1,6),(77,1,23),(78,1,10),(79,1,2),(80,1,2),(81,1,0),(82,1,0),(83,1,0),(84,1,3),(85,1,4),(86,1,0),(87,1,0),(88,1,0),(89,1,100),(90,1,20),(91,1,83),(92,1,90),(93,1,9),(94,1,0),(95,1,74),(96,1,71),(97,1,1028),(98,1,521),(99,1,500),(100,1,1916),(101,1,4488),(102,1,196),(103,1,709),(104,1,5972),(105,1,15),(106,1,215),(107,1,98),(108,1,3080),(109,1,2184),(110,1,550),(111,1,454),(112,1,0),(113,1,0),(114,1,4788),(115,1,3475),(116,1,900),(117,1,770),(118,1,220),(119,1,671),(120,1,2162),(121,1,313),(122,1,125),(123,1,151),(124,1,61),(125,1,179),(126,1,73),(127,1,280),(128,1,258),(129,1,340),(130,1,80),(131,1,71),(132,1,3),(133,1,718),(134,1,696),(135,1,57),(136,1,57),(137,1,446),(138,1,134),(139,1,135),(140,1,24),(141,1,12),(142,1,117),(143,1,16),(144,1,59),(145,1,94),(146,1,77),(147,1,206),(148,1,560),(149,1,49),(150,1,36),(151,1,700),(152,1,75),(153,1,78),(154,1,850),(155,1,1),(156,1,1),(157,1,553),(158,1,139),(159,1,336),(160,1,332),(161,1,223),(162,1,489),(163,1,303),(164,1,187),(165,1,91),(166,1,302),(167,1,55),(168,1,107),(169,1,434),(170,1,529),(171,1,26),(172,1,210),(173,1,17),(174,1,461),(175,1,88),(176,1,300),(177,1,44),(178,1,105),(179,1,147),(180,1,546),(181,1,15),(182,1,94),(183,1,3),(184,1,100),(185,1,125),(186,1,129),(187,1,178),(188,1,200),(189,1,1000),(190,1,20),(191,1,87),(192,1,55),(193,1,497),(194,1,3),(195,1,18),(196,1,5),(197,1,8),(198,1,32),(199,1,32),(200,1,9),(201,1,4),(202,1,3),(203,1,89),(204,1,31),(205,1,176),(206,1,10),(207,1,10),(208,1,46),(209,1,25),(210,1,3),(211,1,7),(212,1,278),(213,1,278),(214,1,69),(215,1,39),(216,1,0),(217,1,0),(218,1,0),(219,1,50),(220,1,15),(221,1,280),(222,1,9),(223,1,22),(224,1,40),(225,1,6),(226,1,0),(227,1,0),(228,1,0),(229,1,0),(230,1,9),(231,1,165),(232,1,2),(233,1,36),(234,1,9),(235,1,403),(236,1,23),(237,1,16),(238,1,15),(239,1,2),(240,1,6),(241,1,1),(242,1,9),(243,1,6),(244,1,3),(245,1,2),(246,1,12),(247,1,5),(248,1,23),(249,1,0),(250,1,20),(251,1,17),(252,1,1),(253,1,1),(254,1,0),(255,1,35),(256,1,52),(257,1,23),(258,1,10),(259,1,3),(260,1,6),(261,1,4),(262,1,3),(263,1,17),(264,1,7),(265,1,3),(266,1,11),(267,1,11),(268,1,3),(269,1,0),(270,1,3),(271,1,0),(272,1,6),(273,1,6),(274,1,10),(275,1,39),(276,1,2),(277,1,76),(278,1,1),(279,1,2),(280,1,9),(281,1,2),(282,1,12),(283,1,20),(284,1,16),(285,1,500),(286,1,22),(287,1,21),(288,1,21),(289,1,54),(290,1,0),(291,1,24),(292,1,47),(293,1,4);
/*!40000 ALTER TABLE `material_almacen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_registro_entradas`
--

DROP TABLE IF EXISTS `material_registro_entradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_registro_entradas` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `MaterialID` int(10) NOT NULL,
  `Registro_EntradasID` int(10) NOT NULL,
  `Cantidad` int(10) DEFAULT NULL,
  `DestinoID` int(10) NOT NULL,
  `CostoUnitario` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FKMaterial_R356246` (`MaterialID`),
  KEY `FKMaterial_R660572` (`Registro_EntradasID`),
  KEY `DestinoID` (`DestinoID`),
  CONSTRAINT `FKMaterial_R356246` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  CONSTRAINT `FKMaterial_R660572` FOREIGN KEY (`Registro_EntradasID`) REFERENCES `registro_entradas` (`ID`),
  CONSTRAINT `material_registro_entradas_ibfk_1` FOREIGN KEY (`DestinoID`) REFERENCES `destino` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_entradas`
--

LOCK TABLES `material_registro_entradas` WRITE;
/*!40000 ALTER TABLE `material_registro_entradas` DISABLE KEYS */;
INSERT INTO `material_registro_entradas` VALUES (1,1,1,200,1,NULL),(2,2,2,3,5,NULL),(3,3,3,4,5,NULL),(4,5,4,991,2,NULL),(5,6,5,118,2,NULL),(6,7,6,178,3,NULL),(7,8,7,115,3,NULL),(8,9,8,381,3,NULL),(9,10,9,180,3,NULL),(10,11,10,475,2,NULL),(11,12,11,5573,3,NULL),(12,13,12,100,8,NULL),(13,14,13,250,3,NULL),(14,15,14,2354,2,NULL),(15,16,15,1398,8,NULL),(16,17,16,7,3,NULL),(17,18,17,968,8,NULL),(18,19,18,6200,2,NULL),(19,20,19,206,2,NULL),(20,21,20,8100,2,NULL),(21,22,21,10120,2,NULL),(22,23,22,7726,2,NULL),(23,24,23,1608,2,NULL),(24,25,24,6790,2,NULL),(25,27,25,712,3,NULL),(26,28,26,6900,3,NULL),(27,30,27,9500,3,NULL),(28,31,28,14400,3,NULL),(29,32,29,300,3,NULL),(30,33,30,150,2,NULL),(31,34,31,200,2,NULL),(32,35,32,400,2,NULL),(33,36,33,150,2,NULL),(34,37,34,150,2,NULL),(35,39,35,23,2,NULL),(36,38,35,50,1,NULL),(37,40,36,32,2,NULL),(38,41,36,60,2,NULL),(39,42,36,26,3,NULL),(40,43,36,68,3,NULL),(41,44,37,24,3,NULL),(42,45,37,35,3,NULL),(43,46,37,500,5,NULL),(44,47,38,35,5,NULL),(45,48,38,1,3,NULL),(46,49,38,11,3,NULL),(47,50,38,13,2,NULL),(48,51,38,49,2,NULL),(49,52,38,67,2,NULL),(50,53,39,43,5,NULL),(51,54,39,47,5,NULL),(52,55,39,35,5,NULL),(53,56,39,78,5,NULL),(54,57,40,31,5,NULL),(55,58,40,10,5,NULL),(56,59,40,23,5,NULL),(57,60,40,3,5,NULL),(58,61,41,2,5,NULL),(59,62,41,26,5,NULL),(60,63,42,2,2,NULL),(61,65,43,30,6,NULL),(62,68,43,61,5,NULL),(63,69,44,33,3,NULL),(64,70,44,1,8,NULL),(65,71,44,2,8,NULL),(66,72,44,2,3,NULL),(67,74,44,10,2,NULL),(68,75,45,1,1,NULL),(69,76,45,6,2,NULL),(70,77,45,23,1,NULL),(71,78,45,10,5,NULL),(72,79,46,2,1,NULL),(73,80,46,2,1,NULL),(74,84,46,3,1,NULL),(75,85,46,4,1,NULL),(76,89,47,100,5,NULL),(77,90,47,20,5,NULL),(78,91,47,83,5,NULL),(79,92,47,90,6,NULL),(80,93,47,9,6,NULL),(81,95,48,76,7,NULL),(82,96,48,84,5,NULL),(83,97,49,1067,7,NULL),(84,98,49,560,7,NULL),(85,99,49,500,7,NULL),(86,100,50,2056,4,NULL),(87,101,50,4560,2,NULL),(88,102,50,196,2,NULL),(89,103,50,754,4,NULL),(90,104,50,6000,4,NULL),(91,105,50,15,4,NULL),(92,106,51,215,7,NULL),(93,107,51,98,7,NULL),(94,108,51,3200,7,NULL),(95,109,51,2240,7,NULL),(96,110,51,550,7,NULL),(97,111,51,560,6,NULL),(98,114,52,4850,9,NULL),(99,115,52,3528,9,NULL),(100,116,52,900,9,NULL),(101,117,52,770,9,NULL),(102,118,53,220,9,NULL),(103,119,53,671,9,NULL),(104,120,53,2234,9,NULL),(105,121,53,313,9,NULL),(106,122,53,125,9,NULL),(107,123,53,165,9,NULL),(108,124,53,61,9,NULL),(109,125,54,179,9,NULL),(110,126,54,73,9,NULL),(111,127,54,280,9,NULL),(112,128,54,305,9,NULL),(113,129,54,340,9,NULL),(114,130,54,94,9,NULL),(115,131,54,71,9,NULL),(116,132,54,3,9,NULL),(117,133,55,715,9,NULL),(118,134,55,697,9,NULL),(119,135,55,57,9,NULL),(120,136,55,57,9,NULL),(121,137,55,460,9,NULL),(122,138,55,148,9,NULL),(123,139,55,148,9,NULL),(124,140,56,24,5,NULL),(125,141,56,12,5,NULL),(126,142,56,131,5,NULL),(127,143,56,30,5,NULL),(128,144,56,59,8,NULL),(129,145,56,94,8,NULL),(130,146,56,88,6,NULL),(131,147,57,230,7,NULL),(132,148,57,682,7,NULL),(133,149,57,49,7,NULL),(134,150,57,36,7,NULL),(135,151,57,700,9,NULL),(136,152,57,75,9,NULL),(137,153,57,93,9,NULL),(138,154,57,864,9,NULL),(139,155,57,1,9,NULL),(140,156,57,1,9,NULL),(141,34,58,300,2,NULL),(142,35,58,100,2,NULL),(143,36,58,350,2,NULL),(144,33,58,150,2,NULL),(145,157,59,618,9,NULL),(146,158,59,139,9,NULL),(147,159,59,349,9,NULL),(148,160,59,409,9,NULL),(149,161,59,223,9,NULL),(150,162,59,489,9,NULL),(151,163,59,339,9,NULL),(152,164,59,213,9,NULL),(153,165,59,91,9,NULL),(154,166,60,338,5,NULL),(155,167,60,42,2,NULL),(156,168,60,107,2,NULL),(157,169,60,470,2,NULL),(158,170,60,602,2,NULL),(159,171,60,26,2,NULL),(160,172,60,210,2,NULL),(161,173,60,17,5,NULL),(162,174,60,503,5,NULL),(163,175,61,104,5,NULL),(164,176,61,321,2,NULL),(165,177,61,44,2,NULL),(166,178,61,105,2,NULL),(167,179,61,147,2,NULL),(168,180,61,546,2,NULL),(169,181,61,15,2,NULL),(170,182,61,94,2,NULL),(171,183,61,3,2,NULL),(172,184,61,158,2,NULL),(173,185,61,141,2,NULL),(174,186,62,141,2,NULL),(175,187,62,178,2,NULL),(176,188,62,200,6,NULL),(177,189,62,100,6,NULL),(178,190,62,20,6,NULL),(179,191,62,87,6,NULL),(180,192,62,55,6,NULL),(181,193,62,497,6,NULL),(182,194,62,3,5,NULL),(183,195,62,18,6,NULL),(184,196,63,5,6,NULL),(185,197,63,8,6,NULL),(186,198,63,32,6,NULL),(187,199,63,32,6,NULL),(188,200,63,9,6,NULL),(189,201,63,4,6,NULL),(190,202,63,3,6,NULL),(191,203,63,90,5,NULL),(192,204,63,45,5,NULL),(193,205,63,180,5,NULL),(194,206,64,10,7,NULL),(195,207,64,10,7,NULL),(196,208,64,48,7,NULL),(197,210,64,3,7,NULL),(198,211,64,7,7,NULL),(199,212,64,298,7,NULL),(200,213,64,300,7,NULL),(201,214,64,69,7,NULL),(202,215,65,39,7,NULL),(203,219,66,50,1,NULL),(204,220,66,15,7,NULL),(205,221,66,300,7,NULL),(206,222,66,9,7,NULL),(207,223,66,22,7,NULL),(208,224,66,40,7,NULL),(209,225,67,6,5,NULL),(210,230,67,9,9,NULL),(211,231,68,182,6,NULL),(212,233,68,36,6,NULL),(213,232,68,2,7,NULL),(214,234,68,9,7,NULL),(215,235,69,443,7,NULL),(216,236,69,23,7,NULL),(217,237,69,16,7,NULL),(218,238,69,15,7,NULL),(219,239,69,2,7,NULL),(220,240,69,6,7,NULL),(221,241,69,1,7,NULL),(222,242,69,9,7,NULL),(223,243,70,6,7,NULL),(224,244,70,3,7,NULL),(225,245,70,2,7,NULL),(226,246,70,12,7,NULL),(227,247,70,5,7,NULL),(228,248,70,23,7,NULL),(229,250,70,20,7,NULL),(230,251,70,17,7,NULL),(231,252,71,1,7,NULL),(232,253,71,1,7,NULL),(233,255,71,37,6,NULL),(234,256,71,53,7,NULL),(235,257,71,23,7,NULL),(236,258,71,10,7,NULL),(237,259,71,3,7,NULL),(238,260,71,6,7,NULL),(239,261,71,4,7,NULL),(240,262,71,3,7,NULL),(241,263,72,17,7,NULL),(242,264,72,7,7,NULL),(243,265,72,3,7,NULL),(244,266,72,11,7,NULL),(245,267,72,12,7,NULL),(246,268,72,3,7,NULL),(247,270,72,3,7,NULL),(248,272,72,6,7,NULL),(249,273,72,6,7,NULL),(250,274,73,10,6,NULL),(251,275,73,53,5,NULL),(252,276,74,2,7,NULL),(253,277,74,57,4,NULL),(254,278,74,1,7,NULL),(255,279,74,2,7,NULL),(256,280,74,9,6,NULL),(257,281,74,2,7,NULL),(258,282,74,12,4,NULL),(259,283,74,20,5,NULL),(260,284,74,16,5,NULL),(261,285,74,624,7,NULL),(262,167,75,13,2,NULL),(263,286,76,23,7,NULL),(264,209,77,35,7,NULL),(265,289,78,54,2,NULL),(266,290,78,12,2,NULL),(267,291,78,24,2,NULL),(268,1,79,45,1,NULL),(269,133,80,80,2,NULL),(270,1,81,50,1,NULL),(271,292,82,100,1,NULL),(272,71,83,2,1,NULL),(273,277,83,31,1,NULL),(274,293,83,4,1,NULL),(275,1,84,100,1,NULL),(276,56,84,36,3,NULL),(277,189,84,1000,4,NULL),(278,74,84,20,1,NULL),(279,59,84,25,4,NULL),(280,60,84,25,4,NULL),(281,2,85,5,5,NULL),(282,287,86,25,2,NULL),(283,288,86,25,2,NULL);
/*!40000 ALTER TABLE `material_registro_entradas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_registro_salidas`
--

DROP TABLE IF EXISTS `material_registro_salidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_registro_salidas` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `MaterialID` int(10) NOT NULL,
  `Registro_SalidasID` int(10) NOT NULL,
  `Cantidad` int(10) DEFAULT NULL,
  `CasaID` int(10) DEFAULT NULL,
  `ManzanaID` int(10) DEFAULT NULL,
  `DestinoID` int(10) DEFAULT NULL,
  `AreaID` int(10) DEFAULT NULL,
  `RubroID` int(10) DEFAULT NULL,
  `UbicacionID` int(10) DEFAULT NULL,
  `CostoUnitario` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FKMaterial_R456168` (`MaterialID`),
  KEY `FKMaterial_R116924` (`Registro_SalidasID`),
  KEY `FKMaterial_R349492` (`DestinoID`),
  KEY `FKMaterial_R249940` (`AreaID`),
  KEY `FKMaterial_R690450` (`ManzanaID`),
  KEY `FKMaterial_R269180` (`CasaID`),
  KEY `RubroID` (`RubroID`),
  KEY `material_registro_salidas_ubicacion_fk` (`UbicacionID`),
  CONSTRAINT `FKMaterial_R116924` FOREIGN KEY (`Registro_SalidasID`) REFERENCES `registro_salidas` (`ID`),
  CONSTRAINT `FKMaterial_R249940` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`),
  CONSTRAINT `FKMaterial_R269180` FOREIGN KEY (`CasaID`) REFERENCES `casa` (`ID`),
  CONSTRAINT `FKMaterial_R349492` FOREIGN KEY (`DestinoID`) REFERENCES `destino` (`ID`),
  CONSTRAINT `FKMaterial_R456168` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  CONSTRAINT `FKMaterial_R690450` FOREIGN KEY (`ManzanaID`) REFERENCES `manzana` (`ID`),
  CONSTRAINT `material_registro_salidas_ibfk_1` FOREIGN KEY (`RubroID`) REFERENCES `rubro` (`ID`),
  CONSTRAINT `material_registro_salidas_ubicacion_fk` FOREIGN KEY (`UbicacionID`) REFERENCES `ubicacion` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=768 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_salidas`
--

LOCK TABLES `material_registro_salidas` WRITE;
/*!40000 ALTER TABLE `material_registro_salidas` DISABLE KEYS */;
INSERT INTO `material_registro_salidas` VALUES (1,275,1,1,NULL,NULL,5,NULL,3,8,NULL),(2,174,1,3,NULL,NULL,5,NULL,3,8,NULL),(3,175,1,1,NULL,NULL,5,NULL,3,8,NULL),(4,137,1,1,NULL,NULL,5,NULL,3,8,NULL),(5,138,1,1,NULL,NULL,5,NULL,3,8,NULL),(6,139,1,1,NULL,NULL,5,NULL,3,8,NULL),(7,142,1,1,NULL,NULL,5,NULL,3,8,NULL),(8,143,1,1,NULL,NULL,5,NULL,3,8,NULL),(9,130,1,1,NULL,NULL,5,NULL,3,8,NULL),(10,115,1,1,NULL,NULL,5,NULL,3,8,NULL),(11,114,1,1,NULL,NULL,5,NULL,3,8,NULL),(12,96,1,1,NULL,NULL,5,NULL,3,8,NULL),(13,114,1,3,NULL,NULL,5,NULL,3,8,NULL),(14,128,1,3,NULL,NULL,5,NULL,3,8,NULL),(15,204,1,1,NULL,NULL,5,NULL,3,8,NULL),(16,123,1,1,NULL,NULL,5,NULL,3,8,NULL),(17,275,2,1,NULL,NULL,5,NULL,3,9,NULL),(18,137,2,1,NULL,NULL,5,NULL,3,9,NULL),(19,138,2,1,NULL,NULL,5,NULL,3,9,NULL),(20,128,2,3,NULL,NULL,5,NULL,3,3,NULL),(21,114,2,3,NULL,NULL,5,NULL,3,9,NULL),(22,174,2,3,NULL,NULL,5,NULL,3,9,NULL),(23,175,2,1,NULL,NULL,5,NULL,3,9,NULL),(24,204,2,1,NULL,NULL,5,NULL,4,9,NULL),(25,142,2,1,NULL,NULL,5,NULL,4,9,NULL),(26,143,2,1,NULL,NULL,5,NULL,4,9,NULL),(27,139,2,1,NULL,NULL,5,NULL,4,9,NULL),(28,130,2,1,NULL,NULL,5,NULL,5,9,NULL),(29,123,2,1,NULL,NULL,5,NULL,5,9,NULL),(30,114,2,1,NULL,NULL,5,NULL,5,9,NULL),(31,115,2,1,NULL,NULL,5,NULL,5,9,NULL),(32,47,2,1,NULL,NULL,5,NULL,6,9,NULL),(33,95,2,1,NULL,NULL,5,NULL,3,9,NULL),(34,275,3,1,NULL,NULL,5,NULL,3,10,NULL),(35,137,3,1,NULL,NULL,5,NULL,3,10,NULL),(36,138,3,1,NULL,NULL,5,NULL,3,10,NULL),(37,128,3,3,NULL,NULL,5,NULL,3,10,NULL),(38,114,3,3,NULL,NULL,5,NULL,3,10,NULL),(39,174,3,3,NULL,NULL,5,NULL,3,10,NULL),(40,175,3,1,NULL,NULL,5,NULL,3,10,NULL),(41,204,3,1,NULL,NULL,5,NULL,4,10,NULL),(42,142,3,1,NULL,NULL,5,NULL,4,10,NULL),(43,143,3,1,NULL,NULL,5,NULL,4,10,NULL),(44,139,3,1,NULL,NULL,5,NULL,4,10,NULL),(45,130,3,1,NULL,NULL,5,NULL,5,10,NULL),(46,123,3,1,NULL,NULL,5,NULL,5,10,NULL),(47,114,3,1,NULL,NULL,5,NULL,5,10,NULL),(48,115,3,1,NULL,NULL,5,NULL,5,10,NULL),(49,96,3,1,NULL,NULL,5,NULL,5,10,NULL),(50,1,4,60,NULL,NULL,1,NULL,1,36,NULL),(51,1,4,15,NULL,NULL,1,NULL,2,36,NULL),(52,1,5,3,NULL,NULL,1,NULL,9,52,NULL),(53,148,6,4,NULL,NULL,1,NULL,11,36,NULL),(54,255,7,1,NULL,NULL,7,NULL,12,27,NULL),(55,286,7,1,NULL,NULL,7,NULL,12,27,NULL),(56,34,8,110,NULL,NULL,1,NULL,13,36,NULL),(57,35,8,100,NULL,NULL,1,NULL,13,36,NULL),(58,74,8,1,NULL,NULL,1,NULL,13,36,NULL),(59,56,9,2,NULL,NULL,5,NULL,10,52,NULL),(60,54,9,3,NULL,NULL,5,NULL,10,52,NULL),(61,62,9,1,NULL,NULL,5,NULL,10,52,NULL),(62,175,9,2,NULL,NULL,5,NULL,10,52,NULL),(63,44,10,1,NULL,NULL,8,NULL,10,52,NULL),(64,47,10,1,NULL,NULL,8,NULL,10,52,NULL),(65,51,10,1,NULL,NULL,8,NULL,10,52,NULL),(66,275,11,1,NULL,NULL,5,NULL,3,11,NULL),(67,137,11,1,NULL,NULL,5,NULL,3,11,NULL),(68,138,11,1,NULL,NULL,5,NULL,3,11,NULL),(69,128,11,3,NULL,NULL,5,NULL,3,11,NULL),(70,114,11,3,NULL,NULL,5,NULL,3,11,NULL),(71,174,11,3,NULL,NULL,5,NULL,3,11,NULL),(72,175,11,1,NULL,NULL,5,NULL,3,11,NULL),(73,204,11,1,NULL,NULL,5,NULL,4,11,NULL),(74,142,11,1,NULL,NULL,5,NULL,4,11,NULL),(75,143,11,1,NULL,NULL,5,NULL,4,11,NULL),(76,139,11,1,NULL,NULL,5,NULL,4,11,NULL),(77,130,11,1,NULL,NULL,5,NULL,5,11,NULL),(78,123,11,1,NULL,NULL,5,NULL,5,11,NULL),(79,114,11,1,NULL,NULL,5,NULL,5,11,NULL),(80,115,11,1,NULL,NULL,5,NULL,5,11,NULL),(81,96,11,1,NULL,NULL,5,NULL,5,11,NULL),(82,47,11,1,NULL,NULL,5,NULL,6,11,NULL),(83,133,11,1,NULL,NULL,5,NULL,3,11,NULL),(84,275,12,1,NULL,NULL,5,NULL,3,12,NULL),(85,137,12,1,NULL,NULL,5,NULL,3,12,NULL),(86,138,12,1,NULL,NULL,5,NULL,3,12,NULL),(87,128,12,3,NULL,NULL,5,NULL,3,12,NULL),(88,114,12,3,NULL,NULL,5,NULL,3,12,NULL),(89,174,12,3,NULL,NULL,5,NULL,3,12,NULL),(90,175,12,1,NULL,NULL,5,NULL,3,12,NULL),(91,204,12,1,NULL,NULL,5,NULL,4,12,NULL),(92,142,12,1,NULL,NULL,5,NULL,4,12,NULL),(93,143,12,1,NULL,NULL,5,NULL,4,12,NULL),(94,139,12,1,NULL,NULL,5,NULL,4,12,NULL),(95,130,12,1,NULL,NULL,5,NULL,5,12,NULL),(96,123,12,1,NULL,NULL,5,NULL,5,12,NULL),(97,114,12,1,NULL,NULL,6,NULL,5,12,NULL),(98,115,12,1,NULL,NULL,6,NULL,5,12,NULL),(99,96,12,1,NULL,NULL,5,NULL,5,12,NULL),(100,275,13,1,NULL,NULL,5,NULL,3,18,NULL),(101,137,13,1,NULL,NULL,5,NULL,3,18,NULL),(102,138,13,1,NULL,NULL,5,NULL,3,18,NULL),(103,128,13,3,NULL,NULL,5,NULL,3,18,NULL),(104,114,13,3,NULL,NULL,5,NULL,3,18,NULL),(105,174,13,3,NULL,NULL,5,NULL,3,18,NULL),(106,175,13,1,NULL,NULL,5,NULL,3,18,NULL),(107,204,13,1,NULL,NULL,5,NULL,4,18,NULL),(108,142,13,1,NULL,NULL,5,NULL,4,18,NULL),(109,143,13,1,NULL,NULL,5,NULL,4,18,NULL),(110,139,13,1,NULL,NULL,5,NULL,4,18,NULL),(111,130,13,1,NULL,NULL,5,NULL,4,18,NULL),(112,123,13,1,NULL,NULL,5,NULL,4,18,NULL),(113,114,13,1,NULL,NULL,5,NULL,4,18,NULL),(114,115,13,1,NULL,NULL,5,NULL,4,18,NULL),(115,2,13,1,NULL,NULL,5,NULL,3,18,NULL),(116,1,14,6,NULL,NULL,2,NULL,14,32,NULL),(117,1,15,15,NULL,NULL,1,NULL,1,48,NULL),(118,1,16,10,NULL,NULL,1,NULL,1,48,NULL),(119,74,16,2,NULL,NULL,1,NULL,15,48,NULL),(120,1,17,10,NULL,NULL,2,NULL,14,34,NULL),(121,275,18,1,NULL,NULL,5,NULL,3,17,NULL),(122,137,18,1,NULL,NULL,5,NULL,3,17,NULL),(123,138,18,1,NULL,NULL,5,NULL,3,17,NULL),(124,128,18,3,NULL,NULL,5,NULL,3,17,NULL),(125,114,18,3,NULL,NULL,5,NULL,3,17,NULL),(126,174,18,3,NULL,NULL,5,NULL,3,17,NULL),(127,175,18,1,NULL,NULL,5,NULL,3,17,NULL),(128,204,18,1,NULL,NULL,5,NULL,4,17,NULL),(129,142,18,1,NULL,NULL,5,NULL,4,17,NULL),(130,143,18,1,NULL,NULL,5,NULL,4,17,NULL),(131,130,18,1,NULL,NULL,5,NULL,4,17,NULL),(132,123,18,1,NULL,NULL,5,NULL,5,17,NULL),(133,114,18,1,NULL,NULL,5,NULL,5,17,NULL),(134,115,18,1,NULL,NULL,5,NULL,5,17,NULL),(135,96,18,1,NULL,NULL,5,NULL,5,17,NULL),(136,275,19,1,NULL,NULL,5,NULL,3,16,NULL),(137,137,19,1,NULL,NULL,5,NULL,3,16,NULL),(138,138,19,1,NULL,NULL,5,NULL,3,16,NULL),(139,128,19,3,NULL,NULL,5,NULL,3,16,NULL),(140,114,19,3,NULL,NULL,5,NULL,3,16,NULL),(141,174,19,3,NULL,NULL,5,NULL,3,16,NULL),(142,175,19,1,NULL,NULL,5,NULL,3,16,NULL),(143,204,19,1,NULL,NULL,5,NULL,4,16,NULL),(144,142,19,1,NULL,NULL,5,NULL,4,16,NULL),(145,143,19,1,NULL,NULL,5,NULL,4,16,NULL),(146,139,19,1,NULL,NULL,5,NULL,4,16,NULL),(147,130,19,1,NULL,NULL,5,NULL,5,16,NULL),(148,123,19,1,NULL,NULL,5,NULL,5,16,NULL),(149,114,19,1,NULL,NULL,5,NULL,5,16,NULL),(150,115,19,1,NULL,NULL,5,NULL,5,16,NULL),(151,275,20,1,NULL,NULL,5,NULL,3,15,NULL),(152,137,20,1,NULL,NULL,5,NULL,3,15,NULL),(153,138,20,1,NULL,NULL,5,NULL,3,15,NULL),(154,128,20,3,NULL,NULL,5,NULL,3,15,NULL),(155,114,20,3,NULL,NULL,5,NULL,3,15,NULL),(156,174,20,3,NULL,NULL,5,NULL,3,15,NULL),(157,175,20,1,NULL,NULL,5,NULL,3,15,NULL),(158,204,20,1,NULL,NULL,5,NULL,4,15,NULL),(159,142,20,1,NULL,NULL,5,NULL,4,15,NULL),(160,143,20,1,NULL,NULL,5,NULL,4,15,NULL),(161,139,20,1,NULL,NULL,5,NULL,4,15,NULL),(162,130,20,1,NULL,NULL,5,NULL,5,15,NULL),(163,123,20,1,NULL,NULL,5,NULL,5,15,NULL),(164,114,20,1,NULL,NULL,5,NULL,5,15,NULL),(165,115,20,1,NULL,NULL,5,NULL,5,15,NULL),(166,96,20,1,NULL,NULL,5,NULL,5,15,NULL),(167,1,21,10,NULL,NULL,1,NULL,1,48,NULL),(168,212,22,20,NULL,NULL,7,NULL,17,48,NULL),(169,213,22,20,NULL,NULL,7,NULL,15,48,NULL),(170,221,22,20,NULL,NULL,7,NULL,17,48,NULL),(171,209,22,10,NULL,NULL,7,NULL,17,48,NULL),(172,208,22,1,NULL,NULL,7,NULL,17,48,NULL),(173,275,23,1,NULL,NULL,5,NULL,3,13,NULL),(174,137,23,1,NULL,NULL,5,NULL,3,13,NULL),(175,138,23,1,NULL,NULL,5,NULL,3,13,NULL),(176,128,23,3,NULL,NULL,5,NULL,3,13,NULL),(177,114,23,3,NULL,NULL,5,NULL,3,13,NULL),(178,174,23,3,NULL,NULL,5,NULL,3,13,NULL),(179,175,23,1,NULL,NULL,5,NULL,3,13,NULL),(180,204,23,1,NULL,NULL,5,NULL,4,13,NULL),(181,142,23,1,NULL,NULL,5,NULL,4,13,NULL),(182,143,23,1,NULL,NULL,5,NULL,4,13,NULL),(183,139,23,1,NULL,NULL,5,NULL,4,13,NULL),(184,130,23,1,NULL,NULL,5,NULL,5,13,NULL),(185,123,23,1,NULL,NULL,5,NULL,5,13,NULL),(186,114,23,1,NULL,NULL,5,NULL,5,13,NULL),(187,114,23,1,NULL,NULL,5,NULL,5,13,NULL),(188,96,23,1,NULL,NULL,5,NULL,5,13,NULL),(189,275,23,1,NULL,NULL,5,NULL,3,14,NULL),(190,137,23,1,NULL,NULL,5,NULL,3,14,NULL),(191,138,23,1,NULL,NULL,5,NULL,3,14,NULL),(192,128,23,3,NULL,NULL,5,NULL,3,14,NULL),(193,114,23,3,NULL,NULL,5,NULL,3,14,NULL),(194,174,23,3,NULL,NULL,5,NULL,3,14,NULL),(195,175,23,1,NULL,NULL,5,NULL,3,14,NULL),(196,204,23,1,NULL,NULL,5,NULL,4,14,NULL),(197,142,23,1,NULL,NULL,5,NULL,4,14,NULL),(198,143,23,1,NULL,NULL,5,NULL,4,14,NULL),(199,139,23,1,NULL,NULL,5,NULL,4,14,NULL),(200,130,23,1,NULL,NULL,5,NULL,5,14,NULL),(201,123,23,1,NULL,NULL,5,NULL,5,14,NULL),(202,114,23,1,NULL,NULL,5,NULL,5,14,NULL),(203,115,23,1,NULL,NULL,5,NULL,5,14,NULL),(204,96,23,1,NULL,NULL,5,NULL,5,14,NULL),(205,1,24,10,NULL,NULL,1,NULL,1,48,NULL),(206,74,24,2,NULL,NULL,1,NULL,15,48,NULL),(207,1,25,6,NULL,NULL,2,NULL,14,33,NULL),(208,285,26,24,NULL,NULL,7,NULL,10,54,NULL),(209,108,26,120,NULL,NULL,7,NULL,10,54,NULL),(210,98,27,3,NULL,NULL,2,NULL,18,31,NULL),(211,97,27,3,NULL,NULL,2,NULL,18,31,NULL),(212,100,27,15,NULL,NULL,2,NULL,18,31,NULL),(213,101,27,6,NULL,NULL,2,NULL,18,31,NULL),(214,109,27,6,NULL,NULL,2,NULL,18,31,NULL),(215,104,27,2,NULL,NULL,2,NULL,18,31,NULL),(216,103,27,2,NULL,NULL,2,NULL,18,31,NULL),(217,111,27,6,NULL,NULL,2,NULL,18,31,NULL),(218,120,27,6,NULL,NULL,2,NULL,18,31,NULL),(219,133,27,1,NULL,NULL,2,NULL,18,31,NULL),(220,98,27,6,NULL,NULL,2,NULL,18,32,NULL),(221,97,27,6,NULL,NULL,2,NULL,18,32,NULL),(222,100,27,15,NULL,NULL,2,NULL,18,32,NULL),(223,101,27,6,NULL,NULL,2,NULL,18,32,NULL),(224,109,27,6,NULL,NULL,2,NULL,18,32,NULL),(225,104,27,2,NULL,NULL,2,NULL,18,32,NULL),(226,103,27,2,NULL,NULL,2,NULL,18,32,NULL),(227,120,27,6,NULL,NULL,2,NULL,18,32,NULL),(228,163,28,3,NULL,NULL,2,NULL,21,31,NULL),(229,157,28,9,NULL,NULL,2,NULL,21,31,NULL),(230,166,28,3,NULL,NULL,2,NULL,21,31,NULL),(231,176,28,1,NULL,NULL,2,NULL,21,31,NULL),(232,148,28,3,NULL,NULL,2,NULL,21,31,NULL),(233,147,28,2,NULL,NULL,2,NULL,21,31,NULL),(234,146,28,1,NULL,NULL,2,NULL,21,31,NULL),(235,160,28,1,NULL,NULL,2,NULL,21,31,NULL),(236,186,28,1,NULL,NULL,2,NULL,21,31,NULL),(237,185,28,1,NULL,NULL,2,NULL,21,31,NULL),(238,164,28,5,NULL,NULL,2,NULL,21,31,NULL),(239,184,28,5,NULL,NULL,2,NULL,20,31,NULL),(240,148,28,5,NULL,NULL,2,NULL,20,31,NULL),(241,169,28,3,NULL,NULL,2,NULL,21,31,NULL),(242,170,28,4,NULL,NULL,2,NULL,21,31,NULL),(243,163,29,3,NULL,NULL,2,NULL,21,32,NULL),(244,157,29,7,NULL,NULL,2,NULL,21,32,NULL),(245,166,29,3,NULL,NULL,2,NULL,21,32,NULL),(246,176,29,1,NULL,NULL,2,NULL,21,32,NULL),(247,148,29,3,NULL,NULL,2,NULL,21,32,NULL),(248,147,29,2,NULL,NULL,2,NULL,21,32,NULL),(249,146,29,1,NULL,NULL,2,NULL,21,32,NULL),(250,160,29,10,NULL,NULL,2,NULL,21,32,NULL),(251,186,29,1,NULL,NULL,2,NULL,21,32,NULL),(252,185,29,1,NULL,NULL,2,NULL,21,32,NULL),(253,164,29,5,NULL,NULL,2,NULL,21,32,NULL),(254,184,29,5,NULL,NULL,2,NULL,20,32,NULL),(255,148,29,5,NULL,NULL,2,NULL,20,32,NULL),(256,169,29,3,NULL,NULL,2,NULL,21,32,NULL),(257,170,29,5,NULL,NULL,2,NULL,21,32,NULL),(258,133,29,1,NULL,NULL,2,NULL,21,32,NULL),(259,134,29,1,NULL,NULL,2,NULL,21,32,NULL),(260,13,30,100,NULL,NULL,8,NULL,23,48,NULL),(261,18,30,50,NULL,NULL,8,NULL,23,48,NULL),(262,5,30,10,NULL,NULL,8,NULL,23,48,NULL),(263,189,31,100,NULL,NULL,1,NULL,25,25,NULL),(264,52,32,3,NULL,NULL,8,NULL,23,48,NULL),(265,146,33,1,NULL,NULL,2,NULL,21,33,NULL),(266,148,33,4,NULL,NULL,2,NULL,21,33,NULL),(267,147,33,2,NULL,NULL,2,NULL,21,33,NULL),(268,169,33,3,NULL,NULL,2,NULL,21,33,NULL),(269,170,33,7,NULL,NULL,2,NULL,21,33,NULL),(270,176,33,1,NULL,NULL,2,NULL,21,33,NULL),(271,153,33,1,NULL,NULL,2,NULL,21,33,NULL),(272,154,33,1,NULL,NULL,2,NULL,21,33,NULL),(273,133,33,1,NULL,NULL,2,NULL,21,33,NULL),(274,157,33,4,NULL,NULL,2,NULL,17,33,NULL),(275,148,34,4,NULL,NULL,2,NULL,21,34,NULL),(276,147,34,2,NULL,NULL,2,NULL,21,34,NULL),(277,146,34,1,NULL,NULL,2,NULL,21,34,NULL),(278,166,34,3,NULL,NULL,2,NULL,21,34,NULL),(279,163,34,3,NULL,NULL,2,NULL,21,34,NULL),(280,176,34,1,NULL,NULL,2,NULL,21,34,NULL),(281,160,34,2,NULL,NULL,2,NULL,21,34,NULL),(282,169,34,3,NULL,NULL,2,NULL,21,34,NULL),(283,170,34,7,NULL,NULL,2,NULL,21,34,NULL),(284,153,34,2,NULL,NULL,2,NULL,21,34,NULL),(285,154,34,2,NULL,NULL,2,NULL,21,34,NULL),(286,157,34,5,NULL,NULL,2,NULL,21,34,NULL),(287,185,34,1,NULL,NULL,2,NULL,21,34,NULL),(288,185,34,1,NULL,NULL,2,NULL,21,33,NULL),(289,166,34,3,NULL,NULL,2,NULL,21,33,NULL),(290,163,34,3,NULL,NULL,2,NULL,21,33,NULL),(291,1,35,15,NULL,NULL,1,NULL,1,48,NULL),(292,51,36,3,NULL,NULL,2,NULL,28,32,NULL),(293,16,37,10,NULL,NULL,1,NULL,23,48,NULL),(294,52,37,2,NULL,NULL,1,NULL,23,48,NULL),(295,285,38,25,NULL,NULL,7,NULL,10,54,NULL),(296,48,39,1,NULL,NULL,8,NULL,29,48,NULL),(297,98,40,3,NULL,NULL,2,NULL,18,33,NULL),(298,97,40,3,NULL,NULL,2,NULL,18,33,NULL),(299,100,40,7,NULL,NULL,2,NULL,18,33,NULL),(300,101,40,6,NULL,NULL,2,NULL,18,33,NULL),(301,109,40,4,NULL,NULL,2,NULL,18,33,NULL),(302,104,40,2,NULL,NULL,2,NULL,18,33,NULL),(303,103,40,2,NULL,NULL,2,NULL,18,33,NULL),(304,111,40,6,NULL,NULL,2,NULL,18,33,NULL),(305,120,40,6,NULL,NULL,2,NULL,18,33,NULL),(306,133,40,1,NULL,NULL,2,NULL,18,33,NULL),(307,98,40,3,NULL,NULL,2,NULL,18,34,NULL),(308,97,40,3,NULL,NULL,2,NULL,18,34,NULL),(309,100,40,7,NULL,NULL,2,NULL,18,34,NULL),(310,101,40,6,NULL,NULL,2,NULL,18,34,NULL),(311,109,40,4,NULL,NULL,2,NULL,18,34,NULL),(312,104,40,2,NULL,NULL,2,NULL,18,34,NULL),(313,103,40,2,NULL,NULL,2,NULL,18,34,NULL),(314,111,40,6,NULL,NULL,2,NULL,18,34,NULL),(315,120,40,6,NULL,NULL,2,NULL,18,34,NULL),(316,1,41,10,NULL,NULL,1,NULL,1,48,NULL),(317,1,41,7,NULL,NULL,1,NULL,30,48,NULL),(318,17,42,7,NULL,NULL,8,NULL,23,48,NULL),(319,51,43,3,NULL,NULL,2,NULL,28,33,NULL),(320,59,44,2,NULL,NULL,1,NULL,1,48,NULL),(321,60,44,2,NULL,NULL,1,NULL,1,48,NULL),(322,148,45,4,NULL,NULL,2,NULL,21,35,NULL),(323,147,45,2,NULL,NULL,2,NULL,21,35,NULL),(324,146,45,1,NULL,NULL,2,NULL,21,35,NULL),(325,166,45,3,NULL,NULL,2,NULL,21,35,NULL),(326,163,45,3,NULL,NULL,2,NULL,21,35,NULL),(327,176,45,2,NULL,NULL,2,NULL,21,35,NULL),(328,160,45,2,NULL,NULL,2,NULL,21,35,NULL),(329,169,45,3,NULL,NULL,2,NULL,21,35,NULL),(330,170,45,7,NULL,NULL,2,NULL,21,35,NULL),(331,153,45,2,NULL,NULL,2,NULL,21,35,NULL),(332,154,45,2,NULL,NULL,2,NULL,21,35,NULL),(333,157,45,5,NULL,NULL,2,NULL,21,35,NULL),(334,185,45,1,NULL,NULL,2,NULL,21,35,NULL),(335,133,45,1,NULL,NULL,2,NULL,21,35,NULL),(336,148,45,4,NULL,NULL,2,NULL,21,36,NULL),(337,147,45,2,NULL,NULL,2,NULL,21,36,NULL),(338,166,45,3,NULL,NULL,2,NULL,21,36,NULL),(339,163,45,2,NULL,NULL,2,NULL,21,36,NULL),(340,176,45,1,NULL,NULL,2,NULL,21,36,NULL),(341,163,45,1,NULL,NULL,2,NULL,21,36,NULL),(342,176,45,2,NULL,NULL,2,NULL,21,36,NULL),(343,160,45,2,NULL,NULL,2,NULL,21,36,NULL),(344,169,45,3,NULL,NULL,2,NULL,21,36,NULL),(345,170,45,7,NULL,NULL,2,NULL,21,36,NULL),(346,153,45,2,NULL,NULL,2,NULL,21,36,NULL),(347,154,45,2,NULL,NULL,2,NULL,21,36,NULL),(348,157,45,5,NULL,NULL,2,NULL,21,36,NULL),(349,185,45,1,NULL,NULL,2,NULL,21,36,NULL),(350,184,46,4,NULL,NULL,2,NULL,20,33,NULL),(351,148,46,5,NULL,NULL,2,NULL,20,33,NULL),(352,160,46,4,NULL,NULL,2,NULL,20,33,NULL),(353,164,46,2,NULL,NULL,2,NULL,20,33,NULL),(354,184,46,4,NULL,NULL,2,NULL,20,34,NULL),(355,148,46,5,NULL,NULL,2,NULL,20,34,NULL),(356,160,46,4,NULL,NULL,2,NULL,20,34,NULL),(357,164,46,2,NULL,NULL,2,NULL,20,34,NULL),(358,184,46,4,NULL,NULL,2,NULL,20,35,NULL),(359,148,46,5,NULL,NULL,2,NULL,20,35,NULL),(360,160,46,4,NULL,NULL,2,NULL,20,35,NULL),(361,164,46,2,NULL,NULL,2,NULL,20,35,NULL),(362,133,46,1,NULL,NULL,2,NULL,20,35,NULL),(363,184,46,4,NULL,NULL,2,NULL,20,36,NULL),(364,148,46,5,NULL,NULL,2,NULL,20,36,NULL),(365,160,46,4,NULL,NULL,2,NULL,20,36,NULL),(366,164,46,2,NULL,NULL,2,NULL,20,36,NULL),(367,23,47,452,NULL,NULL,2,NULL,8,31,NULL),(368,24,47,60,NULL,NULL,2,NULL,8,31,NULL),(369,22,47,540,NULL,NULL,2,NULL,31,31,NULL),(370,5,47,3,NULL,NULL,2,NULL,32,31,NULL),(371,15,47,9,NULL,NULL,2,NULL,26,31,NULL),(372,15,47,13,NULL,NULL,2,NULL,31,31,NULL),(373,15,47,25,NULL,NULL,2,NULL,8,31,NULL),(374,16,47,2,NULL,NULL,2,NULL,27,31,NULL),(375,11,47,15,NULL,NULL,2,NULL,8,31,NULL),(376,23,48,452,NULL,NULL,2,NULL,8,32,NULL),(377,24,48,60,NULL,NULL,2,NULL,8,32,NULL),(378,22,48,540,NULL,NULL,2,NULL,31,32,NULL),(379,5,48,4,NULL,NULL,2,NULL,32,32,NULL),(380,15,48,9,NULL,NULL,2,NULL,26,32,NULL),(381,15,48,13,NULL,NULL,2,NULL,31,32,NULL),(382,15,48,25,NULL,NULL,2,NULL,8,32,NULL),(383,16,48,1,NULL,NULL,2,NULL,27,32,NULL),(384,11,48,15,NULL,NULL,2,NULL,31,32,NULL),(385,52,48,1,NULL,NULL,2,NULL,8,32,NULL),(386,23,48,452,NULL,NULL,2,NULL,8,33,NULL),(387,24,48,60,NULL,NULL,2,NULL,8,33,NULL),(388,22,48,540,NULL,NULL,2,NULL,31,33,NULL),(389,5,48,4,NULL,NULL,2,NULL,8,33,NULL),(390,15,48,9,NULL,NULL,2,NULL,26,33,NULL),(391,15,48,13,NULL,NULL,2,NULL,31,33,NULL),(392,15,48,25,NULL,NULL,2,NULL,8,33,NULL),(393,16,48,1,NULL,NULL,2,NULL,27,33,NULL),(394,11,48,15,NULL,NULL,2,NULL,8,33,NULL),(395,23,49,452,NULL,NULL,2,NULL,8,34,NULL),(396,24,49,60,NULL,NULL,2,NULL,8,34,NULL),(397,22,49,540,NULL,NULL,2,NULL,31,34,NULL),(398,5,49,4,NULL,NULL,2,NULL,32,34,NULL),(399,15,49,9,NULL,NULL,2,NULL,26,34,NULL),(400,15,49,13,NULL,NULL,2,NULL,31,34,NULL),(401,15,49,25,NULL,NULL,2,NULL,8,34,NULL),(402,16,49,1,NULL,NULL,2,NULL,27,34,NULL),(403,11,49,15,NULL,NULL,2,NULL,8,34,NULL),(404,52,49,1,NULL,NULL,2,NULL,31,34,NULL),(405,23,49,452,NULL,NULL,2,NULL,8,35,NULL),(406,24,49,60,NULL,NULL,2,NULL,8,35,NULL),(407,22,49,540,NULL,NULL,2,NULL,31,35,NULL),(408,5,49,3,NULL,NULL,2,NULL,32,35,NULL),(409,15,49,9,NULL,NULL,2,NULL,26,35,NULL),(410,15,49,13,NULL,NULL,2,NULL,31,35,NULL),(411,15,49,25,NULL,NULL,2,NULL,8,35,NULL),(412,16,49,1,NULL,NULL,2,NULL,27,35,NULL),(413,11,49,15,NULL,NULL,2,NULL,31,35,NULL),(414,23,50,452,NULL,NULL,2,NULL,8,36,NULL),(415,24,50,60,NULL,NULL,2,NULL,8,36,NULL),(416,22,50,540,NULL,NULL,2,NULL,31,36,NULL),(417,5,50,4,NULL,NULL,2,NULL,32,36,NULL),(418,15,50,9,NULL,NULL,2,NULL,26,36,NULL),(419,15,50,13,NULL,NULL,2,NULL,31,36,NULL),(420,15,50,25,NULL,NULL,2,NULL,8,36,NULL),(421,16,50,1,NULL,NULL,2,NULL,27,36,NULL),(422,11,50,10,NULL,NULL,2,NULL,8,36,NULL),(423,1,51,6,NULL,NULL,2,NULL,14,35,NULL),(424,1,52,17,NULL,NULL,1,NULL,1,48,NULL),(425,5,53,7,NULL,NULL,8,NULL,23,48,NULL),(426,18,53,10,NULL,NULL,8,NULL,23,48,NULL),(427,52,53,2,NULL,NULL,8,NULL,23,48,NULL),(428,16,53,20,NULL,NULL,8,NULL,23,48,NULL),(429,39,54,5,NULL,NULL,1,NULL,25,25,NULL),(430,40,54,5,NULL,NULL,1,NULL,25,25,NULL),(431,41,54,2,NULL,NULL,1,NULL,25,25,NULL),(432,38,55,30,NULL,NULL,1,NULL,34,48,NULL),(433,38,56,20,NULL,NULL,1,NULL,35,28,NULL),(434,16,57,5,NULL,NULL,1,NULL,13,48,NULL),(435,74,57,1,NULL,NULL,1,NULL,13,48,NULL),(436,98,58,3,NULL,NULL,2,NULL,18,36,NULL),(437,97,58,3,NULL,NULL,2,NULL,18,36,NULL),(438,100,58,7,NULL,NULL,2,NULL,18,36,NULL),(439,101,58,6,NULL,NULL,2,NULL,18,36,NULL),(440,109,58,4,NULL,NULL,2,NULL,18,36,NULL),(441,104,58,6,NULL,NULL,2,NULL,18,36,NULL),(442,103,58,2,NULL,NULL,2,NULL,18,36,NULL),(443,111,58,6,NULL,NULL,2,NULL,18,36,NULL),(444,120,58,6,NULL,NULL,2,NULL,18,36,NULL),(445,133,58,1,NULL,NULL,2,NULL,18,36,NULL),(446,98,58,3,NULL,NULL,2,NULL,18,36,NULL),(447,97,58,3,NULL,NULL,2,NULL,18,36,NULL),(448,100,58,7,NULL,NULL,2,NULL,18,36,NULL),(449,101,58,6,NULL,NULL,2,NULL,18,36,NULL),(450,109,58,4,NULL,NULL,2,NULL,18,36,NULL),(451,104,58,2,NULL,NULL,2,NULL,18,36,NULL),(452,103,58,2,NULL,NULL,2,NULL,18,36,NULL),(453,111,58,6,NULL,NULL,2,NULL,18,36,NULL),(454,120,58,6,NULL,NULL,2,NULL,18,36,NULL),(455,5,59,5,NULL,NULL,8,NULL,23,48,NULL),(456,16,59,20,NULL,NULL,8,NULL,23,48,NULL),(457,18,59,40,NULL,NULL,8,NULL,23,48,NULL),(458,1,60,16,NULL,NULL,1,NULL,1,48,NULL),(459,1,61,5,NULL,NULL,2,NULL,14,37,NULL),(460,1,61,5,NULL,NULL,2,NULL,14,38,NULL),(461,52,62,2,NULL,NULL,1,NULL,23,48,NULL),(462,290,63,8,NULL,NULL,2,NULL,33,38,NULL),(463,133,64,60,NULL,NULL,4,NULL,10,28,NULL),(464,235,65,40,NULL,NULL,1,NULL,16,48,NULL),(465,100,65,40,NULL,NULL,1,NULL,16,48,NULL),(466,115,65,40,NULL,NULL,1,NULL,16,48,NULL),(467,111,65,40,NULL,NULL,1,NULL,16,48,NULL),(468,103,65,21,NULL,NULL,1,NULL,16,48,NULL),(469,208,65,1,NULL,NULL,1,NULL,16,48,NULL),(470,231,65,17,NULL,NULL,1,NULL,16,48,NULL),(471,96,65,3,NULL,NULL,1,NULL,16,48,NULL),(472,133,65,1,NULL,NULL,1,NULL,16,48,NULL),(473,277,66,3,NULL,NULL,5,NULL,36,23,NULL),(474,98,67,3,NULL,NULL,2,NULL,18,37,NULL),(475,97,67,3,NULL,NULL,2,NULL,18,37,NULL),(476,100,67,7,NULL,NULL,2,NULL,18,37,NULL),(477,101,67,6,NULL,NULL,2,NULL,18,37,NULL),(478,109,67,4,NULL,NULL,2,NULL,18,37,NULL),(479,104,67,2,NULL,NULL,2,NULL,18,37,NULL),(480,103,67,2,NULL,NULL,2,NULL,18,37,NULL),(481,111,67,6,NULL,NULL,2,NULL,18,37,NULL),(482,120,67,6,NULL,NULL,2,NULL,18,37,NULL),(483,133,67,1,NULL,NULL,2,NULL,18,37,NULL),(484,98,67,3,NULL,NULL,2,NULL,18,38,NULL),(485,97,67,3,NULL,NULL,2,NULL,18,38,NULL),(486,100,67,7,NULL,NULL,2,NULL,18,38,NULL),(487,101,67,6,NULL,NULL,2,NULL,18,38,NULL),(488,109,67,4,NULL,NULL,2,NULL,18,38,NULL),(489,104,67,2,NULL,NULL,2,NULL,18,38,NULL),(490,103,67,2,NULL,NULL,2,NULL,18,38,NULL),(491,111,67,6,NULL,NULL,2,NULL,18,38,NULL),(492,120,67,6,NULL,NULL,2,NULL,18,38,NULL),(493,267,68,1,NULL,NULL,1,NULL,16,48,NULL),(494,255,68,1,NULL,NULL,1,NULL,16,48,NULL),(495,148,69,4,NULL,NULL,2,NULL,21,37,NULL),(496,147,69,2,NULL,NULL,2,NULL,21,37,NULL),(497,146,69,1,NULL,NULL,2,NULL,21,37,NULL),(498,166,69,3,NULL,NULL,2,NULL,21,37,NULL),(499,163,69,3,NULL,NULL,2,NULL,21,37,NULL),(500,176,69,2,NULL,NULL,2,NULL,21,37,NULL),(501,160,69,2,NULL,NULL,2,NULL,21,37,NULL),(502,169,69,3,NULL,NULL,2,NULL,21,37,NULL),(503,170,69,6,NULL,NULL,2,NULL,21,37,NULL),(504,153,69,1,NULL,NULL,2,NULL,21,37,NULL),(505,154,69,1,NULL,NULL,2,NULL,21,37,NULL),(506,157,69,5,NULL,NULL,2,NULL,21,37,NULL),(507,185,69,2,NULL,NULL,2,NULL,21,37,NULL),(508,148,69,5,NULL,NULL,2,NULL,21,38,NULL),(509,147,69,2,NULL,NULL,2,NULL,21,38,NULL),(510,146,69,1,NULL,NULL,2,NULL,21,38,NULL),(511,166,69,3,NULL,NULL,2,NULL,21,38,NULL),(512,163,69,3,NULL,NULL,2,NULL,21,38,NULL),(513,176,69,2,NULL,NULL,2,NULL,21,38,NULL),(514,160,69,2,NULL,NULL,2,NULL,21,38,NULL),(515,169,69,3,NULL,NULL,2,NULL,21,38,NULL),(516,170,69,6,NULL,NULL,2,NULL,21,38,NULL),(517,157,69,5,NULL,NULL,2,NULL,21,38,NULL),(518,185,69,2,NULL,NULL,2,NULL,21,38,NULL),(519,133,69,1,NULL,NULL,2,NULL,21,38,NULL),(520,159,69,2,NULL,NULL,2,NULL,21,37,NULL),(521,159,69,3,NULL,NULL,2,NULL,21,38,NULL),(522,148,70,5,NULL,NULL,2,NULL,20,37,NULL),(523,184,70,4,NULL,NULL,2,NULL,20,37,NULL),(524,164,70,1,NULL,NULL,2,NULL,20,37,NULL),(525,160,70,4,NULL,NULL,2,NULL,20,37,NULL),(526,148,70,5,NULL,NULL,2,NULL,20,38,NULL),(527,184,70,4,NULL,NULL,2,NULL,20,38,NULL),(528,164,70,1,NULL,NULL,2,NULL,20,38,NULL),(529,160,70,4,NULL,NULL,2,NULL,20,38,NULL),(530,148,70,5,NULL,NULL,2,NULL,20,39,NULL),(531,184,70,4,NULL,NULL,2,NULL,20,39,NULL),(532,164,70,1,NULL,NULL,2,NULL,20,39,NULL),(533,160,70,4,NULL,NULL,2,NULL,20,39,NULL),(534,148,70,5,NULL,NULL,2,NULL,20,40,NULL),(535,184,70,4,NULL,NULL,2,NULL,20,40,NULL),(536,164,70,1,NULL,NULL,2,NULL,20,40,NULL),(537,160,70,4,NULL,NULL,2,NULL,20,40,NULL),(538,133,70,1,NULL,NULL,2,NULL,20,40,NULL),(539,285,71,75,NULL,NULL,4,NULL,10,54,NULL),(540,1,72,5,NULL,NULL,2,NULL,14,39,NULL),(541,1,72,5,NULL,NULL,2,NULL,14,40,NULL),(542,1,73,10,NULL,NULL,1,NULL,1,48,NULL),(543,1,73,10,NULL,NULL,1,NULL,30,48,NULL),(544,71,74,2,NULL,NULL,8,NULL,23,50,NULL),(545,74,75,2,NULL,NULL,2,NULL,13,48,NULL),(546,292,76,20,NULL,NULL,1,NULL,1,48,NULL),(547,292,76,10,NULL,NULL,1,NULL,30,48,NULL),(548,292,77,5,NULL,NULL,2,NULL,14,41,NULL),(549,36,77,50,NULL,NULL,2,NULL,7,42,NULL),(550,292,77,5,NULL,NULL,2,NULL,14,42,NULL),(551,52,77,5,NULL,NULL,3,NULL,8,31,NULL),(552,56,77,9,NULL,NULL,3,NULL,37,32,NULL),(553,50,77,10,NULL,NULL,3,NULL,38,32,NULL),(554,51,77,8,NULL,NULL,3,NULL,38,31,NULL),(555,256,78,1,NULL,NULL,1,NULL,39,48,NULL),(556,128,78,5,NULL,NULL,1,NULL,39,48,NULL),(557,114,78,5,NULL,NULL,1,NULL,39,48,NULL),(558,96,78,1,NULL,NULL,1,NULL,39,48,NULL),(559,275,79,1,NULL,NULL,5,NULL,3,22,NULL),(560,137,79,1,NULL,NULL,5,NULL,3,22,NULL),(561,138,79,1,NULL,NULL,5,NULL,3,22,NULL),(562,128,79,3,NULL,NULL,5,NULL,3,22,NULL),(563,114,79,3,NULL,NULL,5,NULL,3,22,NULL),(564,174,79,3,NULL,NULL,5,NULL,3,22,NULL),(565,175,79,1,NULL,NULL,5,NULL,3,22,NULL),(566,204,79,1,NULL,NULL,5,NULL,4,22,NULL),(567,142,79,1,NULL,NULL,5,NULL,4,22,NULL),(568,143,79,1,NULL,NULL,5,NULL,4,22,NULL),(569,139,79,1,NULL,NULL,5,NULL,4,22,NULL),(570,130,79,1,NULL,NULL,5,NULL,5,22,NULL),(571,123,79,1,NULL,NULL,5,NULL,5,22,NULL),(572,114,79,1,NULL,NULL,5,NULL,5,22,NULL),(573,115,79,1,NULL,NULL,5,NULL,5,22,NULL),(574,47,79,1,NULL,NULL,5,NULL,6,22,NULL),(575,275,80,1,NULL,NULL,5,NULL,3,21,NULL),(576,137,80,1,NULL,NULL,5,NULL,3,21,NULL),(577,138,80,1,NULL,NULL,5,NULL,3,21,NULL),(578,128,80,3,NULL,NULL,5,NULL,3,21,NULL),(579,114,80,3,NULL,NULL,5,NULL,3,21,NULL),(580,174,80,3,NULL,NULL,5,NULL,3,21,NULL),(581,175,80,1,NULL,NULL,5,NULL,3,21,NULL),(582,204,80,1,NULL,NULL,5,NULL,4,21,NULL),(583,142,80,1,NULL,NULL,5,NULL,4,21,NULL),(584,143,80,1,NULL,NULL,5,NULL,4,21,NULL),(585,139,80,1,NULL,NULL,5,NULL,4,21,NULL),(586,130,80,1,NULL,NULL,5,NULL,5,21,NULL),(587,123,80,1,NULL,NULL,5,NULL,5,21,NULL),(588,114,80,1,NULL,NULL,5,NULL,5,21,NULL),(589,115,80,1,NULL,NULL,5,NULL,5,21,NULL),(590,96,80,1,NULL,NULL,5,NULL,5,21,NULL),(591,275,81,1,NULL,NULL,5,NULL,3,20,NULL),(592,137,81,1,NULL,NULL,5,NULL,3,20,NULL),(593,138,81,1,NULL,NULL,5,NULL,3,20,NULL),(594,128,81,3,NULL,NULL,5,NULL,3,20,NULL),(595,114,81,3,NULL,NULL,5,NULL,3,20,NULL),(596,174,81,3,NULL,NULL,5,NULL,3,20,NULL),(597,175,81,1,NULL,NULL,5,NULL,3,20,NULL),(598,204,81,1,NULL,NULL,5,NULL,4,20,NULL),(599,142,81,1,NULL,NULL,5,NULL,4,20,NULL),(600,143,81,1,NULL,NULL,5,NULL,4,20,NULL),(601,139,81,1,NULL,NULL,5,NULL,4,20,NULL),(602,130,81,1,NULL,NULL,5,NULL,4,20,NULL),(603,123,81,1,NULL,NULL,5,NULL,5,20,NULL),(604,114,81,1,NULL,NULL,5,NULL,5,20,NULL),(605,115,81,1,NULL,NULL,5,NULL,5,20,NULL),(606,47,81,1,NULL,NULL,5,NULL,6,20,NULL),(607,95,81,1,NULL,NULL,5,NULL,5,20,NULL),(608,133,81,1,NULL,NULL,5,NULL,5,20,NULL),(609,98,82,3,NULL,NULL,2,NULL,18,39,NULL),(610,97,82,3,NULL,NULL,2,NULL,18,39,NULL),(611,100,82,7,NULL,NULL,2,NULL,18,39,NULL),(612,101,82,6,NULL,NULL,2,NULL,18,39,NULL),(613,109,82,5,NULL,NULL,2,NULL,18,39,NULL),(614,104,82,2,NULL,NULL,2,NULL,18,39,NULL),(615,103,82,2,NULL,NULL,2,NULL,18,39,NULL),(616,111,82,6,NULL,NULL,2,NULL,18,39,NULL),(617,120,82,6,NULL,NULL,2,NULL,18,39,NULL),(618,133,82,1,NULL,NULL,2,NULL,18,39,NULL),(619,98,82,3,NULL,NULL,2,NULL,18,40,NULL),(620,97,82,3,NULL,NULL,2,NULL,18,40,NULL),(621,100,82,7,NULL,NULL,2,NULL,18,40,NULL),(622,101,82,6,NULL,NULL,2,NULL,18,40,NULL),(623,109,82,5,NULL,NULL,2,NULL,18,40,NULL),(624,104,82,2,NULL,NULL,2,NULL,18,40,NULL),(625,103,82,2,NULL,NULL,2,NULL,18,40,NULL),(626,111,82,6,NULL,NULL,2,NULL,18,40,NULL),(627,120,82,6,NULL,NULL,2,NULL,18,40,NULL),(628,98,83,3,NULL,NULL,2,NULL,18,41,NULL),(629,97,83,3,NULL,NULL,2,NULL,18,41,NULL),(630,100,83,7,NULL,NULL,2,NULL,18,41,NULL),(631,101,83,6,NULL,NULL,2,NULL,18,41,NULL),(632,109,83,5,NULL,NULL,2,NULL,18,41,NULL),(633,104,83,2,NULL,NULL,2,NULL,18,41,NULL),(634,103,83,2,NULL,NULL,2,NULL,18,41,NULL),(635,111,83,6,NULL,NULL,2,NULL,18,41,NULL),(636,120,83,6,NULL,NULL,2,NULL,18,41,NULL),(637,98,83,3,NULL,NULL,2,NULL,18,42,NULL),(638,97,83,3,NULL,NULL,2,NULL,18,42,NULL),(639,100,83,7,NULL,NULL,2,NULL,18,42,NULL),(640,101,83,6,NULL,NULL,2,NULL,18,42,NULL),(641,109,83,5,NULL,NULL,2,NULL,18,42,NULL),(642,104,83,2,NULL,NULL,2,NULL,18,42,NULL),(643,103,83,2,NULL,NULL,2,NULL,18,42,NULL),(644,111,83,6,NULL,NULL,2,NULL,18,42,NULL),(645,120,83,6,NULL,NULL,2,NULL,18,42,NULL),(646,16,84,20,NULL,NULL,8,NULL,23,48,NULL),(647,5,84,8,NULL,NULL,8,NULL,23,48,NULL),(648,52,84,3,NULL,NULL,8,NULL,23,48,NULL),(649,18,84,50,NULL,NULL,8,NULL,23,48,NULL),(650,74,85,2,NULL,NULL,1,NULL,13,48,NULL),(651,292,86,1,NULL,NULL,5,NULL,40,20,NULL),(652,292,86,1,NULL,NULL,5,NULL,40,21,NULL),(653,292,86,1,NULL,NULL,5,NULL,40,22,NULL),(654,203,87,1,NULL,NULL,5,NULL,4,5,NULL),(655,205,87,4,NULL,NULL,5,NULL,4,5,NULL),(656,277,87,3,NULL,NULL,5,NULL,36,5,NULL),(657,277,87,3,NULL,NULL,5,NULL,36,5,NULL),(658,277,87,3,NULL,NULL,5,NULL,36,5,NULL),(659,184,88,4,NULL,NULL,7,NULL,20,41,NULL),(660,148,88,5,NULL,NULL,7,NULL,20,41,NULL),(661,160,88,4,NULL,NULL,7,NULL,20,41,NULL),(662,164,88,1,NULL,NULL,7,NULL,20,41,NULL),(663,184,88,4,NULL,NULL,7,NULL,20,42,NULL),(664,148,88,5,NULL,NULL,7,NULL,20,42,NULL),(665,160,88,4,NULL,NULL,7,NULL,20,42,NULL),(666,164,88,1,NULL,NULL,7,NULL,20,42,NULL),(667,184,88,4,NULL,NULL,7,NULL,20,44,NULL),(668,148,88,5,NULL,NULL,7,NULL,20,44,NULL),(669,160,88,4,NULL,NULL,7,NULL,20,44,NULL),(670,164,88,1,NULL,NULL,7,NULL,20,44,NULL),(671,184,88,4,NULL,NULL,7,NULL,20,45,NULL),(672,148,88,5,NULL,NULL,7,NULL,20,45,NULL),(673,160,88,4,NULL,NULL,6,NULL,20,45,NULL),(674,164,88,1,NULL,NULL,7,NULL,20,45,NULL),(675,186,89,1,NULL,NULL,2,NULL,21,33,NULL),(676,186,89,1,NULL,NULL,2,NULL,21,34,NULL),(677,186,89,1,NULL,NULL,2,NULL,21,35,NULL),(678,186,89,1,NULL,NULL,2,NULL,21,47,NULL),(679,186,89,1,NULL,NULL,2,NULL,21,37,NULL),(680,186,89,1,NULL,NULL,2,NULL,21,38,NULL),(681,186,89,1,NULL,NULL,2,NULL,21,39,NULL),(682,186,89,1,NULL,NULL,2,NULL,21,40,NULL),(683,186,89,1,NULL,NULL,2,NULL,21,41,NULL),(684,186,89,1,NULL,NULL,2,NULL,21,42,NULL),(685,148,90,4,NULL,NULL,2,NULL,21,39,NULL),(686,147,90,2,NULL,NULL,2,NULL,21,39,NULL),(687,146,90,1,NULL,NULL,2,NULL,21,39,NULL),(688,166,90,3,NULL,NULL,2,NULL,21,39,NULL),(689,163,90,3,NULL,NULL,2,NULL,21,39,NULL),(690,160,90,2,NULL,NULL,2,NULL,21,39,NULL),(691,169,90,3,NULL,NULL,2,NULL,21,39,NULL),(692,170,90,6,NULL,NULL,2,NULL,21,39,NULL),(693,153,90,2,NULL,NULL,2,NULL,21,39,NULL),(694,154,90,1,NULL,NULL,2,NULL,21,39,NULL),(695,157,90,5,NULL,NULL,2,NULL,21,39,NULL),(696,185,90,1,NULL,NULL,2,NULL,21,39,NULL),(697,176,90,2,NULL,NULL,2,NULL,21,39,NULL),(698,159,90,2,NULL,NULL,2,NULL,21,39,NULL),(699,133,90,1,NULL,NULL,2,NULL,21,39,NULL),(700,148,91,4,NULL,NULL,2,NULL,21,40,NULL),(701,147,91,2,NULL,NULL,2,NULL,21,40,NULL),(702,146,91,1,NULL,NULL,2,NULL,21,40,NULL),(703,166,91,3,NULL,NULL,2,NULL,21,40,NULL),(704,163,91,3,NULL,NULL,2,NULL,21,40,NULL),(705,160,91,2,NULL,NULL,2,NULL,21,40,NULL),(706,169,91,3,NULL,NULL,2,NULL,21,40,NULL),(707,170,91,6,NULL,NULL,2,NULL,21,40,NULL),(708,153,91,1,NULL,NULL,2,NULL,21,40,NULL),(709,154,91,1,NULL,NULL,2,NULL,21,40,NULL),(710,157,91,5,NULL,NULL,2,NULL,21,40,NULL),(711,185,91,2,NULL,NULL,2,NULL,21,40,NULL),(712,176,91,2,NULL,NULL,2,NULL,21,40,NULL),(713,159,91,2,NULL,NULL,2,NULL,21,40,NULL),(714,148,92,4,NULL,NULL,2,NULL,21,41,NULL),(715,147,92,2,NULL,NULL,2,NULL,21,41,NULL),(716,146,92,1,NULL,NULL,2,NULL,21,41,NULL),(717,166,92,3,NULL,NULL,2,NULL,21,41,NULL),(718,163,92,3,NULL,NULL,2,NULL,21,41,NULL),(719,160,92,2,NULL,NULL,2,NULL,21,41,NULL),(720,169,92,3,NULL,NULL,2,NULL,21,41,NULL),(721,170,92,6,NULL,NULL,2,NULL,21,41,NULL),(722,153,92,2,NULL,NULL,2,NULL,21,41,NULL),(723,154,92,2,NULL,NULL,2,NULL,21,41,NULL),(724,157,92,5,NULL,NULL,2,NULL,21,41,NULL),(725,185,92,1,NULL,NULL,2,NULL,21,41,NULL),(726,176,92,2,NULL,NULL,2,NULL,21,41,NULL),(727,159,92,2,NULL,NULL,2,NULL,21,41,NULL),(728,133,92,1,NULL,NULL,2,NULL,21,41,NULL),(729,287,92,2,NULL,NULL,2,NULL,21,41,NULL),(730,288,92,2,NULL,NULL,2,NULL,21,41,NULL),(731,148,93,5,NULL,NULL,2,NULL,21,42,NULL),(732,147,93,2,NULL,NULL,2,NULL,21,42,NULL),(733,146,93,1,NULL,NULL,2,NULL,21,42,NULL),(734,166,93,3,NULL,NULL,2,NULL,21,42,NULL),(735,163,93,3,NULL,NULL,2,NULL,21,42,NULL),(736,160,93,2,NULL,NULL,2,NULL,21,42,NULL),(737,169,93,3,NULL,NULL,2,NULL,21,42,NULL),(738,170,93,6,NULL,NULL,2,NULL,21,42,NULL),(739,153,93,2,NULL,NULL,2,NULL,21,42,NULL),(740,154,93,2,NULL,NULL,2,NULL,21,42,NULL),(741,157,93,5,NULL,NULL,2,NULL,21,42,NULL),(742,185,93,2,NULL,NULL,2,NULL,21,42,NULL),(743,176,93,2,NULL,NULL,2,NULL,21,42,NULL),(744,159,93,2,NULL,NULL,2,NULL,21,42,NULL),(745,133,93,1,NULL,NULL,2,NULL,21,42,NULL),(746,287,93,2,NULL,NULL,2,NULL,21,42,NULL),(747,288,93,2,NULL,NULL,2,NULL,21,42,NULL),(748,74,93,1,NULL,NULL,2,NULL,21,42,NULL),(749,213,94,2,NULL,NULL,1,NULL,15,48,NULL),(750,292,94,5,NULL,NULL,1,NULL,1,48,NULL),(751,292,94,5,NULL,NULL,1,NULL,30,48,NULL),(752,40,94,1,NULL,NULL,1,NULL,1,48,NULL),(753,41,94,1,NULL,NULL,1,NULL,1,48,NULL),(754,8,95,4,NULL,NULL,2,NULL,32,39,NULL),(755,8,95,4,NULL,NULL,2,NULL,32,40,NULL),(756,8,95,4,NULL,NULL,2,NULL,32,41,NULL),(757,8,95,4,NULL,NULL,2,NULL,32,42,NULL),(758,8,95,4,NULL,NULL,2,NULL,32,44,NULL),(759,8,95,4,NULL,NULL,2,NULL,32,45,NULL),(760,37,96,50,NULL,NULL,1,NULL,13,48,NULL),(761,74,96,2,NULL,NULL,1,NULL,13,48,NULL),(762,14,97,2,NULL,NULL,3,NULL,38,31,NULL),(763,14,97,2,NULL,NULL,3,NULL,38,32,NULL),(764,14,97,2,NULL,NULL,3,NULL,38,33,NULL),(765,290,98,4,NULL,NULL,2,NULL,33,46,NULL),(766,39,98,1,NULL,NULL,2,NULL,33,46,NULL),(767,1,99,130,NULL,NULL,7,NULL,19,50,NULL);
/*!40000 ALTER TABLE `material_registro_salidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion_destinatario`
--

DROP TABLE IF EXISTS `notificacion_destinatario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificacion_destinatario` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(60) NOT NULL,
  `Correo` varchar(150) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_correo` (`Correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion_destinatario`
--

LOCK TABLES `notificacion_destinatario` WRITE;
/*!40000 ALTER TABLE `notificacion_destinatario` DISABLE KEYS */;
INSERT INTO `notificacion_destinatario` VALUES (2,'Administración','diegolopez1797@gmail.com',1);
/*!40000 ALTER TABLE `notificacion_destinatario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso`
--

DROP TABLE IF EXISTS `permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permiso` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(60) NOT NULL,
  `Descripcion` varchar(150) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_codigo` (`Codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES (1,'usuario.gestionar','Crear, editar, eliminar y ver usuarios'),(2,'rol.gestionar','Crear y editar roles y su matriz de permisos'),(3,'auditoria.ver','Ver el registro de auditoria'),(4,'catalogo.ver','Listar y buscar en catalogos maestros'),(5,'catalogo.gestionar','Crear, editar y eliminar en catalogos maestros'),(6,'entrada.registrar','Registrar entradas de material'),(7,'salida.registrar','Registrar salidas de material'),(8,'costo.ver','Ver costo promedio y costo unitario en vistas'),(9,'solicitud.crear','Crear una solicitud de material'),(10,'solicitud.ver','Ver solicitudes'),(11,'solicitud.entregar','Ejecutar la entrega de una solicitud aprobada'),(12,'solicitud.aprobar.excepcional','Aprobar cualquier solicitud sin ser el responsable del proyecto'),(13,'dashboard.ver','Ver el dashboard, kardex e informe por usuario'),(14,'inventario.ajustar','Registrar ajustes de inventario (apertura, conteo, pérdida, daño)'),(15,'notificacion.gestionar','Administrar destinatarios de alertas de inventario');
/*!40000 ALTER TABLE `permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyecto`
--

DROP TABLE IF EXISTS `proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proyecto` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  `ResponsableID` int(10) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `proyecto_responsable_fk` (`ResponsableID`),
  CONSTRAINT `proyecto_responsable_fk` FOREIGN KEY (`ResponsableID`) REFERENCES `usuario` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecto`
--

LOCK TABLES `proyecto` WRITE;
/*!40000 ALTER TABLE `proyecto` DISABLE KEYS */;
INSERT INTO `proyecto` VALUES (1,'Ciudadela Salamanca La Nueva',NULL),(2,'Salamanca la Nueva - conjunto residencial',NULL),(3,'Local D1',NULL),(4,'Alcala',NULL);
/*!40000 ALTER TABLE `proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_entradas`
--

DROP TABLE IF EXISTS `registro_entradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_entradas` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `UsuarioID` int(10) NOT NULL,
  `ProveedorID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FKRegistro_E578564` (`UsuarioID`),
  KEY `re_proveedor_fk` (`ProveedorID`),
  CONSTRAINT `FKRegistro_E578564` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`),
  CONSTRAINT `re_proveedor_fk` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedor` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_entradas`
--

LOCK TABLES `registro_entradas` WRITE;
/*!40000 ALTER TABLE `registro_entradas` DISABLE KEYS */;
INSERT INTO `registro_entradas` VALUES (1,'2026-08-12','11:04:37',2,NULL),(2,'2026-08-12','11:10:26',2,NULL),(3,'2026-08-12','11:10:47',2,NULL),(4,'2026-08-12','14:40:08',2,NULL),(5,'2026-08-12','14:40:58',2,NULL),(6,'2026-08-12','14:41:33',2,NULL),(7,'2026-08-12','14:42:02',2,NULL),(8,'2026-08-12','14:42:38',2,NULL),(9,'2026-08-12','14:43:19',2,NULL),(10,'2026-08-12','14:43:53',2,NULL),(11,'2026-08-12','14:44:23',2,NULL),(12,'2026-08-12','14:44:59',2,NULL),(13,'2026-08-12','14:45:20',2,NULL),(14,'2026-08-12','14:45:51',2,NULL),(15,'2026-08-12','14:46:30',2,NULL),(16,'2026-08-12','14:47:05',2,NULL),(17,'2026-08-12','14:47:50',2,NULL),(18,'2026-08-12','14:48:21',2,NULL),(19,'2026-08-12','14:48:44',2,NULL),(20,'2026-08-12','14:49:01',2,NULL),(21,'2026-08-12','14:49:23',2,NULL),(22,'2026-08-12','14:49:47',2,NULL),(23,'2026-08-12','14:50:17',2,NULL),(24,'2026-08-12','14:50:47',2,NULL),(25,'2026-08-12','14:52:14',2,NULL),(26,'2026-08-12','14:52:52',2,NULL),(27,'2026-08-12','14:53:19',2,NULL),(28,'2026-08-12','14:53:45',2,NULL),(29,'2026-08-12','14:54:15',2,NULL),(30,'2026-08-12','14:55:02',2,NULL),(31,'2026-08-12','14:55:28',2,NULL),(32,'2026-08-12','14:56:03',2,NULL),(33,'2026-08-12','14:56:24',2,NULL),(34,'2026-08-12','14:56:49',2,NULL),(35,'2026-08-12','14:58:08',2,NULL),(36,'2026-08-12','15:00:06',2,NULL),(37,'2026-08-12','15:01:02',2,NULL),(38,'2026-08-12','15:03:01',2,NULL),(39,'2026-08-12','15:04:26',2,NULL),(40,'2026-08-12','15:06:08',2,NULL),(41,'2026-08-12','15:07:37',2,NULL),(42,'2026-08-12','15:08:18',2,NULL),(43,'2026-08-12','15:09:21',2,NULL),(44,'2026-08-12','15:11:11',2,NULL),(45,'2026-08-12','15:13:03',2,NULL),(46,'2026-08-13','08:36:28',2,NULL),(47,'2026-08-13','08:38:13',2,NULL),(48,'2026-08-13','08:39:06',2,NULL),(49,'2026-08-13','08:40:13',2,NULL),(50,'2026-08-13','08:42:36',2,NULL),(51,'2026-08-13','08:45:02',2,NULL),(52,'2026-08-13','08:48:16',2,NULL),(53,'2026-08-13','08:53:03',2,NULL),(54,'2026-08-13','08:55:54',2,NULL),(55,'2026-08-13','09:03:21',2,NULL),(56,'2026-08-13','09:07:41',2,NULL),(57,'2026-08-13','09:22:19',2,NULL),(58,'2026-08-13','10:18:23',2,NULL),(59,'2026-08-13','10:29:36',2,NULL),(60,'2026-08-13','10:32:58',2,NULL),(61,'2026-08-13','10:37:11',2,NULL),(62,'2026-08-13','10:40:57',2,NULL),(63,'2026-08-13','10:44:12',2,NULL),(64,'2026-08-13','10:47:43',2,NULL),(65,'2026-08-13','10:48:17',2,NULL),(66,'2026-08-13','10:52:56',2,NULL),(67,'2026-08-13','10:54:14',2,NULL),(68,'2026-08-13','10:56:23',2,NULL),(69,'2026-08-13','10:58:46',2,NULL),(70,'2026-08-13','11:02:58',2,NULL),(71,'2026-08-13','11:06:23',2,NULL),(72,'2026-08-13','11:12:18',2,NULL),(73,'2026-08-13','11:13:25',2,NULL),(74,'2026-08-13','11:39:02',2,NULL),(75,'2026-08-18','08:59:27',2,NULL),(76,'2026-08-21','09:21:26',2,NULL),(77,'2026-08-24','09:22:19',2,NULL),(78,'2026-08-29','09:32:58',2,NULL),(79,'2026-08-31','09:52:30',2,NULL),(80,'2026-09-03','08:14:26',2,NULL),(81,'2026-09-04','08:32:26',2,NULL),(82,'2026-09-07','12:39:48',2,NULL),(83,'2026-09-09','10:28:19',2,NULL),(84,'2026-09-09','10:42:54',2,NULL),(85,'2026-09-09','10:43:27',2,NULL),(86,'2026-09-09','14:14:44',2,NULL);
/*!40000 ALTER TABLE `registro_entradas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_salidas`
--

DROP TABLE IF EXISTS `registro_salidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_salidas` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `UsuarioID` int(10) NOT NULL,
  `ContratistaID` int(10) NOT NULL,
  `ProyectoID` int(10) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FKRegistro_S59635` (`UsuarioID`),
  KEY `FKRegistro_S536051` (`ContratistaID`),
  KEY `FKRegistro_S45085` (`ProyectoID`),
  CONSTRAINT `FKRegistro_S45085` FOREIGN KEY (`ProyectoID`) REFERENCES `proyecto` (`ID`),
  CONSTRAINT `FKRegistro_S536051` FOREIGN KEY (`ContratistaID`) REFERENCES `contratista` (`ID`),
  CONSTRAINT `FKRegistro_S59635` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_salidas`
--

LOCK TABLES `registro_salidas` WRITE;
/*!40000 ALTER TABLE `registro_salidas` DISABLE KEYS */;
INSERT INTO `registro_salidas` VALUES (1,'2026-08-21','08:41:03',2,2,1),(2,'2026-08-21','08:54:33',2,2,1),(3,'2026-08-21','09:00:38',2,2,1),(4,'2026-08-21','09:05:22',2,1,2),(5,'2026-08-21','09:11:22',2,3,3),(6,'2026-08-21','09:13:45',2,1,2),(7,'2026-08-21','09:23:05',2,4,1),(8,'2026-08-21','09:32:05',2,5,2),(9,'2026-08-21','09:38:13',2,2,3),(10,'2026-08-21','15:16:45',2,2,3),(11,'2026-08-22','07:45:10',2,2,1),(12,'2026-08-22','07:50:00',2,2,1),(13,'2026-08-22','07:54:49',2,2,1),(14,'2026-08-22','07:55:33',2,3,2),(15,'2026-08-22','07:57:47',2,1,2),(16,'2026-08-24','09:33:36',2,1,2),(17,'2026-08-24','09:34:19',2,3,2),(18,'2026-08-24','09:42:45',2,2,1),(19,'2026-08-24','09:48:55',2,2,1),(20,'2026-08-24','12:48:42',2,2,1),(21,'2026-08-24','12:49:07',2,1,2),(22,'2026-08-24','13:43:32',2,1,2),(23,'2026-08-24','14:34:22',2,2,1),(24,'2026-08-25','12:15:35',2,1,2),(25,'2026-08-25','12:16:33',2,3,2),(26,'2026-08-25','12:23:56',2,4,4),(27,'2026-08-25','12:33:05',2,3,2),(28,'2026-08-25','13:19:34',2,3,2),(29,'2026-08-25','13:30:14',2,3,2),(30,'2026-08-25','13:33:56',2,2,2),(31,'2026-08-25','14:04:45',2,2,1),(32,'2026-08-25','14:08:04',2,2,2),(33,'2026-08-26','12:59:11',2,3,2),(34,'2026-08-26','13:17:28',2,3,2),(35,'2026-08-26','13:18:10',2,1,2),(36,'2026-08-26','13:19:42',2,3,2),(37,'2026-08-26','13:21:12',2,2,2),(38,'2026-08-27','13:40:45',2,4,4),(39,'2026-08-27','13:43:18',2,2,2),(40,'2026-08-27','13:50:23',2,3,2),(41,'2026-08-27','13:53:37',2,1,2),(42,'2026-08-27','13:58:19',2,2,2),(43,'2026-08-27','14:10:24',2,3,2),(44,'2026-08-28','08:24:31',2,1,2),(45,'2026-08-28','10:27:06',2,3,2),(46,'2026-08-28','10:44:59',2,3,2),(47,'2026-08-29','07:40:26',2,3,2),(48,'2026-08-29','07:48:46',2,3,2),(49,'2026-08-29','07:56:31',2,3,2),(50,'2026-08-29','07:59:37',2,3,2),(51,'2026-08-29','09:50:38',2,3,2),(52,'2026-08-29','09:51:35',2,1,2),(53,'2026-08-29','09:53:59',2,2,2),(54,'2026-08-29','09:57:01',2,2,1),(55,'2026-08-29','09:59:32',2,3,2),(56,'2026-08-29','10:00:12',2,1,1),(57,'2026-08-29','10:01:36',2,5,2),(58,'2026-08-31','09:51:49',2,3,2),(59,'2026-08-31','15:20:56',2,2,2),(60,'2026-09-01','09:06:45',2,1,2),(61,'2026-09-01','09:09:45',2,3,2),(62,'2026-09-01','09:10:14',2,2,2),(63,'2026-09-01','09:11:22',2,3,2),(64,'2026-09-01','13:14:29',2,6,1),(65,'2026-09-01','13:17:25',2,1,2),(66,'2026-09-01','13:20:02',2,6,1),(67,'2026-09-01','13:29:40',2,3,2),(68,'2026-09-01','14:25:48',2,1,2),(69,'2026-09-02','08:34:17',2,3,2),(70,'2026-09-02','08:43:04',2,3,2),(71,'2026-09-02','14:36:54',2,4,4),(72,'2026-09-03','08:25:35',2,3,2),(73,'2026-09-04','09:59:38',2,1,2),(74,'2026-09-07','12:40:46',2,2,2),(75,'2026-09-07','12:41:43',2,5,2),(76,'2026-09-09','08:04:00',2,1,2),(77,'2026-09-09','10:13:03',2,3,2),(78,'2026-09-09','10:16:11',2,1,2),(79,'2026-09-09','10:55:45',2,2,1),(80,'2026-09-09','11:00:03',2,2,1),(81,'2026-09-09','11:05:27',2,2,1),(82,'2026-09-09','11:12:02',2,3,2),(83,'2026-09-09','11:56:45',2,3,2),(84,'2026-09-09','13:33:53',2,2,2),(85,'2026-09-09','13:34:32',2,5,2),(86,'2026-09-09','13:36:28',2,2,1),(87,'2026-09-09','13:42:33',2,6,1),(88,'2026-09-09','13:53:27',2,3,2),(89,'2026-09-09','13:59:36',2,3,2),(90,'2026-09-09','14:08:05',2,3,2),(91,'2026-09-09','14:14:17',2,3,2),(92,'2026-09-09','14:20:34',2,3,2),(93,'2026-09-09','14:27:10',2,3,2),(94,'2026-09-10','13:28:31',2,1,2),(95,'2026-09-10','13:32:39',2,3,2),(96,'2026-09-10','13:33:41',2,5,2),(97,'2026-09-10','13:40:49',2,3,2),(98,'2026-09-10','14:46:30',2,3,2),(99,'2026-09-13','10:34:32',1,2,2);
/*!40000 ALTER TABLE `registro_salidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador'),(2,'Almacenero'),(3,'Residente de Obra'),(4,'Compras'),(5,'Gerencia'),(6,'Consulta');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol_permiso` (
  `RolID` int(10) NOT NULL,
  `PermisoID` int(10) NOT NULL,
  PRIMARY KEY (`RolID`,`PermisoID`),
  KEY `rp_permiso_fk` (`PermisoID`),
  CONSTRAINT `rp_permiso_fk` FOREIGN KEY (`PermisoID`) REFERENCES `permiso` (`ID`),
  CONSTRAINT `rp_rol_fk` FOREIGN KEY (`RolID`) REFERENCES `rol` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permiso`
--

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,11),(1,12),(1,13),(1,14),(1,15),(2,4),(2,5),(2,6),(2,7),(2,11),(2,13),(2,14),(3,4),(3,9),(3,10),(3,13),(4,4),(4,8),(4,10),(4,13),(5,4),(5,8),(5,10),(5,13),(6,4),(6,10),(6,13);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubro`
--

DROP TABLE IF EXISTS `rubro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubro` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubro`
--

LOCK TABLES `rubro` WRITE;
/*!40000 ALTER TABLE `rubro` DISABLE KEYS */;
INSERT INTO `rubro` VALUES (1,'Cajas inspeccion'),(2,'Sumideros vias'),(3,'Terminacion para entrega'),(4,'Cosina'),(5,'instalacion medidor'),(6,'Instalacion regillas'),(7,'Replanteo'),(8,'Viga de cimentacion'),(9,'Cajas elctricas'),(10,'Traspaso'),(11,'Pasantes via'),(12,'Areglos'),(13,'Topografia'),(14,'Solados'),(15,'Red alcantarillado'),(16,'Red acueducto'),(17,'Domisiliarias sanitarias'),(18,'Hidraulica primer piso'),(19,'Patio'),(20,'Aguas lluvias'),(21,'Sanitaria primer piso'),(22,'Sanitaria segundo piso'),(23,'Vias'),(24,'Andenes'),(25,'Arreglo campamanto'),(26,'Dovelas'),(27,'Escaleras'),(28,'Formaleta'),(29,'Corte de via'),(30,'Pozos'),(31,'Columnetas'),(32,'Cimentacion casa'),(33,'Muro cambio de nivel'),(34,'Provisional electrica'),(35,'Parqueadero'),(36,'Reperacion grietas'),(37,'Desmodante latas'),(38,'Taches'),(39,'Puntos hidraulicos'),(40,'Resanes cajas'),(42,'Placa entre piso');
/*!40000 ALTER TABLE `rubro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `UsuarioSolicitaID` int(10) NOT NULL,
  `ProyectoID` int(10) NOT NULL,
  `UbicacionID` int(10) DEFAULT NULL,
  `Estado` varchar(15) NOT NULL DEFAULT 'PENDIENTE',
  `UsuarioApruebaID` int(10) DEFAULT NULL,
  `FechaAprobacion` datetime DEFAULT NULL,
  `RegistroSalidasID` int(10) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `sol_usuario_solicita_fk` (`UsuarioSolicitaID`),
  KEY `sol_proyecto_fk` (`ProyectoID`),
  KEY `sol_ubicacion_fk` (`UbicacionID`),
  KEY `sol_usuario_aprueba_fk` (`UsuarioApruebaID`),
  KEY `sol_registro_salidas_fk` (`RegistroSalidasID`),
  CONSTRAINT `sol_proyecto_fk` FOREIGN KEY (`ProyectoID`) REFERENCES `proyecto` (`ID`),
  CONSTRAINT `sol_registro_salidas_fk` FOREIGN KEY (`RegistroSalidasID`) REFERENCES `registro_salidas` (`ID`),
  CONSTRAINT `sol_ubicacion_fk` FOREIGN KEY (`UbicacionID`) REFERENCES `ubicacion` (`ID`),
  CONSTRAINT `sol_usuario_aprueba_fk` FOREIGN KEY (`UsuarioApruebaID`) REFERENCES `usuario` (`ID`),
  CONSTRAINT `sol_usuario_solicita_fk` FOREIGN KEY (`UsuarioSolicitaID`) REFERENCES `usuario` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud`
--

LOCK TABLES `solicitud` WRITE;
/*!40000 ALTER TABLE `solicitud` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_detalle`
--

DROP TABLE IF EXISTS `solicitud_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_detalle` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `SolicitudID` int(10) NOT NULL,
  `MaterialID` int(10) NOT NULL,
  `CantidadSolicitada` int(10) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `sd_solicitud_fk` (`SolicitudID`),
  KEY `sd_material_fk` (`MaterialID`),
  CONSTRAINT `sd_material_fk` FOREIGN KEY (`MaterialID`) REFERENCES `material` (`ID`),
  CONSTRAINT `sd_solicitud_fk` FOREIGN KEY (`SolicitudID`) REFERENCES `solicitud` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_detalle`
--

LOCK TABLES `solicitud_detalle` WRITE;
/*!40000 ALTER TABLE `solicitud_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_ubicacion`
--

DROP TABLE IF EXISTS `tipo_ubicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_ubicacion` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(30) NOT NULL,
  `Descripcion` varchar(60) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_tipo_codigo` (`Codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_ubicacion`
--

LOCK TABLES `tipo_ubicacion` WRITE;
/*!40000 ALTER TABLE `tipo_ubicacion` DISABLE KEYS */;
INSERT INTO `tipo_ubicacion` VALUES (1,'TORRE','Torre',1),(2,'BLOQUE','Bloque',1),(3,'EDIFICIO','Edificio',1),(4,'MANZANA','Manzana',1),(5,'PISO','Piso',1),(6,'CASA','Casa',1),(7,'APARTAMENTO','Apartamento',1),(8,'LOCAL','Local',1),(9,'OFICINA','Oficina',1),(10,'BODEGA','Bodega',1),(11,'PARQUEADERO','Parqueadero',1),(12,'ZONA','Zona',1),(13,'ETAPA','Etapa',1),(14,'UNIDAD','Unidad',1);
/*!40000 ALTER TABLE `tipo_ubicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubicacion`
--

DROP TABLE IF EXISTS `ubicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubicacion` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `ProyectoID` int(10) NOT NULL,
  `PadreID` int(10) DEFAULT NULL,
  `Nombre` varchar(80) NOT NULL,
  `Codigo` varchar(30) DEFAULT NULL,
  `Orden` int(11) DEFAULT NULL,
  `Nivel` int(11) NOT NULL DEFAULT 0,
  `Tipo` varchar(30) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `ClaveUnicidad` varchar(140) GENERATED ALWAYS AS (concat(`ProyectoID`,'|',coalesce(`PadreID`,0),'|',lcase(`Nombre`))) STORED,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_ubicacion_padre_nombre` (`ClaveUnicidad`),
  KEY `idx_proyecto_padre` (`ProyectoID`,`PadreID`),
  KEY `ubicacion_padre_fk` (`PadreID`),
  CONSTRAINT `ubicacion_padre_fk` FOREIGN KEY (`PadreID`) REFERENCES `ubicacion` (`ID`),
  CONSTRAINT `ubicacion_proyecto_fk` FOREIGN KEY (`ProyectoID`) REFERENCES `proyecto` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubicacion`
--

LOCK TABLES `ubicacion` WRITE;
/*!40000 ALTER TABLE `ubicacion` DISABLE KEYS */;
INSERT INTO `ubicacion` VALUES (1,1,NULL,'ETAPA 4',NULL,NULL,0,'ETAPA',1,'1|0|etapa 4'),(2,1,1,'Manzana K',NULL,NULL,1,'MANZANA',1,'1|1|manzana k'),(3,1,2,'casa 4',NULL,NULL,2,'CASA',1,'1|2|casa 4'),(4,1,1,'Manzana S',NULL,NULL,1,'MANZANA',1,'1|1|manzana s'),(5,1,4,'Casa 55',NULL,NULL,2,'CASA',1,'1|4|casa 55'),(6,1,NULL,'ETAPA 5',NULL,NULL,0,'ETAPA',1,'1|0|etapa 5'),(7,1,6,'Manzana K',NULL,NULL,1,'MANZANA',1,'1|6|manzana k'),(8,1,7,'casa 3',NULL,NULL,2,'CASA',1,'1|7|casa 3'),(9,1,7,'casa 4',NULL,NULL,2,'CASA',1,'1|7|casa 4'),(10,1,7,'casa 5',NULL,NULL,2,'CASA',1,'1|7|casa 5'),(11,1,7,'Casa 6',NULL,NULL,2,'CASA',1,'1|7|casa 6'),(12,1,7,'Casa 7',NULL,NULL,2,'CASA',1,'1|7|casa 7'),(13,1,7,'Casa 22',NULL,NULL,2,'CASA',1,'1|7|casa 22'),(14,1,7,'Casa 23',NULL,NULL,2,'CASA',1,'1|7|casa 23'),(15,1,7,'Casa 24',NULL,NULL,2,'CASA',1,'1|7|casa 24'),(16,1,7,'Casa 25',NULL,NULL,2,'CASA',1,'1|7|casa 25'),(17,1,7,'Casa 26',NULL,NULL,2,'CASA',1,'1|7|casa 26'),(18,1,7,'Casa 27',NULL,NULL,2,'CASA',1,'1|7|casa 27'),(19,1,6,'Manzana M9',NULL,NULL,1,'MANZANA',1,'1|6|manzana m9'),(20,1,19,'casa 1',NULL,NULL,2,'CASA',1,'1|19|casa 1'),(21,1,19,'casa 4',NULL,NULL,2,'CASA',1,'1|19|casa 4'),(22,1,19,'Casa 8',NULL,NULL,2,'CASA',1,'1|19|casa 8'),(23,1,19,'Casa 20',NULL,NULL,2,'CASA',1,'1|19|casa 20'),(24,1,NULL,'ETAPA 6',NULL,NULL,0,'ETAPA',1,'1|0|etapa 6'),(25,1,24,'Urbanismo',NULL,NULL,1,'URBANISMO',1,'1|24|urbanismo'),(26,1,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'1|0|traspaso'),(27,1,26,'Casa 6',NULL,NULL,1,'CASA',1,'1|26|casa 6'),(28,1,26,'Urbanismo',NULL,NULL,1,'URBANISMO',1,'1|26|urbanismo'),(29,2,NULL,'ETAPA 6',NULL,NULL,0,'ETAPA',1,'2|0|etapa 6'),(30,2,29,'Manzana M10',NULL,NULL,1,'MANZANA',1,'2|29|manzana m10'),(31,2,30,'casa 1',NULL,NULL,2,'CASA',1,'2|30|casa 1'),(32,2,30,'casa 2',NULL,NULL,2,'CASA',1,'2|30|casa 2'),(33,2,30,'casa 3',NULL,NULL,2,'CASA',1,'2|30|casa 3'),(34,2,30,'casa 4',NULL,NULL,2,'CASA',1,'2|30|casa 4'),(35,2,30,'casa 5',NULL,NULL,2,'CASA',1,'2|30|casa 5'),(36,2,30,'Casa 6',NULL,NULL,2,'CASA',1,'2|30|casa 6'),(37,2,30,'Casa 7',NULL,NULL,2,'CASA',1,'2|30|casa 7'),(38,2,30,'Casa 8',NULL,NULL,2,'CASA',1,'2|30|casa 8'),(39,2,30,'Casa 9',NULL,NULL,2,'CASA',1,'2|30|casa 9'),(40,2,30,'Casa 10',NULL,NULL,2,'CASA',1,'2|30|casa 10'),(41,2,30,'Casa 11',NULL,NULL,2,'CASA',1,'2|30|casa 11'),(42,2,30,'Casa 12',NULL,NULL,2,'CASA',1,'2|30|casa 12'),(43,2,29,'Manzana M11',NULL,NULL,1,'MANZANA',1,'2|29|manzana m11'),(44,2,43,'casa 1',NULL,NULL,2,'CASA',1,'2|43|casa 1'),(45,2,43,'casa 2',NULL,NULL,2,'CASA',1,'2|43|casa 2'),(46,2,43,'casa 4',NULL,NULL,2,'CASA',1,'2|43|casa 4'),(47,2,43,'Casa 6',NULL,NULL,2,'CASA',1,'2|43|casa 6'),(48,2,29,'Urbanismo',NULL,NULL,1,'URBANISMO',1,'2|29|urbanismo'),(49,2,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'2|0|traspaso'),(50,2,49,'Urbanismo',NULL,NULL,1,'URBANISMO',1,'2|49|urbanismo'),(51,3,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'3|0|traspaso'),(52,3,51,'Casa 6',NULL,NULL,1,'CASA',1,'3|51|casa 6'),(53,4,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'4|0|traspaso'),(54,4,53,'Urbanismo',NULL,NULL,1,'URBANISMO',1,'4|53|urbanismo');
/*!40000 ALTER TABLE `ubicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Identificacion` int(10) NOT NULL,
  `Nombre` varchar(30) DEFAULT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  `Clave` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `RolID` int(10) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FKUsuario36461` (`RolID`),
  CONSTRAINT `FKUsuario36461` FOREIGN KEY (`RolID`) REFERENCES `rol` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,1004035010,'Juan','Lopez','$2y$10$TOQQ.Y0fPY8xNmHXmw.DmuOB3DKyQRzovRj2hIOtyc.Lv4qDND9qS',1),(2,83235047,'Reinaldo','Gordo Losada','$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby',2),(3,1070605738,'Caterine Fernanda','Jovel Rincon','$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa',2);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'inventario'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-13 10:59:39
