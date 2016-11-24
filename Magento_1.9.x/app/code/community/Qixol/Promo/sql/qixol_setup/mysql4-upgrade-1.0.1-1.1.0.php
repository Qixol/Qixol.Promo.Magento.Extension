<?php
$installer = $this;

$installer->startSetup();

$sql=<<<SQLTEXT
update `core_config_data`
set path = replace(path, 'qixol/integraion', 'qixol/integration')
WHERE path like 'qixol/integraion%'
SQLTEXT;
$installer->run($sql);

$installer->endSetup();
	 
?>