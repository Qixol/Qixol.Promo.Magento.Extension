<?php
$installer = $this;

$installer->startSetup();

$sql=<<<SQLTEXT
insert into `core_config_data` (`path`, `value`)
values ('qixol/promo/useHTTPS', '1')
ON DUPLICATE KEY UPDATE
`value` = '1';
SQLTEXT;
$installer->run($sql);

$sql=<<<SQLTEXT
    update qixol_stickers
    set `filename`  = replace(filename, 'custom/stickers',  'Qixol/Promo/stickers')
SQLTEXT;
$installer->run($sql);

$sql=<<<SQLTEXT
ALTER TABLE {$installer->getTable('qixol_promotions_type')}
    CHANGE `from_date` `from_date` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00',
    DROP `is_everyday`
SQLTEXT;
$installer->run($sql);

$sql=<<<SQLTEXT
update `core_config_data`
set path = replace(path, 'qixol/integraion', 'qixol/integration')
WHERE path like 'qixol/integraion%'
SQLTEXT;
$installer->run($sql);

$sql=<<<SQLTEXT
INSERT INTO `magento`.`core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`)
VALUES (NULL, 'default', '0', 'qixol/missed_promotions/show_missed_promotions', '1');
SQLTEXT;
$installer->run($sql);

$installer->endSetup();
	 
?>