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
-- Table structure for table `atividade_aluno`
--

DROP TABLE IF EXISTS `atividade_aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividade_aluno` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `segunda` tinyint(1) DEFAULT '0',
  `terca` tinyint(1) DEFAULT '0',
  `quarta` tinyint(1) DEFAULT '0',
  `quinta` tinyint(1) DEFAULT '0',
  `sexta` tinyint(1) DEFAULT '0',
  `sabado` tinyint(1) DEFAULT '0',
  `domingo` tinyint(1) DEFAULT '0',
  `h_inicial` time NOT NULL,
  `h_final` time NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `local_id` int DEFAULT NULL,
  `dh_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `local_id` (`local_id`),
  CONSTRAINT `atividade_aluno_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `atividade_aluno_ibfk_2` FOREIGN KEY (`local_id`) REFERENCES `local` (`id`),
  CONSTRAINT `atividade_aluno_chk_1` CHECK ((`valor` > 0.00))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_aluno`
--

LOCK TABLES `atividade_aluno` WRITE;
/*!40000 ALTER TABLE `atividade_aluno` DISABLE KEYS */;
INSERT INTO `atividade_aluno` VALUES (1,'dfdf',30.00,0,1,1,1,0,0,0,'08:00:00','16:00:00',1,1,'2024-04-10 15:53:59','2024-04-10 15:53:59',1),(2,'JAZZ',45.00,0,0,0,1,0,0,0,'08:00:00','16:00:00',1,1,'2024-04-12 19:54:08','2024-04-12 19:54:08',1),(3,'JAZZ',45.00,0,0,0,1,0,0,0,'08:00:00','16:00:00',1,1,'2024-04-23 15:34:22','2024-04-23 15:34:22',1),(4,'JAZZ',45.00,0,0,0,1,0,0,0,'08:00:00','16:00:00',1,1,'2024-04-23 15:34:25','2024-04-23 15:34:25',1);
/*!40000 ALTER TABLE `atividade_aluno` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-24  0:30:30
