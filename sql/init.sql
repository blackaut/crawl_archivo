
CREATE TABLE `imgs` (
  `id` int(255) NOT NULL,
  `url` text,
  `nombre` text,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `urls` (
  `id` int(255) NOT NULL,
  `url` text,
  `crawl` enum('0','1') DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


ALTER TABLE `imgs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `urls`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `imgs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `urls`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;
  
  INSERT INTO `crawl_museo`.`urls` ( `url`, `crawl` ) VALUES ( 'http://www.archivonacional.cl/616/w3-channel.html', '0');
  INSERT INTO `crawl_museo`.`urls` ( `url`, `crawl` ) VALUES ( 'http://www.archivonacional.cl/616/w3-alt_propertyvalue-47998.html', '0');
   INSERT INTO `crawl_museo`.`urls` ( `url`, `crawl` ) VALUES ( 'http://www.archivonacional.cl/616/w3-propertyvalue-38617.html', '0');
   INSERT INTO `crawl_museo`.`urls` ( `url`, `crawl` ) VALUES ( 'http://www.archivonacional.cl/616/w3-propertyname-594.html', '0');