<?php
class Qixol_Promo_Block_Startexportbutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $_hlp = Mage::helper('qixol');
        $html = $this->AddJs();

        $start_export_button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Export now')
            ->setOnClick("start_promo_export()")
            ->toHtml();
        $safe_mode_set = ini_get('safe_mode');
        if($safe_mode_set){
            $html .="<p class='sinch-error'><b>You can't start export (safe_mode is 'On'. set safe_mode = Off in php.ini )<b></p>";
        } else {
            $html .= $start_export_button;    
        }

        $html .= '<div id="qixolexport_status_template" name="qixolexport_status_template" style="display:none; margin-top: 5px;">';//none
        $html .= $this->getProcessingHtml();
        $html .= '</div>';

        $html .= '<div id="export_current_status_message" name="export_current_status_message" style="display:true"><br />';
        $export=Mage::getModel('qixol/sinch');
        // TODO: near duplicate of code in AjaxController.php
        $last_export_statuses = $export->getDataOfLatestExport();
        foreach ($last_export_statuses as $last_export_status)
        {
            $message = $last_export_status['last_message'];
            switch ($message)
            {
                case 'error':
                    $html .= '<hr /><p class="sinch-error">';
                    $html .= $last_export_status['export_what'];
                    $html .= ' export failed</p><p>';
                    $html .= $last_export_status['exports_start'];
                    $html .= '</p><p>';
                    $html .= $last_export_status['status_export_message'];
                    $html .= '</p>';
                    break;
                case 'success':
                    $html .= '<hr/><p class="sinch-success">';
                    $html .= $last_export_status['export_what'];
                    $html .= ' exported succesfully</p>';
                    $html .= '<p>';
                    $html .= $last_export_status['exports_start'];
                    $html .= '</p>';
                    break;
                case 'process':
                    $html .= '<hr/><p>';
                    $html .= $last_export_status['export_what'];
                    $html .= ' is running now</p>';
                    $html .= '<p>';
                    $html .= $last_export_status['exports_start'];
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
        
        $html = "Exporting...&nbsp<img src='";
        $html .= $run_pic;
        $html .= "' alt='";
        $html .= $_hlp->__('product export run');
        $html .= "' />";
        
        return $html;
    }

    protected function AddJs()
    {
        $post_url=$this->getUrl('qixol_admin/ajax/ExportProduct');
        $post_url_upd=$this->getUrl('qixol_admin/ajax/UpdateExportStatus');
        $statusHtml = $this->getProcessingHtml();
        $html = "
        <script>
            function start_promo_export(){
                $('qixolexport_status_template').innerHTML = \"$statusHtml\";
                $('qixolexport_status_template').show();
                $('export_current_status_message').hide();
                this_export = new promo_export('$post_url','$post_url_upd');
                this_export.startProductExport();
            }

 var promo_export = Class.create();
 promo_export.prototype = {

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
startProductExport: function () {
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

if (_this.objectMsg.finished == 1) {
    _this.updateExportStatusHtml();
    _this.clearUpdateInterval();
} else {
    _this.updateTimer = setInterval(function(){_this.updateEvent();},4000);
}

},
onFailure: this.ajaxFailure.bind(),
    });
},

updateExportStatusHtml: function(){
    var finishedMessage = 'Export finished.&nbsp;';
    if (_this.objectMsg.error == 1) {
        finishedMessage += '<img src=\"".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/_error.png"."\"/>';
    } else {
        finishedMessage += '<img src=\"".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/_yes.gif"."\"/>';
    }
    $('qixolexport_status_template').innerHTML = finishedMessage;
    $('export_current_status_message').innerHTML = _this.objectMsg.message;
    $('export_current_status_message').show();
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