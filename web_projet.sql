-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 04 Avril 2016 à 21:38
-- Version du serveur :  10.1.9-MariaDB
-- Version de PHP :  5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `web_projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `annexe`
--

CREATE TABLE `annexe` (
  `idAnnexe` int(10) NOT NULL,
  `nomAnnexe` varchar(100) NOT NULL,
  `urlAnnexe` varchar(1000) NOT NULL,
  `idProjetAn` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `annexe`
--

INSERT INTO `annexe` (`idAnnexe`, `nomAnnexe`, `urlAnnexe`, `idProjetAn`) VALUES
(1, 'Sujets 2018', 'Sujets_2018.pdf', 2),
(2, 'Sujets', 'Projets Transpromotions.pdf', 3);

-- --------------------------------------------------------

--
-- Structure de la table `appartient`
--

CREATE TABLE `appartient` (
  `loginEleveAp` varchar(20) NOT NULL,
  `idGroupeAp` int(10) NOT NULL,
  `admin` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `appartient`
--

INSERT INTO `appartient` (`loginEleveAp`, `idGroupeAp`, `admin`) VALUES
('bblaireau', 1, 2),
('bcerf', 2, 0),
('bcerf', 4, 2),
('belephant', 2, 2),
('blapin', 1, 0),
('blapin', 4, 0),
('bpanthere', 5, 2),
('jcriquet', 6, 2),
('llion', 3, 0),
('llion', 5, 0),
('mvache', 1, 0),
('npoisson', 2, 1),
('smouton', 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

CREATE TABLE `candidature` (
  `idCandidature` int(10) NOT NULL,
  `texte` longtext,
  `etat` int(1) NOT NULL DEFAULT '0',
  `loginEleveCa` varchar(20) NOT NULL,
  `idGroupeCa` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `candidature`
--

INSERT INTO `candidature` (`idCandidature`, `texte`, `etat`, `loginEleveCa`, `idGroupeCa`) VALUES
(1, 'Je suis très doué dans tous les domaines !', 0, 'llion', 1);

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

CREATE TABLE `connexion` (
  `login` varchar(20) NOT NULL,
  `mdp` varchar(20) NOT NULL,
  `statut` varchar(15) NOT NULL DEFAULT 'eleve',
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `anneeEleve` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `connexion`
--

INSERT INTO `connexion` (`login`, `mdp`, `statut`, `nom`, `prenom`, `anneeEleve`) VALUES
('bblaireau', '0', 'eleve', 'BLAIREAU', 'Benjamin', '2A'),
('bcerf', '0', 'eleve', 'CERF', 'Bambi', '1A'),
('belephant', '0', 'eleve', 'ÉLÉPHANT', 'Babar', '2A'),
('blapin', '0', 'eleve', 'LAPIN', 'Bunny', '1A'),
('bpanthere', '0', 'eleve', 'PANTHÈRE', 'Bagheera', '1A'),
('dcanard', '0', 'eleve', 'CANARD', 'Donald', '3A'),
('fchat', '0', 'enseignant', 'CHAT', 'Félix', NULL),
('jcriquet', '0', 'eleve', 'CRIQUET', 'Jiminy', '1A'),
('llion', '0', 'eleve', 'LION', 'Léo', '1A'),
('mabeille', '0', 'enseignant', 'ABEILLE', 'Maya', NULL),
('mchien', '0', 'gestion', 'CHIEN', 'Médor', NULL),
('msouris', '0', 'eleve', 'SOURIS', 'Minnie', '3A'),
('mvache', '0', 'eleve', 'VACHE', 'Marguerite', '2A'),
('npoisson', '0', 'eleve', 'POISSON', 'Némo', '2A'),
('rtatouille', '0', 'client', 'TATOUILLE', 'Ratte', NULL),
('scheval', '0', 'enseignant', 'CHEVAL', 'Spirit', NULL),
('smouton', '0', 'eleve', 'MOUTON', 'Shaun', '1A'),
('wours', '0', 'client', 'OURS', 'Winnie', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `gere`
--

CREATE TABLE `gere` (
  `loginClient` varchar(20) NOT NULL,
  `idGroupeGe` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `gere`
--

INSERT INTO `gere` (`loginClient`, `idGroupeGe`) VALUES
('mabeille', 2),
('rtatouille', 2);

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

CREATE TABLE `groupe` (
  `idGroupe` int(10) NOT NULL,
  `nomGroupe` varchar(100) NOT NULL,
  `description` text,
  `validation` int(1) NOT NULL DEFAULT '0',
  `anneeCandid` varchar(6) DEFAULT NULL,
  `idProjetGr` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `groupe`
--

INSERT INTO `groupe` (`idGroupe`, `nomGroupe`, `description`, `validation`, `anneeCandid`, `idProjetGr`) VALUES
(1, 'Le noir et le blanc', 'Un transpromo pour en voir de toutes les couleurs !', 0, '1A2A', 3),
(2, 'Les bulles', '', 2, '1A', 3),
(3, 'Shaun & Léo', '', 0, '', 4),
(4, 'Bunny et Bambi', 'Nous sommes les meilleurs !', 1, '', 4),
(5, 'Bagheera et Léo', '', 0, '1A', 5),
(6, 'Jiminy', '', 0, '1A', 5);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `idMessage` int(10) NOT NULL,
  `loginEnvoi` varchar(20) NOT NULL,
  `loginRecoi` varchar(20) NOT NULL,
  `sujet` text NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `supprEnvoi` tinyint(1) NOT NULL DEFAULT '0',
  `supprRecoi` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

CREATE TABLE `module` (
  `idModule` int(10) NOT NULL,
  `nomModule` varchar(100) NOT NULL,
  `anneeModule` varchar(6) NOT NULL DEFAULT '1A',
  `loginEnseiResp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `module`
--

INSERT INTO `module` (`idModule`, `nomModule`, `anneeModule`, `loginEnseiResp`) VALUES
(1, 'INTRODUCTION À LA PROGRAMMATION', '1A', 'mabeille'),
(2, 'GÉNIE LOGICIEL', '2A', 'mabeille'),
(3, 'PROJET TRANSDISCIPLINAIRE', '1A', 'fchat'),
(4, 'PROJET TRANSPROMOTION', '1A2A', 'fchat');

-- --------------------------------------------------------

--
-- Structure de la table `projet`
--

CREATE TABLE `projet` (
  `idProjet` int(10) NOT NULL,
  `nomProjet` varchar(100) NOT NULL,
  `dateLimite` date DEFAULT NULL,
  `duree` int(4) DEFAULT NULL,
  `tailleGpMin` int(3) DEFAULT NULL,
  `tailleGpMax` int(3) DEFAULT NULL,
  `candidature` tinyint(1) NOT NULL DEFAULT '0',
  `creerGp` varchar(6) DEFAULT NULL,
  `idModulePere` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `projet`
--

INSERT INTO `projet` (`idProjet`, `nomProjet`, `dateLimite`, `duree`, `tailleGpMin`, `tailleGpMax`, `candidature`, `creerGp`, `idModulePere`) VALUES
(1, 'Transdi S1', NULL, 16, 3, 5, 0, '1A', 3),
(2, 'Transdi S2', NULL, 16, 3, 5, 0, '1A', 3),
(3, 'Transpromotion', NULL, 16, 7, 8, 1, '2A', 4),
(4, 'Projet T9', NULL, 3, 2, 2, 0, '1A', 1),
(5, 'Projet T7', '2016-05-10', 5, 2, 2, 1, '1A', 1),
(6, 'Projet T8', NULL, 3, 2, 3, 1, '1A', 1);

-- --------------------------------------------------------

--
-- Structure de la table `rendu`
--

CREATE TABLE `rendu` (
  `idRendu` int(10) NOT NULL,
  `nomRendu` varchar(100) NOT NULL,
  `urlRendu` varchar(1000) NOT NULL,
  `idGroupeRe` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rendu`
--

INSERT INTO `rendu` (`idRendu`, `nomRendu`, `urlRendu`, `idGroupeRe`) VALUES
(1, 'Les bulles', 'bulle.jpg', 2),
(2, 'Autre bulle', 'bubulles.jpg', 2);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `annexe`
--
ALTER TABLE `annexe`
  ADD PRIMARY KEY (`idAnnexe`),
  ADD KEY `idProjetAn` (`idProjetAn`);

--
-- Index pour la table `appartient`
--
ALTER TABLE `appartient`
  ADD PRIMARY KEY (`loginEleveAp`,`idGroupeAp`),
  ADD KEY `idGroupeAp` (`idGroupeAp`);

--
-- Index pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`idCandidature`),
  ADD KEY `loginEleveCa` (`loginEleveCa`),
  ADD KEY `idGroupeCa` (`idGroupeCa`);

--
-- Index pour la table `connexion`
--
ALTER TABLE `connexion`
  ADD PRIMARY KEY (`login`);

--
-- Index pour la table `gere`
--
ALTER TABLE `gere`
  ADD PRIMARY KEY (`loginClient`,`idGroupeGe`),
  ADD KEY `idGroupeGe` (`idGroupeGe`);

--
-- Index pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`idGroupe`),
  ADD KEY `idProjetGr` (`idProjetGr`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`idMessage`),
  ADD KEY `loginEnvoi` (`loginEnvoi`),
  ADD KEY `loginRecoi` (`loginRecoi`);

--
-- Index pour la table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`idModule`),
  ADD KEY `loginEnseiResp` (`loginEnseiResp`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`idProjet`),
  ADD KEY `idModulePere` (`idModulePere`);

--
-- Index pour la table `rendu`
--
ALTER TABLE `rendu`
  ADD PRIMARY KEY (`idRendu`),
  ADD KEY `idGroupeRe` (`idGroupeRe`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `annexe`
--
ALTER TABLE `annexe`
  MODIFY `idAnnexe` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `idCandidature` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `groupe`
--
ALTER TABLE `groupe`
  MODIFY `idGroupe` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `idMessage` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `module`
--
ALTER TABLE `module`
  MODIFY `idModule` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `idProjet` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `rendu`
--
ALTER TABLE `rendu`
  MODIFY `idRendu` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `annexe`
--
ALTER TABLE `annexe`
  ADD CONSTRAINT `annexe_ibfk_1` FOREIGN KEY (`idProjetAn`) REFERENCES `projet` (`idProjet`);

--
-- Contraintes pour la table `appartient`
--
ALTER TABLE `appartient`
  ADD CONSTRAINT `appartient_ibfk_1` FOREIGN KEY (`loginEleveAp`) REFERENCES `connexion` (`login`),
  ADD CONSTRAINT `appartient_ibfk_2` FOREIGN KEY (`idGroupeAp`) REFERENCES `groupe` (`idGroupe`);

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`loginEleveCa`) REFERENCES `connexion` (`login`),
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`idGroupeCa`) REFERENCES `groupe` (`idGroupe`);

--
-- Contraintes pour la table `gere`
--
ALTER TABLE `gere`
  ADD CONSTRAINT `gere_ibfk_1` FOREIGN KEY (`loginClient`) REFERENCES `connexion` (`login`),
  ADD CONSTRAINT `gere_ibfk_2` FOREIGN KEY (`idGroupeGe`) REFERENCES `groupe` (`idGroupe`);

--
-- Contraintes pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`idProjetGr`) REFERENCES `projet` (`idProjet`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`loginEnvoi`) REFERENCES `connexion` (`login`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`loginRecoi`) REFERENCES `connexion` (`login`);

--
-- Contraintes pour la table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`loginEnseiResp`) REFERENCES `connexion` (`login`);

--
-- Contraintes pour la table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`idModulePere`) REFERENCES `module` (`idModule`);

--
-- Contraintes pour la table `rendu`
--
ALTER TABLE `rendu`
  ADD CONSTRAINT `rendu_ibfk_1` FOREIGN KEY (`idGroupeRe`) REFERENCES `groupe` (`idGroupe`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
