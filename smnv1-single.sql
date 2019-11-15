CREATE TABLE `bots` (
  `id` int(11) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `interface_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` varchar(200) NOT NULL,
  `node` int(11) NOT NULL DEFAULT 1,
  `server` varchar(200) NOT NULL,
  `botid` int(11) DEFAULT NULL,
  `audio.stream` varchar(2000) NOT NULL,
  `audio.volume` int(3) NOT NULL DEFAULT 20,
  `channel_commander` smallint(1) NOT NULL DEFAULT 0,
  `is_online` smallint(1) NOT NULL,
  `host_password` varchar(200) NOT NULL,
  `default_channel` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `passwort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stream_quickplay` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `users` (`id`, `passwort`, `username`, `stream_quickplay`) VALUES
(1, '$2y$10$T6r5NwcVJO0hdp0mnIAGDerFRL1o.x/GHkZKKRITgYeGPjj1GnDii', 'SMNJAN', '{}');

ALTER TABLE `bots`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `bots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
