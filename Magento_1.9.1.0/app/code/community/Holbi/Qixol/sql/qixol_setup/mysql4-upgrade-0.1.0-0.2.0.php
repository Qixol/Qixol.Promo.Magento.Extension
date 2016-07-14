<?php
$installer = $this;

$installer->startSetup();

$sql=<<<SQLTEXT
drop table {$installer->getTable(`qixol_product_export_stat`)};		
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable(`qixol_stickers`)}
  DROP `use_default_sticker`,
  DROP `use_default_banner_group`,
  DROP `default_banner_group`,
  DROP `unique_banner_group`,
  DROP `banner_link_name`,
  DROP `status`,
  DROP `sort_order`,
  DROP `created_time`,
  DROP `update_time`,
  ADD  `promo_reference` VARCHAR( 255 ) NOT NULL DEFAULT  '',
  ADD  `promo_type_name` VARCHAR( 255 ) NOT NULL DEFAULT  '',
  ADD  `is_default_for_type` TINYINT NOT NULL DEFAULT  '0',
  ADD  `is_system_default_for_type` TINYINT NOT NULL DEFAULT  '0';
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/bogof.png','','BOGOF',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/bogor.png','','BOGOR',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/bundle.png','','BUNDLE',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/deal.png','','DEAL',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/freeproduct.png','','FREEPRODUCT',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/issuecoupon.png','','ISSUECOUPON',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/issuepoints.png','','ISSUEPOINTS',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/offers.png','','MULTIPLE',1,1);
    INSERT INTO {$installer->getTable(`qixol_stickers`)}(`filename`, `promo_reference`, `promo_type_name`, `is_default_for_type`, `is_system_default_for_type`) VALUES ('custom/stickers/productsreduction.png','','PRODUCTSREDUCTION',1,1);
SQLTEXT;
$installer->run($sql);

//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
?>