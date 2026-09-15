-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: inventario
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratista`
--

LOCK TABLES `contratista` WRITE;
/*!40000 ALTER TABLE `contratista` DISABLE KEYS */;
INSERT INTO `contratista` VALUES (1,'Construcciones hermanos murcia'),(2,'Smartools'),(3,'Amin Narvaez'),(16,'Sendos'),(17,'Grabiel cardona');
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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destino`
--

LOCK TABLES `destino` WRITE;
/*!40000 ALTER TABLE `destino` DISABLE KEYS */;
INSERT INTO `destino` VALUES (1,'Urbanismo'),(2,'Cimentación'),(3,'Industrializado'),(4,'Consumibles'),(5,'Acabados'),(34,'Cubierta'),(35,'Tuberías'),(36,'Vías, andenes y sardineles'),(37,'Accesorios');
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
) ENGINE=InnoDB AUTO_INCREMENT=498 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (22,1,'Cemento gris uso general * 50kg','bulto',19,10,3,NULL),(23,2,'Cemento blanco * 40 kg','bulto',2,2,0,NULL),(24,3,'Yeso extra * 25 kg','bulto',4,2,0,NULL),(25,4,'Sardinel en cemento de 80 cms','UND',0,0,0,NULL),(26,5,'Malla electrosoldada 4mm','UND',947,0,0,NULL),(27,6,'Malla electrosoldada 4 mm en rollo * 18 mts','UND',118,0,0,NULL),(28,7,'Malla electrosoldada 5 mm','UND',178,0,0,NULL),(29,8,'Malla electrosoldada 6 mm','UND',115,0,0,NULL),(30,9,'Malla electrosoldada 6.5 mm','UND',381,0,0,NULL),(31,10,'Malla electrosoldada 8 mm','UND',180,0,0,NULL),(32,11,'Alambre negro liso','kg',390,0,0,NULL),(33,12,'varilla corrugada de 1/4\" * 6 mt','UND',5573,0,0,NULL),(34,13,'varilla corrugada de 1/4\" en chipa','kg',0,0,0,NULL),(35,14,'varilla corrugada de 3/8\" x 6mt','UND',250,0,0,NULL),(36,15,'varilla corrugada de 3/8\" x 12mt','UND',2072,0,0,NULL),(37,16,'varilla corrugada de 1/2\" x 6mt','UND',1336,0,0,NULL),(38,17,'varilla corrugada de 1/2\" x 12mt','UND',0,0,0,NULL),(39,20,'varilla corrugada de 5/8\" x 6mt lisa','UND',868,0,0,NULL),(40,23,'Fleje de 1/4\" de 13cm*18cm','UND',6200,0,0,NULL),(41,24,'Gancho tipo C de 3/8\" de 10*5*5','UND',206,0,0,NULL),(42,25,'Fleje de 3/8\" de 13cm*16cm','UND',8100,0,0,NULL),(43,26,'Gancho tipo C de 1/4 20x8x8','UND',6880,0,0,NULL),(44,27,'Fleje de 1/4\" de 15cm*20cm','UND',5014,0,0,NULL),(45,28,'Fleje de 1/4\" de 15cm*15cm','UND',1248,0,0,NULL),(46,29,'Gancho tipo C de 1/4 10x8x8','UND',6790,0,0,NULL),(47,30,'Silleta plástica SU25 mm','UND',0,0,0,NULL),(48,31,'Silleta plástica CP30 mm','UND',712,0,0,NULL),(49,32,'Silleta plástica CP50 mm','UND',6900,0,0,NULL),(50,33,'Silleta plástica CP65 mm','UND',0,0,0,NULL),(51,34,'Disco separador MC100 mm','UND',9500,0,0,NULL),(52,35,'Disco separador MC120 mm','UND',14400,0,0,NULL),(53,36,'Tuberia conduit de 1/2\" * 3mt','UND',300,0,0,NULL),(54,37,'Medio polín * 3mt','UND',300,0,0,NULL),(55,42,'Estaca en madera de 25cm','UND',390,0,0,NULL),(56,43,'Estaca en madera de 50cm','UND',400,0,0,NULL),(57,44,'Estaca en madera de 75cm','UND',500,0,0,NULL),(58,45,'Estaca en madera de 1mt','UND',150,0,0,NULL),(59,46,'Guadua * 6mt','UND',0,0,0,NULL),(60,48,'puntilla para madera de 2\"','LB',18,0,0,NULL),(61,49,'puntilla para madera de 2 1/2\"','LB',27,0,0,NULL),(62,50,'puntilla para madera de 3\"','LB',58,0,0,NULL),(63,51,'puntilla en acero de 2 1/2\"','LB',26,0,0,NULL),(64,52,'Broca SDS plus de 3/8\" * 6\"','UND',68,0,0,NULL),(65,53,'Broca SDS plus de 1/2\" * 6\"','UND',23,0,0,NULL),(66,54,'Broca SDS plus de 1/2\" * 12\"','UND',35,0,0,NULL),(67,55,'Chazo plastico de 3/8\"','UND',500,0,0,NULL),(68,56,'Disco diamantado continuo de 4 1/2\"','UND',32,0,0,NULL),(69,57,'Disco diamantado segmentado de 9\"','UND',0,0,0,NULL),(70,58,'Disco de copa diamantada doble de 7\" para pulir','UND',11,0,0,NULL),(71,59,'Disco de corte de hierro de 4 1/2\"','UND',13,0,0,NULL),(72,60,'Disco de corte de hierro de 7\"','UND',42,0,0,NULL),(73,62,'Disco de corte de hierro de 14\" - tronzadora','UND',56,0,0,NULL),(74,63,'Brocha de 2 1/2\"','UND',43,0,0,NULL),(75,64,'Brocha de 3\"','UND',44,0,0,NULL),(76,65,'Brocha de 4\"','UND',35,0,0,NULL),(77,66,'Rodillo de Felpa de 9\"','UND',76,0,0,NULL),(78,67,'Lija #150','UND',31,0,0,NULL),(79,68,'Espátula metálica de 3\" mango madera/plastico','UND',10,0,0,NULL),(80,69,'Espátula metálica de 4\" mango madera/plastico','UND',21,0,0,NULL),(81,70,'Espátula metálica de 5\" mango madera/plastico','UND',1,0,0,NULL),(82,72,'Pintura vinilo gris Tipo 1','cuñete',2,0,0,NULL),(83,73,'Pintura vinilo Blanco Tipo 2','cuñete',25,0,0,NULL),(84,80,'Aerosol color rojo','UND',2,0,0,NULL),(85,81,'Aerosol color negro','UND',0,0,0,NULL),(86,83,'Sellante poliuretano topex gris * 300ml','UND',30,0,0,NULL),(87,86,'Illbruck SP523 * blanco 300ml','UND',0,0,0,NULL),(88,92,'Aquacero - impermeabilizante acrilico','cuñete',0,0,0,NULL),(89,94,'Cinta de enmascarar de 1\"','UND',61,0,0,NULL),(90,95,'Mineral bayer Rojo','caja',33,0,0,NULL),(91,97,'Curaseal pf blanco - curador de concreto * 200kg (tambor)','Tambor * 55gls',1,0,0,NULL),(92,100,'Vulken 45 SSl * 5gl','cuñete',2,0,0,NULL),(93,101,'Verticoat No. 2 *30kg','bulto',2,0,0,NULL),(94,104,'Sikadur - 32 primer * 1 KG','UND',0,0,0,NULL),(95,106,'Cal Hidratada * 10KG','bulto',4,0,0,NULL),(96,107,'Plastico negro calibre 6 * 4mts ancho','Rollo',1,0,0,NULL),(97,108,'Lona verde para cerramiento * 2mts alto','Rollo',6,0,0,NULL),(98,110,'Cinta peligro * 500 mts','Rollo',23,0,0,NULL),(99,113,'Cepillo de alambre - para limpieza','UND',10,0,0,NULL),(100,118,'Pala Draga (Hoyadora)','UND',2,0,0,NULL),(101,119,'Pica','UND',2,0,0,NULL),(102,120,'Palín cuadrado','UND',0,0,0,NULL),(103,121,'Barretón','UND',0,0,0,NULL),(104,122,'Cepillo carretero - Escobillón','UND',0,0,0,NULL),(105,123,'Carretilla','UND',3,0,0,NULL),(106,124,'Rastrillo metálico','UND',4,0,0,NULL),(107,126,'Combo sanitario linea institucional','UND',0,0,0,NULL),(108,127,'Lavamanos blanco linea institucional (sin pedestal)','UND',0,0,0,NULL),(109,128,'Enchape cerámico eco plus blanco 20*20 * 2m2','caja',0,0,0,NULL),(110,129,'Enchape cerámico piso pared natal blanco 25*35','caja',100,0,0,NULL),(111,130,'Enchape cerámico blanco 30*30','caja',20,0,0,NULL),(112,131,'Enchape cerámico Stone café 45*45','caja',83,0,0,NULL),(113,132,'Enchape cerámico Slate White EP 51*51','caja',90,0,0,NULL),(114,133,'Pegante cerámico * 25 kg','bulto',9,0,0,NULL),(115,134,'Boquilla blanca * 2 kg','UND',0,0,0,NULL),(116,139,'Hojas de Segueta','UND',75,0,0,NULL),(117,140,'Cinta teflón industrial','UND',76,0,0,NULL),(118,141,'Tuberia pvc presión de 1/2\" * 6 mts - RDE 9','UND',1046,0,0,NULL),(119,142,'Tuberia pvc presión de 3/4\" * 6 mts - RDE 11','UND',539,0,0,NULL),(120,143,'Tuberia pvc presión de 1\" * 6 mts - RDE 13.5','UND',500,0,0,NULL),(121,144,'Codo pvc presión 1/2\"','UND',1998,0,0,NULL),(122,145,'Codo pvc presión 3/4\"','UND',4524,0,0,NULL),(123,146,'Codo pvc presión 1\"','UND',196,0,0,NULL),(124,147,'Unión pvc presión 1/2\"','UND',742,0,0,NULL),(125,148,'Unión pvc presión 3/4\"','UND',5984,0,0,NULL),(126,149,'Unión pvc presión 1\"','UND',15,0,0,NULL),(127,150,'Unión universal pvc presión 1/2\"','UND',215,0,0,NULL),(128,151,'Unión universal pvc presión 1\"','UND',98,0,0,NULL),(129,152,'Tee pvc presión 1/2\"','UND',3080,0,0,NULL),(130,153,'Tee pvc presión 3/4\"','UND',2212,0,0,NULL),(131,154,'Tee pvc presión 1\"','UND',550,0,0,NULL),(132,155,'Tapón pvc presión liso de 1/2\"','UND',530,0,0,NULL),(133,156,'Tapón pvc presión roscado de 1/2\"','UND',0,0,0,NULL),(134,157,'Tapón pvc presión liso de 1\"','UND',0,0,0,NULL),(135,158,'Adaptador pvc presión hembra de 1/2\"','UND',4805,0,0,NULL),(136,159,'Adaptador pvc presión macho de 1/2\"','UND',3518,0,0,NULL),(137,160,'Adaptador pvc presión macho de 3/4\"','UND',900,0,0,NULL),(138,161,'Adaptador pvc presión hembra de 1\"','UND',770,0,0,NULL),(139,162,'Adaptador pvc presión macho de 1\"','UND',220,0,0,NULL),(140,163,'Semicodo pvc presión 1/2\"','UND',671,0,0,NULL),(141,164,'reducción pvc presión 3/4\" x 1/2\" (buje)','UND',2198,0,0,NULL),(142,165,'reducción pvc presión 1\" x 1/2\" (buje)','UND',313,0,0,NULL),(143,166,'reducción pvc presión 1\" x 3/4\" (buje)','UND',125,0,0,NULL),(144,167,'Cheque horizontal de 1/2\" - grival','UND',154,0,0,NULL),(145,168,'Cheque horizontal de 1\" - grival','UND',61,0,0,NULL),(146,169,'Llave de paso pvc presión lisa de 1/2\" - plástica','UND',179,0,0,NULL),(147,170,'Llave de paso pvc presión lisa de 3/4\" - plastica','UND',73,0,0,NULL),(148,171,'Llave de paso pvc presión lisa de 1\" - plástica','UND',280,0,0,NULL),(149,172,'Llave terminal de 1/2\"','UND',272,0,0,NULL),(150,173,'Codo galvanizado de 1/2\"','UND',340,0,0,NULL),(151,174,'Micromedidor de Agua potable de 1/2\"','UND',83,0,0,NULL),(152,175,'Registro ducha completo','UND',71,0,0,NULL),(153,176,'Lavadero prefabricado en concreto 0.50*0.50*0.80','UND',3,0,0,NULL),(154,178,'soldadura pvc Gerfor verde * 1/4 gl','UND',707,0,0,NULL),(155,179,'Limpiador pvc Gerfor * 1/4gl','UND',696,0,0,NULL),(156,180,'Tanque Almacenamiento * 500 lts (polinter)','UND',57,0,0,NULL),(157,181,'Flotador llenado tanque almacenamiento','UND',57,0,0,NULL),(158,182,'Acople para sanitario','UND',449,0,0,NULL),(159,183,'Acople para Lavamanos','UND',137,0,0,NULL),(160,184,'Acople para Lavaplatos','UND',138,0,0,NULL),(161,185,'Arbol de entrada (llenado) tanque sanitario - Repuesto','UND',24,0,0,NULL),(162,186,'Arbol de salida - tanque sanitario - Repuesto','UND',12,0,0,NULL),(163,187,'Canastilla para lavaplatos de 4\"*3\"','UND',120,0,0,NULL),(164,188,'Sifón ajustable tipo acordeón - lavaplatos','UND',19,0,0,NULL),(165,189,'Biscocho / cajilla en cemento para medidor','UND',59,0,0,NULL),(166,190,'Tapa empo metálica - para medidor','UND',94,0,0,NULL),(167,191,'Tubo sanitario de 1 1/2\" * 6 mt','UND',83,0,0,NULL),(168,192,'Tubo sanitario de 3\" * 6 mt','UND',218,0,0,NULL),(169,193,'Tubo sanitario de 4\" * 6 mt','UND',626,0,0,NULL),(170,194,'Tubo sanitario de 4\" * 6 mt - Reventilación (naranja)','UND',49,0,0,NULL),(171,195,'Tubo sanitario de 6\" * 6 mt','UND',36,0,0,NULL),(172,196,'Unión pvc sanitaria de 1 1/2\"','UND',700,0,0,NULL),(173,197,'Unión pvc sanitaria de 2\"','UND',75,0,0,NULL),(174,198,'Unión pvc sanitaria de 3\"','UND',86,0,0,NULL),(175,199,'Unión pvc sanitaria de 4\"','UND',857,0,0,NULL),(176,200,'Unión pvc sanitaria de 6\"','UND',1,0,0,NULL),(177,201,'Unión pvc sanitaria de 8\"','UND',1,0,0,NULL),(178,202,'Codo pvc sanitario de 1 1/2\"','UND',583,0,0,NULL),(179,203,'Codo pvc sanitario de 2\"','UND',139,0,0,NULL),(180,204,'Codo pvc sanitario de 3','UND',349,0,0,NULL),(181,205,'Codo pvc sanitario de 4\"','UND',376,0,0,NULL),(182,206,'Semicodo pvc sanitario de 1\" 1/2\"','UND',223,0,0,NULL),(183,207,'Semicodo pvc sanitario de 2\"','UND',489,0,0,NULL),(184,208,'Semicodo pvc sanitario de 3','UND',321,0,0,NULL),(185,209,'Semicodo pvc sanitario de 4\"','UND',195,0,0,NULL),(186,210,'Sifón pvc sanitario 180° C*C 1 1/2\"','UND',91,0,0,NULL),(187,211,'Sifón pvc sanitario 135° C*E 3\"','UND',320,0,0,NULL),(188,212,'Sifón pvc sanitario 135° C*E 4\"','UND',55,0,0,NULL),(189,213,'Tapa prueba sanitaria de 2\"','UND',107,0,0,NULL),(190,214,'Tapa prueba sanitaria de 3\"','UND',452,0,0,NULL),(191,215,'Tapa prueba sanitaria de 4\"','UND',565,0,0,NULL),(192,216,'Tapa prueba sanitaria de 6\"','UND',26,0,0,NULL),(193,217,'Adaptador de limpieza sanitario de 4','UND',210,0,0,NULL),(194,218,'Rejilla plástica de 2\"','UND',17,0,0,NULL),(195,219,'Rejilla plástica con sosco de 4\" * 3\"','UND',470,0,0,NULL),(196,220,'Rejilla plástica con sosco de 5\" * 4\"','UND',91,0,0,NULL),(197,221,'Buje pvc sanitario de 3\" * 1 1/2\"','UND',312,0,0,NULL),(198,222,'Buje pvc sanitario de 4\" * 2\"','UND',44,0,0,NULL),(199,223,'Buje pvc sanitario de 4\" * 3\"','UND',105,0,0,NULL),(200,224,'Tee pvc sanitaria de 1 1/2\"','UND',147,0,0,NULL),(201,225,'Tee pvc sanitaria de 4\"','UND',546,0,0,NULL),(202,226,'Tee pvc sanitaria de 6\"','UND',15,0,0,NULL),(203,227,'Tee pvc sanitaria reducida de 4\" * 3\"','UND',94,0,0,NULL),(204,228,'Tee pvc sanitaria reducida de 6\" * 4\"','UND',3,0,0,NULL),(205,229,'Yee pvc sanitaria de 4\"','UND',132,0,0,NULL),(206,230,'Yee pvc sanitaria reducida de 4\" * 2\"','UND',135,0,0,NULL),(207,231,'Yee pvc sanitaria reducida de 4\" * 3\"','UND',139,0,0,NULL),(208,232,'Yee pvc sanitaria reducida de 6\" * 4\"','UND',178,0,0,NULL),(209,233,'Teja de zinc * 3mts','UND',200,0,0,NULL),(210,234,'Amarre para teja de zinc','UND',0,0,0,NULL),(211,235,'Tuberia rectangular Cr 3\" * 1 1/2\" * 6 mts calibre 1.1mm (calibre 18)','UND',20,0,0,NULL),(212,243,'Caballete de 2 mts largo X 0,60 mt ancho','UND',87,0,0,NULL),(213,244,'Soldadura Electrica 6013 * 3/32','kg',55,0,0,NULL),(214,245,'Tornillo fijador de ala','UND',497,0,0,NULL),(215,254,'Emulsión asfaltica ED-9','cuñete',3,0,0,NULL),(216,255,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2.5mm (con soplete) * 10m2','Rollo',18,0,0,NULL),(217,256,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2mm (aplicación en frio)','Rollo',5,0,0,NULL),(218,257,'Gárgola prefabricada en cemento','UND',8,0,0,NULL),(219,258,'Flanche en lámina galvanizada de 2.0mt * 25cm desarrollo - calibre 26','UND',32,0,0,NULL),(220,259,'Flanche en lámina galvanizada de 2.40mt * 25cm desarrollo - calibre 26','UND',32,0,0,NULL),(221,260,'Cinta Asfáltica tapa goteras de 10cms * 10mts','UND',9,0,0,NULL),(222,261,'Cinta Asfáltica tapa goteras de 15cms * 10mts','UND',4,0,0,NULL),(223,262,'Cinta Asfáltica tapa goteras de 20cms * 10mts','UND',3,0,0,NULL),(224,263,'Juego de parrillas y quemadores para estufa','UND',90,0,0,NULL),(225,264,'Griferia cuello de ganso - para cocina','UND',34,0,0,NULL),(226,265,'Manijas para mueble de cocina','UND',180,0,0,NULL),(227,266,'Chapa de poma metálica y redonda - para alcoba','UND',10,0,0,NULL),(228,267,'Chapa de poma metálica y redonda - para baño','UND',10,0,0,NULL),(229,268,'Lubricante Gerfor * Tarro 500ml','UND',47,0,0,NULL),(230,269,'Silla fix adhesivo sellante * tarro 310ml','UND',25,0,0,NULL),(231,270,'Geotextil No tejido NT 1600 - rollo de 3.5mt*160ml','Rollo',3,0,0,NULL),(232,271,'Rejilla metálica para sumidero de 0.50mt * 0.80mt marco y contramarco','UND',7,0,0,NULL),(233,273,'Tubería PVC para alcantarillado Novafort 6\" S-4 x 6mt','UND',278,0,0,NULL),(234,274,'Tubería PVC para alcantarillado Novafort 8\" S-8 x 6mt','UND',280,0,0,NULL),(235,275,'Tubería PVC para alcantarillado Novafort 10\" S-8 x 6mt','UND',69,0,0,NULL),(236,276,'Tubería PVC para alcantarillado Novafort 12\" S-8 x 6mt','UND',39,0,0,NULL),(237,277,'Tubería PVC para alcantarillado Novafort 14\" S-8 x 6mt','UND',0,0,0,NULL),(238,279,'Tubería en cemento para alcantarillado diametro 18\" * 1mt','UND',0,0,0,NULL),(239,281,'Tubería en cemento para alcantarillado diámetro 36\" * 1mt','UND',0,0,0,NULL),(240,282,'Tapa antirrobo para pozo de inspección','UND',50,0,0,NULL),(241,283,'Silla Tee pvc sanitario de 6\" * 4\"','UND',15,0,0,NULL),(242,284,'Silla Yee pvc sanitario 8\" * 6\"','UND',280,0,0,NULL),(243,285,'Silla Yee pvc sanitario 10\" * 6\"','UND',9,0,0,NULL),(244,286,'Silla Yee pvc sanitario 12\" * 6\" (315*160)','UND',22,0,0,NULL),(245,287,'Silla Yee pvc sanitario 16\" * 6\" (400*160)','UND',40,0,0,NULL),(246,288,'Sifón pvc sanitario 180° C*C 2\"','UND',6,0,0,NULL),(247,289,'Codo pvc presión 1 1/2\"','UND',0,0,0,NULL),(248,290,'Semicodo pvc presión 1 1/2\"','UND',0,0,0,NULL),(249,291,'Tapón pvc presión liso de 1 1/2\"','UND',0,0,0,NULL),(250,293,'Tapón pvc presión roscado de 1 1/2\"','UND',0,0,0,NULL),(251,294,'Adaptador pvc presión hembra de 2\"','UND',9,0,0,NULL),(252,297,'Tubería campana pvc presión de 3\" * 6 mts - RDE 21','UND',182,0,0,NULL),(253,298,'Tubería campana pvc presión de 4\" * 6 mts - RDE 21','UND',2,0,0,NULL),(254,299,'Tubería campana pvc presión de 6\" * 6 mts - RDE 21','UND',36,0,0,NULL),(255,300,'Tubería campana pvc presión de 8\" * 6 mts - RDE 21','UND',9,0,0,NULL),(256,301,'Collarín pvc presión de 3\" x 1/2\"','UND',443,0,0,NULL),(257,303,'Collarín pvc presión de 4\" x 1/2\"','UND',23,0,0,NULL),(258,305,'Codo pvc presión 90° 4\"','UND',16,0,0,NULL),(259,306,'Codo pvc presión 90° 6\"','UND',15,0,0,NULL),(260,307,'Codo pvc presión gran radio 45° 6','UND',2,0,0,NULL),(261,308,'Semicodo pvc presión 45° 3\"','UND',6,0,0,NULL),(262,310,'Tee pvc presión 90° 3\"','UND',1,0,0,NULL),(263,313,'Tee pvc presión 4\"','UND',9,0,0,NULL),(264,314,'Tee pvc presión 4\" x 4\" x 3\"','UND',6,0,0,NULL),(265,315,'Tee pvc presión 6\"','UND',3,0,0,NULL),(266,316,'Tee H.D presión 6\" x 3\"','UND',2,0,0,NULL),(267,320,'Tapón pvc presión 3\"','UND',12,0,0,NULL),(268,321,'Tapón pvc presión 4\"','UND',5,0,0,NULL),(269,322,'Tapón H.D presión 8\"','UND',23,0,0,NULL),(270,323,'Reducción pvc presión 1 1/2\" x 3/4\" (buje)','UND',0,0,0,NULL),(271,324,'Reducción pvc presión 2\" x 1\" (buje)','UND',20,0,0,NULL),(272,325,'Reducción pvc presión 2\" x 1 1/2\" (buje)','UND',17,0,0,NULL),(273,326,'Reducción pvc presión 4\" x 2\" (buje)','UND',1,0,0,NULL),(274,328,'Reducción pvc presión 6\" x 4\" (buje)','UND',1,0,0,NULL),(275,331,'Unión Z UM presión 4\"','UND',0,0,0,NULL),(276,332,'Unión pasante pvc presión 3\"','UND',36,0,0,NULL),(277,333,'Unión pasante pvc presión 4\"','UND',53,0,0,NULL),(278,334,'Unión pasante pvc presión 6\"','UND',23,0,0,NULL),(279,335,'Unión pasante pvc presión 8\"','UND',10,0,0,NULL),(280,337,'Brida unión de 3\"','UND',3,0,0,NULL),(281,338,'Brida unión de 4\"','UND',6,0,0,NULL),(282,339,'Brida unión de 6\"','UND',4,0,0,NULL),(283,340,'Brida unión de 8\"','UND',3,0,0,NULL),(284,341,'Tapa chorote','UND',17,0,0,NULL),(285,342,'Cruceta H.D 4\" x 3\"','UND',7,0,0,NULL),(286,343,'Cruceta H.D 4\" x 4\"','UND',3,0,0,NULL),(287,344,'Cruceta H.D 6\" x 3\"','UND',11,0,0,NULL),(288,346,'Válvula de compuerta sello elástico H.D 3\"','UND',12,0,0,NULL),(289,347,'Válvula de compuerta sello elástico H.D 4\"','UND',3,0,0,NULL),(290,348,'Válvula de compuerta sello elástico H.D 6\"','UND',0,0,0,NULL),(291,349,'Válvula AQT compuerta elástica 3\" RDE 21','UND',3,0,0,NULL),(292,351,'Válvula ventosa de 1\"','UND',0,0,0,NULL),(293,352,'Válvula ventosa de 2\"','UND',6,0,0,NULL),(294,353,'Hidrante H.D 3\"','UND',6,0,0,NULL),(295,354,'imprermeabilizante color seal','cuñete',10,0,0,NULL),(296,355,'Combo sanitario constructor blanco corona','unidad',42,0,0,NULL),(467,356,'Tubo presion de 10\"','unidad',2,1,0,NULL),(468,357,'Dymonic FC (300cc)  gris','tarro',57,5,2,NULL),(469,358,'Tee UM 3x3x2 presion','unidad',1,1,0,NULL),(470,359,'Tee UM 3x2x3 presion','unidad',2,1,0,NULL),(471,360,'Tee UM 4x3x4 presion','unidad',9,1,0,NULL),(472,361,'Union pasante cxc 2\"','unidad',2,1,0,NULL),(473,362,'Eucoplus 1000','bolsa',12,1,0,NULL),(474,363,'Concrete color ladrillo x 30 kilos','bolsa',20,1,0,NULL),(475,364,'Concrete color gris x 30 kilos','bolsa',16,1,0,NULL),(476,365,'Tubo presion RDE 13.5','unidad',575,5,1,NULL),(477,366,'Union presion simple de 3\"','unidad',22,2,3,NULL),(478,367,'Yee sanitario 3\"x2\"','unidad',NULL,2,3,NULL),(479,368,'Buje sanitario 2\"x1\"1/2','unidad',NULL,2,3,NULL),(480,369,'Polin x 3 mts 5x5','unidad',54,2,3,NULL),(481,370,'Tabla burra x 30 cm','unidad',4,2,3,NULL),(482,371,'Cercos  5x10','unidad',24,2,3,NULL);
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
INSERT INTO `material_almacen` VALUES (22,1,19),(23,1,2),(24,1,4),(25,1,0),(26,1,947),(27,1,118),(28,1,178),(29,1,115),(30,1,381),(31,1,180),(32,1,390),(33,1,5573),(34,1,0),(35,1,250),(36,1,2072),(37,1,1336),(38,1,0),(39,1,868),(40,1,6200),(41,1,206),(42,1,8100),(43,1,6880),(44,1,5014),(45,1,1248),(46,1,6790),(47,1,0),(48,1,712),(49,1,6900),(50,1,0),(51,1,9500),(52,1,14400),(53,1,300),(54,1,300),(55,1,390),(56,1,400),(57,1,500),(58,1,150),(59,1,0),(60,1,18),(61,1,27),(62,1,58),(63,1,26),(64,1,68),(65,1,23),(66,1,35),(67,1,500),(68,1,32),(69,1,0),(70,1,11),(71,1,13),(72,1,42),(73,1,56),(74,1,43),(75,1,44),(76,1,35),(77,1,76),(78,1,31),(79,1,10),(80,1,21),(81,1,1),(82,1,2),(83,1,25),(84,1,2),(85,1,0),(86,1,30),(87,1,0),(88,1,0),(89,1,61),(90,1,33),(91,1,1),(92,1,2),(93,1,2),(94,1,0),(95,1,4),(96,1,1),(97,1,6),(98,1,23),(99,1,10),(100,1,2),(101,1,2),(102,1,0),(103,1,0),(104,1,0),(105,1,3),(106,1,4),(107,1,0),(108,1,0),(109,1,0),(110,1,100),(111,1,20),(112,1,83),(113,1,90),(114,1,9),(115,1,0),(116,1,75),(117,1,76),(118,1,1046),(119,1,539),(120,1,500),(121,1,1998),(122,1,4524),(123,1,196),(124,1,742),(125,1,5984),(126,1,15),(127,1,215),(128,1,98),(129,1,3080),(130,1,2212),(131,1,550),(132,1,530),(133,1,0),(134,1,0),(135,1,4805),(136,1,3518),(137,1,900),(138,1,770),(139,1,220),(140,1,671),(141,1,2198),(142,1,313),(143,1,125),(144,1,154),(145,1,61),(146,1,179),(147,1,73),(148,1,280),(149,1,272),(150,1,340),(151,1,83),(152,1,71),(153,1,3),(154,1,707),(155,1,696),(156,1,57),(157,1,57),(158,1,449),(159,1,137),(160,1,138),(161,1,24),(162,1,12),(163,1,120),(164,1,19),(165,1,59),(166,1,94),(167,1,83),(168,1,218),(169,1,626),(170,1,49),(171,1,36),(172,1,700),(173,1,75),(174,1,86),(175,1,857),(176,1,1),(177,1,1),(178,1,583),(179,1,139),(180,1,349),(181,1,376),(182,1,223),(183,1,489),(184,1,321),(185,1,195),(186,1,91),(187,1,320),(188,1,55),(189,1,107),(190,1,452),(191,1,565),(192,1,26),(193,1,210),(194,1,17),(195,1,470),(196,1,91),(197,1,312),(198,1,44),(199,1,105),(200,1,147),(201,1,546),(202,1,15),(203,1,94),(204,1,3),(205,1,132),(206,1,135),(207,1,139),(208,1,178),(209,1,200),(210,1,0),(211,1,20),(212,1,87),(213,1,55),(214,1,497),(215,1,3),(216,1,18),(217,1,5),(218,1,8),(219,1,32),(220,1,32),(221,1,9),(222,1,4),(223,1,3),(224,1,90),(225,1,34),(226,1,180),(227,1,10),(228,1,10),(229,1,47),(230,1,25),(231,1,3),(232,1,7),(233,1,278),(234,1,280),(235,1,69),(236,1,39),(237,1,0),(238,1,0),(239,1,0),(240,1,50),(241,1,15),(242,1,280),(243,1,9),(244,1,22),(245,1,40),(246,1,6),(247,1,0),(248,1,0),(249,1,0),(250,1,0),(251,1,9),(252,1,182),(253,1,2),(254,1,36),(255,1,9),(256,1,443),(257,1,23),(258,1,16),(259,1,15),(260,1,2),(261,1,6),(262,1,1),(263,1,9),(264,1,6),(265,1,3),(266,1,2),(267,1,12),(268,1,5),(269,1,23),(270,1,0),(271,1,20),(272,1,17),(273,1,1),(274,1,1),(275,1,0),(276,1,36),(277,1,53),(278,1,23),(279,1,10),(280,1,3),(281,1,6),(282,1,4),(283,1,3),(284,1,17),(285,1,7),(286,1,3),(287,1,11),(288,1,12),(289,1,3),(290,1,0),(291,1,3),(292,1,0),(293,1,6),(294,1,6),(295,1,10),(296,1,42),(467,1,2),(468,1,57),(469,1,1),(470,1,2),(471,1,9),(472,1,2),(473,1,12),(474,1,20),(475,1,16),(476,1,575),(477,1,22),(478,1,0),(479,1,0),(480,1,54),(481,1,4),(482,1,24);
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
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_entradas`
--

LOCK TABLES `material_registro_entradas` WRITE;
/*!40000 ALTER TABLE `material_registro_entradas` DISABLE KEYS */;
INSERT INTO `material_registro_entradas` VALUES (33,22,48,200,1,NULL),(34,23,49,3,5,NULL),(35,24,50,4,5,NULL),(36,26,51,991,2,NULL),(37,27,52,118,2,NULL),(38,28,53,178,3,NULL),(39,29,54,115,3,NULL),(40,30,55,381,3,NULL),(41,31,56,180,3,NULL),(42,32,57,475,2,NULL),(43,33,58,5573,3,NULL),(44,34,59,100,36,NULL),(45,35,60,250,3,NULL),(46,36,61,2354,2,NULL),(47,37,62,1398,36,NULL),(48,38,63,7,3,NULL),(49,39,64,968,36,NULL),(50,40,65,6200,2,NULL),(51,41,66,206,2,NULL),(52,42,67,8100,2,NULL),(53,43,68,10120,2,NULL),(54,44,69,7726,2,NULL),(55,45,70,1608,2,NULL),(56,46,71,6790,2,NULL),(57,48,72,712,3,NULL),(58,49,73,6900,3,NULL),(59,51,74,9500,3,NULL),(60,52,75,14400,3,NULL),(61,53,76,300,3,NULL),(62,54,77,150,2,NULL),(63,55,78,200,2,NULL),(64,56,79,400,2,NULL),(65,57,80,150,2,NULL),(66,58,81,150,2,NULL),(67,60,82,23,2,NULL),(68,59,82,50,1,NULL),(69,61,83,32,2,NULL),(70,62,83,60,2,NULL),(71,63,83,26,3,NULL),(72,64,83,68,3,NULL),(73,65,84,24,3,NULL),(74,66,84,35,3,NULL),(75,67,84,500,5,NULL),(76,68,85,35,5,NULL),(77,69,85,1,3,NULL),(78,70,85,11,3,NULL),(79,71,85,13,2,NULL),(80,72,85,49,2,NULL),(81,73,85,67,2,NULL),(82,74,86,43,5,NULL),(83,75,86,47,5,NULL),(84,76,86,35,5,NULL),(85,77,86,78,5,NULL),(86,78,87,31,5,NULL),(87,79,87,10,5,NULL),(88,80,87,23,5,NULL),(89,81,87,3,5,NULL),(90,82,88,2,5,NULL),(91,83,88,26,5,NULL),(92,84,89,2,2,NULL),(93,86,90,30,34,NULL),(94,89,90,61,5,NULL),(95,90,91,33,3,NULL),(96,91,91,1,36,NULL),(97,92,91,2,36,NULL),(98,93,91,2,3,NULL),(99,95,91,10,2,NULL),(100,96,92,1,1,NULL),(101,97,92,6,2,NULL),(102,98,92,23,1,NULL),(103,99,92,10,5,NULL),(104,100,93,2,1,NULL),(105,101,93,2,1,NULL),(106,105,93,3,1,NULL),(107,106,93,4,1,NULL),(108,110,94,100,5,NULL),(109,111,94,20,5,NULL),(110,112,94,83,5,NULL),(111,113,94,90,34,NULL),(112,114,94,9,34,NULL),(113,116,95,76,35,NULL),(114,117,95,84,5,NULL),(115,118,96,1067,35,NULL),(116,119,96,560,35,NULL),(117,120,96,500,35,NULL),(118,121,97,2056,4,NULL),(119,122,97,4560,2,NULL),(120,123,97,196,2,NULL),(121,124,97,754,4,NULL),(122,125,97,6000,4,NULL),(123,126,97,15,4,NULL),(124,127,98,215,35,NULL),(125,128,98,98,35,NULL),(126,129,98,3200,35,NULL),(127,130,98,2240,35,NULL),(128,131,98,550,35,NULL),(129,132,98,560,34,NULL),(130,135,99,4850,37,NULL),(131,136,99,3528,37,NULL),(132,137,99,900,37,NULL),(133,138,99,770,37,NULL),(134,139,100,220,37,NULL),(135,140,100,671,37,NULL),(136,141,100,2234,37,NULL),(137,142,100,313,37,NULL),(138,143,100,125,37,NULL),(139,144,100,165,37,NULL),(140,145,100,61,37,NULL),(141,146,101,179,37,NULL),(142,147,101,73,37,NULL),(143,148,101,280,37,NULL),(144,149,101,305,37,NULL),(145,150,101,340,37,NULL),(146,151,101,94,37,NULL),(147,152,101,71,37,NULL),(148,153,101,3,37,NULL),(149,154,102,715,37,NULL),(150,155,102,697,37,NULL),(151,156,102,57,37,NULL),(152,157,102,57,37,NULL),(153,158,102,460,37,NULL),(154,159,102,148,37,NULL),(155,160,102,148,37,NULL),(156,161,103,24,5,NULL),(157,162,103,12,5,NULL),(158,163,103,131,5,NULL),(159,164,103,30,5,NULL),(160,165,103,59,36,NULL),(161,166,103,94,36,NULL),(162,167,103,88,34,NULL),(163,168,104,230,35,NULL),(164,169,104,682,35,NULL),(165,170,104,49,35,NULL),(166,171,104,36,35,NULL),(167,172,104,700,37,NULL),(168,173,104,75,37,NULL),(169,174,104,93,37,NULL),(170,175,104,864,37,NULL),(171,176,104,1,37,NULL),(172,177,104,1,37,NULL),(173,55,105,300,2,NULL),(174,56,105,100,2,NULL),(175,57,105,350,2,NULL),(176,54,105,150,2,NULL),(177,178,106,618,37,NULL),(178,179,106,139,37,NULL),(179,180,106,349,37,NULL),(180,181,106,409,37,NULL),(181,182,106,223,37,NULL),(182,183,106,489,37,NULL),(183,184,106,339,37,NULL),(184,185,106,213,37,NULL),(185,186,106,91,37,NULL),(186,187,107,338,5,NULL),(187,188,107,42,2,NULL),(188,189,107,107,2,NULL),(189,190,107,470,2,NULL),(190,191,107,602,2,NULL),(191,192,107,26,2,NULL),(192,193,107,210,2,NULL),(193,194,107,17,5,NULL),(194,195,107,503,5,NULL),(195,196,108,104,5,NULL),(196,197,108,321,2,NULL),(197,198,108,44,2,NULL),(198,199,108,105,2,NULL),(199,200,108,147,2,NULL),(200,201,108,546,2,NULL),(201,202,108,15,2,NULL),(202,203,108,94,2,NULL),(203,204,108,3,2,NULL),(204,205,108,158,2,NULL),(205,206,108,141,2,NULL),(206,207,109,141,2,NULL),(207,208,109,178,2,NULL),(208,209,109,200,34,NULL),(209,210,109,100,34,NULL),(210,211,109,20,34,NULL),(211,212,109,87,34,NULL),(212,213,109,55,34,NULL),(213,214,109,497,34,NULL),(214,215,109,3,5,NULL),(215,216,109,18,34,NULL),(216,217,110,5,34,NULL),(217,218,110,8,34,NULL),(218,219,110,32,34,NULL),(219,220,110,32,34,NULL),(220,221,110,9,34,NULL),(221,222,110,4,34,NULL),(222,223,110,3,34,NULL),(223,224,110,90,5,NULL),(224,225,110,45,5,NULL),(225,226,110,180,5,NULL),(226,227,111,10,35,NULL),(227,228,111,10,35,NULL),(228,229,111,48,35,NULL),(229,231,111,3,35,NULL),(230,232,111,7,35,NULL),(231,233,111,298,35,NULL),(232,234,111,300,35,NULL),(233,235,111,69,35,NULL),(234,236,112,39,35,NULL),(235,240,113,50,1,NULL),(236,241,113,15,35,NULL),(237,242,113,300,35,NULL),(238,243,113,9,35,NULL),(239,244,113,22,35,NULL),(240,245,113,40,35,NULL),(241,246,114,6,5,NULL),(242,251,114,9,37,NULL),(243,252,115,182,34,NULL),(244,254,115,36,34,NULL),(245,253,115,2,35,NULL),(246,255,115,9,35,NULL),(247,256,116,443,35,NULL),(248,257,116,23,35,NULL),(249,258,116,16,35,NULL),(250,259,116,15,35,NULL),(251,260,116,2,35,NULL),(252,261,116,6,35,NULL),(253,262,116,1,35,NULL),(254,263,116,9,35,NULL),(255,264,117,6,35,NULL),(256,265,117,3,35,NULL),(257,266,117,2,35,NULL),(258,267,117,12,35,NULL),(259,268,117,5,35,NULL),(260,269,117,23,35,NULL),(261,271,117,20,35,NULL),(262,272,117,17,35,NULL),(263,273,118,1,35,NULL),(264,274,118,1,35,NULL),(265,276,118,37,34,NULL),(266,277,118,53,35,NULL),(267,278,118,23,35,NULL),(268,279,118,10,35,NULL),(269,280,118,3,35,NULL),(270,281,118,6,35,NULL),(271,282,118,4,35,NULL),(272,283,118,3,35,NULL),(273,284,119,17,35,NULL),(274,285,119,7,35,NULL),(275,286,119,3,35,NULL),(276,287,119,11,35,NULL),(277,288,119,12,35,NULL),(278,289,119,3,35,NULL),(279,291,119,3,35,NULL),(280,293,119,6,35,NULL),(281,294,119,6,35,NULL),(282,295,120,10,34,NULL),(283,296,120,53,5,NULL),(284,467,121,2,35,NULL),(285,468,121,57,4,NULL),(286,469,121,1,35,NULL),(287,470,121,2,35,NULL),(288,471,121,9,34,NULL),(289,472,121,2,35,NULL),(290,473,121,12,4,NULL),(291,474,121,20,5,NULL),(292,475,121,16,5,NULL),(293,476,121,624,35,NULL),(294,188,122,13,2,NULL),(295,477,123,23,35,NULL),(296,230,124,35,35,NULL),(297,480,125,54,2,NULL),(298,481,125,12,2,NULL),(299,482,125,24,2,NULL),(300,22,126,45,1,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=544 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_salidas`
--

LOCK TABLES `material_registro_salidas` WRITE;
/*!40000 ALTER TABLE `material_registro_salidas` DISABLE KEYS */;
INSERT INTO `material_registro_salidas` VALUES (82,296,89,1,NULL,NULL,5,NULL,258,6909,NULL),(83,195,89,3,NULL,NULL,5,NULL,258,6909,NULL),(84,196,89,1,NULL,NULL,5,NULL,258,6909,NULL),(85,158,89,1,NULL,NULL,5,NULL,258,6909,NULL),(86,159,89,1,NULL,NULL,5,NULL,258,6909,NULL),(87,160,89,1,NULL,NULL,5,NULL,258,6909,NULL),(88,163,89,1,NULL,NULL,5,NULL,258,6909,NULL),(89,164,89,1,NULL,NULL,5,NULL,258,6909,NULL),(90,151,89,1,NULL,NULL,5,NULL,258,6909,NULL),(91,136,89,1,NULL,NULL,5,NULL,258,6909,NULL),(92,135,89,1,NULL,NULL,5,NULL,258,6909,NULL),(93,117,89,1,NULL,NULL,5,NULL,258,6909,NULL),(94,135,89,3,NULL,NULL,5,NULL,258,6909,NULL),(95,149,89,3,NULL,NULL,5,NULL,258,6909,NULL),(96,225,89,1,NULL,NULL,5,NULL,258,6909,NULL),(97,144,89,1,NULL,NULL,5,NULL,258,6909,NULL),(98,296,90,1,NULL,NULL,5,NULL,258,6910,NULL),(99,158,90,1,NULL,NULL,5,NULL,258,6910,NULL),(100,159,90,1,NULL,NULL,5,NULL,258,6910,NULL),(101,149,90,3,NULL,NULL,5,NULL,258,6908,NULL),(102,135,90,3,NULL,NULL,5,NULL,258,6910,NULL),(103,195,90,3,NULL,NULL,5,NULL,258,6910,NULL),(104,196,90,1,NULL,NULL,5,NULL,258,6910,NULL),(105,225,90,1,NULL,NULL,5,NULL,259,6910,NULL),(106,163,90,1,NULL,NULL,5,NULL,259,6910,NULL),(107,164,90,1,NULL,NULL,5,NULL,259,6910,NULL),(108,160,90,1,NULL,NULL,5,NULL,259,6910,NULL),(109,151,90,1,NULL,NULL,5,NULL,260,6910,NULL),(110,144,90,1,NULL,NULL,5,NULL,260,6910,NULL),(111,135,90,1,NULL,NULL,5,NULL,260,6910,NULL),(112,136,90,1,NULL,NULL,5,NULL,260,6910,NULL),(113,68,90,1,NULL,NULL,5,NULL,261,6910,NULL),(114,116,90,1,NULL,NULL,5,NULL,258,6910,NULL),(115,296,91,1,NULL,NULL,5,NULL,258,6911,NULL),(116,158,91,1,NULL,NULL,5,NULL,258,6911,NULL),(117,159,91,1,NULL,NULL,5,NULL,258,6911,NULL),(118,149,91,3,NULL,NULL,5,NULL,258,6911,NULL),(119,135,91,3,NULL,NULL,5,NULL,258,6911,NULL),(120,195,91,3,NULL,NULL,5,NULL,258,6911,NULL),(121,196,91,1,NULL,NULL,5,NULL,258,6911,NULL),(122,225,91,1,NULL,NULL,5,NULL,259,6911,NULL),(123,163,91,1,NULL,NULL,5,NULL,259,6911,NULL),(124,164,91,1,NULL,NULL,5,NULL,259,6911,NULL),(125,160,91,1,NULL,NULL,5,NULL,259,6911,NULL),(126,151,91,1,NULL,NULL,5,NULL,260,6911,NULL),(127,144,91,1,NULL,NULL,5,NULL,260,6911,NULL),(128,135,91,1,NULL,NULL,5,NULL,260,6911,NULL),(129,136,91,1,NULL,NULL,5,NULL,260,6911,NULL),(130,117,91,1,NULL,NULL,5,NULL,260,6911,NULL),(131,22,92,60,NULL,NULL,1,NULL,256,6905,NULL),(132,22,92,15,NULL,NULL,1,NULL,257,6905,NULL),(133,22,93,3,NULL,NULL,1,NULL,264,6888,NULL),(134,169,94,4,NULL,NULL,1,NULL,266,6905,NULL),(135,276,95,1,NULL,NULL,35,NULL,267,6889,NULL),(136,477,95,1,NULL,NULL,35,NULL,267,6889,NULL),(137,55,96,110,NULL,NULL,1,NULL,268,6905,NULL),(138,56,96,100,NULL,NULL,1,NULL,268,6905,NULL),(139,95,96,1,NULL,NULL,1,NULL,268,6905,NULL),(140,77,97,2,NULL,NULL,5,NULL,265,6888,NULL),(141,75,97,3,NULL,NULL,5,NULL,265,6888,NULL),(142,83,97,1,NULL,NULL,5,NULL,265,6888,NULL),(143,196,97,2,NULL,NULL,5,NULL,265,6888,NULL),(144,65,98,1,NULL,NULL,36,NULL,265,6888,NULL),(145,68,98,1,NULL,NULL,36,NULL,265,6888,NULL),(146,72,98,1,NULL,NULL,36,NULL,265,6888,NULL),(147,296,99,1,NULL,NULL,5,NULL,258,6912,NULL),(148,158,99,1,NULL,NULL,5,NULL,258,6912,NULL),(149,159,99,1,NULL,NULL,5,NULL,258,6912,NULL),(150,149,99,3,NULL,NULL,5,NULL,258,6912,NULL),(151,135,99,3,NULL,NULL,5,NULL,258,6912,NULL),(152,195,99,3,NULL,NULL,5,NULL,258,6912,NULL),(153,196,99,1,NULL,NULL,5,NULL,258,6912,NULL),(154,225,99,1,NULL,NULL,5,NULL,259,6912,NULL),(155,163,99,1,NULL,NULL,5,NULL,259,6912,NULL),(156,164,99,1,NULL,NULL,5,NULL,259,6912,NULL),(157,160,99,1,NULL,NULL,5,NULL,259,6912,NULL),(158,151,99,1,NULL,NULL,5,NULL,260,6912,NULL),(159,144,99,1,NULL,NULL,5,NULL,260,6912,NULL),(160,135,99,1,NULL,NULL,5,NULL,260,6912,NULL),(161,136,99,1,NULL,NULL,5,NULL,260,6912,NULL),(162,117,99,1,NULL,NULL,5,NULL,260,6912,NULL),(163,68,99,1,NULL,NULL,5,NULL,261,6912,NULL),(164,154,99,1,NULL,NULL,5,NULL,258,6912,NULL),(165,296,100,1,NULL,NULL,5,NULL,258,6913,NULL),(166,158,100,1,NULL,NULL,5,NULL,258,6913,NULL),(167,159,100,1,NULL,NULL,5,NULL,258,6913,NULL),(168,149,100,3,NULL,NULL,5,NULL,258,6913,NULL),(169,135,100,3,NULL,NULL,5,NULL,258,6913,NULL),(170,195,100,3,NULL,NULL,5,NULL,258,6913,NULL),(171,196,100,1,NULL,NULL,5,NULL,258,6913,NULL),(172,225,100,1,NULL,NULL,5,NULL,259,6913,NULL),(173,163,100,1,NULL,NULL,5,NULL,259,6913,NULL),(174,164,100,1,NULL,NULL,5,NULL,259,6913,NULL),(175,160,100,1,NULL,NULL,5,NULL,259,6913,NULL),(176,151,100,1,NULL,NULL,5,NULL,260,6913,NULL),(177,144,100,1,NULL,NULL,5,NULL,260,6913,NULL),(178,135,100,1,NULL,NULL,34,NULL,260,6913,NULL),(179,136,100,1,NULL,NULL,34,NULL,260,6913,NULL),(180,117,100,1,NULL,NULL,5,NULL,260,6913,NULL),(181,296,101,1,NULL,NULL,5,NULL,258,6919,NULL),(182,158,101,1,NULL,NULL,5,NULL,258,6919,NULL),(183,159,101,1,NULL,NULL,5,NULL,258,6919,NULL),(184,149,101,3,NULL,NULL,5,NULL,258,6919,NULL),(185,135,101,3,NULL,NULL,5,NULL,258,6919,NULL),(186,195,101,3,NULL,NULL,5,NULL,258,6919,NULL),(187,196,101,1,NULL,NULL,5,NULL,258,6919,NULL),(188,225,101,1,NULL,NULL,5,NULL,259,6919,NULL),(189,163,101,1,NULL,NULL,5,NULL,259,6919,NULL),(190,164,101,1,NULL,NULL,5,NULL,259,6919,NULL),(191,160,101,1,NULL,NULL,5,NULL,259,6919,NULL),(192,151,101,1,NULL,NULL,5,NULL,259,6919,NULL),(193,144,101,1,NULL,NULL,5,NULL,259,6919,NULL),(194,135,101,1,NULL,NULL,5,NULL,259,6919,NULL),(195,136,101,1,NULL,NULL,5,NULL,259,6919,NULL),(196,23,101,1,NULL,NULL,5,NULL,258,6919,NULL),(197,22,102,6,NULL,NULL,2,NULL,269,6901,NULL),(198,22,103,15,NULL,NULL,1,NULL,256,6887,NULL),(199,22,104,10,NULL,NULL,1,NULL,256,6887,NULL),(200,95,104,2,NULL,NULL,1,NULL,270,6887,NULL),(201,22,105,10,NULL,NULL,2,NULL,269,6903,NULL),(202,296,106,1,NULL,NULL,5,NULL,258,6918,NULL),(203,158,106,1,NULL,NULL,5,NULL,258,6918,NULL),(204,159,106,1,NULL,NULL,5,NULL,258,6918,NULL),(205,149,106,3,NULL,NULL,5,NULL,258,6918,NULL),(206,135,106,3,NULL,NULL,5,NULL,258,6918,NULL),(207,195,106,3,NULL,NULL,5,NULL,258,6918,NULL),(208,196,106,1,NULL,NULL,5,NULL,258,6918,NULL),(209,225,106,1,NULL,NULL,5,NULL,259,6918,NULL),(210,163,106,1,NULL,NULL,5,NULL,259,6918,NULL),(211,164,106,1,NULL,NULL,5,NULL,259,6918,NULL),(212,151,106,1,NULL,NULL,5,NULL,259,6918,NULL),(213,144,106,1,NULL,NULL,5,NULL,260,6918,NULL),(214,135,106,1,NULL,NULL,5,NULL,260,6918,NULL),(215,136,106,1,NULL,NULL,5,NULL,260,6918,NULL),(216,117,106,1,NULL,NULL,5,NULL,260,6918,NULL),(217,296,107,1,NULL,NULL,5,NULL,258,6917,NULL),(218,158,107,1,NULL,NULL,5,NULL,258,6917,NULL),(219,159,107,1,NULL,NULL,5,NULL,258,6917,NULL),(220,149,107,3,NULL,NULL,5,NULL,258,6917,NULL),(221,135,107,3,NULL,NULL,5,NULL,258,6917,NULL),(222,195,107,3,NULL,NULL,5,NULL,258,6917,NULL),(223,196,107,1,NULL,NULL,5,NULL,258,6917,NULL),(224,225,107,1,NULL,NULL,5,NULL,259,6917,NULL),(225,163,107,1,NULL,NULL,5,NULL,259,6917,NULL),(226,164,107,1,NULL,NULL,5,NULL,259,6917,NULL),(227,160,107,1,NULL,NULL,5,NULL,259,6917,NULL),(228,151,107,1,NULL,NULL,5,NULL,260,6917,NULL),(229,144,107,1,NULL,NULL,5,NULL,260,6917,NULL),(230,135,107,1,NULL,NULL,5,NULL,260,6917,NULL),(231,136,107,1,NULL,NULL,5,NULL,260,6917,NULL),(232,296,108,1,NULL,NULL,5,NULL,258,6916,NULL),(233,158,108,1,NULL,NULL,5,NULL,258,6916,NULL),(234,159,108,1,NULL,NULL,5,NULL,258,6916,NULL),(235,149,108,3,NULL,NULL,5,NULL,258,6916,NULL),(236,135,108,3,NULL,NULL,5,NULL,258,6916,NULL),(237,195,108,3,NULL,NULL,5,NULL,258,6916,NULL),(238,196,108,1,NULL,NULL,5,NULL,258,6916,NULL),(239,225,108,1,NULL,NULL,5,NULL,259,6916,NULL),(240,163,108,1,NULL,NULL,5,NULL,259,6916,NULL),(241,164,108,1,NULL,NULL,5,NULL,259,6916,NULL),(242,160,108,1,NULL,NULL,5,NULL,259,6916,NULL),(243,151,108,1,NULL,NULL,5,NULL,260,6916,NULL),(244,144,108,1,NULL,NULL,5,NULL,260,6916,NULL),(245,135,108,1,NULL,NULL,5,NULL,260,6916,NULL),(246,136,108,1,NULL,NULL,5,NULL,260,6916,NULL),(247,117,108,1,NULL,NULL,5,NULL,260,6916,NULL),(248,22,109,10,NULL,NULL,1,NULL,256,6887,NULL),(249,233,110,20,NULL,NULL,35,NULL,272,6887,NULL),(250,234,110,20,NULL,NULL,35,NULL,270,6887,NULL),(251,242,110,20,NULL,NULL,35,NULL,272,6887,NULL),(252,230,110,10,NULL,NULL,35,NULL,272,6887,NULL),(253,229,110,1,NULL,NULL,35,NULL,272,6887,NULL),(254,296,111,1,NULL,NULL,5,NULL,258,6914,NULL),(255,158,111,1,NULL,NULL,5,NULL,258,6914,NULL),(256,159,111,1,NULL,NULL,5,NULL,258,6914,NULL),(257,149,111,3,NULL,NULL,5,NULL,258,6914,NULL),(258,135,111,3,NULL,NULL,5,NULL,258,6914,NULL),(259,195,111,3,NULL,NULL,5,NULL,258,6914,NULL),(260,196,111,1,NULL,NULL,5,NULL,258,6914,NULL),(261,225,111,1,NULL,NULL,5,NULL,259,6914,NULL),(262,163,111,1,NULL,NULL,5,NULL,259,6914,NULL),(263,164,111,1,NULL,NULL,5,NULL,259,6914,NULL),(264,160,111,1,NULL,NULL,5,NULL,259,6914,NULL),(265,151,111,1,NULL,NULL,5,NULL,260,6914,NULL),(266,144,111,1,NULL,NULL,5,NULL,260,6914,NULL),(267,135,111,1,NULL,NULL,5,NULL,260,6914,NULL),(268,135,111,1,NULL,NULL,5,NULL,260,6914,NULL),(269,117,111,1,NULL,NULL,5,NULL,260,6914,NULL),(270,296,111,1,NULL,NULL,5,NULL,258,6915,NULL),(271,158,111,1,NULL,NULL,5,NULL,258,6915,NULL),(272,159,111,1,NULL,NULL,5,NULL,258,6915,NULL),(273,149,111,3,NULL,NULL,5,NULL,258,6915,NULL),(274,135,111,3,NULL,NULL,5,NULL,258,6915,NULL),(275,195,111,3,NULL,NULL,5,NULL,258,6915,NULL),(276,196,111,1,NULL,NULL,5,NULL,258,6915,NULL),(277,225,111,1,NULL,NULL,5,NULL,259,6915,NULL),(278,163,111,1,NULL,NULL,5,NULL,259,6915,NULL),(279,164,111,1,NULL,NULL,5,NULL,259,6915,NULL),(280,160,111,1,NULL,NULL,5,NULL,259,6915,NULL),(281,151,111,1,NULL,NULL,5,NULL,260,6915,NULL),(282,144,111,1,NULL,NULL,5,NULL,260,6915,NULL),(283,135,111,1,NULL,NULL,5,NULL,260,6915,NULL),(284,136,111,1,NULL,NULL,5,NULL,260,6915,NULL),(285,117,111,1,NULL,NULL,5,NULL,260,6915,NULL),(286,22,112,10,NULL,NULL,1,NULL,256,6887,NULL),(287,95,112,2,NULL,NULL,1,NULL,270,6887,NULL),(288,22,113,6,NULL,NULL,2,NULL,269,6902,NULL),(289,476,114,24,NULL,NULL,35,NULL,265,6890,NULL),(290,129,114,120,NULL,NULL,35,NULL,265,6890,NULL),(291,119,115,3,NULL,NULL,2,NULL,273,6900,NULL),(292,118,115,3,NULL,NULL,2,NULL,273,6900,NULL),(293,121,115,15,NULL,NULL,2,NULL,273,6900,NULL),(294,122,115,6,NULL,NULL,2,NULL,273,6900,NULL),(295,130,115,6,NULL,NULL,2,NULL,273,6900,NULL),(296,125,115,2,NULL,NULL,2,NULL,273,6900,NULL),(297,124,115,2,NULL,NULL,2,NULL,273,6900,NULL),(298,132,115,6,NULL,NULL,2,NULL,273,6900,NULL),(299,141,115,6,NULL,NULL,2,NULL,273,6900,NULL),(300,154,115,1,NULL,NULL,2,NULL,273,6900,NULL),(301,119,115,6,NULL,NULL,2,NULL,273,6901,NULL),(302,118,115,6,NULL,NULL,2,NULL,273,6901,NULL),(303,121,115,15,NULL,NULL,2,NULL,273,6901,NULL),(304,122,115,6,NULL,NULL,2,NULL,273,6901,NULL),(305,130,115,6,NULL,NULL,2,NULL,273,6901,NULL),(306,125,115,2,NULL,NULL,2,NULL,273,6901,NULL),(307,124,115,2,NULL,NULL,2,NULL,273,6901,NULL),(308,141,115,6,NULL,NULL,2,NULL,273,6901,NULL),(309,184,116,3,NULL,NULL,2,NULL,276,6900,NULL),(310,178,116,9,NULL,NULL,2,NULL,276,6900,NULL),(311,187,116,3,NULL,NULL,2,NULL,276,6900,NULL),(312,197,116,1,NULL,NULL,2,NULL,276,6900,NULL),(313,169,116,3,NULL,NULL,2,NULL,276,6900,NULL),(314,168,116,2,NULL,NULL,2,NULL,276,6900,NULL),(315,167,116,1,NULL,NULL,2,NULL,276,6900,NULL),(316,181,116,1,NULL,NULL,2,NULL,276,6900,NULL),(317,207,116,1,NULL,NULL,2,NULL,276,6900,NULL),(318,206,116,1,NULL,NULL,2,NULL,276,6900,NULL),(319,185,116,5,NULL,NULL,2,NULL,276,6900,NULL),(320,205,116,5,NULL,NULL,2,NULL,275,6900,NULL),(321,169,116,5,NULL,NULL,2,NULL,275,6900,NULL),(322,190,116,3,NULL,NULL,2,NULL,276,6900,NULL),(323,191,116,4,NULL,NULL,2,NULL,276,6900,NULL),(324,184,117,3,NULL,NULL,2,NULL,276,6901,NULL),(325,178,117,7,NULL,NULL,2,NULL,276,6901,NULL),(326,187,117,3,NULL,NULL,2,NULL,276,6901,NULL),(327,197,117,1,NULL,NULL,2,NULL,276,6901,NULL),(328,169,117,3,NULL,NULL,2,NULL,276,6901,NULL),(329,168,117,2,NULL,NULL,2,NULL,276,6901,NULL),(330,167,117,1,NULL,NULL,2,NULL,276,6901,NULL),(331,181,117,10,NULL,NULL,2,NULL,276,6901,NULL),(332,207,117,1,NULL,NULL,2,NULL,276,6901,NULL),(333,206,117,1,NULL,NULL,2,NULL,276,6901,NULL),(334,185,117,5,NULL,NULL,2,NULL,276,6901,NULL),(335,205,117,5,NULL,NULL,2,NULL,275,6901,NULL),(336,169,117,5,NULL,NULL,2,NULL,275,6901,NULL),(337,190,117,3,NULL,NULL,2,NULL,276,6901,NULL),(338,191,117,5,NULL,NULL,2,NULL,276,6901,NULL),(339,154,117,1,NULL,NULL,2,NULL,276,6901,NULL),(340,155,117,1,NULL,NULL,2,NULL,276,6901,NULL),(341,34,118,100,NULL,NULL,36,NULL,278,6887,NULL),(342,39,118,50,NULL,NULL,36,NULL,278,6887,NULL),(343,26,118,10,NULL,NULL,36,NULL,278,6887,NULL),(344,210,119,100,NULL,NULL,1,NULL,280,6889,NULL),(345,73,120,3,NULL,NULL,36,NULL,278,6887,NULL),(346,167,121,1,NULL,NULL,2,NULL,276,6902,NULL),(347,169,121,4,NULL,NULL,2,NULL,276,6902,NULL),(348,168,121,2,NULL,NULL,2,NULL,276,6902,NULL),(349,190,121,3,NULL,NULL,2,NULL,276,6902,NULL),(350,191,121,7,NULL,NULL,2,NULL,276,6902,NULL),(351,197,121,1,NULL,NULL,2,NULL,276,6902,NULL),(352,174,121,1,NULL,NULL,2,NULL,276,6902,NULL),(353,175,121,1,NULL,NULL,2,NULL,276,6902,NULL),(354,154,121,1,NULL,NULL,2,NULL,276,6902,NULL),(355,178,121,4,NULL,NULL,2,NULL,272,6902,NULL),(356,169,122,4,NULL,NULL,2,NULL,276,6903,NULL),(357,168,122,2,NULL,NULL,2,NULL,276,6903,NULL),(358,167,122,1,NULL,NULL,2,NULL,276,6903,NULL),(359,187,122,3,NULL,NULL,2,NULL,276,6903,NULL),(360,184,122,3,NULL,NULL,2,NULL,276,6903,NULL),(361,197,122,1,NULL,NULL,2,NULL,276,6903,NULL),(362,181,122,2,NULL,NULL,2,NULL,276,6903,NULL),(363,190,122,3,NULL,NULL,2,NULL,276,6903,NULL),(364,191,122,7,NULL,NULL,2,NULL,276,6903,NULL),(365,174,122,2,NULL,NULL,2,NULL,276,6903,NULL),(366,175,122,2,NULL,NULL,2,NULL,276,6903,NULL),(367,178,122,5,NULL,NULL,2,NULL,276,6903,NULL),(368,206,122,1,NULL,NULL,2,NULL,276,6903,NULL),(369,206,122,1,NULL,NULL,2,NULL,276,6902,NULL),(370,187,122,3,NULL,NULL,2,NULL,276,6902,NULL),(371,184,122,3,NULL,NULL,2,NULL,276,6902,NULL),(372,22,123,15,NULL,NULL,1,NULL,256,6887,NULL),(373,72,124,3,NULL,NULL,2,NULL,283,6901,NULL),(374,37,125,10,NULL,NULL,1,NULL,278,6887,NULL),(375,73,125,2,NULL,NULL,1,NULL,278,6887,NULL),(376,476,126,25,NULL,NULL,35,NULL,265,6890,NULL),(377,69,127,1,NULL,NULL,36,NULL,284,6887,NULL),(378,119,128,3,NULL,NULL,2,NULL,273,6902,NULL),(379,118,128,3,NULL,NULL,2,NULL,273,6902,NULL),(380,121,128,7,NULL,NULL,2,NULL,273,6902,NULL),(381,122,128,6,NULL,NULL,2,NULL,273,6902,NULL),(382,130,128,4,NULL,NULL,2,NULL,273,6902,NULL),(383,125,128,2,NULL,NULL,2,NULL,273,6902,NULL),(384,124,128,2,NULL,NULL,2,NULL,273,6902,NULL),(385,132,128,6,NULL,NULL,2,NULL,273,6902,NULL),(386,141,128,6,NULL,NULL,2,NULL,273,6902,NULL),(387,154,128,1,NULL,NULL,2,NULL,273,6902,NULL),(388,119,128,3,NULL,NULL,2,NULL,273,6903,NULL),(389,118,128,3,NULL,NULL,2,NULL,273,6903,NULL),(390,121,128,7,NULL,NULL,2,NULL,273,6903,NULL),(391,122,128,6,NULL,NULL,2,NULL,273,6903,NULL),(392,130,128,4,NULL,NULL,2,NULL,273,6903,NULL),(393,125,128,2,NULL,NULL,2,NULL,273,6903,NULL),(394,124,128,2,NULL,NULL,2,NULL,273,6903,NULL),(395,132,128,6,NULL,NULL,2,NULL,273,6903,NULL),(396,141,128,6,NULL,NULL,2,NULL,273,6903,NULL),(397,22,129,10,NULL,NULL,1,NULL,256,6887,NULL),(398,22,129,7,NULL,NULL,1,NULL,285,6887,NULL),(399,38,130,7,NULL,NULL,36,NULL,278,6887,NULL),(400,72,131,3,NULL,NULL,2,NULL,283,6902,NULL),(401,80,132,2,NULL,NULL,1,NULL,256,6887,NULL),(402,81,132,2,NULL,NULL,1,NULL,256,6887,NULL),(403,169,133,4,NULL,NULL,2,NULL,276,6904,NULL),(404,168,133,2,NULL,NULL,2,NULL,276,6904,NULL),(405,167,133,1,NULL,NULL,2,NULL,276,6904,NULL),(406,187,133,3,NULL,NULL,2,NULL,276,6904,NULL),(407,184,133,3,NULL,NULL,2,NULL,276,6904,NULL),(408,197,133,2,NULL,NULL,2,NULL,276,6904,NULL),(409,181,133,2,NULL,NULL,2,NULL,276,6904,NULL),(410,190,133,3,NULL,NULL,2,NULL,276,6904,NULL),(411,191,133,7,NULL,NULL,2,NULL,276,6904,NULL),(412,174,133,2,NULL,NULL,2,NULL,276,6904,NULL),(413,175,133,2,NULL,NULL,2,NULL,276,6904,NULL),(414,178,133,5,NULL,NULL,2,NULL,276,6904,NULL),(415,206,133,1,NULL,NULL,2,NULL,276,6904,NULL),(416,154,133,1,NULL,NULL,2,NULL,276,6904,NULL),(417,169,133,4,NULL,NULL,2,NULL,276,6905,NULL),(418,168,133,2,NULL,NULL,2,NULL,276,6905,NULL),(419,187,133,3,NULL,NULL,2,NULL,276,6905,NULL),(420,184,133,2,NULL,NULL,2,NULL,276,6905,NULL),(421,197,133,1,NULL,NULL,2,NULL,276,6905,NULL),(422,184,133,1,NULL,NULL,2,NULL,276,6905,NULL),(423,197,133,2,NULL,NULL,2,NULL,276,6905,NULL),(424,181,133,2,NULL,NULL,2,NULL,276,6905,NULL),(425,190,133,3,NULL,NULL,2,NULL,276,6905,NULL),(426,191,133,7,NULL,NULL,2,NULL,276,6905,NULL),(427,174,133,2,NULL,NULL,2,NULL,276,6905,NULL),(428,175,133,2,NULL,NULL,2,NULL,276,6905,NULL),(429,178,133,5,NULL,NULL,2,NULL,276,6905,NULL),(430,206,133,1,NULL,NULL,2,NULL,276,6905,NULL),(431,205,134,4,NULL,NULL,2,NULL,275,6902,NULL),(432,169,134,5,NULL,NULL,2,NULL,275,6902,NULL),(433,181,134,4,NULL,NULL,2,NULL,275,6902,NULL),(434,185,134,2,NULL,NULL,2,NULL,275,6902,NULL),(435,205,134,4,NULL,NULL,2,NULL,275,6903,NULL),(436,169,134,5,NULL,NULL,2,NULL,275,6903,NULL),(437,181,134,4,NULL,NULL,2,NULL,275,6903,NULL),(438,185,134,2,NULL,NULL,2,NULL,275,6903,NULL),(439,205,134,4,NULL,NULL,2,NULL,275,6904,NULL),(440,169,134,5,NULL,NULL,2,NULL,275,6904,NULL),(441,181,134,4,NULL,NULL,2,NULL,275,6904,NULL),(442,185,134,2,NULL,NULL,2,NULL,275,6904,NULL),(443,154,134,1,NULL,NULL,2,NULL,275,6904,NULL),(444,205,134,4,NULL,NULL,2,NULL,275,6905,NULL),(445,169,134,5,NULL,NULL,2,NULL,275,6905,NULL),(446,181,134,4,NULL,NULL,2,NULL,275,6905,NULL),(447,185,134,2,NULL,NULL,2,NULL,275,6905,NULL),(448,44,135,452,NULL,NULL,2,NULL,263,6900,NULL),(449,45,135,60,NULL,NULL,2,NULL,263,6900,NULL),(450,43,135,540,NULL,NULL,2,NULL,286,6900,NULL),(451,26,135,3,NULL,NULL,2,NULL,287,6900,NULL),(452,36,135,9,NULL,NULL,2,NULL,281,6900,NULL),(453,36,135,13,NULL,NULL,2,NULL,286,6900,NULL),(454,36,135,25,NULL,NULL,2,NULL,263,6900,NULL),(455,37,135,2,NULL,NULL,2,NULL,282,6900,NULL),(456,32,135,15,NULL,NULL,2,NULL,263,6900,NULL),(457,44,136,452,NULL,NULL,2,NULL,263,6901,NULL),(458,45,136,60,NULL,NULL,2,NULL,263,6901,NULL),(459,43,136,540,NULL,NULL,2,NULL,286,6901,NULL),(460,26,136,4,NULL,NULL,2,NULL,287,6901,NULL),(461,36,136,9,NULL,NULL,2,NULL,281,6901,NULL),(462,36,136,13,NULL,NULL,2,NULL,286,6901,NULL),(463,36,136,25,NULL,NULL,2,NULL,263,6901,NULL),(464,37,136,1,NULL,NULL,2,NULL,282,6901,NULL),(465,32,136,15,NULL,NULL,2,NULL,286,6901,NULL),(466,73,136,1,NULL,NULL,2,NULL,263,6901,NULL),(467,44,136,452,NULL,NULL,2,NULL,263,6902,NULL),(468,45,136,60,NULL,NULL,2,NULL,263,6902,NULL),(469,43,136,540,NULL,NULL,2,NULL,286,6902,NULL),(470,26,136,4,NULL,NULL,2,NULL,263,6902,NULL),(471,36,136,9,NULL,NULL,2,NULL,281,6902,NULL),(472,36,136,13,NULL,NULL,2,NULL,286,6902,NULL),(473,36,136,25,NULL,NULL,2,NULL,263,6902,NULL),(474,37,136,1,NULL,NULL,2,NULL,282,6902,NULL),(475,32,136,15,NULL,NULL,2,NULL,263,6902,NULL),(476,44,137,452,NULL,NULL,2,NULL,263,6903,NULL),(477,45,137,60,NULL,NULL,2,NULL,263,6903,NULL),(478,43,137,540,NULL,NULL,2,NULL,286,6903,NULL),(479,26,137,4,NULL,NULL,2,NULL,287,6903,NULL),(480,36,137,9,NULL,NULL,2,NULL,281,6903,NULL),(481,36,137,13,NULL,NULL,2,NULL,286,6903,NULL),(482,36,137,25,NULL,NULL,2,NULL,263,6903,NULL),(483,37,137,1,NULL,NULL,2,NULL,282,6903,NULL),(484,32,137,15,NULL,NULL,2,NULL,263,6903,NULL),(485,73,137,1,NULL,NULL,2,NULL,286,6903,NULL),(486,44,137,452,NULL,NULL,2,NULL,263,6904,NULL),(487,45,137,60,NULL,NULL,2,NULL,263,6904,NULL),(488,43,137,540,NULL,NULL,2,NULL,286,6904,NULL),(489,26,137,3,NULL,NULL,2,NULL,287,6904,NULL),(490,36,137,9,NULL,NULL,2,NULL,281,6904,NULL),(491,36,137,13,NULL,NULL,2,NULL,286,6904,NULL),(492,36,137,25,NULL,NULL,2,NULL,263,6904,NULL),(493,37,137,1,NULL,NULL,2,NULL,282,6904,NULL),(494,32,137,15,NULL,NULL,2,NULL,286,6904,NULL),(495,44,138,452,NULL,NULL,2,NULL,263,6905,NULL),(496,45,138,60,NULL,NULL,2,NULL,263,6905,NULL),(497,43,138,540,NULL,NULL,2,NULL,286,6905,NULL),(498,26,138,4,NULL,NULL,2,NULL,287,6905,NULL),(499,36,138,9,NULL,NULL,2,NULL,281,6905,NULL),(500,36,138,13,NULL,NULL,2,NULL,286,6905,NULL),(501,36,138,25,NULL,NULL,2,NULL,263,6905,NULL),(502,37,138,1,NULL,NULL,2,NULL,282,6905,NULL),(503,32,138,10,NULL,NULL,2,NULL,263,6905,NULL),(504,22,139,6,NULL,NULL,2,NULL,269,6904,NULL),(505,22,140,17,NULL,NULL,1,NULL,256,6887,NULL),(506,26,141,7,NULL,NULL,36,NULL,278,6887,NULL),(507,39,141,10,NULL,NULL,36,NULL,278,6887,NULL),(508,73,141,2,NULL,NULL,36,NULL,278,6887,NULL),(509,37,141,20,NULL,NULL,36,NULL,278,6887,NULL),(510,60,142,5,NULL,NULL,1,NULL,280,6889,NULL),(511,61,142,5,NULL,NULL,1,NULL,280,6889,NULL),(512,62,142,2,NULL,NULL,1,NULL,280,6889,NULL),(513,59,143,30,NULL,NULL,1,NULL,289,6887,NULL),(514,59,144,20,NULL,NULL,1,NULL,290,6889,NULL),(515,37,145,5,NULL,NULL,1,NULL,268,6887,NULL),(516,95,145,1,NULL,NULL,1,NULL,268,6887,NULL),(517,119,146,3,NULL,NULL,2,NULL,273,6905,NULL),(518,118,146,3,NULL,NULL,2,NULL,273,6905,NULL),(519,121,146,7,NULL,NULL,2,NULL,273,6905,NULL),(520,122,146,6,NULL,NULL,2,NULL,273,6905,NULL),(521,130,146,4,NULL,NULL,2,NULL,273,6905,NULL),(522,125,146,6,NULL,NULL,2,NULL,273,6905,NULL),(523,124,146,2,NULL,NULL,2,NULL,273,6905,NULL),(524,132,146,6,NULL,NULL,2,NULL,273,6905,NULL),(525,141,146,6,NULL,NULL,2,NULL,273,6905,NULL),(526,154,146,1,NULL,NULL,2,NULL,273,6905,NULL),(527,119,146,3,NULL,NULL,2,NULL,273,6905,NULL),(528,118,146,3,NULL,NULL,2,NULL,273,6905,NULL),(529,121,146,7,NULL,NULL,2,NULL,273,6905,NULL),(530,122,146,6,NULL,NULL,2,NULL,273,6905,NULL),(531,130,146,4,NULL,NULL,2,NULL,273,6905,NULL),(532,125,146,2,NULL,NULL,2,NULL,273,6905,NULL),(533,124,146,2,NULL,NULL,2,NULL,273,6905,NULL),(534,132,146,6,NULL,NULL,2,NULL,273,6905,NULL),(535,141,146,6,NULL,NULL,2,NULL,273,6905,NULL),(536,26,147,5,NULL,NULL,36,NULL,278,6887,NULL),(537,37,147,20,NULL,NULL,36,NULL,278,6887,NULL),(538,39,147,40,NULL,NULL,36,NULL,278,6887,NULL),(539,22,148,16,NULL,NULL,1,NULL,256,6887,NULL),(540,22,149,5,NULL,NULL,2,NULL,269,6906,NULL),(541,22,149,5,NULL,NULL,2,NULL,269,6907,NULL),(542,73,150,2,NULL,NULL,1,NULL,278,6887,NULL),(543,481,151,8,NULL,NULL,2,NULL,288,6907,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecto`
--

LOCK TABLES `proyecto` WRITE;
/*!40000 ALTER TABLE `proyecto` DISABLE KEYS */;
INSERT INTO `proyecto` VALUES (1,'Salamanca la nueva conjunto residencial',NULL),(2,'Tienda D1',NULL),(3,'Ciudadela salamanca la nueva',NULL),(62,'Alcala',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_entradas`
--

LOCK TABLES `registro_entradas` WRITE;
/*!40000 ALTER TABLE `registro_entradas` DISABLE KEYS */;
INSERT INTO `registro_entradas` VALUES (48,'2026-08-12','11:04:37',23,NULL),(49,'2026-08-12','11:10:26',23,NULL),(50,'2026-08-12','11:10:47',23,NULL),(51,'2026-08-12','14:40:08',23,NULL),(52,'2026-08-12','14:40:58',23,NULL),(53,'2026-08-12','14:41:33',23,NULL),(54,'2026-08-12','14:42:02',23,NULL),(55,'2026-08-12','14:42:38',23,NULL),(56,'2026-08-12','14:43:19',23,NULL),(57,'2026-08-12','14:43:53',23,NULL),(58,'2026-08-12','14:44:23',23,NULL),(59,'2026-08-12','14:44:59',23,NULL),(60,'2026-08-12','14:45:20',23,NULL),(61,'2026-08-12','14:45:51',23,NULL),(62,'2026-08-12','14:46:30',23,NULL),(63,'2026-08-12','14:47:05',23,NULL),(64,'2026-08-12','14:47:50',23,NULL),(65,'2026-08-12','14:48:21',23,NULL),(66,'2026-08-12','14:48:44',23,NULL),(67,'2026-08-12','14:49:01',23,NULL),(68,'2026-08-12','14:49:23',23,NULL),(69,'2026-08-12','14:49:47',23,NULL),(70,'2026-08-12','14:50:17',23,NULL),(71,'2026-08-12','14:50:47',23,NULL),(72,'2026-08-12','14:52:14',23,NULL),(73,'2026-08-12','14:52:52',23,NULL),(74,'2026-08-12','14:53:19',23,NULL),(75,'2026-08-12','14:53:45',23,NULL),(76,'2026-08-12','14:54:15',23,NULL),(77,'2026-08-12','14:55:02',23,NULL),(78,'2026-08-12','14:55:28',23,NULL),(79,'2026-08-12','14:56:03',23,NULL),(80,'2026-08-12','14:56:24',23,NULL),(81,'2026-08-12','14:56:49',23,NULL),(82,'2026-08-12','14:58:08',23,NULL),(83,'2026-08-12','15:00:06',23,NULL),(84,'2026-08-12','15:01:02',23,NULL),(85,'2026-08-12','15:03:01',23,NULL),(86,'2026-08-12','15:04:26',23,NULL),(87,'2026-08-12','15:06:08',23,NULL),(88,'2026-08-12','15:07:37',23,NULL),(89,'2026-08-12','15:08:18',23,NULL),(90,'2026-08-12','15:09:21',23,NULL),(91,'2026-08-12','15:11:11',23,NULL),(92,'2026-08-12','15:13:03',23,NULL),(93,'2026-08-13','08:36:28',23,NULL),(94,'2026-08-13','08:38:13',23,NULL),(95,'2026-08-13','08:39:06',23,NULL),(96,'2026-08-13','08:40:13',23,NULL),(97,'2026-08-13','08:42:36',23,NULL),(98,'2026-08-13','08:45:02',23,NULL),(99,'2026-08-13','08:48:16',23,NULL),(100,'2026-08-13','08:53:03',23,NULL),(101,'2026-08-13','08:55:54',23,NULL),(102,'2026-08-13','09:03:21',23,NULL),(103,'2026-08-13','09:07:41',23,NULL),(104,'2026-08-13','09:22:19',23,NULL),(105,'2026-08-13','10:18:23',23,NULL),(106,'2026-08-13','10:29:36',23,NULL),(107,'2026-08-13','10:32:58',23,NULL),(108,'2026-08-13','10:37:11',23,NULL),(109,'2026-08-13','10:40:57',23,NULL),(110,'2026-08-13','10:44:12',23,NULL),(111,'2026-08-13','10:47:43',23,NULL),(112,'2026-08-13','10:48:17',23,NULL),(113,'2026-08-13','10:52:56',23,NULL),(114,'2026-08-13','10:54:14',23,NULL),(115,'2026-08-13','10:56:23',23,NULL),(116,'2026-08-13','10:58:46',23,NULL),(117,'2026-08-13','11:02:58',23,NULL),(118,'2026-08-13','11:06:23',23,NULL),(119,'2026-08-13','11:12:18',23,NULL),(120,'2026-08-13','11:13:25',23,NULL),(121,'2026-08-13','11:39:02',23,NULL),(122,'2026-08-18','08:59:27',23,NULL),(123,'2026-08-21','09:21:26',23,NULL),(124,'2026-08-24','09:22:19',23,NULL),(125,'2026-08-29','09:32:58',23,NULL),(126,'2026-08-31','09:52:30',23,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_salidas`
--

LOCK TABLES `registro_salidas` WRITE;
/*!40000 ALTER TABLE `registro_salidas` DISABLE KEYS */;
INSERT INTO `registro_salidas` VALUES (89,'2026-08-21','08:41:03',23,1,3),(90,'2026-08-21','08:54:33',23,1,3),(91,'2026-08-21','09:00:38',23,1,3),(92,'2026-08-21','09:05:22',23,3,1),(93,'2026-08-21','09:11:22',23,2,2),(94,'2026-08-21','09:13:45',23,3,1),(95,'2026-08-21','09:23:05',23,16,3),(96,'2026-08-21','09:32:05',23,17,1),(97,'2026-08-21','09:38:13',23,1,2),(98,'2026-08-21','15:16:45',23,1,2),(99,'2026-08-22','07:45:10',23,1,3),(100,'2026-08-22','07:50:00',23,1,3),(101,'2026-08-22','07:54:49',23,1,3),(102,'2026-08-22','07:55:33',23,2,1),(103,'2026-08-22','07:57:47',23,3,1),(104,'2026-08-24','09:33:36',23,3,1),(105,'2026-08-24','09:34:19',23,2,1),(106,'2026-08-24','09:42:45',23,1,3),(107,'2026-08-24','09:48:55',23,1,3),(108,'2026-08-24','12:48:42',23,1,3),(109,'2026-08-24','12:49:07',23,3,1),(110,'2026-08-24','13:43:32',23,3,1),(111,'2026-08-24','14:34:22',23,1,3),(112,'2026-08-25','12:15:35',23,3,1),(113,'2026-08-25','12:16:33',23,2,1),(114,'2026-08-25','12:23:56',23,16,62),(115,'2026-08-25','12:33:05',23,2,1),(116,'2026-08-25','13:19:34',23,2,1),(117,'2026-08-25','13:30:14',23,2,1),(118,'2026-08-25','13:33:56',23,1,1),(119,'2026-08-25','14:04:45',23,1,3),(120,'2026-08-25','14:08:04',23,1,1),(121,'2026-08-26','12:59:11',23,2,1),(122,'2026-08-26','13:17:28',23,2,1),(123,'2026-08-26','13:18:10',23,3,1),(124,'2026-08-26','13:19:42',23,2,1),(125,'2026-08-26','13:21:12',23,1,1),(126,'2026-08-27','13:40:45',23,16,62),(127,'2026-08-27','13:43:18',23,1,1),(128,'2026-08-27','13:50:23',23,2,1),(129,'2026-08-27','13:53:37',23,3,1),(130,'2026-08-27','13:58:19',23,1,1),(131,'2026-08-27','14:10:24',23,2,1),(132,'2026-08-28','08:24:31',23,3,1),(133,'2026-08-28','10:27:06',23,2,1),(134,'2026-08-28','10:44:59',23,2,1),(135,'2026-08-29','07:40:26',23,2,1),(136,'2026-08-29','07:48:46',23,2,1),(137,'2026-08-29','07:56:31',23,2,1),(138,'2026-08-29','07:59:37',23,2,1),(139,'2026-08-29','09:50:38',23,2,1),(140,'2026-08-29','09:51:35',23,3,1),(141,'2026-08-29','09:53:59',23,1,1),(142,'2026-08-29','09:57:01',23,1,3),(143,'2026-08-29','09:59:32',23,2,1),(144,'2026-08-29','10:00:12',23,3,3),(145,'2026-08-29','10:01:36',23,17,1),(146,'2026-08-31','09:51:49',23,2,1),(147,'2026-08-31','15:20:56',23,1,1),(148,'2026-09-01','09:06:45',23,3,1),(149,'2026-09-01','09:09:45',23,2,1),(150,'2026-09-01','09:10:14',23,1,1),(151,'2026-09-01','09:11:22',23,2,1);
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
INSERT INTO `rol_permiso` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,11),(1,12),(1,13),(1,14),(1,15),(2,4),(2,6),(2,7),(2,11),(2,13),(2,14),(3,4),(3,9),(3,10),(3,13),(4,4),(4,8),(4,10),(4,13),(5,4),(5,8),(5,10),(5,13),(6,4),(6,10),(6,13);
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
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubro`
--

LOCK TABLES `rubro` WRITE;
/*!40000 ALTER TABLE `rubro` DISABLE KEYS */;
INSERT INTO `rubro` VALUES (256,'Cajas inspeccion'),(257,'Sumideros vias'),(258,'Terminacion para entrega'),(259,'Cosina'),(260,'instalacion medidor'),(261,'Instalacion regillas'),(262,'Replanteo'),(263,'Viga de cimentacion'),(264,'Cajas elctricas'),(265,'Traspaso'),(266,'Pasantes via'),(267,'Areglos'),(268,'Topografia'),(269,'Solados'),(270,'Red alcantarillado'),(271,'Red acueducto'),(272,'Domisiliarias sanitarias'),(273,'Hidraulica primer piso'),(274,'Patio'),(275,'Aguas lluvias'),(276,'Sanitaria primer piso'),(277,'Sanitaria segundo piso'),(278,'Vias'),(279,'Andenes'),(280,'Arreglo campamanto'),(281,'Dovelas'),(282,'Escaleras'),(283,'Formaleta'),(284,'Corte de via'),(285,'Pozos'),(286,'Columnetas'),(287,'Cimentacion casa'),(288,'Muro cambio de nivel'),(289,'Provisional electrica'),(290,'Parqueadero');
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
) ENGINE=InnoDB AUTO_INCREMENT=6931 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubicacion`
--

LOCK TABLES `ubicacion` WRITE;
/*!40000 ALTER TABLE `ubicacion` DISABLE KEYS */;
INSERT INTO `ubicacion` VALUES (6887,1,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'1|0|traspaso'),(6888,2,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'2|0|traspaso'),(6889,3,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'3|0|traspaso'),(6890,62,NULL,'Traspaso',NULL,NULL,0,'TRASPASO',1,'62|0|traspaso'),(6894,1,NULL,'ETAPA 6',NULL,NULL,0,'ETAPA',1,'1|0|etapa 6'),(6895,3,NULL,'ETAPA 4',NULL,NULL,0,'ETAPA',1,'3|0|etapa 4'),(6896,3,NULL,'ETAPA 5',NULL,NULL,0,'ETAPA',1,'3|0|etapa 5'),(6897,1,6894,'Manzana M10',NULL,NULL,1,'MANZANA',1,'1|6894|manzana m10'),(6898,3,6895,'K',NULL,NULL,1,'MANZANA',1,'3|6895|k'),(6899,3,6896,'K',NULL,NULL,1,'MANZANA',1,'3|6896|k'),(6900,1,6897,'casa 1',NULL,NULL,2,'CASA',1,'1|6897|casa 1'),(6901,1,6897,'casa 2',NULL,NULL,2,'CASA',1,'1|6897|casa 2'),(6902,1,6897,'casa 3',NULL,NULL,2,'CASA',1,'1|6897|casa 3'),(6903,1,6897,'casa 4',NULL,NULL,2,'CASA',1,'1|6897|casa 4'),(6904,1,6897,'casa 5',NULL,NULL,2,'CASA',1,'1|6897|casa 5'),(6905,1,6897,'Casa 6',NULL,NULL,2,'CASA',1,'1|6897|casa 6'),(6906,1,6897,'Casa 7',NULL,NULL,2,'CASA',1,'1|6897|casa 7'),(6907,1,6897,'Casa 8',NULL,NULL,2,'CASA',1,'1|6897|casa 8'),(6908,3,6898,'casa 4',NULL,NULL,2,'CASA',1,'3|6898|casa 4'),(6909,3,6899,'casa 3',NULL,NULL,2,'CASA',1,'3|6899|casa 3'),(6910,3,6899,'casa 4',NULL,NULL,2,'CASA',1,'3|6899|casa 4'),(6911,3,6899,'casa 5',NULL,NULL,2,'CASA',1,'3|6899|casa 5'),(6912,3,6899,'Casa 6',NULL,NULL,2,'CASA',1,'3|6899|casa 6'),(6913,3,6899,'Casa 7',NULL,NULL,2,'CASA',1,'3|6899|casa 7'),(6914,3,6899,'Casa 22',NULL,NULL,2,'CASA',1,'3|6899|casa 22'),(6915,3,6899,'Casa 23',NULL,NULL,2,'CASA',1,'3|6899|casa 23'),(6916,3,6899,'Casa 24',NULL,NULL,2,'CASA',1,'3|6899|casa 24'),(6917,3,6899,'Casa 25',NULL,NULL,2,'CASA',1,'3|6899|casa 25'),(6918,3,6899,'Casa 26',NULL,NULL,2,'CASA',1,'3|6899|casa 26'),(6919,3,6899,'Casa 27',NULL,NULL,2,'CASA',1,'3|6899|casa 27');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,1004035010,'Juan Diego','Lopez ','$2y$10$TOQQ.Y0fPY8xNmHXmw.DmuOB3DKyQRzovRj2hIOtyc.Lv4qDND9qS',1),(23,83235047,'Reinaldo','Gordo Losada','$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby',2),(24,1070605738,'Caterine Fernanda','Jovel Rincon','$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa',2);
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

-- Dump completed on 2026-09-01 11:23:03
