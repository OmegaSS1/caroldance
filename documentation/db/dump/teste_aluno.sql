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
-- Table structure for table `aluno`
--

DROP TABLE IF EXISTS `aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aluno` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sobrenome` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_nascimento` timestamp NULL DEFAULT NULL,
  `cpf` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `atividade_aluno_id` int DEFAULT NULL,
  `dh_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `atividade_aluno_id` (`atividade_aluno_id`),
  CONSTRAINT `aluno_ibfk_1` FOREIGN KEY (`atividade_aluno_id`) REFERENCES `atividade_aluno` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aluno`
--

LOCK TABLES `aluno` WRITE;
/*!40000 ALTER TABLE `aluno` DISABLE KEYS */;
INSERT INTO `aluno` VALUES (106,'Alice','Dantas Andrade',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(107,'Beatriz','Duarte Cruz',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(108,'Helena','Serafim Franco Nascimento',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(109,'Lívia','Pimentel Monteiro',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(110,'Maria Cecília','Oliveira Cabral',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(111,'Maria Julia','Reis Boaventura',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(112,'Maria Teresa','Motta Gonçalves Sá',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(113,'Valentina O.','Matos Queiroz',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(114,'Eva','Amoedo Vilas Boas',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(115,'Maria Clara','Souza Bichara',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(116,'Maria Luiza','Gonzaga Gaspar',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(117,'Marina','Alves Serra Costa',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(118,'Misa','Oliveira Matos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(119,'Camilly Victória','Santos Idorno',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(120,'Clara Victória','Aguiar Gomes',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(121,'Gabriela','Bomfim Trindade',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(122,'Gabrielle','Fernandez Gil Amorim',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(123,'Marina','Capinan Santiago',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(124,'Maria Júlia','Santos Amoedo',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(125,'Pérola Andrade','Seixas Pereira da Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(126,'Catarina','Carigé Lopes',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(127,'Julia Kleivi','Hosana de Oliveira Brito',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(128,'Júlia','Ribeiro Pimenta',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(129,'Melissa','Santana Cruz dos Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(130,'Maria Clara','Ralin Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(131,'Maria Luiza','Guido',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(132,'Sofia','Rocha Ranã',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(133,'Victória','Sales Araújo',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(134,'Andressa','da Silva Moreira',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(135,'Júlia','Guimarães de Outeiro',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(136,'Júlia','Miranda Santos Assis',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(137,'Laura','Guimarães de Outeiro',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(138,'Sofia','Passos Cardozo de Lima',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(139,'Céu','Olifer Malaquias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(140,'Laura','Gomes de Oliveira e Lima',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(141,'Mariana','Pereira Alves',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(142,'Maitê','Serafim Franco Nascimento',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(143,'Pietra','Andrade Iglesias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(144,'Pietra','Marinho Argolo',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(145,'Valentina Lis','Cardoso Almeida',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(146,'Alicia','Pedreira Nascimento',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(147,'Luíza','Lustosa Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(148,'Luisa Fernanda','Costa Sousa',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(149,'Maria Valentina','Santana',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(150,'Vitória','Santana',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(151,'Bruna','Siviero Figueredo',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(152,'Giovana','Botelho Dória Alves Demetrio',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(153,'Giulia','Miguez Ribeiro Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(154,'Heloísa','Ribeiro de Novais Santiago Souza',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(155,'Iolanda Vitória','Monteiro da Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(156,'Laura','Santos Esteves',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(157,'Luise','Pestana Bervian',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(158,'Maria Carolina','Moreira da Silva Vieira',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(159,'Maria Luiza','Santana Bahia Pinto Soares',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(160,'Maria Rafaella','Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(161,'Mariana','Oliveira Nobre',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(162,'Paola','Santos Andrade de Oliveira',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(163,'Ana Vitória','Silva Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(164,'Cristiane','Chaves da Silva',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(165,'Marilia Barbara','Cruz Souzer dos Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(166,'Mirelle Leonidia','dos Santos do Sacramento',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(167,'Aymara','Montezuma de Mello',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(168,'Alicia','Pedreira Nascimento',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(169,'Beatriz','Prazeres Cruz Farias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(170,'Gabriela','Duarte Tondroff',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(171,'Juliana','Almeida Vieira Campos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(172,'Luna Clara S.','Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(173,'Maria Paula','da Purificação Damasceno',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(174,'Ana Clara','Prazeres Cruz Farias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(175,'Beatriz','Michelli Batista',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(176,'Cecilia','Barrena Duarte',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(177,'Elis','Póvoa França',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(178,'Giovana','Michelli Batista',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(179,'Helena','Brito de Almeida Dias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(180,'Flora','Café Carvalho',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(181,'Janaína','dos Santos Pita da Hora',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(182,'Júlia','Vigas Sodré',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(183,'Lara','Alemany e Almeida',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(184,'Lara','Barreto',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(185,'Manuela','Cruz de Andrade Gomes',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(186,'Marina','Viana Barreto',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(187,'Naomi','Ferreira Sousa Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(188,'Sofia','de Andrade Apolônio Gomes',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(189,'Ana Beatriz','Silva Meneses',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(190,'Carolina','Freitas Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(191,'Giovanna','Sady Ribeiro Souza',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(192,'Isabella','Bastos Serra',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(193,'Maria Luísa','Cunha Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(194,'Mirele','de Carvalho Moreira',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(195,'Monique','Cunha Santos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(196,'Alice','Pinto Goes de Oliveira',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(197,'Brisa','Prazeres Cruz Farias',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(198,'Liz','Costa Vasconcelos',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(199,'Mila','Pedrosa Portugal',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(200,'Renata','Souza Doria',NULL,NULL,1,'2024-05-20 02:25:56','2024-05-20 02:25:56',1),(201,'Beatriz','da Silva Santos Barros',NULL,NULL,1,'2024-05-23 12:32:01','2024-05-23 12:32:01',1);
/*!40000 ALTER TABLE `aluno` ENABLE KEYS */;
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
