<?php
$installer = $this;

$installer->startSetup();

$sql=<<<SQLTEXT
drop table {$installer->getTable(`qixol_product_export_stat`)};		
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable(`qixol_banners`)}
  DROP `is_default`,
  DROP `sort_order`,
  DROP `banner_group`,
  DROP `banner_type`,
  DROP `url` 
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable(`qixol_shipping_name_map`)}
  ADD 'carrier_title' varchar(255),
  ADD 'carrier_method' varchar(255),
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
RENAME TABLE  `magento`.`qixol_banner_images` TO  `magento`.`qixol_banner_image` ;
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable(`qixol_banner_image`)}
  ADD `banner_id` INT,
  ADD `sort_order` INT,
  ADD 'url' varchar(255),
  ADD 'promotion_reference' varchar(255),
  CONSTRAINT `FK_qixol_banner_image_banner_id` FOREIGN KEY (`banner_id`) REFERENCES `qixol_banners` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE;		
SQLTEXT;

$installer->run($sql);

// TODO: migrate banner/image references from banner_has_images to banner_images

$sql=<<<SQLTEXT
drop table {$installer->getTable(`qixol_banner_has_images`)};
SQLTEXT;

$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable(`qixol_stickers`)}
  DROP `use_default_sticker`,
  DROP `use_default_banner_group`,
  DROP `default_banner_group`,
  DROP `unique_banner_group`,
  DROP `status`,
  DROP `sort_order`,
  DROP `created_time`,
  DROP `update_time`,
  CHANGE `banner_link_name` `display_zone` varchar(255) not null default '',
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