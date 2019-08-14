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
try{
  $installer->run("

  ALTER TABLE codilar_gopigen ADD partner VARCHAR(256) AFTER awb;

    ");
}catch(Exception $e){}
  
  $installer->endSetup();