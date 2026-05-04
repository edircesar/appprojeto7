CREATE DATABASE IF NOT EXISTS `projeto7_apphub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `projeto7_apphub`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `plan` ENUM('free', 'premium') DEFAULT 'free',
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `is_admin` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `apps` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(100) NOT NULL, -- e.g. material icon name
  `category` VARCHAR(50),
  `is_public` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL UNIQUE,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `usage_limits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `app_id` INT NOT NULL,
  `usage_count` INT DEFAULT 0,
  `last_used` DATETIME,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`app_id`) REFERENCES `apps`(`id`) ON DELETE CASCADE
);

-- Insert dummy apps
INSERT INTO `apps` (`name`, `description`, `url`, `icon`, `category`, `is_public`) VALUES
('PDF Compressor', 'Compress your PDF files easily.', 'https://app1.projeto7.com/compress', 'picture_as_pdf', 'PDF', 1),
('Image Editor', 'Edit images online quickly.', 'https://app2.projeto7.com/edit', 'image', 'Imagem', 1),
('Video Converter', 'Convert videos to multiple formats.', 'https://app3.projeto7.com/convert', 'videocam', 'Video', 0),
('Code Formatter', 'Format and beautify your code.', 'https://app4.projeto7.com/format', 'code', 'Code', 0);

-- Insert admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `is_admin`, `plan`) VALUES
('Admin', 'admin@projeto7.com', '$2y$10$N1lFDojfz6JWgMO0RPiuPOL8r4cYHZaj9i5Vt7SmaSRfsk/34FpMC', 1, 'premium');
