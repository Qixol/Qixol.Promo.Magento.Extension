<?php
$installer = $this;

$installer->startSetup();

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable('qixol_promotions_type')}
    CHANGE `from_date` `from_date` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00',
    DROP `is_everyday`
SQLTEXT;
$installer->run($sql);

$installer->endSetup();
	 
?>