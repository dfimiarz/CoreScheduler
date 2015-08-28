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
-- Temporary view structure for view `core_event_details_view`
--

DROP TABLE IF EXISTS `core_event_details_view`;
/*!50001 DROP VIEW IF EXISTS `core_event_details_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `core_event_details_view` AS SELECT 
 1 AS `record_id`,
 1 AS `user_id`,
 1 AS `firstname`,
 1 AS `lastname`,
 1 AS `username`,
 1 AS `email`,
 1 AS `piname`,
 1 AS `timestamp`,
 1 AS `start`,
 1 AS `end`,
 1 AS `note`,
 1 AS `event_state`,
 1 AS `service_id`,
 1 AS `service_name`,
 1 AS `resource_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `core_event_details_view`
--

/*!50001 DROP VIEW IF EXISTS `core_event_details_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `core_event_details_view` AS select `cta`.`id` AS `record_id`,`cu`.`id` AS `user_id`,`cu`.`firstname` AS `firstname`,`cu`.`lastname` AS `lastname`,`cu`.`username` AS `username`,`cu`.`email` AS `email`,concat(`p`.`first_name`,' ',`p`.`last_name`) AS `piname`,`cta`.`time_modified` AS `timestamp`,`cta`.`start` AS `start`,`cta`.`end` AS `end`,`cta`.`note` AS `note`,`cta`.`state` AS `event_state`,`cta`.`service_id` AS `service_id`,`cs`.`short_name` AS `service_name`,`cr`.`name` AS `resource_name` from ((((`core_timed_activity` `cta` join `core_users` `cu`) join `core_services` `cs`) join `core_resources` `cr`) join `people` `p`) where ((`cu`.`id` = `cta`.`user`) and (`cs`.`id` = `cta`.`service_id`) and (`p`.`individual_id` = `cu`.`pi`) and (`cr`.`id` = `cs`.`resource_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-28 16:12:28
