-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hotel_db
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
-- Table structure for table `hospedes`
--

DROP TABLE IF EXISTS `hospedes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hospedes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `doc_tipo` enum('Cart├úo de Cidad├úo','Passaporte','Outro') NOT NULL,
  `doc_numero` varchar(50) NOT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `estado` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  KEY `utilizador_id` (`utilizador_id`),
  CONSTRAINT `hospedes_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hospedes`
--

LOCK TABLES `hospedes` WRITE;
/*!40000 ALTER TABLE `hospedes` DISABLE KEYS */;
INSERT INTO `hospedes` VALUES (1,3,'Jo├úo Silva','Cart├úo de Cidad├úo','12345678','123456789','912345678','ativo');
/*!40000 ALTER TABLE `hospedes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acao` varchar(80) NOT NULL,
  `descricao` text NOT NULL,
  `utilizador_id` int(11) DEFAULT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `referencia_tipo` varchar(50) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `utilizador_id` (`utilizador_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reserva_id` int(11) NOT NULL,
  `montante` decimal(10,2) NOT NULL,
  `tipo` enum('parcial','total') NOT NULL,
  `metodo` enum('numerario','cartao','transferencia') NOT NULL DEFAULT 'numerario',
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `operador_id` int(11) NOT NULL,
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reserva_id` (`reserva_id`),
  KEY `operador_id` (`operador_id`),
  CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`),
  CONSTRAINT `pagamentos_ibfk_2` FOREIGN KEY (`operador_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos`
--

LOCK TABLES `pagamentos` WRITE;
/*!40000 ALTER TABLE `pagamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quartos`
--

DROP TABLE IF EXISTS `quartos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quartos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) NOT NULL,
  `tipo_quarto_id` int(11) NOT NULL,
  `estado` enum('livre','ocupado') NOT NULL DEFAULT 'livre',
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `tipo_quarto_id` (`tipo_quarto_id`),
  CONSTRAINT `quartos_ibfk_1` FOREIGN KEY (`tipo_quarto_id`) REFERENCES `tipos_quarto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quartos`
--

LOCK TABLES `quartos` WRITE;
/*!40000 ALTER TABLE `quartos` DISABLE KEYS */;
INSERT INTO `quartos` VALUES (1,'101',1,'livre',NULL),(2,'102',1,'livre',NULL),(3,'201',2,'livre',NULL),(4,'202',2,'livre',NULL),(5,'301',3,'livre',NULL),(6,'302',3,'livre',NULL),(7,'401',4,'livre',NULL),(8,'402',4,'livre',NULL);
/*!40000 ALTER TABLE `quartos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hospede_id` int(11) NOT NULL,
  `tipo_quarto_id` int(11) NOT NULL,
  `quarto_id` int(11) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `num_hospedes` int(11) NOT NULL DEFAULT 1,
  `pequeno_almoco` tinyint(1) NOT NULL DEFAULT 0,
  `nif_faturacao` varchar(9) DEFAULT NULL,
  `estado` enum('pendente','ativa','cancelada','concluida') NOT NULL DEFAULT 'pendente',
  `checkin_feito` tinyint(1) NOT NULL DEFAULT 0,
  `checkout_feito` tinyint(1) NOT NULL DEFAULT 0,
  `total_estimado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `hospede_id` (`hospede_id`),
  KEY `tipo_quarto_id` (`tipo_quarto_id`),
  KEY `quarto_id` (`quarto_id`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`hospede_id`) REFERENCES `hospedes` (`id`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`tipo_quarto_id`) REFERENCES `tipos_quarto` (`id`),
  CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`quarto_id`) REFERENCES `quartos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,1,1,NULL,'2026-06-03','2026-06-07',1,0,'','pendente',0,0,240.00,'2026-06-03 20:18:53');
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_quarto`
--

DROP TABLE IF EXISTS `tipos_quarto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_quarto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) NOT NULL,
  `capacidade_base` int(11) NOT NULL,
  `capacidade_maxima` int(11) NOT NULL,
  `preco_diaria` decimal(8,2) NOT NULL,
  `preco_hospede_extra` decimal(8,2) NOT NULL DEFAULT 0.00,
  `preco_pequeno_almoco` decimal(8,2) NOT NULL DEFAULT 0.00,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_quarto`
--

LOCK TABLES `tipos_quarto` WRITE;
/*!40000 ALTER TABLE `tipos_quarto` DISABLE KEYS */;
INSERT INTO `tipos_quarto` VALUES (1,'Single',1,1,60.00,0.00,8.00,'Quarto individual.',1),(2,'Duplo',2,2,90.00,0.00,8.00,'Quarto com duas camas.',1),(3,'Casal',2,2,95.00,0.00,8.00,'Quarto com cama de casal.',1),(4,'Familiar',2,4,130.00,20.00,8.00,'Quarto para fam├¡lias at├® 4 pessoas.',1),(5,'Single',1,1,60.00,0.00,8.00,'Quarto individual.',1),(6,'Duplo',2,2,90.00,0.00,8.00,'Quarto com duas camas.',1),(7,'Casal',2,2,95.00,0.00,8.00,'Quarto com cama de casal.',1),(8,'Familiar',2,4,130.00,20.00,8.00,'Quarto para famÝlias atÚ 4 pessoas.',1);
/*!40000 ALTER TABLE `tipos_quarto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('cliente','rececionista','gestor') NOT NULL DEFAULT 'cliente',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` VALUES (1,'Gestor Admin','gestor@hotel.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gestor',1,'2026-06-03 19:40:58'),(2,'Ana Rececionista','rececionista@hotel.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rececionista',1,'2026-06-03 19:40:58'),(3,'Jo├úo Cliente','joao@email.pt','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente',1,'2026-06-03 19:40:58');
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-03 23:08:19
