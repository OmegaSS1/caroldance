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
-- Table structure for table `perfil_usuario`
--

DROP TABLE IF EXISTS `perfil_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfil_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `usuario_dashboard` tinyint(1) DEFAULT '0',
  `usuario_aluno` tinyint(1) DEFAULT '0',
  `adm_dashboard` tinyint(1) DEFAULT '0',
  `adm_calendario` tinyint(1) DEFAULT '0',
  `adm_cadastro_aluno` tinyint(1) DEFAULT '0',
  `adm_cadastro_usuario` tinyint(1) DEFAULT '0',
  `adm_cadastro_atividade` tinyint(1) DEFAULT '0',
  `adm_relatorio_aluno` tinyint(1) DEFAULT '0',
  `adm_relatorio_usuario` tinyint(1) DEFAULT '0',
  `adm_relatorio_balancete` tinyint(1) DEFAULT '0',
  `adm_grafico_atividade_mensal` tinyint(1) DEFAULT '0',
  `adm_grafico_mensalidade_mes` tinyint(1) DEFAULT '0',
  `adm_grafico_atividade` tinyint(1) DEFAULT '0',
  `dh_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil_usuario`
--

LOCK TABLES `perfil_usuario` WRITE;
/*!40000 ALTER TABLE `perfil_usuario` DISABLE KEYS */;
INSERT INTO `perfil_usuario` VALUES (1,'Visitante',0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-04-06 14:51:33','2024-04-06 14:51:33',1),(2,'Usuario',1,1,0,0,0,0,0,0,0,0,0,0,0,'2024-04-06 14:51:33','2024-04-06 14:51:33',1),(3,'Administrador',0,0,1,1,0,0,0,0,0,0,0,0,0,'2024-04-06 14:51:33','2024-04-06 14:51:33',1),(4,'Administrador Master',0,0,1,1,1,1,1,1,1,1,1,1,0,'2024-04-06 14:51:33','2024-04-06 14:51:33',1);
/*!40000 ALTER TABLE `perfil_usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-24  0:30:27
