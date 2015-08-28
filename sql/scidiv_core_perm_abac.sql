CREATE DATABASE  IF NOT EXISTS `scidiv` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `scidiv`;
-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: localhost    Database: scidiv
-- ------------------------------------------------------
-- Server version	5.6.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `core_perm_abac`
--

DROP TABLE IF EXISTS `core_perm_abac`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_perm_abac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perm_id` int(11) NOT NULL,
  `attribs` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tpconf_perm_id_idx` (`perm_id`),
  CONSTRAINT `tpconf_perm_id` FOREIGN KEY (`perm_id`) REFERENCES `core_permission` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Implementation of attribute based access controll system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_perm_abac`
--

LOCK TABLES `core_perm_abac` WRITE;
/*!40000 ALTER TABLE `core_perm_abac` DISABLE KEYS */;
INSERT INTO `core_perm_abac` (`id`, `perm_id`, `attribs`) VALUES (1,1,'{\"user_roles\":[0,500,1000,2000,2500,3000,4000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(3,3,'{\"user_roles\":[2500],\"service_states\":[1,2],\"time_states\":[0]}'),(4,3,'{\"user_roles\":[3000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(5,3,'{\"user_roles\":[4000],\"service_states\":[0,1,2],\"time_states\":[0,1,2]}'),(6,7,'{\"user_roles\":[2500],\"service_states\":[2],\"time_states\":[0,1]}'),(7,7,'{\"user_roles\":[4000],\"service_states\":[0,1,2],\"time_states\":[0,1,2]}'),(8,2,'{\"user_roles\":[4000],\"service_states\":[0,1,2],\"time_states\":[0,1,2]}'),(9,2,'{\"user_roles\":[3000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(10,2,'{\"user_roles\":[2000],\"service_states\":[2],\"time_states\":[0]}'),(11,4,'{\"user_roles\":[2500],\"service_states\":[2],\"time_states\":[0,1]}'),(12,4,'{\"user_roles\":[3000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(13,4,'{\"user_roles\":[4000],\"service_states\":[0,1,2],\"time_states\":[0,1,2]}'),(14,8,'{\"user_roles\":[3000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(15,8,'{\"user_roles\":[4000],\"service_states\":[1,2],\"time_states\":[0,1,2]}'),(16,10,'{\"user_roles\":[3000,4000]}');
/*!40000 ALTER TABLE `core_perm_abac` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-28 16:10:27
