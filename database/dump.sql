-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: teste
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
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` int(11) NOT NULL,
  `origin` enum('internal','external') NOT NULL DEFAULT 'internal',
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `article_ibfk_4` (`event`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`status`) REFERENCES `article_status` (`id`),
  CONSTRAINT `article_ibfk_4` FOREIGN KEY (`event`) REFERENCES `event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` (`id`, `event`, `origin`, `title`, `summary`, `keywords`, `status`, `created_at`) VALUES (294,12,'internal','teste','teste','teste; teste; teste',2,'2026-10-15 02:27:39');
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_advisors`
--

DROP TABLE IF EXISTS `article_advisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_advisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `is_coadvisor` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article` (`article`),
  KEY `user` (`user`),
  CONSTRAINT `article_advisors_ibfk_1` FOREIGN KEY (`article`) REFERENCES `article` (`id`),
  CONSTRAINT `article_advisors_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_advisors`
--

LOCK TABLES `article_advisors` WRITE;
/*!40000 ALTER TABLE `article_advisors` DISABLE KEYS */;
INSERT INTO `article_advisors` (`id`, `article`, `user`, `is_coadvisor`, `created_at`) VALUES (81,294,42,0,'2026-10-15 02:27:50');
/*!40000 ALTER TABLE `article_advisors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_authors`
--

DROP TABLE IF EXISTS `article_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `course` int(11) DEFAULT NULL,
  `schooling` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article` (`article`),
  KEY `user` (`user`),
  KEY `course` (`course`),
  KEY `schooling` (`schooling`),
  CONSTRAINT `article_authors_ibfk_1` FOREIGN KEY (`article`) REFERENCES `article` (`id`),
  CONSTRAINT `article_authors_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `article_authors_ibfk_3` FOREIGN KEY (`course`) REFERENCES `course` (`id`),
  CONSTRAINT `article_authors_ibfk_4` FOREIGN KEY (`schooling`) REFERENCES `schooling` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_authors`
--

LOCK TABLES `article_authors` WRITE;
/*!40000 ALTER TABLE `article_authors` DISABLE KEYS */;
INSERT INTO `article_authors` (`id`, `article`, `user`, `course`, `schooling`, `created_at`) VALUES (267,294,97,1,NULL,'2026-10-15 02:27:39');
/*!40000 ALTER TABLE `article_authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_comments`
--

DROP TABLE IF EXISTS `article_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `article` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `article` (`article`),
  CONSTRAINT `article_comments_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `article_comments_ibfk_2` FOREIGN KEY (`article`) REFERENCES `article` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_comments`
--

LOCK TABLES `article_comments` WRITE;
/*!40000 ALTER TABLE `article_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_external_participants`
--

DROP TABLE IF EXISTS `article_external_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_external_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `type` enum('advisor','co_advisor','author') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article` (`article`),
  CONSTRAINT `article_external_participants_ibfk_1` FOREIGN KEY (`article`) REFERENCES `article` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_external_participants`
--

LOCK TABLES `article_external_participants` WRITE;
/*!40000 ALTER TABLE `article_external_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_external_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_references`
--

DROP TABLE IF EXISTS `article_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` int(11) NOT NULL,
  `reference` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article` (`article`),
  CONSTRAINT `article_references_ibfk_1` FOREIGN KEY (`article`) REFERENCES `article` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_references`
--

LOCK TABLES `article_references` WRITE;
/*!40000 ALTER TABLE `article_references` DISABLE KEYS */;
INSERT INTO `article_references` (`id`, `article`, `reference`, `created_at`) VALUES (114,294,'testse','2026-10-15 02:28:04');
/*!40000 ALTER TABLE `article_references` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_status`
--

DROP TABLE IF EXISTS `article_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_status`
--

LOCK TABLES `article_status` WRITE;
/*!40000 ALTER TABLE `article_status` DISABLE KEYS */;
INSERT INTO `article_status` (`id`, `name`, `description`, `created_at`) VALUES (1,'in_submission','Estado do artigo no qual o autor está submetendo','2024-04-25 11:50:53'),(2,'in_revision','Estado do artigo no qual o autor submeteu e o revisor deve fazer a revisão','2024-04-26 15:02:22'),(3,'in_correction','Estado do artigo no qual o autor está corrigindo','2024-04-26 15:02:22'),(4,'approved','Estado do artigo no qual ele está aprovado e disponível para o administrador','2024-04-26 15:02:22'),(5,'finished','Estado do artigo no qual o administrador finalizou o artigo','2024-04-26 17:25:28'),(6,'disapproved','Estado do artigo no qual a administração reprovou a submissão','2026-09-07 20:39:31');
/*!40000 ALTER TABLE `article_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` (`id`, `name`, `description`, `image`, `status`, `created_at`) VALUES (1,'Engenharia de software','Curso de tecnologia',NULL,1,'2024-04-25 11:50:44'),(2,'Administração','Curso de gerencia',NULL,1,'2024-04-25 11:50:45'),(3,'Fisioterapia','Curso da área da saúde',NULL,1,'2024-04-25 11:50:45'),(5,'Direito','Curso de direito',NULL,1,'2024-08-09 13:54:00'),(6,'Medicina','Curso da área da saúde',NULL,1,'2024-08-09 13:54:59'),(7,'Ciências Contábeis','Curso especializado em fazer Imposto de renda.',NULL,1,'2024-10-08 22:32:32');
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `number_words` int(11) DEFAULT NULL,
  `number_keywords` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`id`, `name`, `start`, `end`, `number_words`, `number_keywords`, `instructions`, `status`, `created_at`) VALUES (12,'Evento teste Unifae - Demo Maco','2024-09-01 00:00:00','2029-10-10 00:00:00',350,5,'INSTRUÇÕES GERAIS:\n- O resumo NÃO deve conter: citações, referências, abreviaturas, quadros, tabelas ou figuras, exceto nos relatos de caso que devem conter introdução, exposição do caso e conclusão.\n- No método deve ser informado se o trabalho possui aprovação do\ncomitê de ética. Informar apenas o número (CAAE ou registro na CEUA\nou PROSPERO), caso se aplique ao trabalho submetido.\n- Palavras-chave/descritores: Todo resumo deve ser acompanhado de\npalavras-chave/descritores (3 a 5 palavras). Os descritores são palavras fundamentais que auxiliam na indexação dos artigos embases de dados nacionais e internacionais.\n- Todo resumo deve ser acompanhado das três principais referências\nutilizadas. \n- É obrigatório informar o(s) nome(s) completo(s) do(s) aluno(s),\ndo(s) professor(es) orientador(es), e-mail institucional e o curso\nde graduação, técnico ou profissionalizante.\n- Não serão aceitos resumos enviados sem o nome do professor\norientador.\n- Serão aceitos trabalhos originais quanti ou qualitativos, estudos de caso, revisões narrativas, integrativas e sistemáticas com ou semmetanálise e relatos de experiência. Os resumos submetidos não podem ter sido previamente publicados.\n- Não serão aceitos projetos e nem protocolos de estudos.\n- O título original do trabalho deverá ser mantido desde a submissãoaté a apresentação. Alterações posteriores implicarão em desclassificação do trabalho.\n- Verifique o correto e-mail de submissão. Uma vez submetido, o resumo não poderá ser editado. Será considerada sempre a primeira versão.\n- Não será permitida a inclusão de novos autores, ou substituição,\ndepois de do envio do e-mail.\n- Os resumos que não estiverem em conformidade com as regras de\nsubmissão, serão automaticamente reprovados.',1,'2024-09-25 01:08:45');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`log`)),
  `area` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1503 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` (`id`, `log`, `area`, `created_at`) VALUES (1481,'{\"user_id\":\"84\",\"action\":\"Vincula\\u00e7\\u00e3o de curso no usu\\u00e1rio\",\"body\":{\"user\":\"84\",\"course\":1}}','Usuário','2026-10-15 02:25:32'),(1482,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":84,\"name\":\"Admin\",\"cpf\":\"307.271.370-77\",\"ra\":\"43564-5\",\"email\":\"admin@email.com\",\"password\":\"\",\"role\":1,\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:25:34'),(1483,'{\"user_id\":\"84\",\"action\":\"Vincula\\u00e7\\u00e3o de curso no usu\\u00e1rio\",\"body\":{\"user\":\"1\",\"course\":1}}','Usuário','2026-10-15 02:25:45'),(1484,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":1,\"name\":\"Jo\\u00e3o Victor Cordeiro\",\"cpf\":\"001.234.567-89\",\"ra\":\"27099-1\",\"email\":\"cordeiro@gmail.com\",\"password\":\"\",\"role\":3,\"status\":1,\"schooling\":null,\"isUnifaeStudent\":true}}','Usuário','2026-10-15 02:25:47'),(1485,'{\"user_id\":\"84\",\"action\":\"Vincula\\u00e7\\u00e3o de curso no usu\\u00e1rio\",\"body\":{\"user\":\"97\",\"course\":1}}','Usuário','2026-10-15 02:26:10'),(1486,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":97,\"name\":\"Aluno Interno\",\"cpf\":\"875.937.590-67\",\"ra\":\"12315-3\",\"email\":\"interno@email.com\",\"password\":\"\",\"role\":3,\"status\":1,\"schooling\":null,\"isUnifaeStudent\":true}}','Usuário','2026-10-15 02:26:13'),(1487,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":1,\"name\":\"Jo\\u00e3o Victor Cordeiro\",\"cpf\":\"001.234.567-89\",\"ra\":\"27099-1\",\"email\":\"cordeiro@gmail.com\",\"password\":\"\",\"role\":\"1\",\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:26:45'),(1488,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":1,\"name\":\"Jo\\u00e3o Victor Cordeiro\",\"cpf\":\"001.234.567-89\",\"ra\":\"27099-1\",\"email\":\"cordeiro@gmail.com\",\"password\":\"\",\"role\":1,\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:26:56'),(1489,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":42,\"name\":\"Henrique Magnoli Master\",\"cpf\":\"123.456.546-54\",\"ra\":\"21232-1\",\"email\":\"henriquemagnoli@hotmail.com\",\"password\":\"\",\"role\":\"1\",\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:27:11'),(1490,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":42,\"name\":\"Henrique Magnoli Master\",\"cpf\":\"123.456.546-54\",\"ra\":\"21232-1\",\"email\":\"henriquemagnoli@hotmail.com\",\"password\":\"\",\"role\":\"2\",\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:27:14'),(1491,'{\"user_id\":\"84\",\"action\":\"Vincula\\u00e7\\u00e3o de curso no usu\\u00e1rio\",\"body\":{\"user\":\"42\",\"course\":1}}','Usuário','2026-10-15 02:27:16'),(1492,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":42,\"name\":\"Henrique Magnoli Master\",\"cpf\":\"123.456.546-54\",\"ra\":\"21232-1\",\"email\":\"henriquemagnoli@hotmail.com\",\"password\":\"\",\"role\":2,\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:27:19'),(1493,'{\"user_id\":\"97\",\"action\":\"Adi\\u00e7\\u00e3o de artigo\",\"body\":{\"event\":\"12\",\"title\":\" \",\"keywords\":\" \",\"summary\":\" \",\"status\":1}}','Artigo','2026-10-15 02:27:39'),(1494,'{\"user_id\":\"97\",\"action\":\"Adi\\u00e7\\u00e3o de revisor\",\"body\":{\"article\":\"294\",\"advisor\":42,\"coadvisor\":0}}','Artigo','2026-10-15 02:27:50'),(1495,'{\"user_id\":\"97\",\"action\":\"Edi\\u00e7\\u00e3o de palavras chaves\",\"body\":{\"article\":\"294\",\"keywords\":\"teste\"}}','Artigo','2026-10-15 02:27:53'),(1496,'{\"user_id\":\"97\",\"action\":\"Edi\\u00e7\\u00e3o de palavras chaves\",\"body\":{\"article\":\"294\",\"keywords\":\"teste; teste\"}}','Artigo','2026-10-15 02:27:56'),(1497,'{\"user_id\":\"97\",\"action\":\"Edi\\u00e7\\u00e3o de palavras chaves\",\"body\":{\"article\":\"294\",\"keywords\":\"teste; teste; teste\"}}','Artigo','2026-10-15 02:28:01'),(1498,'{\"user_id\":\"97\",\"action\":\"Adi\\u00e7\\u00e3o de refer\\u00eancias\",\"body\":{\"article\":\"294\",\"reference\":\"testse\"}}','Artigo','2026-10-15 02:28:04'),(1499,'{\"user_id\":\"97\",\"action\":\"Edi\\u00e7\\u00e3o do artigo\",\"body\":{\"article\":\"294\",\"title\":\"teste\",\"summary\":\"teste\"}}','Artigo','2026-10-15 02:28:05'),(1500,'{\"user_id\":\"97\",\"action\":\"Edi\\u00e7\\u00e3o de status de artigo\",\"body\":{\"article\":\"294\",\"status\":2}}','Artigo','2026-10-15 02:28:09'),(1501,'{\"user_id\":\"\",\"action\":\"Cadastro de usu\\u00e1rio\",\"body\":{\"name\":\"Nicollas Trevisan\",\"cpf\":\"624.978.000-90\",\"ra\":\"29641-4\",\"email\":\"nicollas@email.com\",\"role\":3,\"password\":\"123123123\",\"course\":1,\"schooling\":null,\"isUnifaeStudent\":true}}','Usuário','2026-10-15 02:35:24'),(1502,'{\"user_id\":\"84\",\"action\":\"Edi\\u00e7\\u00e3o de usu\\u00e1rio\",\"body\":{\"id\":108,\"name\":\"Nicollas Trevisan\",\"cpf\":\"624.978.000-90\",\"ra\":\"29641-4\",\"email\":\"nicollas@email.com\",\"password\":\"\",\"role\":\"1\",\"status\":1,\"schooling\":null,\"isUnifaeStudent\":false}}','Usuário','2026-10-15 02:35:32');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `description`, `created_at`) VALUES (1,'Admin','2024-04-25 11:50:47'),(2,'Advisor','2024-04-25 11:50:47'),(3,'Author','2024-04-25 11:50:48'),(4,'External','2024-04-25 11:50:48');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schooling`
--

DROP TABLE IF EXISTS `schooling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schooling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schooling`
--

LOCK TABLES `schooling` WRITE;
/*!40000 ALTER TABLE `schooling` DISABLE KEYS */;
INSERT INTO `schooling` (`id`, `name`, `status`, `created_at`) VALUES (1,'Ensino Médio',1,'2024-04-25 11:50:45'),(2,'Ensino Técnico e Profissionalizante',1,'2024-04-25 11:50:45'),(3,'Tecnólogo',1,'2024-04-25 11:50:45'),(4,'Bacharelado e Licenciatura',1,'2024-04-25 11:50:45'),(5,'Pós-Graduação',1,'2024-04-25 11:50:45');
/*!40000 ALTER TABLE `schooling` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `cpf` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ra` varchar(255) DEFAULT NULL,
  `schooling` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `ra` (`ra`),
  KEY `role` (`role`),
  KEY `schooling` (`schooling`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`),
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`schooling`) REFERENCES `schooling` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `name`, `cpf`, `email`, `ra`, `schooling`, `password`, `role`, `status`, `created_at`) VALUES (1,'João Victor Cordeiro','00123456789','cordeiro@gmail.com','27099-1',NULL,'$2y$10$HCs.G0pZXHt6w/Goi6An2OhhSMsZqK65J78ZeJrslxaLcmwO1ZDMC',1,1,'2024-04-25 11:50:49'),(42,'Henrique Magnoli Master','12345654654','henriquemagnoli@hotmail.com','21232-1',NULL,'$2y$10$Km2EFl0X.uutu8RN47IoP.YnulsF4pGuFupmiKI85vKIiCh.1DiUy',2,1,'2024-09-25 01:12:04'),(75,'Aluno Externo','08005794029','externo@email.com','',3,'$2y$10$vbzPg7dvCQsg4.VPNKrktuS.T3qhk9PMZlYiCbNvnX2aAmkTvDsze',4,1,'2026-01-15 05:12:33'),(84,'Admin','30727137077','admin@email.com','43564-5',NULL,'$2y$10$vbzPg7dvCQsg4.VPNKrktuS.T3qhk9PMZlYiCbNvnX2aAmkTvDsze',1,1,'2026-01-15 05:43:27'),(97,'Aluno Interno','87593759067','interno@email.com','12315-3',NULL,'$2y$10$vbzPg7dvCQsg4.VPNKrktuS.T3qhk9PMZlYiCbNvnX2aAmkTvDsze',3,1,'2026-01-15 05:57:14'),(108,'Nicollas Trevisan','62497800090','nicollas@email.com','29641-4',NULL,'$2y$10$vgAIb/sJm9w42t4KhOHsiei7FooJ/0VpoQoUF/2F7lo6nEu9HSSoS',1,1,'2026-10-15 02:35:24');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_course`
--

DROP TABLE IF EXISTS `user_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `course` (`course`),
  CONSTRAINT `user_course_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `user_course_ibfk_2` FOREIGN KEY (`course`) REFERENCES `course` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_course`
--

LOCK TABLES `user_course` WRITE;
/*!40000 ALTER TABLE `user_course` DISABLE KEYS */;
INSERT INTO `user_course` (`id`, `user`, `course`, `created_at`) VALUES (67,84,1,'2026-10-15 02:25:32'),(68,1,1,'2026-10-15 02:25:45'),(69,97,1,'2026-10-15 02:26:10'),(70,42,1,'2026-10-15 02:27:16'),(71,108,1,'2026-10-15 02:35:24');
/*!40000 ALTER TABLE `user_course` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-14 23:54:21
