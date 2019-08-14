<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     installer query to setup database table at the time of module installation
 * @author      Codilar Team
 **/
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('codilar_gopigen')};
CREATE TABLE {$this->getTable('codilar_gopigen')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `awb` varchar(255) NOT NULL DEFAULT '',
  `order_id` varchar(255) DEFAULT NULL,
  `label` varchar(1000) NOT NULL DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `created_time` DATETIME NULL,
  `update_time` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
