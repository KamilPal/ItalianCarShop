-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 18, 2024 at 09:52 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projektpai`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `payment` enum('karta','przelew','gotowka') DEFAULT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `vehicle_id`, `name`, `surname`, `email`, `country`, `city`, `address`, `payment`, `purchase_date`) VALUES
(1, 4, 4, 'Aha', 'OdAdmina', 'aha@admin.com', 'Polska', 'Poznań', 'Dworzec', 'przelew', '2024-06-16 20:18:50'),
(2, 7, 13, 'Tescik', 'Tescik', 'emailsednerforprojects24@gmail.com', 'Malediwy', 'XDowo', 'Wielkopolska 38', 'gotowka', '2024-06-16 20:48:23'),
(3, 4, 7, 'Aha', 'OdAdmina', 'aha@admin.com', 'Fajny', 'Aha', '123 Dworzec', 'karta', '2024-06-17 09:38:43');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activation_code` varchar(32) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `password`, `activation_code`, `is_active`, `admin`, `created_at`, `updated_at`) VALUES
(1, 'Test', 'Tes', '4@gmail.com', '$2y$10$1E0EELa/B/HkIhOEDB0pn.CdaFIHI8Tin2Tw.lQnjx6YKlMznyWBO', '5a6f253da999c7755d8b02799db5b5f3', 1, 0, '2024-06-16 19:33:00', '2024-06-17 07:51:30'),
(3, 'test', 'test', 'test@test.com', '$2y$10$ZJ3gSwd0.1W/JsotqGEsUeI99xDvejtD.s3TB80JbzKiPNTAxnNsG', 'c16c08c7d719a359d6fc4535a1eda503', 1, 1, '2024-06-16 19:43:54', '2024-06-16 19:44:29'),
(4, 'Aha', 'OdAdmina', 'aha@admin.com', '$2y$10$kQ/X02ZEBnnhZOgfGD0b3uPWXsHUq8hHNCcZ03fP20p2GV4toOYD.', '', 1, 0, '2024-06-16 19:53:34', '2024-06-16 19:53:34'),
(5, 'test', 'Kocham', 'aha@oh.pl', '$2y$10$QVh5yERFdEHfQFPCn9u5iOTVqVDUBkq/XkYfRxEs7m5QH1z2cAN7C', 'fb7b5bbafd341441afcc168ac55fa274', 0, 0, '2024-06-16 19:57:45', '2024-06-16 19:57:45'),
(6, 'test', 'kocham', 'jakimail@mail.pl', '$2y$10$pu5aXdRoXilvGqgGrdR8.uObVN9kYl7I4bznsSDD3a6WxKT/0zxKu', '', 1, 0, '2024-06-16 19:58:41', '2024-06-16 19:58:41'),
(7, 'Tescik', 'Tescik', 'emailsednerforprojects24@gmail.com', '$2y$10$I9M5HQHF6trezTJ8n.8znuF17Nze9XtpCGJFjBjj7bsoTCSNOUdZe', 'c89844d51fc3e6849bcb45707731cec2', 1, 0, '2024-06-16 20:44:17', '2024-06-16 20:44:47'),
(9, 'test', 'test', '1test@test.test', '$2y$10$f.RWdcTXVcy3aIJZQAZL.eoX0gYVGq0XS0awko.tPPF9BqjA8kXrW', '6dccf5e4a3c11feb4fac449e6995e4b7', 1, 0, '2024-06-17 18:33:01', '2024-06-18 07:17:59');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `brand`, `model`, `year`, `price`, `image`, `description`) VALUES
(1, 'Abarth', '500', 2022, 22000.00, '../images/abarth_500.jpg', 'The Abarth 500 is a sporty version of the iconic city car, known for its lively performance and distinctive styling. It\'s perfect for those who want a fun and practical ride.'),
(2, 'Abarth', '124 Spider', 2022, 32000.00, '../images/abarth_124_spider.jpg', 'The Abarth 124 Spider is a convertible sports car that combines Italian style with open-air thrills. Its turbocharged engine and lightweight design make every drive memorable.'),
(3, 'Alfa Romeo', 'Brera', 2005, 35000.00, '../images/alfa_romeo_brera.jpg', 'The Alfa Romeo Brera is a stylish coupe known for its distinctive design and engaging driving experience.'),
(4, 'Alfa Romeo', 'Giulia', 2023, 40000.00, '../images/alfa_romeo_giulia.jpg', 'The Alfa Romeo Giulia blends Italian elegance with sporty performance. Its sleek design and dynamic driving experience make it a true driver\'s car, perfect for enthusiasts.'),
(5, 'Alfa Romeo', '8C Competizione', 2007, 200000.00, '../images/alfa_romeo_8c_competizione.jpg', 'The Alfa Romeo 8C Competizione is a limited-production sports car that combines stunning design with thrilling performance.'),
(6, 'Alfa Romeo', 'Giulietta', 2016, 25000.00, '../images/alfa_romeo_giulietta.jpg', 'The Alfa Romeo Giulietta is a stylish compact car known for its Italian charm and spirited performance.'),
(7, 'Ferrari', 'Enzo', 2003, 1000000.00, '../images/ferrari_enzo.jpg', 'The Ferrari Enzo is a limited-production supercar, named after the company\'s founder, Enzo Ferrari.'),
(8, 'Ferrari', '458 Italia', 2015, 250000.00, '../images/ferrari_458_italia.jpg', 'The Ferrari 458 Italia is a mid-engine sports car known for its stunning design and blistering performance.'),
(9, 'Ferrari', 'La Ferrari', 2016, 3000000.00, '../images/ferrari_la_ferrari.jpg', 'The Ferrari LaFerrari is a hybrid hypercar that represents the pinnacle of Ferrari\'s engineering prowess.'),
(10, 'Fiat', 'Panda', 2023, 15000.00, '../images/fiat_panda.jpg', 'The Fiat Panda is a compact city car known for its practicality and efficiency. Despite its small size, it offers surprising interior space and a fun driving experience.'),
(11, 'Fiat', 'Punto', 2018, 18000.00, '../images/fiat_punto.jpg', 'The Fiat Punto is a practical hatchback known for its affordable price and fuel-efficient engines.'),
(12, 'Lamborghini', 'Huracan', 2014, 200000.00, '../images/lamborghini_huracan.jpg', 'The Lamborghini Huracan is a mid-engine supercar known for its sharp styling and exhilarating performance.'),
(13, 'Lamborghini', 'Gallardo', 2003, 100000.00, '../images/lamborghini_gallardo.jpg', 'The Lamborghini Gallardo is a classic supercar known for its breathtaking performance and iconic design.'),
(14, 'Lamborghini', 'Murcielago', 2001, 300000.00, '../images/lamborghini_murcielago.jpg', 'The Lamborghini Murcielago is a legendary supercar known for its dramatic styling and thunderous V12 engine.'),
(15, 'Lancia', 'Kappa', 1994, 10000.00, '../images/lancia_kappa.jpg', 'The Lancia Kappa is a stylish sedan known for its comfortable ride and distinctive Italian design.'),
(16, 'Maserati', 'GranTurismo', 2007, 150000.00, '../images/maserati_granturismo.jpg', 'The Maserati GranTurismo is a luxury grand tourer known for its elegant design and powerful engine.'),
(17, 'Maserati', 'Ghibli', 2014, 70000.00, '../images/maserati_ghibli.jpg', 'The Maserati Ghibli is a luxury sedan known for its Italian flair and sporty performance.'),
(18, 'Pagani', 'Zonda', 1999, 2000000.00, '../images/pagani_zonda.jpg', 'The Pagani Zonda is an iconic hypercar known for its extreme performance and bespoke craftsmanship.'),
(19, 'Alfa Romeo', 'Mito', 2017, 27500.00, '../images/alfa_romeo_mito.jpg', 'Samochód został po raz pierwszy oficjalnie zaprezentowany podczas targów motoryzacyjnych w Genewie na początku 2008 roku. Zbudowany został na bazie platformy SCCS pochodzącej z modelu Fiat Grande Punto oraz Opel Corsa D. Nazwa MiTo nawiązuje do nazw dwóch włoskich miast: Mediolanu (wł. Milano), gdzie auto zostało zaprojektowane oraz Turynu (wł. Torino), gdzie jest produkowane. Słowo mito w języku włoskim oznacza mit lub legendę.');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
