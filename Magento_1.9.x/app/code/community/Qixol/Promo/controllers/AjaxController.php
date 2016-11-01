<?php 
class Qixol_Promo_AjaxController extends Mage_Adminhtml_Controller_Action
{
    var $_logFile;

    public function UpdateExportStatusAction()
    {
        $_hlp = Mage::helper('qixol');
        $export = Mage::getModel('qixol/sinch');
        $message_output = '';
        $finished = true;
        $error = false;
        // TODO: near duplicate of code in Startexportbutton.php
        $last_export_statuses = $export->getDataOfLatestExport();
        foreach ($last_export_statuses as $last_export_status)
        {
            $message = $last_export_status['last_message'];
            switch ($message)
            {
                case 'error':
                    $message_output .= '<hr /><p class="sinch-error">';
                    $message_output .= $last_export_status['export_what'];
                    $message_output .= ' export failed</p><p>';
                    $message_output .= $last_export_status['exports_start'];
                    $message_output .= '</p><p>';
                    $message_output .= $last_export_status['status_export_message'];
                    $message_output .= '</p>';
                    $error = true;
                    break;
                case 'success':
                    $message_output .= '<hr/><p class="sinch-success">';
                    $message_output .= $last_export_status['export_what'];
                    $message_output .= ' exported succesfully</p>';
                    $message_output .= '<p>';
                    $message_output .= $last_export_status['exports_start'];
                    $message_output .= '</p>';
                    break;
                case 'process':
                    $message_output .= '<hr/><p>';
                    $message_output .= $last_export_status['export_what'];
                    $message_output .= ' is running now</p>';
                    $message_output .= '<p>';
                    $message_output .= $last_export_status['exports_start'];
                    $message_output .= '</p>';
                    $finished = false;
                    break;
                // TODO: not started / not enabled?
                default:
                    break;
            }
        }
        
        if ($message_output!='') {
            print '{ "error": "'.$error.'", "message": "'.addslashes(nl2br((string)$message_output)).'", "finished": "'. $finished .'"}';
        }
        else{
            print '{ "message": "", "finished": "0"}';
        }

        return;
    }


    public function UpdateImportStatusAction() {

        $_hlp = Mage::helper('qixol');
        $import = Mage::getModel('qixol/sinch');
        $message_output = '';
        $finished = true;
        $error = false;
        // TODO: near duplicate of code in Startimportbutton.php
        $last_import_statuses = $import->getDataOfLatestImport();
        foreach ($last_import_statuses as $last_import_status)
        {
            $message = $last_import_status['last_message'];
            switch ($message)
            {
                case 'error':
                    $message_output .= '<hr /><p class="sinch-error">';
                    $message_output .= $last_import_status['import_what'];
                    $message_output .= ' import failed</p><p>';
                    $message_output .= $last_import_status['imports_start'];
                    $message_output .= '</p><p>';
                    $message_output .= $last_import_status['status_import_message'];
                    $message_output .= '</p>';
                    $error = true;
                    break;
                case 'success':
                    $message_output .= '<hr/><p class="sinch-success">';
                    $message_output .= $last_import_status['import_what'];
                    $message_output .= ' imported succesfully</p>';
                    $message_output .= '<p>';
                    $message_output .= $last_import_status['imports_start'];
                    $message_output .= '</p>';
                    break;
                case 'process':
                    $message_output .= '<hr/><p>';
                    $message_output .= $last_import_status['import_what'];
                    $message_output .= ' is running now</p>';
                    $message_output .= '<p>';
                    $message_output .= $last_import_status['imports_start'];
                    $message_output .= '</p>';
                    $finished = false;
                    break;
                // TODO: not started / not enabled?
                default:
                    break;
            }
        }

        if ($message_output!='') {
            print '{ "error": "'.$error.'", "message": "'.addslashes(nl2br((string)$message_output)).'", "finished": "'. $finished .'"}';
        }
        else{
            print '{ "message": "", "finished": "0"}';
        }

        return;
    }

    public function indexAction(){
        $export=Mage::getModel('qixol/sinch');
        //echo "Start export <br>";
        //$dir = dirname(__FILE__);
        $export->run_export();
        //exec("nohup ".$sinch->php_run_string.$dir."/../sinch_import_start_ajax.php > /dev/null & echo $!");
        //echo "Finish export<br>";

    }

    public function ExportProductAction(){
        $export=Mage::getModel('qixol/sinch');
        //echo "Start export <br>";
        //$dir = dirname(__FILE__);
        $export->run_export();
        //exec("nohup ".$sinch->php_run_string.$dir."/../sinch_import_start_ajax.php > /dev/null & echo $!");
        //echo "Finish export<br>";

    }

    public function ImportPromotionAction(){
        $export=Mage::getModel('qixol/sinch');
        //echo "Start export <br>";
        //$dir = dirname(__FILE__);
        $export->run_import();
        //exec("nohup ".$sinch->php_run_string.$dir."/../sinch_import_start_ajax.php > /dev/null & echo $!");
        //echo "Finish export<br>";

    }

}  
    ?>
