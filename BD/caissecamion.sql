-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 12:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `caissecamion`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `postnom` text NOT NULL,
  `pwd` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nom`, `postnom`, `pwd`) VALUES
(1, 'KAMBERE', 'KAMWIRA', '1234'),
(2, 'KOMBI', 'GLAD', '1234'),
(3, 'Michelinne', 'kavira', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `categoried`
--

CREATE TABLE `categoried` (
  `codeCat` int(11) NOT NULL,
  `categorie` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categoried`
--

INSERT INTO `categoried` (`codeCat`, `categorie`) VALUES
(1, 'legere'),
(2, 'lourde'),
(3, 'Michelinne');

-- --------------------------------------------------------

--
-- Table structure for table `categoriet`
--

CREATE TABLE `categoriet` (
  `codeCat` int(11) NOT NULL,
  `categorie` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categoriet`
--

INSERT INTO `categoriet` (`codeCat`, `categorie`) VALUES
(2, 'chauffeur'),
(7, 'aide chauffeur');

-- --------------------------------------------------------

--
-- Table structure for table `depenses`
--

CREATE TABLE `depenses` (
  `id` int(11) NOT NULL,
  `libelle` text NOT NULL,
  `dateD` date NOT NULL,
  `montant` float NOT NULL,
  `details` text NOT NULL,
  `codeT` int(11) NOT NULL,
  `codeCatDepanse` int(11) NOT NULL,
  `statut` int(11) NOT NULL,
  `etat` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `depenses`
--

INSERT INTO `depenses` (`id`, `libelle`, `dateD`, `montant`, `details`, `codeT`, `codeCatDepanse`, `statut`, `etat`) VALUES
(1, 'ACHAT BRIDE', '2025-06-01', 10, 'GLAD', 1, 1, 0, 1),
(2, 'ACHAT BRIDE', '2025-06-01', 10, 'GLAD', 2, 2, 0, 1),
(3, 'Achat bride', '2025-06-01', 10, 'Aprouver par Glad', 1, 2, 0, 1),
(4, 'Soudure parchoc', '2025-06-01', 5, 'Aprouver par Glad', 1, 1, 0, 1),
(5, 'Renouvellement pneu', '2025-06-01', 100, 'Aprouver par Glad', 2, 1, 0, 1),
(6, 'Renouvellement couvre volant', '2025-06-01', 2, 'Aprouver par Glad', 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payement`
--

CREATE TABLE `payement` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `detail` text NOT NULL,
  `dateD` date NOT NULL,
  `dateF` date NOT NULL,
  `aideChauf` float NOT NULL,
  `idchauffeur` int(11) NOT NULL,
  `montant` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payement`
--

INSERT INTO `payement` (`id`, `date`, `detail`, `dateD`, `dateF`, `aideChauf`, `idchauffeur`, `montant`) VALUES
(1, '2025-06-02', 'Paiement mensuel', '2025-05-15', '2025-06-01', 39.2, 1, 78.4);

-- --------------------------------------------------------

--
-- Table structure for table `recette`
--

CREATE TABLE `recette` (
  `id` int(11) NOT NULL,
  `idT` int(11) NOT NULL,
  `dateR` date NOT NULL,
  `montant` float NOT NULL,
  `libelle` text NOT NULL,
  `statut` int(11) NOT NULL,
  `etat` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `recette`
--

INSERT INTO `recette` (`id`, `idT`, `dateR`, `montant`, `libelle`, `statut`, `etat`) VALUES
(1, 1, '2025-06-01', 234, 'RECETTE', 0, 1),
(2, 2, '2025-06-01', 235, 'RECETTE', 0, 1),
(3, 1, '2025-06-01', 98, 'RECETTE', 0, 1),
(4, 2, '2025-06-01', 100, 'RECETTE', 0, 1),
(5, 1, '2025-06-01', 235, 'Recette journalier', 0, 1),
(6, 1, '2025-06-01', 234, 'Recette 2 jours', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `travailleur`
--

CREATE TABLE `travailleur` (
  `id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `postnom` text NOT NULL,
  `numTel` text NOT NULL,
  `residence` text NOT NULL,
  `matricule` text NOT NULL,
  `codeCat` int(11) NOT NULL,
  `codeV` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `travailleur`
--

INSERT INTO `travailleur` (`id`, `nom`, `postnom`, `numTel`, `residence`, `matricule`, `codeCat`, `codeV`) VALUES
(1, 'Eugene', 'Kambale', '0987456345', 'NJIAPANDA', 'M001', 2, 1),
(2, 'Muhindo', 'Wasi', '0813345678', 'BUTEMBO', 'M002', 2, 2),
(3, 'Olivier', 'Kalubenge', '0987564554', 'BWINONGO', 'M003', 7, 2),
(4, 'Eugene', 'Mbusa', '0986857645', 'BWINONGO', 'M004', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicule`
--

CREATE TABLE `vehicule` (
  `id` int(11) NOT NULL,
  `numP` text NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vehicule`
--

INSERT INTO `vehicule` (`id`, `numP`, `description`) VALUES
(1, 'AC4356 A19', 'FUSO'),
(2, 'AC4356 A19', 'MIGNONE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categoried`
--
ALTER TABLE `categoried`
  ADD PRIMARY KEY (`codeCat`);

--
-- Indexes for table `categoriet`
--
ALTER TABLE `categoriet`
  ADD PRIMARY KEY (`codeCat`);

--
-- Indexes for table `depenses`
--
ALTER TABLE `depenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payement`
--
ALTER TABLE `payement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recette`
--
ALTER TABLE `recette`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travailleur`
--
ALTER TABLE `travailleur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categoried`
--
ALTER TABLE `categoried`
  MODIFY `codeCat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categoriet`
--
ALTER TABLE `categoriet`
  MODIFY `codeCat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `depenses`
--
ALTER TABLE `depenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payement`
--
ALTER TABLE `payement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recette`
--
ALTER TABLE `recette`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `travailleur`
--
ALTER TABLE `travailleur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
