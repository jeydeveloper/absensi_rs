CREATE TABLE `abs_bagian` (
  `bag_id` int(11) NOT NULL,
  `bag_name` varchar(100) NOT NULL,
  `bag_void` tinyint(4) NOT NULL,
  `bag_created_at` datetime NOT NULL,
  `bag_updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `abs_bagian`
  ADD PRIMARY KEY (`bag_id`);

ALTER TABLE `abs_bagian`
  MODIFY `bag_id` int(11) NOT NULL AUTO_INCREMENT;
