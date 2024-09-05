-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: faculdade_fiap
-- ------------------------------------------------------
-- Server version	9.0.1

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
-- Table structure for table `alunos`
--
CREATE DATABASE IF NOT EXISTS faculdade_fiap;
USE faculdade_fiap;
DROP TABLE IF EXISTS `alunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alunos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alunos`
--

LOCK TABLES `alunos` WRITE;
/*!40000 ALTER TABLE `alunos` DISABLE KEYS */;
INSERT INTO `alunos` VALUES (1,'Alice Silva','2000-01-15','alice.silva'),(2,'Alice Silva','2000-01-15','alice.silva'),(3,'Bruno Oliveira','1999-02-20','bruno.oliveira'),(4,'Carla Souza','1998-03-30','carla.souza'),(5,'Diego Costa','2001-04-10','diego.costa'),(6,'Eva Lima','2000-05-25','eva.lima'),(7,'Felipe Martins','1999-06-05','felipe.martins'),(8,'Gabriela Rocha','1998-07-12','gabriela.rocha'),(9,'Hugo Fernandes','2000-08-22','hugo.fernandes'),(10,'Isabela Pereira','2001-09-18','isabela.pereira'),(11,'João Ribeiro','1999-10-30','joao.ribeiro'),(24,'Felipe','1991-05-30',NULL);
/*!40000 ALTER TABLE `alunos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matriculas`
--

DROP TABLE IF EXISTS `matriculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matriculas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `aluno_id` int NOT NULL,
  `turma_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aluno_id` (`aluno_id`,`turma_id`),
  KEY `turma_id` (`turma_id`),
  CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matriculas`
--

LOCK TABLES `matriculas` WRITE;
/*!40000 ALTER TABLE `matriculas` DISABLE KEYS */;
INSERT INTO `matriculas` VALUES (15,1,1),(1,1,10),(2,2,2),(3,3,3),(4,4,4),(5,5,5),(6,6,6),(7,7,7),(8,8,8),(9,9,9),(10,10,10);
/*!40000 ALTER TABLE `matriculas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turmas`
--

DROP TABLE IF EXISTS `turmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turmas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turmas`
--

LOCK TABLES `turmas` WRITE;
/*!40000 ALTER TABLE `turmas` DISABLE KEYS */;
INSERT INTO `turmas` VALUES (1,'Turma A','Turma de Ciência da Computação','Bacharelado'),(2,'Turma B','Turma de Engenharia de Software','Bacharelado'),(3,'Turma C','Turma de Análise e Desenvolvimento de Sistemas','Tecnólogo'),(4,'Turma D','Turma de Redes de Computadores','Tecnólogo'),(5,'Turma E','Turma de Gestão de TI','Tecnólogo'),(6,'Turma F','Turma de Sistemas de Informação','Bacharelado'),(7,'Turma G','Turma de Ciência de Dados','Bacharelado'),(8,'Turma H','Turma de Engenharia de Controle e Automação','Bacharelado'),(9,'Turma I','Turma de Gestão de Projetos de TI','Tecnólogo'),(10,'Turma J','Turma de Segurança da Informação','Bacharelado'),(11,'Turma A','Turma de Ciência da Computação','Bacharelado');
/*!40000 ALTER TABLE `turmas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'Alice Silva','alice','e7d80ffeefa212b7c5c55700e4f7193e'),(3,'Bruno Oliveira','bruno','e7d80ffeefa212b7c5c55700e4f7193e'),(4,'Carla Souza','carla','e7d80ffeefa212b7c5c55700e4f7193e'),(5,'Diego Costa','diego','e7d80ffeefa212b7c5c55700e4f7193e'),(6,'Eva Lima','eva','e7d80ffeefa212b7c5c55700e4f7193e'),(7,'Felipe Martins','felipe','e7d80ffeefa212b7c5c55700e4f7193e'),(8,'Gabriela Rocha','gabriela','e7d80ffeefa212b7c5c55700e4f7193e'),(9,'Hugo Fernandes','hugo','e7d80ffeefa212b7c5c55700e4f7193e'),(10,'Isabela Pereira','isabela','e7d80ffeefa212b7c5c55700e4f7193e'),(11,'Administrador silva','administrador silva','$2y$10$PV8TJeBzbDqffHXTjeXeLuZ3ENrznKQhof4jV3io18mFO.sYxNccK'),(31,'Felipe N','Felipe n','$2y$10$sSt4HLeDqLT/AclRLO7Qaebipqu7FpT4.Ym.TbuUdShM0WarE6Owe'),(32,'Lucas Silva2','Admin2','$2y$10$6Oy5GZop2TGL/EbkTEmLhulAUbQup0KfcK8czUE5PyyyDAFfY6UZK'),(38,'Lucas Silva4','Admin4','$2y$10$LFOubPrupXcCKSsQezivcOg7IPY9ZyhUsp7Fol/9SJi.CbVfmy2Mu'),(39,'Lucas Silva4124','4141241244','$2y$10$5nVVA6DpT/UfhuWD0BRzyeUABuv/imhu9U3QjrrlbwdQ4Js.nAuOy'),(40,'Lucas Silva4124124','4124124','$2y$10$M0CeoUC2WyxjXBy4aaA9wu6lzBw6SuqTLHAdfT9hqQY3Yg5983CCu'),(41,'dasdasdsadsa','dasdsadsadasdsa','$2y$10$vts0abObqfM9aBRTjh0fJeB4XoL9OgmTx947ZttJ3s1bJE1sZAcEq'),(43,'Lucas Silva44124124','412412412412412','$2y$10$aHHNaIe4h30Ngl9RvlITbOqAEWfJZBBl3PMCzt9o08bWJxpD3q04u'),(44,'teste','teste','$2y$10$yxVENe6phmVhebW3allqzO/RVXaB.Y9oe8G5GpJwxsG4..w22aK3S');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-04 23:10:22
