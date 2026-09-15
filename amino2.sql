CREATE DATABASE IF NOT EXISTS `amino2`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `amino2`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comunidades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_criador` INT UNSIGNED NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT NOT NULL,
  `imagem` VARCHAR(255) DEFAULT NULL,
  `background` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comunidades_criador` (`id_criador`),
  CONSTRAINT `fk_comunidades_criador`
    FOREIGN KEY (`id_criador`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `membros_comunidade` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `id_comunidade` INT UNSIGNED NOT NULL,
  `nickname` VARCHAR(100) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `data_entrada` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_membro_comunidade` (`id_usuario`, `id_comunidade`),
  KEY `idx_membros_comunidade` (`id_comunidade`),
  CONSTRAINT `fk_membros_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_membros_comunidade`
    FOREIGN KEY (`id_comunidade`) REFERENCES `comunidades` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `id_comunidade` INT UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `texto` TEXT NOT NULL,
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_posts_usuario` (`id_usuario`),
  KEY `idx_posts_comunidade_data` (`id_comunidade`, `data_criacao`),
  CONSTRAINT `fk_posts_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_posts_comunidade`
    FOREIGN KEY (`id_comunidade`) REFERENCES `comunidades` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_post` INT UNSIGNED NOT NULL,
  `id_usuario` INT UNSIGNED NOT NULL,
  `comentario` TEXT NOT NULL,
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comentarios_post_data` (`id_post`, `data_criacao`),
  KEY `idx_comentarios_usuario` (`id_usuario`),
  CONSTRAINT `fk_comentarios_post`
    FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_comentarios_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `curtidas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `id_post` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_curtida_usuario_post` (`id_usuario`, `id_post`),
  KEY `idx_curtidas_post` (`id_post`),
  CONSTRAINT `fk_curtidas_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_curtidas_post`
    FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;