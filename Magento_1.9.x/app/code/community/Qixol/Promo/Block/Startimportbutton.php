<?php
class Qixol_Promo_Block_Startimportbutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $_hlp = Mage::helper('qixol');
        $html = $this->AddJs();

        $start_import_button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Import now')
            ->setOnClick("start_promotion_import()")
            ->toHtml();
        $safe_mode_set = ini_get('safe_mode');
        if($safe_mode_set){
            $html .="<p class='sinch-error'><b>You can't start import (safe_mode is 'On'. set safe_mode = Off in php.ini )<b></p>";
        } else {
            $html .= $start_import_button;    
        }

        $html .= '<div id="qixolimport_status_template" name="qixolimport_status_template" style="display:none; margin-top: 5px;">';//none
        $html .= $this->getProcessingHtml();
        $html .= '</div>';

        $html .= '<div id="import_current_status_message" name="import_current_status_message" style="display:true"><br />';
        $import=Mage::getModel('qixol/sinch');
        // TODO: near duplicate of code in AjaxController.php
        $last_import_statuses = $import->getDataOfLatestImport();
        foreach ($last_import_statuses as $last_import_status)
        {
            $message = $last_import_status['last_message'];
            switch ($message)
            {
                case 'error':
                    $html .= '<hr /><p class="sinch-error">';
                    $html .= $last_import_status['import_what'];
                    $html .= ' import failed</p><p>';
                    $html .= $last_import_status['imports_start'];
                    $html .= '</p><p>';
                    $html .= $last_import_status['status_import_message'];
                    $html .= '</p></div>';
                    break;
                case 'success':
                    $html .= '<hr/><p class="sinch-success">';
                    $html .= $last_import_status['import_what'];
                    $html .= ' imported succesfully</p>';
                    $html .= '<p>';
                    $html .= $last_import_status['imports_start'];
                    $html .= '</p>';
                    break;
                case 'process':
                    $html .= '<hr/><p>';
                    $html .= $last_import_status['import_what'];
                    $html .= ' is running now</p>';
                    $html .= '<p>';
                    $html .= $last_import_status['imports_start'];
                    $html .= '</p>';
                    break;
                default:
                    break;
            }
        }
        $html .= '</div>';

        return $html;        
    }

    protected function getProcessingHtml()
    {
        $_hlp = Mage::helper('qixol');
        $run_pic = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/_run.gif";

        $html = "Importing...&nbsp<img src='";
        $html .= $run_pic;
        $html .= "' alt='";
        $html .= $_hlp->__('product import run');
        $html .= "' />";

        return $html;
    }

    protected function AddJs()
    {
        $post_url=$this->getUrl('qixol_admin/ajax/ImportPromotion');
        $post_url_upd=$this->getUrl('qixol_admin/ajax/UpdateImportStatus');
        $statusHtml = $this->getProcessingHtml();
        $html = "
        <script>
            function start_promotion_import(){
                $('qixolimport_status_template').innerHTML = \"$statusHtml\";
                $('qixolimport_status_template').show();
                $('import_current_status_message').hide();
                this_import = new promo_import('$post_url','$post_url_upd');
                this_import.startPromotionImport();
            }

 var promo_import = Class.create();
 promo_import.prototype = {

initialize: function(postUrl, postUrlUpd) {
                this.postUrl = postUrl; 
                this.postUrlUpd = postUrlUpd;
                this.failureUrl = document.URL;
                // unique user session ID
                this.SID = null;
                // object with event message data
                this.objectMsg = null;
                this.prevMsg = '';
                // interval object
                this.updateTimer = null;
                // default shipping code. Display on errors

                 elem = 'checkoutSteps';
                 clickableEntity = '.head';

                // overwrite Accordion class method
                var headers = $$('#' + elem + ' .section ' + clickableEntity);
                headers.each(function(header) {
                        Event.observe(header,'click',this.sectionClicked.bindAsEventListener(this));
                        }.bind(this));
            },
startPromotionImport: function () {
                 _this = this;
                 new Ajax.Request(this.postUrl,
                         {
          method:'post',
          parameters: '',
          requestTimeout: 10,
          /*
          onLoading:function(){
            alert('onLoading');
            },
            onLoaded:function(){
            alert('onLoaded');
            },
          */
onSuccess: function(transport) {
    var response = transport.responseText || null;
    _this.SID = response;
    if (_this.SID) {
    _this.updateEvent();
    $('session_id').value = _this.SID;
    } else {
    alert('Can not get your session ID. Please reload the page!');
    }
},
onTimeout: function() { alert('Can not get your session ID. Timeout!'); },
onFailure: function() { alert('Something went wrong...') }
    });

},

updateEvent: function () {
                 _this = this;
                 new Ajax.Request(this.postUrlUpd,
                         {
method: 'post',
parameters: {session_id: this.SID},
onSuccess: function(transport) {
_this.objectMsg = transport.responseText.evalJSON();

if (_this.objectMsg.finished == 1){
    _this.updateImportStatusHtml();
    _this.clearUpdateInterval();
} else {
    _this.updateTimer = setInterval(function(){_this.updateEvent();},4000);
}

},
onFailure: this.ajaxFailure.bind(),
    });
},

updateImportStatusHtml: function(){
    var finishedMessage = 'Import finished.&nbsp;';
    if (_this.objectMsg.error == 1) {
        finishedMessage += '<img src=\"".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/_error.png"."\"/>';
    } else {
        finishedMessage += '<img src=\"".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/_yes.gif"."\"/>';
    }
    $('qixolimport_status_template').innerHTML = finishedMessage;
    $('import_current_status_message').innerHTML = _this.objectMsg.message;
    $('import_current_status_message').show();
},

ajaxFailure: function(){
                     this.clearUpdateInterval();     
                     location.href = this.failureUrl;
},

clearUpdateInterval: function () {
                             clearInterval(this.updateTimer);
},


 }
        </script>
        ";
        return $html;
    }
}