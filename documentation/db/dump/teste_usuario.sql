-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 172.30.39.17    Database: teste
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `sobrenome` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `email` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `cpf` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `perfil_usuario_id` int DEFAULT NULL,
  `telefone_whatsapp` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `telefone_recado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senha` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `token_redefinicao_senha` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dh_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `perfil_usuario_id` (`perfil_usuario_id`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`perfil_usuario_id`) REFERENCES `perfil_usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Jubileu','Henrique','1995-01-01','teste@teste.com','49305617018',2,'11996221903',NULL,'$2y$10$930PEsBYFS6M9B0lGIHznuYh5cZVZVTudCUe4q7tPY/SoxRljXkH.',NULL,'2024-04-08 23:33:40','2024-04-08 23:33:40',1),(2,'Vitor','Hugo','1995-01-01','teste@teste.com','01733094539',2,'11996221903',NULL,'$2y$10$4w1sseZPzZ2HPX9G3a0jdeE/cwMig5.mWWcxByJDmbfqunAkpLimu',NULL,'2024-04-08 23:37:50','2024-04-08 23:37:50',1),(3,'Vinicius','Silva','2000-01-01','teste@teste.com','12345678909',1,'11996221903',NULL,'$2y$10$28bj7GnVvIH99lnqoH7i3ePRdftI25ODxI56xK/L9DH4HB4YyQxgS',NULL,'2024-04-23 16:03:20','2024-04-23 16:03:20',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-24  0:30:28
