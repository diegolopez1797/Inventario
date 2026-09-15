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
INSERT INTO `ajuste_inventario` VALUES (23,'2026-08-26','19:35:30',1,1,'PERDIDA',6,5,-1,41996.70,'Se mojo',NULL),(24,'2026-08-26','19:36:19',1,1,'PERDIDA',5,4,-1,41996.70,'otro',NULL),(25,'2026-08-27','10:57:51',1,1,'CONTEO',1,2,1,NULL,'hgefhefh',NULL),(26,'2026-08-27','17:14:36',1,1,'DANO',199,198,-1,44985.06,'se mojo',NULL),(27,'2026-08-28','16:53:53',1,1,'PERDIDA',295,245,-50,45057.92,'se mojo',NULL);
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
INSERT INTO `auditoria` VALUES (37,'RegistroEntradas',37,'CREAR',1,'2026-08-25 15:58:58',NULL,'{\"Fecha\":\"2026-08-25\",\"Hora\":\"15:58:58\",\"Materiales\":1}'),(38,'Ubicacion',17,'CREAR',1,'2026-08-25 16:29:39',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":\"5\",\"Nombre\":\"M20\",\"Tipo\":\"manzana\"}'),(39,'Ubicacion',17,'ELIMINAR',1,'2026-08-25 16:29:53','{\"ProyectoID\":1,\"PadreID\":5,\"Nombre\":\"M20\",\"Tipo\":\"manzana\"}',NULL),(40,'Ubicacion',18,'CREAR',1,'2026-08-25 16:30:06',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":\"5\",\"Nombre\":\"3\",\"Tipo\":\"casa\"}'),(41,'Ubicacion',19,'CREAR',1,'2026-08-25 16:30:33',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":null,\"Nombre\":\"2\",\"Tipo\":\"etapa\"}'),(42,'Ubicacion',19,'EDITAR',1,'2026-08-25 16:30:48','{\"Nombre\":\"2\",\"Tipo\":\"etapa\"}','{\"Nombre\":\"etapa 2\",\"Tipo\":\"etapa\"}'),(43,'RegistroEntradas',38,'CREAR',1,'2026-08-25 16:42:24',NULL,'{\"Fecha\":\"2026-08-25\",\"Hora\":\"16:42:24\",\"Materiales\":1}'),(75,'RolPermiso',1,'EDITAR',1,'2026-08-25 19:15:30','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}'),(99,'Solicitud',14,'CREAR',1,'2026-08-25 19:56:46',NULL,'{\"ProyectoID\":\"1\",\"UbicacionID\":null,\"Materiales\":1}'),(100,'Solicitud',14,'APROBAR',1,'2026-08-25 19:57:06','{\"Estado\":\"PENDIENTE\"}','{\"Estado\":\"APROBADA\"}'),(101,'RegistroSalidas',46,'CREAR',1,'2026-08-25 19:57:26',NULL,'{\"Fecha\":\"2026-08-25\",\"Hora\":\"19:57:26\",\"ContratistaID\":\"1\",\"ProyectoID\":1,\"Materiales\":1}'),(102,'Solicitud',14,'ENTREGAR',1,'2026-08-25 19:57:26','{\"Estado\":\"APROBADA\"}','{\"Estado\":\"ENTREGADA\",\"RegistroSalidasID\":\"46\"}'),(103,'Solicitud',15,'CREAR',1,'2026-08-25 20:05:52',NULL,'{\"ProyectoID\":\"2\",\"UbicacionID\":null,\"Materiales\":1}'),(107,'Usuario',1,'EDITAR',1,'2026-08-25 20:57:13','{\"Identificacion\":1004035010,\"Nombre\":\"Juan\",\"Apellido\":\"Lopez \",\"RolID\":1}','{\"Identificacion\":\"1004035010\",\"Nombre\":\"Juan\",\"Apellido\":\"Lopez \",\"RolID\":\"1\"}'),(108,'Usuario',1,'EDITAR',1,'2026-08-25 20:58:30','{\"Identificacion\":1004035010,\"Nombre\":\"Juan\",\"Apellido\":\"Lopez \",\"RolID\":1}','{\"Identificacion\":\"1004035010\",\"Nombre\":\"Juan Diego\",\"Apellido\":\"Lopez \",\"RolID\":\"1\"}'),(109,'Solicitud',15,'RECHAZAR',1,'2026-08-25 21:12:14','{\"Estado\":\"PENDIENTE\"}','{\"Estado\":\"RECHAZADA\"}'),(110,'Material',2,'EDITAR',1,'2026-08-25 21:12:39','{\"Codigo\":69,\"Descripcion\":\"tubo liso de 6 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":219,\"Min_Almacen\":0,\"Max_Casa\":0}','{\"Codigo\":\"69\",\"Descripcion\":\"tubo liso de 6 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":\"219\",\"Min_Almacen\":\"5\",\"Max_Casa\":\"0\"}'),(111,'Material',3,'EDITAR',1,'2026-08-25 21:12:45','{\"Codigo\":87,\"Descripcion\":\"union pvc de 2 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":81,\"Min_Almacen\":0,\"Max_Casa\":0}','{\"Codigo\":\"87\",\"Descripcion\":\"union pvc de 2 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":\"81\",\"Min_Almacen\":\"5\",\"Max_Casa\":\"0\"}'),(112,'Material',2,'EDITAR',1,'2026-08-25 21:12:58','{\"Codigo\":69,\"Descripcion\":\"tubo liso de 6 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":219,\"Min_Almacen\":5,\"Max_Casa\":0}','{\"Codigo\":\"69\",\"Descripcion\":\"tubo liso de 6 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":\"219\",\"Min_Almacen\":\"1000\",\"Max_Casa\":\"0\"}'),(113,'Material',3,'EDITAR',1,'2026-08-25 21:13:13','{\"Codigo\":87,\"Descripcion\":\"union pvc de 2 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":81,\"Min_Almacen\":5,\"Max_Casa\":0}','{\"Codigo\":\"87\",\"Descripcion\":\"union pvc de 2 sanitario\",\"Unidad\":\"Unidad\",\"Saldo\":\"81\",\"Min_Almacen\":\"500\",\"Max_Casa\":\"0\"}'),(185,'RegistroSalidas',58,'CREAR',1,'2026-08-26 18:57:14',NULL,'{\"Fecha\":\"2026-08-26\",\"Hora\":\"18:57:14\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":18}]}'),(186,'RegistroSalidas',59,'CREAR',1,'2026-08-26 19:34:25',NULL,'{\"Fecha\":\"2026-08-26\",\"Hora\":\"19:34:25\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":2,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6},{\"MaterialID\":22,\"Cantidad\":\"1\",\"UbicacionID\":6}]}'),(187,'AjusteInventario',23,'CREAR',1,'2026-08-26 19:35:30',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"PERDIDA\",\"CantidadAnterior\":6,\"CantidadNueva\":5,\"CantidadAjuste\":-1,\"Motivo\":\"Se mojo\",\"CostoUnitario\":\"41996.70\"}'),(188,'AjusteInventario',24,'CREAR',1,'2026-08-26 19:36:19',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"PERDIDA\",\"CantidadAnterior\":5,\"CantidadNueva\":4,\"CantidadAjuste\":-1,\"Motivo\":\"otro\",\"CostoUnitario\":\"41996.70\"}'),(195,'RegistroSalidas',62,'CREAR',1,'2026-08-27 09:35:16',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"09:35:16\",\"ContratistaID\":1,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(197,'RegistroSalidas',64,'CREAR',1,'2026-08-27 10:55:24',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"10:55:24\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":18}]}'),(198,'RegistroSalidas',65,'CREAR',1,'2026-08-27 10:57:03',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"10:57:03\",\"ContratistaID\":1,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(199,'AjusteInventario',25,'CREAR',1,'2026-08-27 10:57:51',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"CONTEO\",\"CantidadAnterior\":1,\"CantidadNueva\":2,\"CantidadAjuste\":1,\"Motivo\":\"hgefhefh\",\"CostoUnitario\":null}'),(200,'Ubicacion',6615,'CREAR',1,'2026-08-27 11:05:37',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":null,\"Nombre\":\"urbanismo\",\"Tipo\":\"urbanismo\"}'),(201,'Ubicacion',6617,'CREAR',1,'2026-08-27 11:10:09',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":\"4577\",\"Nombre\":\"Piso 1\",\"Tipo\":\"Piso\"}'),(202,'Ubicacion',6618,'CREAR',1,'2026-08-27 11:10:29',NULL,'{\"ProyectoID\":\"1\",\"PadreID\":\"6617\",\"Nombre\":\"101\",\"Tipo\":\"Apartamento\"}'),(203,'TipoUbicacion',13,'EDITAR',1,'2026-08-27 11:30:30','{\"Descripcion\":\"Etapa\"}','{\"Descripcion\":\"Etapaaa\"}'),(204,'TipoUbicacion',13,'EDITAR',1,'2026-08-27 11:35:34','{\"Descripcion\":\"Etapaaa\"}','{\"Descripcion\":\"Etapa\"}'),(205,'Ubicacion',4577,'DESACTIVAR',1,'2026-08-27 11:55:52','{\"Nombre\":\"Torre 1\",\"Activo\":1}','{\"Activo\":0}'),(206,'Ubicacion',4577,'EDITAR',1,'2026-08-27 11:56:10','{\"Nombre\":\"Torre 1\",\"Tipo\":\"Torre\",\"PadreID\":null}','{\"Nombre\":\"Torre 1\",\"Tipo\":\"Torre\",\"PadreID\":null}'),(207,'Ubicacion',6621,'GENERAR_MASIVO',1,'2026-08-27 11:57:47',NULL,'{\"ProyectoID\":1,\"PadreID\":null,\"ContenedorNuevo\":\"Torre 2\",\"Niveles\":[{\"tipo\":\"Piso\",\"cantidad\":25,\"patron\":\"Piso {N:02}\"},{\"tipo\":\"Apartamento\",\"cantidad\":6,\"patron\":\"Apto {N:02}\"}],\"TotalGenerado\":175}'),(208,'RegistroSalidas',66,'CREAR',1,'2026-08-27 11:59:27',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"11:59:27\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6648}]}'),(209,'RegistroEntradas',40,'CREAR',1,'2026-08-27 12:03:05',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:03:05\",\"Materiales\":1}'),(210,'RegistroEntradas',41,'CREAR',1,'2026-08-27 12:05:10',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:05:10\",\"Materiales\":2}'),(211,'RegistroEntradas',42,'CREAR',1,'2026-08-27 12:06:36',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:06:36\",\"Materiales\":2}'),(212,'RegistroEntradas',43,'CREAR',1,'2026-08-27 12:07:29',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:07:29\",\"Materiales\":2}'),(213,'RegistroSalidas',67,'CREAR',1,'2026-08-27 12:08:10',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:08:10\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"2\",\"UbicacionID\":6732}]}'),(214,'RegistroSalidas',68,'CREAR',1,'2026-08-27 12:10:06',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"12:10:06\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":2,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6647},{\"MaterialID\":1,\"Cantidad\":\"2\",\"UbicacionID\":6648}]}'),(215,'RolPermiso',1,'EDITAR',1,'2026-08-27 12:14:09','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"inventario.ajustar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}','{\"Permisos\":[\"usuario.gestionar\",\"rol.gestionar\",\"auditoria.ver\",\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"costo.ver\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.aprobar.excepcional\",\"dashboard.ver\",\"inventario.ajustar\"]}'),(216,'RegistroSalidas',69,'CREAR',1,'2026-08-27 16:32:04',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"16:32:04\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6773}]}'),(217,'RegistroSalidas',70,'CREAR',1,'2026-08-27 17:13:26',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"17:13:26\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":2,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6719},{\"MaterialID\":22,\"Cantidad\":\"1\",\"UbicacionID\":6}]}'),(218,'AjusteInventario',26,'CREAR',1,'2026-08-27 17:14:36',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"DANO\",\"CantidadAnterior\":199,\"CantidadNueva\":198,\"CantidadAjuste\":-1,\"Motivo\":\"se mojo\",\"CostoUnitario\":\"44985.06\"}'),(219,'RegistroSalidas',71,'CREAR',1,'2026-08-27 17:16:54',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"17:16:54\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(220,'RegistroSalidas',72,'CREAR',1,'2026-08-27 18:12:20',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"18:12:20\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6755}]}'),(221,'RegistroSalidas',73,'CREAR',1,'2026-08-27 18:19:57',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"18:19:57\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6749}]}'),(222,'RegistroSalidas',74,'CREAR',1,'2026-08-27 18:37:32',NULL,'{\"Fecha\":\"2026-08-27\",\"Hora\":\"18:37:32\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6761}]}'),(223,'Ubicacion',6797,'CREAR',1,'2026-08-27 22:04:22',NULL,'{\"ProyectoID\":\"57\",\"PadreID\":null,\"Nombre\":\"Piso 1\",\"Tipo\":\"Piso\"}'),(224,'Ubicacion',6798,'CREAR',1,'2026-08-27 22:04:43',NULL,'{\"ProyectoID\":\"57\",\"PadreID\":\"6797\",\"Nombre\":\"Local 101\",\"Tipo\":\"Local\"}'),(225,'RegistroEntradas',44,'CREAR',1,'2026-08-28 14:27:08',NULL,'{\"Fecha\":\"2026-08-28\",\"Hora\":\"14:27:08\",\"Materiales\":1}'),(227,'RegistroEntradas',46,'CREAR',1,'2026-08-28 16:50:44',NULL,'{\"Fecha\":\"2026-08-28\",\"Hora\":\"16:50:44\",\"Materiales\":1}'),(228,'AjusteInventario',27,'CREAR',1,'2026-08-28 16:53:53',NULL,'{\"MaterialID\":\"1\",\"Tipo\":\"PERDIDA\",\"CantidadAnterior\":295,\"CantidadNueva\":245,\"CantidadAjuste\":-50,\"Motivo\":\"se mojo\",\"CostoUnitario\":\"45057.92\"}'),(229,'RegistroSalidas',75,'CREAR',1,'2026-08-28 17:03:17',NULL,'{\"Fecha\":\"2026-08-28\",\"Hora\":\"17:03:17\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":2,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6749},{\"MaterialID\":22,\"Cantidad\":\"1\",\"UbicacionID\":6}]}'),(230,'RegistroSalidas',76,'CREAR',1,'2026-08-28 17:56:51',NULL,'{\"Fecha\":\"2026-08-28\",\"Hora\":\"17:56:51\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6743}]}'),(231,'RolPermiso',1,'EDITAR',1,'2026-08-28 19:25:47','{\"Permisos\":[\"usuario.gestionar\",\"rol.gestionar\",\"auditoria.ver\",\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"costo.ver\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.aprobar.excepcional\",\"dashboard.ver\",\"inventario.ajustar\"]}','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"inventario.ajustar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}'),(232,'Solicitud',17,'CREAR',1,'2026-08-28 19:27:31',NULL,'{\"ProyectoID\":\"1\",\"UbicacionID\":null,\"Materiales\":1}'),(233,'Solicitud',17,'APROBAR',1,'2026-08-28 19:28:00','{\"Estado\":\"PENDIENTE\"}','{\"Estado\":\"APROBADA\"}'),(234,'RolPermiso',2,'EDITAR',1,'2026-08-28 19:41:35','{\"Permisos\":[\"catalogo.ver\",\"entrada.registrar\",\"salida.registrar\",\"solicitud.ver\",\"solicitud.entregar\",\"dashboard.ver\",\"inventario.ajustar\"]}','{\"Permisos\":[\"catalogo.ver\",\"entrada.registrar\",\"salida.registrar\",\"solicitud.entregar\",\"dashboard.ver\",\"inventario.ajustar\"]}'),(235,'RolPermiso',1,'EDITAR',1,'2026-08-28 19:41:51','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"inventario.ajustar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}','{\"Permisos\":[\"usuario.gestionar\",\"rol.gestionar\",\"auditoria.ver\",\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"costo.ver\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.aprobar.excepcional\",\"dashboard.ver\",\"inventario.ajustar\"]}'),(239,'RolPermiso',1,'EDITAR',1,'2026-08-31 09:52:28','{\"Permisos\":[\"usuario.gestionar\",\"rol.gestionar\",\"auditoria.ver\",\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"costo.ver\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.aprobar.excepcional\",\"dashboard.ver\",\"inventario.ajustar\"]}','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"inventario.ajustar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}'),(240,'RegistroSalidas',77,'CREAR',1,'2026-08-31 10:01:43',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"10:01:43\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"2\",\"UbicacionID\":6611}]}'),(241,'Usuario',1,'EDITAR',1,'2026-08-31 10:02:57','{\"Identificacion\":1004035010,\"Nombre\":\"Juan Diego\",\"Apellido\":\"Lopez \",\"RolID\":1}','{\"Identificacion\":\"1004035010\",\"Nombre\":\"Juan Diego\",\"Apellido\":\"Lopez \",\"RolID\":\"1\"}'),(242,'RolPermiso',1,'EDITAR',1,'2026-08-31 10:42:58','{\"Permisos\":[\"auditoria.ver\",\"catalogo.gestionar\",\"catalogo.ver\",\"costo.ver\",\"dashboard.ver\",\"entrada.registrar\",\"inventario.ajustar\",\"rol.gestionar\",\"salida.registrar\",\"solicitud.aprobar.excepcional\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.ver\",\"usuario.gestionar\"]}','{\"Permisos\":[\"usuario.gestionar\",\"rol.gestionar\",\"auditoria.ver\",\"catalogo.ver\",\"catalogo.gestionar\",\"entrada.registrar\",\"salida.registrar\",\"costo.ver\",\"solicitud.crear\",\"solicitud.entregar\",\"solicitud.aprobar.excepcional\",\"dashboard.ver\",\"inventario.ajustar\"]}'),(247,'RegistroSalidas',78,'CREAR',1,'2026-08-31 16:16:52',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"16:16:52\",\"ContratistaID\":2,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"240\",\"UbicacionID\":6755}]}'),(248,'RegistroSalidas',79,'CREAR',1,'2026-08-31 16:22:26',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"16:22:26\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(249,'RegistroEntradas',47,'CREAR',1,'2026-08-31 16:35:27',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"16:35:27\",\"Materiales\":1}'),(250,'RegistroSalidas',80,'CREAR',1,'2026-08-31 16:36:03',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"16:36:03\",\"ContratistaID\":2,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6755}]}'),(251,'RegistroSalidas',81,'CREAR',1,'2026-08-31 16:45:58',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"16:45:58\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(252,'RegistroSalidas',82,'CREAR',1,'2026-08-31 17:38:59',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"17:38:59\",\"ContratistaID\":1,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6615}]}'),(253,'NotificacionDestinatario',2,'EDITAR',1,'2026-08-31 17:41:47','{\"Nombre\":\"Administración\",\"Correo\":\"diegolopez1797@gmail.com\"}','{\"Nombre\":\"Administración\",\"Correo\":\"samuelgutierrezlopez56@gmail.com\"}'),(254,'RegistroSalidas',83,'CREAR',1,'2026-08-31 17:42:10',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"17:42:10\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(255,'RegistroSalidas',84,'CREAR',1,'2026-08-31 18:45:07',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"18:45:07\",\"ContratistaID\":2,\"ProyectoID\":1,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6755}]}'),(256,'RegistroSalidas',85,'CREAR',1,'2026-08-31 18:47:33',NULL,'{\"Fecha\":\"2026-08-31\",\"Hora\":\"18:47:33\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(257,'RegistroSalidas',86,'CREAR',1,'2026-09-01 08:20:40',NULL,'{\"Fecha\":\"2026-09-01\",\"Hora\":\"08:20:40\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(258,'NotificacionDestinatario',2,'EDITAR',1,'2026-09-01 08:21:17','{\"Nombre\":\"Administración\",\"Correo\":\"samuelgutierrezlopez56@gmail.com\"}','{\"Nombre\":\"Administración\",\"Correo\":\"diegolopez1797@gmail.com\"}'),(259,'RegistroSalidas',87,'CREAR',1,'2026-09-01 08:21:38',NULL,'{\"Fecha\":\"2026-09-01\",\"Hora\":\"08:21:38\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}'),(260,'RegistroSalidas',88,'CREAR',1,'2026-09-01 08:21:57',NULL,'{\"Fecha\":\"2026-09-01\",\"Hora\":\"08:21:57\",\"ContratistaID\":2,\"ProyectoID\":2,\"Materiales\":1,\"Lineas\":[{\"MaterialID\":1,\"Cantidad\":\"1\",\"UbicacionID\":6611}]}');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratista`
--

LOCK TABLES `contratista` WRITE;
/*!40000 ALTER TABLE `contratista` DISABLE KEYS */;
INSERT INTO `contratista` VALUES (1,'Construcciones hermanos murcia'),(2,'Smartools'),(3,'Construcciones narvaez camacho');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destino`
--

LOCK TABLES `destino` WRITE;
/*!40000 ALTER TABLE `destino` DISABLE KEYS */;
INSERT INTO `destino` VALUES (1,'Urbanismo'),(2,'Cimentación'),(3,'Industrializado'),(4,'Consumibles'),(5,'Acabados');
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
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,1022,'cemento','Bulto',1,10,15,48000.00),(2,69,'tubo liso de 6 sanitario','Unidad',219,1000,0,NULL),(3,87,'union pvc de 2 sanitario','Unidad',81,500,0,NULL),(4,89,'tee pvc de 2 sanitario','Unidad',148,0,0,NULL),(5,90,'union pvc de 1 1/2 sanitario','Unidad',721,0,0,NULL),(6,92,'semicodo pvc de 2 sanitario','Unidad',490,0,0,NULL),(7,102,'varilla corrugada de 1/2 x 6 mts','Unidad',1675,0,0,NULL),(8,108,'malla electrosoldada # 6.5mm','Unidad',384,0,0,NULL),(9,191,'tee sanitaria de 4','Unidad',554,0,0,NULL),(10,192,'adaptador limpieza de 4','Unidad',216,0,0,NULL),(11,196,'buje de 2 x 1 1/2 presion','Unidad',4,0,0,NULL),(12,209,'espatula metalica de 3','Unidad',11,0,0,NULL),(13,236,'tee presion de 4','Unidad',10,0,0,NULL),(14,267,'eucobar x 190 kilos x 55 galones','Caneca',1,0,0,NULL),(15,268,'buje sanitario de 4x3','Unidad',109,0,0,NULL),(16,270,'union pvc de 3 presion','Unidad',27,0,0,NULL),(17,295,'pintura vinilo blanco','Cuñete',4,0,0,NULL),(18,297,'union sanitaria de 6','Unidad',50,0,0,NULL),(19,301,'cemento blanco x 40 kilos','Bulto',7,0,0,NULL),(20,321,'medio polin x 3 mts','Unidad',100,0,0,NULL),(21,324,'tapa prueba de 2','Unidad',110,0,0,NULL),(22,1,'Cemento gris uso general * 50kg','bulto',14,0,0,40125.00),(23,2,'Cemento blanco * 40 kg','bulto',0,0,0,NULL),(24,3,'Yeso extra * 25 kg','bulto',0,0,0,NULL),(25,4,'Sardinel en cemento de 80 cms','UND',0,0,0,NULL),(26,5,'Malla electrosoldada 4mm','UND',0,0,0,NULL),(27,6,'Malla electrosoldada 4 mm en rollo * 18 mts','UND',0,0,0,NULL),(28,7,'Malla electrosoldada 5 mm','UND',0,0,0,NULL),(29,8,'Malla electrosoldada 6 mm','UND',0,0,0,NULL),(30,9,'Malla electrosoldada 6.5 mm','UND',0,0,0,NULL),(31,10,'Malla electrosoldada 8 mm','UND',0,0,0,NULL),(32,11,'Alambre negro liso','kg',0,0,0,NULL),(33,12,'varilla corrugada de 1/4\" * 6 mt','UND',0,0,0,NULL),(34,13,'varilla corrugada de 1/4\" en chipa','kg',0,0,0,NULL),(35,14,'varilla corrugada de 3/8\" x 6mt','UND',0,0,0,NULL),(36,15,'varilla corrugada de 3/8\" x 12mt','UND',0,0,0,NULL),(37,16,'varilla corrugada de 1/2\" x 6mt','UND',0,0,0,NULL),(38,17,'varilla corrugada de 1/2\" x 12mt','UND',0,0,0,NULL),(39,20,'varilla corrugada de 5/8\" x 6mt lisa','UND',0,0,0,NULL),(40,23,'Fleje de 1/4\" de 13cm*18cm','UND',0,0,0,NULL),(41,24,'Gancho tipo C de 3/8\" de 10*5*5','UND',0,0,0,NULL),(42,25,'Fleje de 3/8\" de 13cm*16cm','UND',0,0,0,NULL),(43,26,'Gancho tipo C de 1/4\" de 10*8*8','UND',0,0,0,NULL),(44,27,'Fleje de 1/4\" de 15cm*20cm','UND',0,0,0,NULL),(45,28,'Fleje de 1/4\" de 15cm*15cm','UND',0,0,0,NULL),(46,29,'Gancho tipo C de 1/4\" de 21*8*8 longitud= 37 cm','UND',0,0,0,NULL),(47,30,'Silleta plástica SU25 mm','UND',0,0,0,NULL),(48,31,'Silleta plástica CP30 mm','UND',0,0,0,NULL),(49,32,'Silleta plástica CP50 mm','UND',0,0,0,NULL),(50,33,'Silleta plástica CP65 mm','UND',0,0,0,NULL),(51,34,'Disco separador MC100 mm','UND',0,0,0,NULL),(52,35,'Disco separador MC120 mm','UND',0,0,0,NULL),(53,36,'Tuberia conduit de 1/2\" * 3mt','UND',0,0,0,NULL),(54,37,'Medio polín * 3mt','UND',0,0,0,NULL),(55,42,'Estaca en madera de 25cm','UND',0,0,0,NULL),(56,43,'Estaca en madera de 50cm','UND',0,0,0,NULL),(57,44,'Estaca en madera de 75cm','UND',0,0,0,NULL),(58,45,'Estaca en madera de 1mt','UND',0,0,0,NULL),(59,46,'Guadua * 6mt','UND',0,0,0,NULL),(60,48,'puntilla para madera de 2\"','LB',0,0,0,NULL),(61,49,'puntilla para madera de 2 1/2\"','LB',0,0,0,NULL),(62,50,'puntilla para madera de 3\"','LB',0,0,0,NULL),(63,51,'puntilla en acero de 2 1/2\"','LB',0,0,0,NULL),(64,52,'Broca SDS plus de 3/8\" * 6\"','UND',0,0,0,NULL),(65,53,'Broca SDS plus de 1/2\" * 6\"','UND',0,0,0,NULL),(66,54,'Broca SDS plus de 1/2\" * 12\"','UND',0,0,0,NULL),(67,55,'Chazo plastico de 3/8\"','UND',0,0,0,NULL),(68,56,'Disco diamantado continuo de 4 1/2\"','UND',0,0,0,NULL),(69,57,'Disco diamantado segmentado de 9\"','UND',0,0,0,NULL),(70,58,'Disco de copa diamantada doble de 7\" para pulir','UND',0,0,0,NULL),(71,59,'Disco de corte de hierro de 4 1/2\"','UND',0,0,0,NULL),(72,60,'Disco de corte de hierro de 7\"','UND',0,0,0,NULL),(73,62,'Disco de corte de hierro de 14\" - tronzadora','UND',0,0,0,NULL),(74,63,'Brocha de 2 1/2\"','UND',0,0,0,NULL),(75,64,'Brocha de 3\"','UND',0,0,0,NULL),(76,65,'Brocha de 4\"','UND',0,0,0,NULL),(77,66,'Rodillo de Felpa de 9\"','UND',0,0,0,NULL),(78,67,'Lija #150','UND',0,0,0,NULL),(79,68,'Espátula metálica de 3\" mango madera/plastico','UND',0,0,0,NULL),(80,69,'Espátula metálica de 4\" mango madera/plastico','UND',0,0,0,NULL),(81,70,'Espátula metálica de 5\" mango madera/plastico','UND',0,0,0,NULL),(82,72,'Pintura vinilo gris Tipo 1','cuñete',0,0,0,NULL),(83,73,'Pintura vinilo Blanco Tipo 2','cuñete',0,0,0,NULL),(84,80,'Aerosol color rojo','UND',0,0,0,NULL),(85,81,'Aerosol color negro','UND',0,0,0,NULL),(86,83,'Sellante poliuretano topex gris * 300ml','UND',0,0,0,NULL),(87,86,'Illbruck SP523 * blanco 300ml','UND',0,0,0,NULL),(88,92,'Aquacero - impermeabilizante acrilico','cuñete',0,0,0,NULL),(89,94,'Cinta de enmascarar de 1\"','UND',0,0,0,NULL),(90,95,'Mineral bayer Rojo','caja',0,0,0,NULL),(91,97,'Curaseal pf blanco - curador de concreto * 200kg (tambor)','Tambor * 55gls',0,0,0,NULL),(92,100,'Vulken 45 SSl * 5gl','cuñete',0,0,0,NULL),(93,101,'Verticoat No. 2 *30kg','bulto',0,0,0,NULL),(94,104,'Sikadur - 32 primer * 1 KG','UND',0,0,0,NULL),(95,106,'Cal Hidratada * 10KG','bulto',0,0,0,NULL),(96,107,'Plastico negro calibre 6 * 4mts ancho','Rollo',0,0,0,NULL),(97,108,'Lona verde para cerramiento * 2mts alto','Rollo',0,0,0,NULL),(98,110,'Cinta peligro * 500 mts','Rollo',0,0,0,NULL),(99,113,'Cepillo de alambre - para limpieza','UND',0,0,0,NULL),(100,118,'Pala Draga (Hoyadora)','UND',0,0,0,NULL),(101,119,'Pica','UND',0,0,0,NULL),(102,120,'Palín cuadrado','UND',0,0,0,NULL),(103,121,'Barretón','UND',0,0,0,NULL),(104,122,'Cepillo carretero - Escobillón','UND',0,0,0,NULL),(105,123,'Carretilla','UND',0,0,0,NULL),(106,124,'Rastrillo metálico','UND',0,0,0,NULL),(107,126,'Combo sanitario linea institucional','UND',0,0,0,NULL),(108,127,'Lavamanos blanco linea institucional (sin pedestal)','UND',0,0,0,NULL),(109,128,'Enchape cerámico eco plus blanco 20*20 * 2m2','caja',0,0,0,NULL),(110,129,'Enchape cerámico piso pared natal blanco 25*35','caja',0,0,0,NULL),(111,130,'Enchape cerámico blanco 30*30','caja',0,0,0,NULL),(112,131,'Enchape cerámico Stone café 45*45','caja',0,0,0,NULL),(113,132,'Enchape cerámico Slate White EP 51*51','caja',0,0,0,NULL),(114,133,'Pegante cerámico * 25 kg','bulto',0,0,0,NULL),(115,134,'Boquilla blanca * 2 kg','UND',0,0,0,NULL),(116,139,'Hojas de Segueta','UND',0,0,0,NULL),(117,140,'Cinta teflón industrial','UND',0,0,0,NULL),(118,141,'Tuberia pvc presiòn de 1/2\" * 6 mts - RDE 9','UND',0,0,0,NULL),(119,142,'Tuberia pvc presiòn de 3/4\" * 6 mts - RDE 11','UND',0,0,0,NULL),(120,143,'Tuberia pvc presiòn de 1\" * 6 mts - RDE 13.5','UND',0,0,0,NULL),(121,144,'Codo pvc presión 1/2\"','UND',0,0,0,NULL),(122,145,'Codo pvc presión 3/4\"','UND',0,0,0,NULL),(123,146,'Codo pvc presión 1\"','UND',0,0,0,NULL),(124,147,'Unión pvc presión 1/2\"','UND',0,0,0,NULL),(125,148,'Unión pvc presión 3/4\"','UND',0,0,0,NULL),(126,149,'Unión pvc presión 1\"','UND',0,0,0,NULL),(127,150,'Unión universal pvc presión 1/2\"','UND',0,0,0,NULL),(128,151,'Unión universal pvc presión 1\"','UND',0,0,0,NULL),(129,152,'Tee pvc presión 1/2\"','UND',0,0,0,NULL),(130,153,'Tee pvc presión 3/4\"','UND',0,0,0,NULL),(131,154,'Tee pvc presión 1\"','UND',0,0,0,NULL),(132,155,'Tapón pvc presión liso de 1/2\"','UND',0,0,0,NULL),(133,156,'Tapón pvc presión roscado de 1/2\"','UND',0,0,0,NULL),(134,157,'Tapón pvc presión liso de 1\"','UND',0,0,0,NULL),(135,158,'Adaptador pvc presión hembra de 1/2\"','UND',0,0,0,NULL),(136,159,'Adaptador pvc presión macho de 1/2\"','UND',0,0,0,NULL),(137,160,'Adaptador pvc presión macho de 3/4\"','UND',0,0,0,NULL),(138,161,'Adaptador pvc presión hembra de 1\"','UND',0,0,0,NULL),(139,162,'Adaptador pvc presión macho de 1\"','UND',0,0,0,NULL),(140,163,'Semicodo pvc presión 1/2\"','UND',0,0,0,NULL),(141,164,'reducción pvc presión 3/4\" x 1/2\" (buje)','UND',0,0,0,NULL),(142,165,'reducción pvc presión 1\" x 1/2\" (buje)','UND',0,0,0,NULL),(143,166,'reducción pvc presión 1\" x 3/4\" (buje)','UND',0,0,0,NULL),(144,167,'Cheque horizontal de 1/2\" - grival','UND',0,0,0,NULL),(145,168,'Cheque horizontal de 1\" - grival','UND',0,0,0,NULL),(146,169,'Llave de paso pvc presión lisa de 1/2\" - plástica','UND',0,0,0,NULL),(147,170,'Llave de paso pvc presión lisa de 3/4\" - plastica','UND',0,0,0,NULL),(148,171,'Llave de paso pvc presión lisa de 1\" - plástica','UND',0,0,0,NULL),(149,172,'Llave terminal de 1/2\"','UND',0,0,0,NULL),(150,173,'Codo galvanizado de 1/2\"','UND',0,0,0,NULL),(151,174,'Micromedidor de Agua potable de 1/2\"','UND',0,0,0,NULL),(152,175,'Registro ducha completo','UND',0,0,0,NULL),(153,176,'Lavadero prefabricado en concreto 0.50*0.50*0.80','UND',0,0,0,NULL),(154,178,'soldadura pvc Gerfor verde * 1/4 gl','UND',0,0,0,NULL),(155,179,'Limpiador pvc Gerfor * 1/4gl','UND',0,0,0,NULL),(156,180,'Tanque Almacenamiento * 500 lts (polinter)','UND',0,0,0,NULL),(157,181,'Flotador llenado tanque almacenamiento','UND',0,0,0,NULL),(158,182,'Acople para sanitario','UND',0,0,0,NULL),(159,183,'Acople para Lavamanos','UND',0,0,0,NULL),(160,184,'Acople para Lavaplatos','UND',0,0,0,NULL),(161,185,'Arbol de entrada (llenado) tanque sanitario - Repuesto','UND',0,0,0,NULL),(162,186,'Arbol de salida - tanque sanitario - Repuesto','UND',0,0,0,NULL),(163,187,'Canastilla para lavaplatos de 4\"*3\"','UND',0,0,0,NULL),(164,188,'Sifón ajustable tipo acordeón - lavaplatos','UND',0,0,0,NULL),(165,189,'Biscocho / cajilla en cemento para medidor','UND',0,0,0,NULL),(166,190,'Tapa empo metálica - para medidor','UND',0,0,0,NULL),(167,191,'Tubo sanitario de 1 1/2\" * 6 mt','UND',0,0,0,NULL),(168,192,'Tubo sanitario de 3\" * 6 mt','UND',0,0,0,NULL),(169,193,'Tubo sanitario de 4\" * 6 mt','UND',0,0,0,NULL),(170,194,'Tubo sanitario de 4\" * 6 mt - Reventilación (naranja)','UND',0,0,0,NULL),(171,195,'Tubo sanitario de 6\" * 6 mt','UND',0,0,0,NULL),(172,196,'Unión pvc sanitaria de 1 1/2\"','UND',0,0,0,NULL),(173,197,'Unión pvc sanitaria de 2\"','UND',0,0,0,NULL),(174,198,'Unión pvc sanitaria de 3\"','UND',0,0,0,NULL),(175,199,'Unión pvc sanitaria de 4\"','UND',0,0,0,NULL),(176,200,'Unión pvc sanitaria de 6\"','UND',0,0,0,NULL),(177,201,'Unión pvc sanitaria de 8\"','UND',0,0,0,NULL),(178,202,'Codo pvc sanitario de 1 1/2\"','UND',0,0,0,NULL),(179,203,'Codo pvc sanitario de 2\"','UND',0,0,0,NULL),(180,204,'Codo pvc sanitario de 3','UND',0,0,0,NULL),(181,205,'Codo pvc sanitario de 4\"','UND',0,0,0,NULL),(182,206,'Semicodo pvc sanitario de 1\" 1/2\"','UND',0,0,0,NULL),(183,207,'Semicodo pvc sanitario de 2\"','UND',0,0,0,NULL),(184,208,'Semicodo pvc sanitario de 3','UND',0,0,0,NULL),(185,209,'Semicodo pvc sanitario de 4\"','UND',0,0,0,NULL),(186,210,'Sifón pvc sanitario 180° C*C 1 1/2\"','UND',0,0,0,NULL),(187,211,'Sifón pvc sanitario 135° C*E 3\"','UND',0,0,0,NULL),(188,212,'Sifón pvc sanitario 135° C*E 4\"','UND',0,0,0,NULL),(189,213,'Tapa prueba sanitaria de 2\"','UND',0,0,0,NULL),(190,214,'Tapa prueba sanitaria de 3\"','UND',0,0,0,NULL),(191,215,'Tapa prueba sanitaria de 4\"','UND',0,0,0,NULL),(192,216,'Tapa prueba sanitaria de 6\"','UND',0,0,0,NULL),(193,217,'Adaptador de limpieza sanitario de 4','UND',0,0,0,NULL),(194,218,'Rejilla plástica de 2\"','UND',0,0,0,NULL),(195,219,'Rejilla plástica con sosco de 4\" * 3\"','UND',0,0,0,NULL),(196,220,'Rejilla plástica con sosco de 5\" * 4\"','UND',0,0,0,NULL),(197,221,'Buje pvc sanitario de 3\" * 1 1/2\"','UND',0,0,0,NULL),(198,222,'Buje pvc sanitario de 4\" * 2\"','UND',0,0,0,NULL),(199,223,'Buje pvc sanitario de 4\" * 3\"','UND',0,0,0,NULL),(200,224,'Tee pvc sanitaria de 1 1/2\"','UND',0,0,0,NULL),(201,225,'Tee pvc sanitaria de 4\"','UND',0,0,0,NULL),(202,226,'Tee pvc sanitaria de 6\"','UND',0,0,0,NULL),(203,227,'Tee pvc sanitaria reducida de 4\" * 3\"','UND',0,0,0,NULL),(204,228,'Tee pvc sanitaria reducida de 6\" * 4\"','UND',0,0,0,NULL),(205,229,'Yee pvc sanitaria de 4\"','UND',0,0,0,NULL),(206,230,'Yee pvc sanitaria reducida de 4\" * 2\"','UND',0,0,0,NULL),(207,231,'Yee pvc sanitaria reducida de 4\" * 3\"','UND',0,0,0,NULL),(208,232,'Yee pvc sanitaria reducida de 6\" * 4\"','UND',0,0,0,NULL),(209,233,'Teja de zinc * 3mts','UND',0,0,0,NULL),(210,234,'Amarre para teja de zinc','UND',0,0,0,NULL),(211,235,'Tuberia rectangular Cr 3\" * 1 1/2\" * 6 mts calibre 1.1mm (calibre 18)','UND',0,0,0,NULL),(212,243,'Caballete de 2 mts largo X 0,60 mt ancho','UND',0,0,0,NULL),(213,244,'Soldadura Electrica 6013 * 3/32','kg',0,0,0,NULL),(214,245,'Tornillo fijador de ala','UND',0,0,0,NULL),(215,254,'Emulsión asfaltica ED-9','cuñete',0,0,0,NULL),(216,255,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2.5mm (con soplete) * 10m2','Rollo',0,0,0,NULL),(217,256,'Manto asfaltico impermeabilizante con foil de aluminio calibre 2mm (aplicación en frio)','Rollo',0,0,0,NULL),(218,257,'Gárgola prefabricada en cemento','UND',0,0,0,NULL),(219,258,'Flanche en lámina galvanizada de 2.0mt * 25cm desarrollo - calibre 26','UND',0,0,0,NULL),(220,259,'Flanche en lámina galvanizada de 2.40mt * 25cm desarrollo - calibre 26','UND',0,0,0,NULL),(221,260,'Cinta Asfáltica tapa goteras de 10cms * 10mts','UND',0,0,0,NULL),(222,261,'Cinta Asfáltica tapa goteras de 15cms * 10mts','UND',0,0,0,NULL),(223,262,'Cinta Asfáltica tapa goteras de 20cms * 10mts','UND',0,0,0,NULL),(224,263,'Juego de parrillas y quemadores para estufa','UND',0,0,0,NULL),(225,264,'Griferia cuello de ganso - para cocina','UND',0,0,0,NULL),(226,265,'Manijas para mueble de cocina','UND',0,0,0,NULL),(227,266,'Chapa de poma metálica y redonda - para alcoba','UND',0,0,0,NULL),(228,267,'Chapa de poma metálica y redonda - para baño','UND',0,0,0,NULL),(229,268,'Lubricante Gerfor * Tarro 500ml','UND',0,0,0,NULL),(230,269,'Silla fix adhesivo sellante * tarro 310ml','UND',0,0,0,NULL),(231,270,'Geotextil No tejido NT 1600 - rollo de 3.5mt*160ml','Rollo',0,0,0,NULL),(232,271,'Rejilla metálica para sumidero de 0.50mt * 0.80mt marco y contramarco','UND',0,0,0,NULL),(233,273,'Tubería PVC para alcantarillado Novafort 6\" S-4 x 6mt','UND',0,0,0,NULL),(234,274,'Tubería PVC para alcantarillado Novafort 8\" S-8 x 6mt','UND',0,0,0,NULL),(235,275,'Tubería PVC para alcantarillado Novafort 10\" S-8 x 6mt','UND',0,0,0,NULL),(236,276,'Tubería PVC para alcantarillado Novafort 12\" S-8 x 6mt','UND',0,0,0,NULL),(237,277,'Tubería PVC para alcantarillado Novafort 14\" S-8 x 6mt','UND',0,0,0,NULL),(238,279,'Tubería en cemento para alcantarillado diametro 18\" * 1mt','UND',0,0,0,NULL),(239,281,'Tubería en cemento para alcantarillado diámetro 36\" * 1mt','UND',0,0,0,NULL),(240,282,'Tapa antirrobo para pozo de inspección','UND',0,0,0,NULL),(241,283,'Silla Tee pvc sanitario de 6\" * 4\"','UND',0,0,0,NULL),(242,284,'Silla Yee pvc sanitario 8\" * 6\"','UND',0,0,0,NULL),(243,285,'Silla Yee pvc sanitario 10\" * 6\"','UND',0,0,0,NULL),(244,286,'Silla Yee pvc sanitario 12\" * 6\" (315*160)','UND',0,0,0,NULL),(245,287,'Silla Yee pvc sanitario 16\" * 6\" (400*160)','UND',0,0,0,NULL),(246,288,'Sifón pvc sanitario 180° C*C 2\"','UND',0,0,0,NULL),(247,289,'Codo pvc presión 1 1/2\"','UND',0,0,0,NULL),(248,290,'Semicodo pvc presión 1 1/2\"','UND',0,0,0,NULL),(249,291,'Tapón pvc presión liso de 1 1/2\"','UND',0,0,0,NULL),(250,293,'Tapón pvc presión roscado de 1 1/2\"','UND',0,0,0,NULL),(251,294,'Adaptador pvc presión hembra de 2\"','UND',0,0,0,NULL),(252,297,'Tubería campana pvc presión de 3\" * 6 mts - RDE 21','UND',0,0,0,NULL),(253,298,'Tubería campana pvc presión de 4\" * 6 mts - RDE 21','UND',0,0,0,NULL),(254,299,'Tubería campana pvc presión de 6\" * 6 mts - RDE 21','UND',0,0,0,NULL),(255,300,'Tubería campana pvc presión de 8\" * 6 mts - RDE 21','UND',0,0,0,NULL),(256,301,'Collarín pvc presión de 3\" x 1/2\"','UND',0,0,0,NULL),(257,303,'Collarín pvc presión de 4\" x 1/2\"','UND',0,0,0,NULL),(258,305,'Codo pvc presión 90° 4\"','UND',0,0,0,NULL),(259,306,'Codo pvc presión 90° 6\"','UND',0,0,0,NULL),(260,307,'Codo pvc presión gran radio 45° 6','UND',0,0,0,NULL),(261,308,'Semicodo pvc presión 45° 3\"','UND',0,0,0,NULL),(262,310,'Tee pvc presión 90° 3\"','UND',0,0,0,NULL),(263,313,'Tee pvc presión 4\"','UND',0,0,0,NULL),(264,314,'Tee pvc presión 4\" x 4\" x 3\"','UND',0,0,0,NULL),(265,315,'Tee pvc presión 6\"','UND',0,0,0,NULL),(266,316,'Tee H.D presión 6\" x 3\"','UND',0,0,0,NULL),(267,320,'Tapón pvc presión 3\"','UND',0,0,0,NULL),(268,321,'Tapón pvc presión 4\"','UND',0,0,0,NULL),(269,322,'Tapón H.D presión 8\"','UND',0,0,0,NULL),(270,323,'Reducción pvc presión 1 1/2\" x 3/4\" (buje)','UND',0,0,0,NULL),(271,324,'Reducción pvc presión 2\" x 1\" (buje)','UND',0,0,0,NULL),(272,325,'Reducción pvc presión 2\" x 1 1/2\" (buje)','UND',0,0,0,NULL),(273,326,'Reducción pvc presión 4\" x 2\" (buje)','UND',0,0,0,NULL),(274,328,'Reducción pvc presión 6\" x 4\" (buje)','UND',0,0,0,NULL),(275,331,'Unión Z UM presión 4\"','UND',0,0,0,NULL),(276,332,'Unión pasante pvc presión 3\"','UND',0,0,0,NULL),(277,333,'Unión pasante pvc presión 4\"','UND',0,0,0,NULL),(278,334,'Unión pasante pvc presión 6\"','UND',0,0,0,NULL),(279,335,'Unión pasante pvc presión 8\"','UND',0,0,0,NULL),(280,337,'Brida unión de 3\"','UND',0,0,0,NULL),(281,338,'Brida unión de 4\"','UND',0,0,0,NULL),(282,339,'Brida unión de 6\"','UND',0,0,0,NULL),(283,340,'Brida unión de 8\"','UND',0,0,0,NULL),(284,341,'Tapa chorote','UND',0,0,0,NULL),(285,342,'Cruceta H.D 4\" x 3\"','UND',0,0,0,NULL),(286,343,'Cruceta H.D 4\" x 4\"','UND',0,0,0,NULL),(287,344,'Cruceta H.D 6\" x 3\"','UND',0,0,0,NULL),(288,346,'Válvula de compuerta sello elástico H.D 3\"','UND',0,0,0,NULL),(289,347,'Válvula de compuerta sello elástico H.D 4\"','UND',0,0,0,NULL),(290,348,'Válvula de compuerta sello elástico H.D 6\"','UND',0,0,0,NULL),(291,349,'Válvula AQT compuerta elástica 3\" RDE 21','UND',0,0,0,NULL),(292,351,'Válvula ventosa de 1\"','UND',0,0,0,NULL),(293,352,'Válvula ventosa de 2\"','UND',0,0,0,NULL),(294,353,'Hidrante H.D 3\"','UND',0,0,0,NULL),(295,354,'imprermeabilizante color seal','cuñete',0,0,0,NULL),(296,355,'Combo sanitario constructor blanco corona','unidad',0,0,0,NULL);
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
INSERT INTO `material_almacen` VALUES (1,1,1),(2,1,219),(3,1,81),(4,1,148),(5,1,721),(6,1,490),(7,1,1675),(8,1,384),(9,1,554),(10,1,216),(11,1,4),(12,1,11),(13,1,10),(14,1,1),(15,1,109),(16,1,27),(17,1,4),(18,1,50),(19,1,7),(20,1,100),(21,1,110),(22,1,14),(23,1,0),(24,1,0),(25,1,0),(26,1,0),(27,1,0),(28,1,0),(29,1,0),(30,1,0),(31,1,0),(32,1,0),(33,1,0),(34,1,0),(35,1,0),(36,1,0),(37,1,0),(38,1,0),(39,1,0),(40,1,0),(41,1,0),(42,1,0),(43,1,0),(44,1,0),(45,1,0),(46,1,0),(47,1,0),(48,1,0),(49,1,0),(50,1,0),(51,1,0),(52,1,0),(53,1,0),(54,1,0),(55,1,0),(56,1,0),(57,1,0),(58,1,0),(59,1,0),(60,1,0),(61,1,0),(62,1,0),(63,1,0),(64,1,0),(65,1,0),(66,1,0),(67,1,0),(68,1,0),(69,1,0),(70,1,0),(71,1,0),(72,1,0),(73,1,0),(74,1,0),(75,1,0),(76,1,0),(77,1,0),(78,1,0),(79,1,0),(80,1,0),(81,1,0),(82,1,0),(83,1,0),(84,1,0),(85,1,0),(86,1,0),(87,1,0),(88,1,0),(89,1,0),(90,1,0),(91,1,0),(92,1,0),(93,1,0),(94,1,0),(95,1,0),(96,1,0),(97,1,0),(98,1,0),(99,1,0),(100,1,0),(101,1,0),(102,1,0),(103,1,0),(104,1,0),(105,1,0),(106,1,0),(107,1,0),(108,1,0),(109,1,0),(110,1,0),(111,1,0),(112,1,0),(113,1,0),(114,1,0),(115,1,0),(116,1,0),(117,1,0),(118,1,0),(119,1,0),(120,1,0),(121,1,0),(122,1,0),(123,1,0),(124,1,0),(125,1,0),(126,1,0),(127,1,0),(128,1,0),(129,1,0),(130,1,0),(131,1,0),(132,1,0),(133,1,0),(134,1,0),(135,1,0),(136,1,0),(137,1,0),(138,1,0),(139,1,0),(140,1,0),(141,1,0),(142,1,0),(143,1,0),(144,1,0),(145,1,0),(146,1,0),(147,1,0),(148,1,0),(149,1,0),(150,1,0),(151,1,0),(152,1,0),(153,1,0),(154,1,0),(155,1,0),(156,1,0),(157,1,0),(158,1,0),(159,1,0),(160,1,0),(161,1,0),(162,1,0),(163,1,0),(164,1,0),(165,1,0),(166,1,0),(167,1,0),(168,1,0),(169,1,0),(170,1,0),(171,1,0),(172,1,0),(173,1,0),(174,1,0),(175,1,0),(176,1,0),(177,1,0),(178,1,0),(179,1,0),(180,1,0),(181,1,0),(182,1,0),(183,1,0),(184,1,0),(185,1,0),(186,1,0),(187,1,0),(188,1,0),(189,1,0),(190,1,0),(191,1,0),(192,1,0),(193,1,0),(194,1,0),(195,1,0),(196,1,0),(197,1,0),(198,1,0),(199,1,0),(200,1,0),(201,1,0),(202,1,0),(203,1,0),(204,1,0),(205,1,0),(206,1,0),(207,1,0),(208,1,0),(209,1,0),(210,1,0),(211,1,0),(212,1,0),(213,1,0),(214,1,0),(215,1,0),(216,1,0),(217,1,0),(218,1,0),(219,1,0),(220,1,0),(221,1,0),(222,1,0),(223,1,0),(224,1,0),(225,1,0),(226,1,0),(227,1,0),(228,1,0),(229,1,0),(230,1,0),(231,1,0),(232,1,0),(233,1,0),(234,1,0),(235,1,0),(236,1,0),(237,1,0),(238,1,0),(239,1,0),(240,1,0),(241,1,0),(242,1,0),(243,1,0),(244,1,0),(245,1,0),(246,1,0),(247,1,0),(248,1,0),(249,1,0),(250,1,0),(251,1,0),(252,1,0),(253,1,0),(254,1,0),(255,1,0),(256,1,0),(257,1,0),(258,1,0),(259,1,0),(260,1,0),(261,1,0),(262,1,0),(263,1,0),(264,1,0),(265,1,0),(266,1,0),(267,1,0),(268,1,0),(269,1,0),(270,1,0),(271,1,0),(272,1,0),(273,1,0),(274,1,0),(275,1,0),(276,1,0),(277,1,0),(278,1,0),(279,1,0),(280,1,0),(281,1,0),(282,1,0),(283,1,0),(284,1,0),(285,1,0),(286,1,0),(287,1,0),(288,1,0),(289,1,0),(290,1,0),(291,1,0),(292,1,0),(293,1,0),(294,1,0),(295,1,0),(296,1,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_entradas`
--

LOCK TABLES `material_registro_entradas` WRITE;
/*!40000 ALTER TABLE `material_registro_entradas` DISABLE KEYS */;
INSERT INTO `material_registro_entradas` VALUES (1,1,24,2,2,NULL),(2,1,25,10,2,NULL),(3,1,25,15,4,NULL),(4,2,25,10,1,NULL),(5,1,26,2,1,NULL),(6,2,26,3,2,NULL),(7,1,26,4,5,NULL),(8,1,27,2,2,NULL),(9,1,27,8,5,NULL),(10,22,28,100,1,NULL),(11,2,29,100,2,NULL),(12,1,30,1000,1,NULL),(13,1,31,2,1,NULL),(17,1,35,10,1,50000.00),(19,1,37,200,2,2000.00),(20,1,38,5,1,30000.00),(22,1,40,200,1,45000.00),(23,22,41,1,1,40000.00),(24,22,41,1,1,42000.00),(25,1,42,1,1,NULL),(26,1,42,2,1,NULL),(27,1,43,1,1,NULL),(28,1,43,1,1,NULL),(29,1,44,1,1,NULL),(31,1,46,100,2,45200.00),(32,1,47,10,1,48000.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_registro_salidas`
--

LOCK TABLES `material_registro_salidas` WRITE;
/*!40000 ALTER TABLE `material_registro_salidas` DISABLE KEYS */;
INSERT INTO `material_registro_salidas` VALUES (1,22,20,1,1,1,1,1,2,6,NULL),(2,22,21,2,1,2,2,1,2,16,NULL),(3,22,22,1,1,2,5,1,2,8,NULL),(4,22,23,1,1,1,5,1,2,6,NULL),(5,22,24,2,1,2,4,1,3,16,NULL),(6,2,24,1,1,1,2,1,2,14,NULL),(7,2,25,1,1,2,2,1,2,16,NULL),(8,22,26,71,1,1,1,1,2,6,NULL),(9,22,27,1,1,1,1,1,2,6,NULL),(10,22,28,1,1,1,2,1,3,11,NULL),(11,1,29,135,1,2,1,1,3,8,NULL),(12,1,30,1,1,1,1,1,2,11,NULL),(16,1,34,1,1,1,1,1,2,6,NULL),(17,1,35,11,1,1,2,1,3,11,NULL),(20,1,38,6,1,2,2,1,3,8,41996.70),(21,1,39,6,1,2,5,1,3,8,41996.70),(22,1,40,1191,1,1,5,1,3,6,41996.70),(28,1,46,1,1,1,1,1,2,6,41996.70),(42,1,58,1,NULL,NULL,1,NULL,2,18,41996.70),(43,1,59,1,NULL,NULL,1,NULL,2,6,41996.70),(44,22,59,1,NULL,NULL,1,NULL,2,6,NULL),(50,1,62,1,NULL,NULL,1,NULL,2,6611,41996.70),(54,1,64,1,NULL,NULL,1,NULL,2,18,41996.70),(55,1,65,1,NULL,NULL,1,NULL,NULL,6611,41996.70),(56,1,66,1,NULL,NULL,1,NULL,2,6648,41996.70),(57,1,67,2,NULL,NULL,NULL,NULL,NULL,6732,44985.06),(58,1,68,1,NULL,NULL,NULL,NULL,NULL,6647,44985.06),(59,1,68,2,NULL,NULL,NULL,NULL,NULL,6648,44985.06),(60,1,69,1,NULL,NULL,NULL,NULL,NULL,6773,44985.06),(61,1,70,1,NULL,NULL,NULL,NULL,NULL,6719,44985.06),(62,22,70,1,NULL,NULL,NULL,NULL,NULL,6,40125.00),(63,1,71,1,NULL,NULL,NULL,NULL,NULL,6611,44985.06),(64,1,72,1,NULL,NULL,1,NULL,2,6755,44985.06),(65,1,73,1,NULL,NULL,NULL,NULL,NULL,6749,44985.06),(66,1,74,1,NULL,NULL,1,NULL,2,6761,44985.06),(67,1,75,1,NULL,NULL,1,NULL,2,6749,45057.92),(68,22,75,1,NULL,NULL,1,NULL,2,6,40125.00),(69,1,76,1,NULL,NULL,1,NULL,2,6743,45057.92),(70,1,77,2,NULL,NULL,1,NULL,2,6611,45057.92),(71,1,78,240,NULL,NULL,5,NULL,3,6755,45057.92),(72,1,79,1,NULL,NULL,1,NULL,2,6611,45057.92),(73,1,80,1,NULL,NULL,1,NULL,2,6755,48000.00),(74,1,81,1,NULL,NULL,1,NULL,2,6611,48000.00),(75,1,82,1,NULL,NULL,1,NULL,3,6615,48000.00),(76,1,83,1,NULL,NULL,1,NULL,2,6611,48000.00),(77,1,84,1,NULL,NULL,1,NULL,2,6755,48000.00),(78,1,85,1,NULL,NULL,1,NULL,2,6611,48000.00),(79,1,86,1,NULL,NULL,1,NULL,2,6611,48000.00),(80,1,87,1,NULL,NULL,1,NULL,2,6611,48000.00),(81,1,88,1,NULL,NULL,1,NULL,2,6611,48000.00);
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
INSERT INTO `proveedor` VALUES (4,'Andamios del sur');
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecto`
--

LOCK TABLES `proyecto` WRITE;
/*!40000 ALTER TABLE `proyecto` DISABLE KEYS */;
INSERT INTO `proyecto` VALUES (1,'Salamanca la nueva conjunto residencial',NULL),(2,'Tienda D1',NULL),(3,'Ciudadela salamanca la nueva',NULL),(57,'CC san pablo',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_entradas`
--

LOCK TABLES `registro_entradas` WRITE;
/*!40000 ALTER TABLE `registro_entradas` DISABLE KEYS */;
INSERT INTO `registro_entradas` VALUES (1,'2026-07-30','07:57:24',1,NULL),(2,'2026-07-30','11:19:06',1,NULL),(3,'2026-07-30','14:19:10',1,NULL),(4,'2026-07-31','10:37:42',1,NULL),(5,'2026-08-04','10:39:48',1,NULL),(6,'2026-08-04','10:48:39',1,NULL),(7,'2026-08-04','10:54:38',1,NULL),(8,'2026-08-04','10:55:57',1,NULL),(9,'2026-08-04','10:56:41',1,NULL),(10,'2026-08-04','10:57:31',1,NULL),(11,'2026-08-04','10:58:15',1,NULL),(12,'2026-08-04','11:04:45',1,NULL),(13,'2026-08-04','11:06:41',1,NULL),(14,'2026-08-04','11:07:31',1,NULL),(15,'2026-08-04','11:22:07',1,NULL),(16,'2026-08-04','11:30:53',1,NULL),(17,'2026-08-04','11:41:36',1,NULL),(18,'2026-08-04','11:42:04',1,NULL),(19,'2026-08-04','11:50:37',1,NULL),(20,'2026-08-04','11:52:08',1,NULL),(21,'2026-08-05','11:34:33',1,NULL),(22,'2026-08-05','11:35:45',1,NULL),(23,'2026-08-05','11:47:25',1,NULL),(24,'2026-08-05','12:05:07',1,NULL),(25,'2026-08-05','12:06:27',1,NULL),(26,'2026-08-05','12:18:10',1,NULL),(27,'2026-08-06','08:54:11',1,NULL),(28,'2026-08-19','12:23:22',1,NULL),(29,'2026-08-20','09:51:24',1,NULL),(30,'2026-08-25','10:25:02',1,NULL),(31,'2026-08-25','14:47:06',1,NULL),(35,'2026-08-25','15:41:53',1,4),(37,'2026-08-25','15:58:58',1,4),(38,'2026-08-25','16:42:24',1,4),(40,'2026-08-27','12:03:05',1,4),(41,'2026-08-27','12:05:10',1,4),(42,'2026-08-27','12:06:36',1,4),(43,'2026-08-27','12:07:29',1,4),(44,'2026-08-28','14:27:08',1,NULL),(46,'2026-08-28','16:50:44',1,4),(47,'2026-08-31','16:35:27',1,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_salidas`
--

LOCK TABLES `registro_salidas` WRITE;
/*!40000 ALTER TABLE `registro_salidas` DISABLE KEYS */;
INSERT INTO `registro_salidas` VALUES (1,'2026-07-30','08:02:03',1,1,1),(2,'2026-07-30','08:09:41',1,1,1),(3,'2026-07-30','08:39:35',1,1,1),(4,'2026-07-30','11:22:46',1,1,1),(5,'2026-07-30','14:21:27',1,1,1),(6,'2026-07-30','15:15:41',1,2,2),(7,'2026-07-30','15:45:50',1,3,3),(8,'2026-07-30','15:46:54',1,2,2),(9,'2026-07-30','19:54:29',1,2,2),(10,'2026-07-31','09:28:27',1,1,1),(11,'2026-07-31','10:39:15',1,2,1),(12,'2026-08-04','12:15:54',1,2,3),(13,'2026-08-04','19:18:56',1,1,1),(14,'2026-08-06','08:53:47',1,1,1),(15,'2026-08-19','12:23:46',1,1,1),(16,'2026-08-19','12:25:38',1,1,1),(17,'2026-08-19','12:27:33',1,1,1),(18,'2026-08-19','12:33:38',1,1,1),(19,'2026-08-19','12:34:58',1,1,1),(20,'2026-08-19','12:36:46',1,1,1),(21,'2026-08-20','08:21:33',1,2,3),(22,'2026-08-20','09:11:54',1,1,1),(23,'2026-08-20','09:21:23',1,1,1),(24,'2026-08-20','09:51:47',1,3,3),(25,'2026-08-20','09:54:03',1,1,3),(26,'2026-08-24','13:41:34',1,1,1),(27,'2026-08-24','13:49:46',1,1,1),(28,'2026-08-24','13:52:59',1,2,2),(29,'2026-08-24','17:08:21',1,1,1),(30,'2026-08-25','14:47:57',1,1,2),(34,'2026-08-25','15:17:01',1,1,1),(35,'2026-08-25','15:21:03',1,1,2),(38,'2026-08-25','16:46:40',1,1,1),(39,'2026-08-25','16:47:33',1,1,1),(40,'2026-08-25','16:48:23',1,1,1),(46,'2026-08-25','19:57:26',1,1,1),(58,'2026-08-26','18:57:14',1,1,1),(59,'2026-08-26','19:34:25',1,1,1),(62,'2026-08-27','09:35:16',1,1,2),(64,'2026-08-27','10:55:24',1,1,1),(65,'2026-08-27','10:57:03',1,1,2),(66,'2026-08-27','11:59:27',1,1,1),(67,'2026-08-27','12:08:10',1,1,1),(68,'2026-08-27','12:10:06',1,1,1),(69,'2026-08-27','16:32:04',1,1,1),(70,'2026-08-27','17:13:26',1,1,1),(71,'2026-08-27','17:16:54',1,2,2),(72,'2026-08-27','18:12:20',1,1,1),(73,'2026-08-27','18:19:57',1,1,1),(74,'2026-08-27','18:37:32',1,1,1),(75,'2026-08-28','17:03:17',1,1,1),(76,'2026-08-28','17:56:51',1,1,1),(77,'2026-08-31','10:01:43',1,2,2),(78,'2026-08-31','16:16:52',1,2,1),(79,'2026-08-31','16:22:26',1,2,2),(80,'2026-08-31','16:36:03',1,2,1),(81,'2026-08-31','16:45:58',1,2,2),(82,'2026-08-31','17:38:59',1,1,1),(83,'2026-08-31','17:42:10',1,2,2),(84,'2026-08-31','18:45:07',1,2,1),(85,'2026-08-31','18:47:33',1,2,2),(86,'2026-09-01','08:20:40',1,2,2),(87,'2026-09-01','08:21:38',1,2,2),(88,'2026-09-01','08:21:57',1,2,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubro`
--

LOCK TABLES `rubro` WRITE;
/*!40000 ALTER TABLE `rubro` DISABLE KEYS */;
INSERT INTO `rubro` VALUES (2,'Cajilla'),(3,'Anden');
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
INSERT INTO `solicitud` VALUES (14,'2026-08-25','19:56:46',1,1,NULL,'ENTREGADA',1,'2026-08-25 19:57:06',46),(15,'2026-08-25','20:05:52',1,2,NULL,'RECHAZADA',1,'2026-08-25 21:12:14',NULL),(17,'2026-08-28','19:27:31',1,1,NULL,'APROBADA',1,'2026-08-28 19:28:00',NULL);
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
INSERT INTO `solicitud_detalle` VALUES (12,14,1,1),(13,15,1,1),(15,17,1,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=6799 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubicacion`
--

LOCK TABLES `ubicacion` WRITE;
/*!40000 ALTER TABLE `ubicacion` DISABLE KEYS */;
INSERT INTO `ubicacion` VALUES (4,1,NULL,'Etapa 1',NULL,NULL,0,'etapa',1,'1|0|etapa 1'),(5,1,4,'M10',NULL,NULL,1,'manzana',1,'1|4|m10'),(6,1,5,'1',NULL,NULL,2,'casa',1,'1|5|1'),(7,1,4,'M11',NULL,NULL,1,'manzana',1,'1|4|m11'),(8,1,7,'1',NULL,NULL,2,'casa',1,'1|7|1'),(9,2,NULL,'Etapa 1',NULL,NULL,0,'etapa',0,'2|0|etapa 1'),(10,2,9,'M10',NULL,NULL,1,'manzana',0,'2|9|m10'),(11,2,10,'1',NULL,NULL,2,'casa',0,'2|10|1'),(12,3,NULL,'Etapa 1',NULL,NULL,0,'etapa',1,'3|0|etapa 1'),(13,3,12,'M10',NULL,NULL,1,'manzana',1,'3|12|m10'),(14,3,13,'1',NULL,NULL,2,'casa',1,'3|13|1'),(15,3,12,'M11',NULL,NULL,1,'manzana',1,'3|12|m11'),(16,3,15,'1',NULL,NULL,2,'casa',1,'3|15|1'),(18,1,5,'3',NULL,NULL,2,'casa',1,'1|5|3'),(19,1,NULL,'etapa 2',NULL,NULL,0,'etapa',1,'1|0|etapa 2'),(4577,1,NULL,'Torre 1',NULL,NULL,0,'Torre',0,'1|0|torre 1'),(6611,2,NULL,'Local comercial',NULL,NULL,0,'Local',1,'2|0|local comercial'),(6615,1,NULL,'urbanismo',NULL,NULL,0,'urbanismo',1,'1|0|urbanismo'),(6617,1,4577,'Piso 1',NULL,NULL,1,'Piso',1,'1|4577|piso 1'),(6618,1,6617,'101',NULL,NULL,2,'Apartamento',1,'1|6617|101'),(6621,1,NULL,'Torre 2','Torre 2',1,0,'Torre',1,'1|0|torre 2'),(6622,1,6621,'Piso 01','Piso 01',1,1,'Piso',1,'1|6621|piso 01'),(6623,1,6621,'Piso 02','Piso 02',2,1,'Piso',1,'1|6621|piso 02'),(6624,1,6621,'Piso 03','Piso 03',3,1,'Piso',1,'1|6621|piso 03'),(6625,1,6621,'Piso 04','Piso 04',4,1,'Piso',1,'1|6621|piso 04'),(6626,1,6621,'Piso 05','Piso 05',5,1,'Piso',1,'1|6621|piso 05'),(6627,1,6621,'Piso 06','Piso 06',6,1,'Piso',1,'1|6621|piso 06'),(6628,1,6621,'Piso 07','Piso 07',7,1,'Piso',1,'1|6621|piso 07'),(6629,1,6621,'Piso 08','Piso 08',8,1,'Piso',1,'1|6621|piso 08'),(6630,1,6621,'Piso 09','Piso 09',9,1,'Piso',1,'1|6621|piso 09'),(6631,1,6621,'Piso 10','Piso 10',10,1,'Piso',1,'1|6621|piso 10'),(6632,1,6621,'Piso 11','Piso 11',11,1,'Piso',1,'1|6621|piso 11'),(6633,1,6621,'Piso 12','Piso 12',12,1,'Piso',1,'1|6621|piso 12'),(6634,1,6621,'Piso 13','Piso 13',13,1,'Piso',1,'1|6621|piso 13'),(6635,1,6621,'Piso 14','Piso 14',14,1,'Piso',1,'1|6621|piso 14'),(6636,1,6621,'Piso 15','Piso 15',15,1,'Piso',1,'1|6621|piso 15'),(6637,1,6621,'Piso 16','Piso 16',16,1,'Piso',1,'1|6621|piso 16'),(6638,1,6621,'Piso 17','Piso 17',17,1,'Piso',1,'1|6621|piso 17'),(6639,1,6621,'Piso 18','Piso 18',18,1,'Piso',1,'1|6621|piso 18'),(6640,1,6621,'Piso 19','Piso 19',19,1,'Piso',1,'1|6621|piso 19'),(6641,1,6621,'Piso 20','Piso 20',20,1,'Piso',1,'1|6621|piso 20'),(6642,1,6621,'Piso 21','Piso 21',21,1,'Piso',1,'1|6621|piso 21'),(6643,1,6621,'Piso 22','Piso 22',22,1,'Piso',1,'1|6621|piso 22'),(6644,1,6621,'Piso 23','Piso 23',23,1,'Piso',1,'1|6621|piso 23'),(6645,1,6621,'Piso 24','Piso 24',24,1,'Piso',1,'1|6621|piso 24'),(6646,1,6621,'Piso 25','Piso 25',25,1,'Piso',1,'1|6621|piso 25'),(6647,1,6622,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6622|apto 01'),(6648,1,6622,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6622|apto 02'),(6649,1,6622,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6622|apto 03'),(6650,1,6622,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6622|apto 04'),(6651,1,6622,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6622|apto 05'),(6652,1,6622,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6622|apto 06'),(6653,1,6623,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6623|apto 01'),(6654,1,6623,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6623|apto 02'),(6655,1,6623,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6623|apto 03'),(6656,1,6623,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6623|apto 04'),(6657,1,6623,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6623|apto 05'),(6658,1,6623,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6623|apto 06'),(6659,1,6624,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6624|apto 01'),(6660,1,6624,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6624|apto 02'),(6661,1,6624,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6624|apto 03'),(6662,1,6624,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6624|apto 04'),(6663,1,6624,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6624|apto 05'),(6664,1,6624,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6624|apto 06'),(6665,1,6625,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6625|apto 01'),(6666,1,6625,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6625|apto 02'),(6667,1,6625,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6625|apto 03'),(6668,1,6625,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6625|apto 04'),(6669,1,6625,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6625|apto 05'),(6670,1,6625,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6625|apto 06'),(6671,1,6626,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6626|apto 01'),(6672,1,6626,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6626|apto 02'),(6673,1,6626,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6626|apto 03'),(6674,1,6626,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6626|apto 04'),(6675,1,6626,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6626|apto 05'),(6676,1,6626,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6626|apto 06'),(6677,1,6627,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6627|apto 01'),(6678,1,6627,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6627|apto 02'),(6679,1,6627,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6627|apto 03'),(6680,1,6627,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6627|apto 04'),(6681,1,6627,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6627|apto 05'),(6682,1,6627,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6627|apto 06'),(6683,1,6628,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6628|apto 01'),(6684,1,6628,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6628|apto 02'),(6685,1,6628,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6628|apto 03'),(6686,1,6628,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6628|apto 04'),(6687,1,6628,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6628|apto 05'),(6688,1,6628,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6628|apto 06'),(6689,1,6629,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6629|apto 01'),(6690,1,6629,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6629|apto 02'),(6691,1,6629,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6629|apto 03'),(6692,1,6629,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6629|apto 04'),(6693,1,6629,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6629|apto 05'),(6694,1,6629,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6629|apto 06'),(6695,1,6630,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6630|apto 01'),(6696,1,6630,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6630|apto 02'),(6697,1,6630,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6630|apto 03'),(6698,1,6630,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6630|apto 04'),(6699,1,6630,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6630|apto 05'),(6700,1,6630,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6630|apto 06'),(6701,1,6631,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6631|apto 01'),(6702,1,6631,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6631|apto 02'),(6703,1,6631,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6631|apto 03'),(6704,1,6631,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6631|apto 04'),(6705,1,6631,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6631|apto 05'),(6706,1,6631,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6631|apto 06'),(6707,1,6632,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6632|apto 01'),(6708,1,6632,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6632|apto 02'),(6709,1,6632,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6632|apto 03'),(6710,1,6632,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6632|apto 04'),(6711,1,6632,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6632|apto 05'),(6712,1,6632,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6632|apto 06'),(6713,1,6633,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6633|apto 01'),(6714,1,6633,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6633|apto 02'),(6715,1,6633,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6633|apto 03'),(6716,1,6633,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6633|apto 04'),(6717,1,6633,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6633|apto 05'),(6718,1,6633,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6633|apto 06'),(6719,1,6634,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6634|apto 01'),(6720,1,6634,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6634|apto 02'),(6721,1,6634,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6634|apto 03'),(6722,1,6634,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6634|apto 04'),(6723,1,6634,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6634|apto 05'),(6724,1,6634,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6634|apto 06'),(6725,1,6635,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6635|apto 01'),(6726,1,6635,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6635|apto 02'),(6727,1,6635,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6635|apto 03'),(6728,1,6635,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6635|apto 04'),(6729,1,6635,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6635|apto 05'),(6730,1,6635,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6635|apto 06'),(6731,1,6636,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6636|apto 01'),(6732,1,6636,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6636|apto 02'),(6733,1,6636,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6636|apto 03'),(6734,1,6636,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6636|apto 04'),(6735,1,6636,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6636|apto 05'),(6736,1,6636,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6636|apto 06'),(6737,1,6637,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6637|apto 01'),(6738,1,6637,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6637|apto 02'),(6739,1,6637,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6637|apto 03'),(6740,1,6637,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6637|apto 04'),(6741,1,6637,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6637|apto 05'),(6742,1,6637,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6637|apto 06'),(6743,1,6638,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6638|apto 01'),(6744,1,6638,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6638|apto 02'),(6745,1,6638,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6638|apto 03'),(6746,1,6638,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6638|apto 04'),(6747,1,6638,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6638|apto 05'),(6748,1,6638,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6638|apto 06'),(6749,1,6639,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6639|apto 01'),(6750,1,6639,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6639|apto 02'),(6751,1,6639,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6639|apto 03'),(6752,1,6639,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6639|apto 04'),(6753,1,6639,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6639|apto 05'),(6754,1,6639,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6639|apto 06'),(6755,1,6640,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6640|apto 01'),(6756,1,6640,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6640|apto 02'),(6757,1,6640,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6640|apto 03'),(6758,1,6640,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6640|apto 04'),(6759,1,6640,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6640|apto 05'),(6760,1,6640,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6640|apto 06'),(6761,1,6641,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6641|apto 01'),(6762,1,6641,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6641|apto 02'),(6763,1,6641,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6641|apto 03'),(6764,1,6641,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6641|apto 04'),(6765,1,6641,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6641|apto 05'),(6766,1,6641,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6641|apto 06'),(6767,1,6642,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6642|apto 01'),(6768,1,6642,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6642|apto 02'),(6769,1,6642,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6642|apto 03'),(6770,1,6642,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6642|apto 04'),(6771,1,6642,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6642|apto 05'),(6772,1,6642,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6642|apto 06'),(6773,1,6643,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6643|apto 01'),(6774,1,6643,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6643|apto 02'),(6775,1,6643,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6643|apto 03'),(6776,1,6643,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6643|apto 04'),(6777,1,6643,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6643|apto 05'),(6778,1,6643,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6643|apto 06'),(6779,1,6644,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6644|apto 01'),(6780,1,6644,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6644|apto 02'),(6781,1,6644,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6644|apto 03'),(6782,1,6644,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6644|apto 04'),(6783,1,6644,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6644|apto 05'),(6784,1,6644,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6644|apto 06'),(6785,1,6645,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6645|apto 01'),(6786,1,6645,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6645|apto 02'),(6787,1,6645,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6645|apto 03'),(6788,1,6645,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6645|apto 04'),(6789,1,6645,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6645|apto 05'),(6790,1,6645,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6645|apto 06'),(6791,1,6646,'Apto 01','Apto 01',1,2,'Apartamento',1,'1|6646|apto 01'),(6792,1,6646,'Apto 02','Apto 02',2,2,'Apartamento',1,'1|6646|apto 02'),(6793,1,6646,'Apto 03','Apto 03',3,2,'Apartamento',1,'1|6646|apto 03'),(6794,1,6646,'Apto 04','Apto 04',4,2,'Apartamento',1,'1|6646|apto 04'),(6795,1,6646,'Apto 05','Apto 05',5,2,'Apartamento',1,'1|6646|apto 05'),(6796,1,6646,'Apto 06','Apto 06',6,2,'Apartamento',1,'1|6646|apto 06'),(6797,57,NULL,'Piso 1',NULL,NULL,0,'Piso',1,'57|0|piso 1'),(6798,57,6797,'Local 101',NULL,NULL,1,'Local',1,'57|6797|local 101');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,1004035010,'Juan Diego','Lopez ','$2y$10$TOQQ.Y0fPY8xNmHXmw.DmuOB3DKyQRzovRj2hIOtyc.Lv4qDND9qS',1),(2,123456789,'Pedro','Perez','$2y$10$jCp3qX/8AEwPoTQjEc5MqOBXcGxbJ3XH3HgH/JQ6pXbQ7o.7ZzZ7y',2);
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

-- Dump completed on 2026-09-01 10:55:19
