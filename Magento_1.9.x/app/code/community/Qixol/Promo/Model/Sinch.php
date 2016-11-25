<?php

//ini_set('memory_limit','256M');
require_once ('config.php');
require_once('RESTPromoService.php');
require_once('SOAPPromoService.php');

class Qixol_Promo_Model_Sinch extends Mage_Core_Model_Abstract
{
    private $process_export_status_table;
    private $process_export_status_id=array();
    private $export_poducts_statistic_table;
    private $export_poducts_statistic_id=array();
    private $export_by = 'MANUAL';
    private $promoService;

    function __construct()
    {
        $this->process_export_status_table=Mage::getSingleton('core/resource')->getTableName('qixol_process_export_status_table');
        $this->_logFile=LOG_FILE;  
        $this->pushLog("constructor");

        if (Mage::getStoreConfig('qixol/integration/serviceProtocol') == 'REST') {
            $this->promoService = new RESTPromoService();
        } else {
            $this->promoService = new SOAPPromoService();
        }
    }

    function pushLog($log){

        if(trim($log)!=''){
            Mage::log($log, null, $this->_logFile);
            //store in some log table if required
        }
    }

    function cron_run_export(){//call from cron product export
        $this->pushLog("Start export from cron:".date("Y-m-d H:i:s"));
        $this->export_by='CRON';
        $this->run_export_qixolData(); 
        $this->pushLog("Finish export from cron".date("Y-m-d H:i:s"));   
    }

    function cron_run_import(){//call from cron product export
        $this->pushLog("Start export from cron:".date("Y-m-d H:i:s"));
        $this->export_by='CRON';
         if (Mage::getStoreConfig('qixol/promo/enabled')>0){
        $this->run_import_promotionsForProducts(); 
        $this->run_import_promotionsForBaskets();
        }
        $this->pushLog("Finish export from cron".date("Y-m-d H:i:s"));   
    }

    function getDataOfLatestExport(){
        $_status=array();

        $tmp_data=$this->getExportStatus('customers');
        $customerGroupsStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $customerGroupsStatus = array('last_message'=>'error',
                        'export_what'=>'Customers',
                        'status_export_message'=>$tmp_data['extended_message'],
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $customerGroupsStatus = array('last_message'=>'process',
                        'export_what'=>'Customers',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $customerGroupsStatus = array('last_message'=>'success',
                        'export_what'=>'Customers',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $customerGroupsStatus = array('last_message'=>'not started',
                    'export_what'=>'Customers',
                    'exports_start'=>'',
                    'is_finished'=>true);
        }
        
        $tmp_data=$this->getExportStatus('delivery');
        $deliveryMethodsStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $deliveryMethodsStatus = array('last_message'=>'error',
                        'export_what'=>'Delivery',
                        'status_export_message'=>$tmp_data['extended_message'],
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $deliveryMethodsStatus = array('last_message'=>'process',
                        'export_what'=>'Delivery',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $deliveryMethodsStatus = array('last_message'=>'success',
                        'export_what'=>'Delivery',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $customerGroupsStatus = array('last_message'=>'not started',
                    'export_what'=>'Delivery',
                    'exports_start'=>'',
                    'is_finished'=>true);
        }

        $tmp_data=$this->getExportStatus('products');
        $productsStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $productsStatus = array('last_message'=>'error',
                        'export_what'=>'Products',
                        'status_export_message'=>$tmp_data['extended_message'],
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $productsStatus = array('last_message'=>'process',
                        'export_what'=>'Products',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $productsStatus = array('last_message'=>'success',
                        'export_what'=>'Products',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $customerGroupsStatus = array('last_message'=>'not started',
                    'export_what'=>'Products',
                    'exports_start'=>'',
                    'is_finished'=>true);
        }


        $tmp_data=$this->getExportStatus('currency');
        $currencyStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $currencyStatus = array('last_message'=>'error',
                        'export_what'=>'Currency',
                        'status_export_message'=>$tmp_data['extended_message'],
                        'exports_start'=>$tmp_data['exports_start'],
                            'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $currencyStatus = array('last_message'=>'process',
                        'export_what'=>'Currency',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $currencyStatus = array('last_message'=>'success',
                        'export_what'=>'Currency',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $customerGroupsStatus = array('last_message'=>'not started',
                    'export_what'=>'Currency',
                    'exports_start'=>'',
                    'is_finished'=>true);
        }

        
        $tmp_data=$this->getExportStatus('store');
        $storeStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $storeStatus = array('last_message'=>'error',
                        'export_what'=>'Store',
                        'status_export_message'=>$tmp_data['extended_message'],
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $storeStatus = array('last_message'=>'process',
                        'export_what'=>'Store',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $storeStatus = array('last_message'=>'success',
                        'export_what'=>'Store',
                        'exports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $customerGroupsStatus = array('last_message'=>'not started',
                    'export_what'=>'Store',
                    'exports_start'=>'',
                    'is_finished'=>true);
        }


        $_status = array($productsStatus, $customerGroupsStatus, $storeStatus, $currencyStatus, $deliveryMethodsStatus);
        
        return $_status;
    }
    
    function getDataOfLatestImport()
    {
        $_status=array();

        $tmp_data = $this->getExportStatus('productpromotions');
        $promotionsStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $promotionsStatus = array('last_message'=>'error',
                        'import_what'=>'Product Promotions',
                        'status_import_message'=>$tmp_data['extended_message'],
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $promotionsStatus = array('last_message'=>'process',
                        'import_what'=>'Product Promotions',
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $promotionsStatus = array('last_message'=>'success',
                        'import_what'=>'Product Promotions',
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $promotionsStatus = array('last_message'=>'not started',
                    'import_what'=>'Product Promotions',
                    'imports_start'=>'',
                    'is_finished'=>true);
        }

        $tmp_data = $this->getExportStatus('basketpromotions');
        $basketPromotionsStatus = array();
        if ($tmp_data['id'] > 0)
        {
            switch ($tmp_data['message'])
            {
                case 'error':
                    $basketPromotionsStatus = array('last_message'=>'error',
                        'import_what'=>'Basket Promotions',
                        'status_import_message'=>$tmp_data['extended_message'],
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'process':
                    $basketPromotionsStatus = array('last_message'=>'process',
                        'import_what'=>'Basket Promotions',
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
                case 'success':
                    $basketPromotionsStatus = array('last_message'=>'success',
                        'import_what'=>'Basket Promotions',
                        'imports_start'=>$tmp_data['exports_start'],
                        'is_finished'=>$tmp_data['is_finished']);
                    break;
            }
        }
        else
        {
                $basketPromotionsStatus = array('last_message'=>'not started',
                    'import_what'=>'Basket Promotions',
                    'imports_start'=>'',
                    'is_finished'=>true);
        }

         
        $_status = array($promotionsStatus, $basketPromotionsStatus);
        
        return $_status;
    }
    
    function run_export(){
       $this->run_export_qixolData();
       echo 'done';
    }   
    
    function run_import(){
        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }
        $this->run_import_promotionsForProducts();
        $this->run_import_promotionsForBaskets();
        echo 'done';
    }

    function run_import_promotionsForProducts(){
        
        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }
        
        $this->addExportStatus("process", 'productpromotions', '', 0);

        $products_list = Mage::getModel('catalog/product')->getCollection()
                                  ->addAttributeToSelect('*')->addAttributeToFilter('visibility', array('neq'=>1))
                                  ->addAttributeToSort('entity_id', 'desc'); 

        foreach ($products_list as $product) {
            $data .= '<product productcode="'.$product->getSku().'" variantcode="" barcode="" price="'.$product->getPrice().'"><description>'.$this->CDT($product->getName()).'</description><imageurl>'.$this->CDT($product->getImage() != 'no_selection' ? $product->getImageUrl() : '').'</imageurl>';
            $products_data.='<product productcode="'.$product->getSku().'" variantcode="" />';
            if ($product->isConfigurable()){ //with variations
                //$associatedAttributes = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);    
                $childs_products_list=$product->getTypeInstance()->getUsedProducts();
                foreach ($childs_products_list as $childProduct_tmp) {    
                    $childProduct = Mage::getModel('catalog/product')->load($childProduct_tmp->getId());
                    $products_data.='<product productcode="'.$product->getSku().'" variantcode="'.$childProduct->getSku().'" />';
                }
            }

        }
        
        if ($products_data!=''){

            $promotionsXml = '<request companykey="';
            $promotionsXml .= Mage::getStoreConfig('qixol/integration/companykey');
            $promotionsXml .= '" validationdate="';
            $promotionsXml .= date("Y-m-d"); //date("Y-m-d",strtotime("+ 1 DAY"))
            $promotionsXml .= 'T00:00:00" channel="';
            $promotionsXml .= Mage::getStoreConfig('qixol/syhchronized/channel');
            $promotionsXml .= '" storegroup="'.Mage::getStoreConfig('qixol/syhchronized/storegroup');
            $promotionsXml .= '" store="';
            $promotionsXml .= Mage::getStoreConfig('qixol/syhchronized/channel');
            $promotionsXml .= '" validatefortime="false"><products>';
            $promotionsXml .= $products_data;
            $promotionsXml .= '</products></request>';

            $result = $this->promoService->PromotionsForProducts($promotionsXml);

            if ($result->success) {                  
                if ($result->message != ''){
                    $this->promoService->parsePromotionsForProducts($result->message);
                    $this->addExportStatus("success", 'productpromotions', 'imported', 1);
                } else {
                    $this->addExportStatus("success", 'productpromotions', 'imported - no promotions', 1);
                }
            } else {
                $this->addExportStatus("error", 'productpromotions', addslashes($result->message), 1);
                $this->pushLog("Finish import promotions error ".$result->message);
            }
        } else {
            $this->addExportStatus("process", 'productpromotions', 'no products found for promotion retrieval', 1);
        }
        return;
    }

    function run_import_promotionsForBaskets(){

        $this->addExportStatus("process", 'basketpromotions', '', 0);
        $promotionsXml = '<request companykey="';
        $promotionsXml .= Mage::getStoreConfig('qixol/integration/companykey');
        $promotionsXml .= '" validationdate="';
        $promotionsXml .= date("Y-m-d"); //date("Y-m-d", strtotime("+ 1 DAY"));
        $promotionsXml .= 'T00:00:00" channel="';
        $promotionsXml .= Mage::getStoreConfig('qixol/syhchronized/channel');
        $promotionsXml .= '" storegroup="';
        $promotionsXml .= Mage::getStoreConfig('qixol/syhchronized/storegroup');
        $promotionsXml .= '" store="'.Mage::getStoreConfig('qixol/syhchronized/channel');
        $promotionsXml .= '" validatefortime="false"></request>';

        $result = $this->promoService->PromotionsForBaskets($promotionsXml);
        if ($result->success)
        {
            $this->promoService->parsePromotionsForBaskets($result->message);
            $this->addExportStatus("success", 'basketpromotions', 'imported', 1);
        } else {
            $this->addExportStatus("error", 'basketpromotions', addslashes($result->message), 1);
            $this->pushLog("Finish import promotions error ".$result->message);
        }
        return;
    }

    function run_export_customerGroups() {

        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }

        if (Mage::getStoreConfig('qixol/syhchronized/synchcustomer') == 0){
            return;
        }
        
        if ($this->isRunning('customers'))
        {
            return;
        }

        $this->addExportStatus("process", 'customers' ,'',0);

        $list_map_names = Mage::getModel('qixol/Customergrouspmap')->getCollection();

        $group_to_send='';
        foreach ($list_map_names as $list_map){
            $group_to_send .= '<item display="';
            $group_to_send .= $list_map->getCustomerGroupName();
            $group_to_send .= '">';
            $group_to_send .= $list_map->getIntegrationCode();
            $group_to_send .= '</item>';  
        }

        if ($group_to_send != '')
        {
            $data = '<import companykey="';
            $data .= Mage::getStoreConfig('qixol/integration/companykey');
            $data .= '" attributetoken="customergroup"><items>';
            $data .= $group_to_send;
            $data .= '</items></import>';
          
            $result = $this->promoService->CustomerGroupExport($data);
            if ($result->success)
            {
                $message = $result->message;
                $promoResult = $this->getPromoResult($message);
                $this->addExportStatus($promoResult, 'customers', addslashes($result->message),1);
            }
            else
            {
                $this->addExportStatus("error", 'customers', addslashes($result->message),1);
            }
        }
    }

    function run_export_shippingMethods() {

        if (Mage::getStoreConfig('qixol/promo/enabled') == 0)
        {
            return;
        }

        if (Mage::getStoreConfig('qixol/syhchronized/synchship') == 0)
        {
            return;
        }
        
        if ($this->isRunning('delivery'))
        {
            return;
        }
        
        $this->addExportStatus("process", 'delivery' ,'',0);

        $list_map_names = Mage::getModel('qixol/Shippingmap')->getCollection();

        $list_map_names_exists=array();

        foreach ($list_map_names as $list_map){
            $list_map_names_exists[$list_map->getShippingName()] = $list_map->getShippingNameMap();
        }

        //$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $methods = Mage::getSingleton('shipping/config')->getAllCarriers();
            
        $selectedgroups = Mage::getStoreConfig('qixol/shippings/list');

        unset($selectedgroups_array);
        
        if(trim($selectedgroups)!='')
        {
            $selectedgroups_array=explode(",",$selectedgroups);
        }

        foreach($list_map_names as $list_map)
        {
            $shipping_to_send .= '<item display="';
            $shipping_to_send .= $list_map->getCarrierTitle() . ': ' . $list_map->getCarrierMethod();
            $shipping_to_send .= '">';
            $shipping_to_send .= $list_map->getIntegrationCode();
            $shipping_to_send .= '</item>';  
        }

        if ($shipping_to_send!='')
        {
            $data='<import companykey="'.Mage::getStoreConfig('qixol/integration/companykey').'" attributetoken="deliverymethod"><items>'.$shipping_to_send.'</items></import>';
            $result = $this->promoService->ShippingMethodsExport($data);
            if ($result->success)
            {
                $message = $result->message;
                $promoResult = $this->getPromoResult($message);
                $this->addExportStatus($promoResult, 'delivery' ,addslashes($result->message),1);
            }
            else
            {
              $this->addExportStatus("error", 'delivery' ,addslashes($result->message),1);
            }
        }
        else
        {
            $this->addExportStatus("success", 'delivery', 'There are no shipping methods to send', 1);
        }
    }    

    function run_export_currencies()
    {

        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }

        if (Mage::getStoreConfig('qixol/syhchronized/synchcurrency') == 0){
            return;
        }
        
        if ($this->isRunning('currency'))
        {
            return;
        }

        $this->addExportStatus("process", 'currency' ,'',0);
        $currency_to_send='';
        $only_active_currency=Mage::getStoreConfig('currency/options/allow');
        $currencies_array = explode(',',$only_active_currency);
        foreach($currencies_array as $code_curr)
        {
            $currency_to_send .= '<item display="';
            $currency_to_send .= Mage::app()->getLocale()->currency( $code_curr )->getName();
            $currency_to_send .= '">';
            $currency_to_send .= $code_curr;
            $currency_to_send .= '</item>';  
        }

        if ($currency_to_send != '')
        {
            $data = '<import companykey="';
            $data .= Mage::getStoreConfig('qixol/integration/companykey');
            $data .= '" attributetoken="currencycode"><items>';
            $data .= $currency_to_send;
            $data .= '</items></import>';
            
            $result = $this->promoService->CurrenciesExport($data);
            if ($result->success)
            {
                $message = $result->message;
                $promoResult = $this->getPromoResult($message);
                $this->addExportStatus($promoResult, 'currency', addslashes($result->message), 1);
            }
            else
            {
                $this->addExportStatus("error", 'currency', addslashes($result->message), 1);
            }
        }
        else
        {
              $this->addExportStatus("success", 'currency', 'No currencies to send', 1);
        }
    }

    function run_export_stores() {

        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }

        if (Mage::getStoreConfig('qixol/syhchronized/synchstores') == 0){
            return;
        }

        if ($this->isRunning('store'))
        {
            return;
        }

        $this->addExportStatus("process", 'store' ,'',0);

        $list_store_integration_codes = Mage::getModel('qixol/Storesmap')->getCollection();

        foreach ($list_store_integration_codes as $list_map)
        {
            $list_store_integration_codes_exists[$list_map->getWebsite()][$list_map->getStoreGroup()][$list_map->getStoreName()]=$list_map->getIntegrationCode();
        }

        $xmlToSend = '';
        
        foreach (Mage::app()->getWebsites() as $website)
        {
            $xmlChannel = '<channel display="';
            $xmlChannel .= $website->getName();
            $xmlChannel .= '" value="';
            $xmlChannel .= $website->getName();
            $xmlChannel .= '">';
            
            $hasStoreGroups = false;
            foreach ($website->getGroups() as $group) {
                $xmlStoreGroup = '<storegroup display="';
                $xmlStoreGroup .= $group->getName();
                $xmlStoreGroup .= '" value="';
                $xmlStoreGroup .= $group->getName();
                $xmlStoreGroup .= '">';
                
                $stores = $group->getStores();

                $hasStores = false;
                foreach ($stores as $store) {
                    $integrationCode = $list_store_integration_codes_exists[$website->getName()][$group->getName()][$store->getName()];
                    if (!empty($integrationCode))
                    {
                        $hasStores = true;
                        $xmlStore .= '<store display="';
                        $xmlStore .= $store->getName();
                        $xmlStore .= '" value="';
                        $xmlStore .= $integrationCode;
                        $xmlStore .= '" />';  
                    }
                }
                if ($hasStores)
                {
                    $hasStoreGroups = true;
                    $xmlStoreGroup .= $xmlStore;
                }
                
                $xmlStoreGroup .= '</storegroup>';
            }
            
            if ($hasStoreGroups)
            {
                $xmlChannel .= $xmlStoreGroup;
                $xmlChannel .= '</channel>';
                
                $xmlToSend .= $xmlChannel;
            }
        }
    

        if ($xmlToSend != '')
        {
            $data = '<import companykey="';
            $data .= Mage::getStoreConfig('qixol/integration/companykey');
            $data .= '" hierarchytoken="store">';
            $data .= $xmlToSend;
            $data .= '</import>';
            
            $result =$this->promoService->StoresExport($data);
            if ($result->success)
            {
                $message = $result->message;
                $promoResult = $this->getPromoResult($message);
                $this->addExportStatus($promoResult, 'store', addslashes($result->message), 1);
            }
            else
            {
                $this->addExportStatus("error", 'store', addslashes($result->message), 1);
            }
        }
        else
        {
            $this->addExportStatus("success", 'store', 'There are no stores to send', 1);
        }
    }

    function run_export_products() {

        if (Mage::getStoreConfig('qixol/promo/enabled')==0) {
            return;
        }
      
        if (Mage::getStoreConfig('qixol/syhchronized/synchproducts')==0) {
            return;
        }
      
        if ($this->isRunning('products'))
        {
            return;
        }
      
        $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write_data->query("
            delete from ".$this->process_export_status_table." where export_what='products'
        ");

        //process products here
        $number_products_exported=0;
        $this->addExportStatus("process", 'products' ,'',0);

        $products_list = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('visibility', array('neq'=>1))->addAttributeToSort('entity_id', 'desc'); 
        
        $products_deleted = Mage::getModel('qixol/Deletedproduct')->getCollection();
        $remove_deleted=array();

        if (count($products_list)||count($products_deleted)){
            $data = '<import companykey="'.Mage::getStoreConfig('qixol/integration/companykey').'"><products>';

            //assign deleted product first
            if (count($products_deleted))
                foreach ($products_deleted as $products_deleted_data) {
                    $data .= '<product productcode="'.$products_deleted_data->getData('product_sku').'" variantcode="'.$products_deleted_data->getData('child_sku').'" barcode="" deleted="true"></product>';
                    $remove_deleted[]=$products_deleted_data->getData('entity_id');                
                }

                if (count($products_list))
                foreach ($products_list as $product) {
                    $send_product=true;
                    $imageUrl = $this->CDT($product->getImage() != 'no_selection' ? $product->getImageUrl() : '');
                    if (!$product->isConfigurable()){
                        $parentId = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                        //sometimes could be grouped if such module installed
                         /*try {
                               if((!$parentId)||(!is_array($parentId)))
                               $parentId = Mage::getResourceSingleton('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
                           }
                           catch(Exception $e) {
                                  ; //if object not supported appears exception                                
                           }*/
                        if (is_array($parentId)&&count($parentId))
                        {
                            foreach ($parentId as  $parent_product_id){
                                $prod_parent_obj = Mage::getModel('catalog/product')->load($parent_product_id);
                                //do not send if this product exists as child in any active parent product
                                if (strtolower($prod_parent_obj->getAttributeText('status'))=='enabled'&&$prod_parent_obj->getVisibility()>1){
                                    $send_product=false;
                                    break;
                                }
                            }
                            $parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
                            if ($imageUrl == '') {
                            $imageUrl = $parentProduct->getImageUrl();
                            }
                        }
                    }
                    if  ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                        $send_product = false;
                    }
                    if ($product->getPrice() == '') {
                        $send_product = false;
                    }
                    if ($send_product){
                        $productCategories = $product->getCategoryIds();
                        if (!$product->isConfigurable()) {
                            $productCode = $product->getSku();
                            $variantCode = '';
                            $price = $product->getPrice();
                            $description = $this->CDT($product->getName());

                            $data .= $this->productXmlElement($product, $productCode, $variantCode, $price, $description, $imageUrl, $productCategories);
                            $number_products_exported++;

                        } else {
                            //$associatedAttributes = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);    
                            $childs_products_list=$product->getTypeInstance()->getUsedProducts();
                            if (empty($childs_products_list) || (count($childs_products_list) == 0)) {
                                $productCode = $product->getSku();
                                $variantCode = '';
                                $price = $product->getPrice();
                                $description = $this->CDT($product->getName());

                                $data .= $this->productXmlElement($product, $productCode, $variantCode, $price, $description, $imageUrl, $productCategories);
                                $number_products_exported++;
                            } else {
                                foreach ($childs_products_list as $childProduct_tmp) {                           
                                    $childProduct = Mage::getModel('catalog/product')->load($childProduct_tmp->getId());
                                    $productCode = $product->getSku();
                                    $variantCode = $childProduct->getSku();
                                    $price = $childProduct->getPrice();
                                    $description = $product->getName(); // $this->CDT($childProduct->getName());
                                    $image = $childProduct->getImage();
                                    if ($image == NULL) {
                                        $image = 'no_selection';
                                    }
                                    $imageUrl = ($image != 'no_selection' ? $childProduct->getImageUrl() : '');

                                    $data .= $this->productXmlElement($product, $productCode, $variantCode, $price, $description, $imageUrl, $productCategories);

                                    $number_products_exported++;
                                }
                            }
                        }
                    }
                }

            $data .= '</products></import>';
            if ($data!=''){
                $result = $this->promoService->ProductsExport($data);
                
                if ($result->success)
                {
                    $message = $result->message;
                    $promoResult = $this->getPromoResult($message);
                    $this->addExportStatus($promoResult, 'products', addslashes($message), 1);
                    if (is_array($remove_deleted)&&count($remove_deleted)>0)
                    {
                        $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $write_data->query("delete from qixol_product_to_delete where entity_id in (".join(",",$remove_deleted).")");
                    }
                }
                else {
                    $this->addExportStatus("error", 'products', addslashes($result->message), 1);
                }
            }
            else
            {
                $this->addExportStatus("success", 'products', 'no products to send', 1);
            }
        }
    }
    
    function attributeXmlElement($product, $productCategories) {
        $attributes=Mage::getStoreConfig('qixol/productattrib/attributes');
        $attributeXmlElement = '';
        $attributes_arr=explode(",",$attributes);
        if (count($attributes_arr)){
            foreach ($attributes_arr as $attribute_id){
                $attribute = $product->getResource()->getAttribute($attribute_id);
                if ($attribute != NULL) {
                    $is_attribute_text_value = $attribute->getFrontend()->getValue($product);
                    $attributeXmlElement .= '<attribute>';
                    $attributeXmlElement .= '<name>'.$attribute_id.'</name>';
                    $attributeXmlElement .= '<value>'.$this->CDT($is_attribute_text_value!=''?$is_attribute_text_value:$product->getData($attribute_id)).'</value>';
                    $attributeXmlElement .= '</attribute>';
                }
            }
        }
        if (Mage::getStoreConfig('qixol/syhchronized/synchcatproducts')>0){
            foreach ($productCategories as $product_category_id){
                if ($product_category_id==0) continue;
                $current_ctaegory_id=$product_category_id;                     
                $category_name_push='';
                while($current_ctaegory_id != 0){
                    $category = Mage::getModel('catalog/category')->load($current_ctaegory_id);
                    $current_ctaegory_id=$category->getParentId();
                    if (strtolower($category->getName())!='root catalog') {
                        $category_name_push=$category->getName().($category_name_push!=''?" / ".$category_name_push:"");
                    }
                }
                $attributeXmlElement .= '<attribute>';
                $attributeXmlElement .= '<name>categorycode</name>';
                $attributeXmlElement .= '<value>'.$this->CDT($category_name_push).'</value>';
                $attributeXmlElement .= '</attribute>';
            }
        }                

        if (!empty($attributeXmlElement)) {
            $attributeXmlElement = '<attributes>'.$attributeXmlElement.'</attributes>';
        }
        return $attributeXmlElement;
    }
    
    function productXmlElement($product, $productCode, $variantCode, $price, $description, $imageUrl, $productCategories) {
        $productXmlElement = '<product productcode="'.$productCode.'" variantcode="'.$variantCode.'"';
        $productXmlElement .= ' barcode="" price="'.$price.'"><description>'.$description.'</description>';
        $productXmlElement .= '<imageurl>'.$imageUrl.'</imageurl>';
        $productXmlElement .= $this->attributeXmlElement($product, $productCategories);
        $productXmlElement .= '</product>';
        
        return $productXmlElement;
    }
    
    function run_export_qixolData(){

        if (Mage::getStoreConfig('qixol/promo/enabled') == 0){
            return;
        }

        $this->run_export_customerGroups();
        $this->run_export_shippingMethods();
        $this->run_export_currencies();
        $this->run_export_stores();
        $this->run_export_products();
    }

    function CDT($in_str)
    {
      return "<![CDATA[".$in_str."]]>";
    }   

    public function getExportStatus($for='products'){

        $query = "SELECT * FROM ";
        $query .= $this->process_export_status_table;
        $query .= " where export_what ='";
        $query .= $for;
        $query .= "' ORDER BY id DESC LIMIT 1";

        $read_data = Mage::getSingleton('core/resource')->getConnection('core_read');

        if ($readresult=$read_data->query($query))
        {
            $_status = $readresult->fetch();
        }
        else
        {
            $_status=array();
        }
        
        if ($_status['id'] > 0)
        {
              // small fudge to cope with single quotes in XML failing the evalJSON function later on
            $last_message = str_replace("'", '"', $_status['last_message']);
            $extended_message = str_replace("'", '"', $_status['extended_message']);
            $messages=array('id'=>$_status['id'],
                'message'=>$last_message,
                'extended_message'=>$extended_message,
                'error'=>((int)$_status['is_finished']<0?1:0),
                'finished'=>((int)$_status['is_finished'] > 0 ? true : false),
                'last_updated'=>$_status['exports_last_updated'],
                'exports_start'=>$_status['exports_start']);
        }
        else
        {
            $messages=array('id'=>'0','message'=>'inactive', 'error'=>0, 'finished'=>true);
        }

        return $messages;
   }

    public function addExportStatus($message, $for ,$extended_message='',$finished=0){
        if (!isset($this->process_export_status_id[$for])||$this->process_export_status_id[$for]==0){
            $query="INSERT INTO ".$this->process_export_status_table." 
                (last_message, export_what,exports_start,exports_last_updated,is_finished,extended_message) 
                VALUES('".$message."','".$for."',now(),now(), '".(int)$finished."','".$extended_message."')";    

            $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write_data->query($query);
            $this->process_export_status_id[$for]=$write_data->lastInsertId();
        } else {
            $query="update ".$this->process_export_status_table." 
                 set last_message='".$message."', exports_last_updated=now(), is_finished='".(int)$finished."', extended_message='".$extended_message."'
                 where id=".(int)$this->process_export_status_id[$for]."";  
            $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write_data->query($query);
        }

        //extended latst transactions stat table disabled as now it dublicate main status table function
       /* if ($this->export_poducts_statistic_id==0){
          $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
          $write_data->query("
            insert into ".$this->export_poducts_statistic_table."
              (start_export,number_of_items,status_export,export_by,status_export_message)
            values(now(),0,'process','".$this->export_by."','export start')
          ");
            $this->export_poducts_statistic_id=$write_data->lastInsertId();
        }
        if($finished==0){
                 $query = "UPDATE ".$this->export_poducts_statistic_table." 
                          SET status_export='".$message."' ,
                              status_export_message='".addslashes($extended_message)."'
                          WHERE export_id=".$this->export_poducts_statistic_id;
            $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write_data->query($query);
        } else {
                  $query = "UPDATE ".$this->export_poducts_statistic_table." 
                          SET 
                            status_export='success', 
                            status_export_message='".addslashes($extended_message)."'
                            finish_export=now()  
                          WHERE 
                                export_id=".$this->export_poducts_statistic_id;
            $write_data = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write_data->query($query);
        }*/
   return ;
    }

    function isRunning($exportType)
    {
        $current_state=$this->getExportStatus($exportType);
        
        if ($current_state['id']==0) return false;
        if ($current_state['finished']) return false;
        if (strtotime($current_state['last_updated'])<strtotime("-1 hour")) return false;

        return true;
    }
    
    function getPromoResult($message)
    {
	libxml_use_internal_errors(true);
        $xml_message = simplexml_load_string($message);
        if (!$xml_message)
        {
            return "error";
        }
        if (!($xml_message instanceof SimpleXMLElement))
        {
            return "error";
        }

        $promoResult = "error";
        foreach ($xml_message as $xml_root_key=>$xml_object_sub)
        {
            switch ($xml_root_key)
            {
                case "summary":
                    $attributes = $xml_object_sub->attributes();
                    if ($attributes['result']=="true")
                    {
                        $promoResult = "success";
                    }
                    else
                    {
                        $promoResult = "error";
                    }
                    break;
                default:
                    break;
            }
        }
        return $promoResult;
    }
}
