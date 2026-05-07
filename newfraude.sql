-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 07 mai 2026 à 15:47
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fraude_assurance`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('agent','super_admin') NOT NULL DEFAULT 'agent',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'Super Admin', 'admin@fraude.ci', '$2y$10$exampleHashedPasswordHere', 'super_admin', '2026-05-07 13:41:06');

-- --------------------------------------------------------

--
-- Structure de la table `alertes`
--

CREATE TABLE `alertes` (
  `id` int(11) NOT NULL,
  `sinistre_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type_alerte` varchar(80) DEFAULT NULL,
  `score_declencheur` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('en_attente','en_cours','resolue') DEFAULT 'en_attente',
  `traite_par` int(11) DEFAULT NULL,
  `date_alerte` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `preuves`
--

CREATE TABLE `preuves` (
  `id` int(11) NOT NULL,
  `sinistre_id` int(11) NOT NULL,
  `nom_fichier` varchar(200) DEFAULT NULL,
  `chemin_fichier` varchar(300) DEFAULT NULL,
  `type_fichier` varchar(20) DEFAULT NULL,
  `date_exif` datetime DEFAULT NULL,
  `gps_exif` varchar(100) DEFAULT NULL,
  `score_metadata` int(11) DEFAULT 0,
  `date_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sinistres`
--

CREATE TABLE `sinistres` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type_sinistre` varchar(80) NOT NULL,
  `date_sinistre` date NOT NULL,
  `date_declaration` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `montant_estime` decimal(15,2) DEFAULT NULL,
  `localisation` varchar(200) DEFAULT NULL,
  `score_sinistre` int(11) DEFAULT 0,
  `statut` enum('normal','douteux','frauduleux') DEFAULT 'normal',
  `commentaire_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sinistres`
--

INSERT INTO `sinistres` (`id`, `utilisateur_id`, `type_sinistre`, `date_sinistre`, `date_declaration`, `description`, `montant_estime`, `localisation`, `score_sinistre`, `statut`, `commentaire_admin`) VALUES
(1, 1, 'Accident automobile', '2026-04-20', '2026-05-07 13:41:06', 'Collision sur l autoroute du nord, véhicule détruit', 3500000.00, 'Abidjan, Autoroute du Nord', 45, 'douteux', NULL),
(2, 2, 'Vol de moto', '2026-04-28', '2026-05-07 13:41:06', 'Moto disparue devant le marché, urgent besoin remboursement', 1200000.00, 'Adjamé, Marché', 70, 'douteux', NULL),
(3, 3, 'Incendie domicile', '2026-05-01', '2026-05-07 13:41:06', 'Incendie dans la nuit, tout détruit rapidement', 8000000.00, 'Yopougon', 95, 'frauduleux', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse_ip` varchar(45) DEFAULT NULL,
  `score_global` int(11) DEFAULT 0,
  `statut` enum('normal','douteux','frauduleux') DEFAULT 'normal',
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `adresse_ip`, `score_global`, `statut`, `date_inscription`) VALUES
(1, 'Koné', 'Mamadou', 'mamadou@test.ci', '$2y$10$hash1', '0701000001', '192.168.1.10', 0, 'normal', '2026-05-07 13:41:06'),
(2, 'Traoré', 'Aïcha', 'aicha@test.ci', '$2y$10$hash2', '0702000002', '192.168.1.11', 0, 'normal', '2026-05-07 13:41:06'),
(3, 'Bamba', 'Seydou', 'seydou@test.ci', '$2y$10$hash3', '0703000003', '192.168.1.10', 0, 'normal', '2026-05-07 13:41:06');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `alertes`
--
ALTER TABLE `alertes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sinistre_id` (`sinistre_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `traite_par` (`traite_par`);

--
-- Index pour la table `preuves`
--
ALTER TABLE `preuves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sinistre_id` (`sinistre_id`);

--
-- Index pour la table `sinistres`
--
ALTER TABLE `sinistres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `alertes`
--
ALTER TABLE `alertes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `preuves`
--
ALTER TABLE `preuves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sinistres`
--
ALTER TABLE `sinistres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `alertes`
--
ALTER TABLE `alertes`
  ADD CONSTRAINT `alertes_ibfk_1` FOREIGN KEY (`sinistre_id`) REFERENCES `sinistres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertes_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertes_ibfk_3` FOREIGN KEY (`traite_par`) REFERENCES `administrateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `preuves`
--
ALTER TABLE `preuves`
  ADD CONSTRAINT `preuves_ibfk_1` FOREIGN KEY (`sinistre_id`) REFERENCES `sinistres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sinistres`
--
ALTER TABLE `sinistres`
  ADD CONSTRAINT `sinistres_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
