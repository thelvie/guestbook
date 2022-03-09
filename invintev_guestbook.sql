-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2022 at 11:13 PM
-- Server version: 10.3.32-MariaDB-log-cll-lve
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invintev_guestbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(30) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tgl` date NOT NULL,
  `poto` varchar(200) NOT NULL,
  `admin_id` int(30) NOT NULL,
  `template` varchar(500) DEFAULT NULL,
  `urll` varchar(500) DEFAULT NULL,
  `welcome` varchar(50) NOT NULL DEFAULT 'Welcome',
  `warna` varchar(200) DEFAULT '#cdff00',
  `warna_bg` varchar(330) DEFAULT '#0d0d0d',
  `wa` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `nama`, `tgl`, `poto`, `admin_id`, `template`, `urll`, `welcome`, `warna`, `warna_bg`, `wa`) VALUES
(1, 'RamaSinta', '2022-01-29', 'wedding.jpg', 1, NULL, NULL, '', '#005dff', '16ffc1bb3c2a866e9ce0189f8285fe2a.png', NULL),
(2, 'International Women\'s Conference for the Liberation of Al-Aqsa anda Palestine', '2022-03-17', '3146807e4e538c778ddec845296c8cd8.png', 1, NULL, NULL, 'Welcome', '#cdff00', '#0d0d0d', 'Kepada Yth Bapak/Ibu/Saudara/i :  [NAMA-TAMU]\r\nTanpa mengurangi rasa Hormat, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami, untuk selengkapnya silahkan buka Link Undangan berikut ini :\r\n\r\n[LINK-UNDANGAN]\r\n\r\nMerupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir dan memberikan doa restu kepada kami.'),
(3, 'Internasional Women\'s Conference for the Liberation of Al -Aqsa and Palestine', '2022-01-31', '48fd6edb38319d5b7dcada971e7d5293.png', 0, NULL, NULL, 'Welcome', '#cdff00', '#0d0d0d', 'Kepada Yth Bapak/Ibu/Saudara/i :  [NAMA-TAMU]\r\nTanpa mengurangi rasa Hormat, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami, untuk selengkapnya silahkan buka Link Undangan berikut ini :\r\n\r\n[LINK-UNDANGAN]\r\n\r\nMerupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir dan memberikan doa restu kepada kami.');

-- --------------------------------------------------------

--
-- Table structure for table `konek`
--

CREATE TABLE `konek` (
  `id` int(30) UNSIGNED NOT NULL,
  `event_id` int(30) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `url` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `konek`
--

INSERT INTO `konek` (`id`, `event_id`, `kode`, `url`) VALUES
(1, 1, '61f569c3805a1', 'https://guestbook.invintevent.com/');

-- --------------------------------------------------------

--
-- Table structure for table `serial`
--

CREATE TABLE `serial` (
  `id` int(30) UNSIGNED NOT NULL,
  `serial` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` int(1) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `serial`
--

INSERT INTO `serial` (`id`, `serial`, `email`, `active`, `status`) VALUES
(1, '3715910237', 'thelvie.elf@gmail.com', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `seting`
--

CREATE TABLE `seting` (
  `id` int(30) UNSIGNED NOT NULL,
  `bg_login` varchar(200) NOT NULL,
  `logo_login` varchar(200) NOT NULL,
  `url` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `seting`
--

INSERT INTO `seting` (`id`, `bg_login`, `logo_login`, `url`) VALUES
(1, '33906449a4d257afc06976da019d163b.jpeg', 'ec2fb0b6105ebaa056929de13a655770.png', '15bf9608d5d55fb6ad1b7d10e9ecee8d.mp4');

-- --------------------------------------------------------

--
-- Table structure for table `tamu`
--

CREATE TABLE `tamu` (
  `id` int(30) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `event_id` int(30) NOT NULL,
  `poto` varchar(200) NOT NULL,
  `hadir` int(60) NOT NULL DEFAULT 0,
  `pesan` text DEFAULT NULL,
  `kehadiran` int(11) NOT NULL DEFAULT 0,
  `status_pesan` int(11) NOT NULL DEFAULT 0,
  `qr` varchar(300) DEFAULT NULL,
  `sapa` varchar(300) NOT NULL DEFAULT '0',
  `timer` varchar(300) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tamu`
--

INSERT INTO `tamu` (`id`, `nama`, `alamat`, `telp`, `event_id`, `poto`, `hadir`, `pesan`, `kehadiran`, `status_pesan`, `qr`, `sapa`, `timer`) VALUES
(1, 'Rifqi Abdillah', 'Klapanunggal', '0', 1, 'tamu.jpg', 1643539672, NULL, 0, 0, '164352612061f637e85cc6c', '0', '1643549329'),
(2, 'delfi arsita', 'Cikahuripan', '0', 1, 'tamu.jpg', 1643549350, NULL, 0, 0, '164354928861f69268b11d6', '0', '1643552857'),
(3, 'Anzala Nafsa Bilhusna', 'Pasir angin', '0', 1, 'tamu.jpg', 1643549450, NULL, 0, 0, '164354945061f6930a420ac', '0', '1643549460'),
(4, 'Ilyasa', 'Ciampea', '0', 1, 'tamu.jpg', 1643552825, NULL, 0, 0, '164355279161f6a0179b028', '0', '1643552835');

-- --------------------------------------------------------

--
-- Table structure for table `undangan`
--

CREATE TABLE `undangan` (
  `id` int(30) UNSIGNED NOT NULL,
  `link` varchar(400) NOT NULL,
  `status` int(1) NOT NULL,
  `apk` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `undangan`
--

INSERT INTO `undangan` (`id`, `link`, `status`, `apk`) VALUES
(1, 'https://guestbook.invintevent.com/', 2, 'c1ccb8e6285da0f4a33c1e1e69d87992.zip');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(30) UNSIGNED NOT NULL,
  `nama` varchar(400) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `poto` varchar(200) NOT NULL,
  `active` int(10) NOT NULL,
  `role` int(10) NOT NULL,
  `member` varchar(100) NOT NULL,
  `register` date NOT NULL,
  `expired` date NOT NULL,
  `kunci` int(10) NOT NULL,
  `event_id` int(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `email`, `password`, `poto`, `active`, `role`, `member`, `register`, `expired`, `kunci`, `event_id`) VALUES
(1, 'myadmin', 'admin', 'thelvie.elf@gmail.com', '$2y$10$.AaVMU1byFLZUp18QovYRetE1RFlyZqw4hLQSR5kIflaEBI1Ptjau', 'user.jpg', 1, 1, 'Gold', '2022-01-29', '2100-12-30', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `konek`
--
ALTER TABLE `konek`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `serial`
--
ALTER TABLE `serial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seting`
--
ALTER TABLE `seting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tamu`
--
ALTER TABLE `tamu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `undangan`
--
ALTER TABLE `undangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `konek`
--
ALTER TABLE `konek`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `serial`
--
ALTER TABLE `serial`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `seting`
--
ALTER TABLE `seting`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tamu`
--
ALTER TABLE `tamu`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `undangan`
--
ALTER TABLE `undangan`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
