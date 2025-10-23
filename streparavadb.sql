CREATE DATABASE  IF NOT EXISTS `streparavadb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `streparavadb`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: streparavadb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `armario`
--

DROP TABLE IF EXISTS `armario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `armario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ferramenta` int(11) DEFAULT NULL,
  `linha` varchar(45) DEFAULT NULL,
  `qtd_estoque` varchar(45) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_armario_ferramentas_idx` (`id_ferramenta`),
  KEY `fk_armario_ferramentas_nome_idx` (`linha`),
  CONSTRAINT `fk_armarioferramenta` FOREIGN KEY (`id_ferramenta`) REFERENCES `ferramentas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `armario`
--

LOCK TABLES `armario` WRITE;
/*!40000 ALTER TABLE `armario` DISABLE KEYS */;
INSERT INTO `armario` VALUES (1,8,'1','18','manha','Broca'),(2,8,'1','3','tarde','Broca'),(3,8,'1','1','noite','Broca'),(4,8,'2','10','Manhã','Broca'),(5,8,'2','10','Tarde','Broca'),(6,8,'2','10','Noite','Broca'),(7,9,'4','1','Manhã','Pastilha'),(8,8,'4','1','Tarde','Broca'),(9,9,'1','1','Manhã','Pastilha'),(10,8,'3','1','Manhã','Broca'),(11,8,'5','1','Noite','Broca'),(12,12,'1','1','Noite','Martelo');
/*!40000 ALTER TABLE `armario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ferramentas`
--

DROP TABLE IF EXISTS `ferramentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferramentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `descricao` varchar(45) DEFAULT NULL,
  `vida_util` varchar(45) DEFAULT NULL,
  `qtd_estoque` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ferramentas`
--

LOCK TABLES `ferramentas` WRITE;
/*!40000 ALTER TABLE `ferramentas` DISABLE KEYS */;
INSERT INTO `ferramentas` VALUES (8,'Broca','Fura','5 dias',100),(9,'Pastilha','Freia','1 dias',100),(10,'Trena','Mede','2 meses',100),(12,'Martelo','Bate','10 anos',19);
/*!40000 ALTER TABLE `ferramentas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) DEFAULT NULL,
  `senha` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (2,'Leal','202cb962ac59075b964b07152d234b70');
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

-- Dump completed on 2025-10-23 15:53:56
