-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 04 Juin 2015 à 12:52
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `mygames`
--

-- --------------------------------------------------------

--
-- Structure de la table `mg_cards`
--

CREATE TABLE IF NOT EXISTS `mg_cards` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `word1` varchar(100) NOT NULL,
  `word2` varchar(100) NOT NULL,
  `word3` varchar(100) NOT NULL,
  `word4` varchar(100) NOT NULL,
  `word5` varchar(100) NOT NULL,
  `word6` varchar(100) NOT NULL,
  `word7` varchar(100) NOT NULL,
  `word8` varchar(100) NOT NULL,
  `word9` varchar(100) NOT NULL,
  `word10` varchar(100) NOT NULL,
  `word11` varchar(100) NOT NULL,
  `word12` varchar(100) NOT NULL,
  `mode` int(11) NOT NULL,
  `language` varchar(7) NOT NULL DEFAULT 'fr_FR',
  `difficulty` int(11) NOT NULL DEFAULT '1',
  `category` int(11) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `persons` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mg_connexions`
--

CREATE TABLE IF NOT EXISTS `mg_connexions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `created` date NOT NULL,
  `retry` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mg_games`
--

CREATE TABLE IF NOT EXISTS `mg_games` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rounds` int(11) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `mode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Structure de la table `mg_parts`
--

CREATE TABLE IF NOT EXISTS `mg_parts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) NOT NULL,
  `created` date NOT NULL,
  `password` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `nb_players` int(11) NOT NULL,
  `nb_teams` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `nb_cards` int(11) NOT NULL,
  `nb_rounds` int(11) NOT NULL,
  `round` int(11) NOT NULL DEFAULT '1',
  `next_player` int(11) NOT NULL DEFAULT '1',
  `next_round` int(11) NOT NULL DEFAULT '1',
  `score` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mg_rounds`
--

CREATE TABLE IF NOT EXISTS `mg_rounds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_game` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mg_teams`
--

CREATE TABLE IF NOT EXISTS `mg_teams` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `points1` int(11) NOT NULL,
  `points2` int(11) NOT NULL,
  `points3` int(11) NOT NULL,
  `points4` int(11) NOT NULL,
  `points5` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mg_users`
--

CREATE TABLE IF NOT EXISTS `mg_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password2` varchar(50) NOT NULL,
  `created` date NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `language` varchar(7) NOT NULL DEFAULT 'fr_FR',
  `sound` int(11) NOT NULL DEFAULT '1',
  `nb_parts` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;




INSERT INTO `mg_games` (`id`, `name`, `rounds`, `picture`, `mode`) VALUES
(1, 'Time''s up', 4, '', 1),
(2, 'Time''s up extended', 5, '', 1),
(3, 'Taboo', 3, '', 3),
(4, 'Brainstorm', 3, '', 2);
(5, 'Loup Garous', 1, '', 4);
(6, 'Petits meurtres', 2, '', 6);
(7, 'Pictionary', 2, '', 7);
(8, 'Animaux', 3, '', 8);

INSERT INTO `mg_rounds` (`id`, `id_game`, `name`, `order`) VALUES
(1, 1, 'Discovered', 1),
(2, 1, '1 Word', 2),
(3, 1, 'Movements', 3),
(4, 2, 'Discovered', 1),
(5, 2, '1 Word', 2),
(6, 2, 'Movements', 3),
(7, 2, 'Pose', 4),
(8, 1, 'Remove', 0),
(9, 2, 'Remove', 0);

INSERT INTO `mg_users` (`id`, `email`, `password`, `password2`, `created`, `role`, `language`, `sound`) VALUES
(1, 'admin', '', 'admin', '2015-06-01', 'admin', 'fr_FR', 1);
