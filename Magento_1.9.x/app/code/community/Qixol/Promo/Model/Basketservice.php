<?php

//ini_set('memory_limit','256M');
require_once ('config.php');
require_once('RESTPromoService.php');
require_once('SOAPPromoService.php');

class Qixol_Promo_Model_Basketservice extends Mage_Core_Model_Abstract
{
    private $process_export_status_table;
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

    function run_processOrder($cart){
        global $_SESSION;
        return $this->run_ValidateBasket($cart, false, true);// set true to confirm cart(process cart)
    }

    function run_ValidateBasket($cart, $get_missed_promotions = false, $set_confirmed = false)
    {
        global $_SESSION;
        $_SESSION['inside_request']=time();

        $item_id=0;
        $data_products='';
        $coupons_applyed='';
        $customer_groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();               
        if ($customer_groupId > 0)
        {
            $customergroup = Mage::getModel('customer/group')->load($customer_groupId);
            $customergroupName=$customergroup->getCode($customergroup);
        }
        else
        {
            $customergroupName='NOT LOGGED IN';
        }

        foreach ($cart->getAllVisibleItems() as $item)
        {
            $item_id++;
            //getProduct()->getIsVirtual()
            $data_products.='<item id="'.$item->getId()/*$item_id*/.'" ';
            if ($item->getProductType()=='configurable'){
                //$item->getOptionByCode('simple_product')->getValue();    
                $product_search_tmp = Mage::getModel('catalog/product')->load($item->getProductId());
                $data_products.='productcode="'.$product_search_tmp->getSku().'" variantcode="'.$item->getSku().'"';
            //print_r($item->_getData('product'));
            }/*else if ($item->getProductType()=='grouped'){
               //do not apply now
            }else if ($item->getProductType()=='bundle'){
               //do not apply now
            }*/else { //simple
                $data_products.=' productcode="'.$item->getSku().'" variantcode="" ';
            }
            $data_products.=' barcode=""  quantity="'.$item->getQty().'" price="'.$item->getPrice().'" ></item>';
            //print_r(Zend_Debug::dump($item->debug()));

            //print_r(Zend_Debug::dump($item->debug()));
            //echo $data_products."<br>\n";
        }

        //delivery methid if exists and delivery price....
        $shipping_method_exists=$cart->getShippingAddress()->getShippingMethod();
        $shipping_price_exists=0;
        $product_to_enter_in_cart=array();
        if ($shipping_method_exists!='')
        {
            $shipping_price_exists=$cart->getShippingAddress()->getShippingAmount();
            if ($shipping_price_exists==0)
            {//somethimes returns zero
                // sometimes magento returning shipping method but not returning correct price
                //searching price for method
                $address = $cart->getShippingAddress();
                $rates = $address->collectShippingRates()
                                ->getGroupedAllShippingRates();
                foreach ($rates as $carrier)
                {
                    foreach ($carrier as $rate)
                    {
                        if ($rate->getCode()==$shipping_method_exists)
                        {
                             $shipping_price_exists=$rate->getPrice();
                             break;
                        }
                    }
                }
            }
        }


        if (isset($_SESSION['qixol_quoted_items']['coupons'])&&count($_SESSION['qixol_quoted_items']['coupons'])){
            $coupons_applyed.='<coupons>';
            foreach ($_SESSION['qixol_quoted_items']['coupons'] as $entered_coupons=>$tmp_val){
                if ((bool)$tmp_val['issued']==false)
                $coupons_applyed.='<coupon code="'.trim($entered_coupons).'"/>';
            }
            $coupons_applyed.='</coupons>';
        }

        //get mapping    
        // TODO: this conversion from data to array happens in lots of places - move it to the model?
        $list_customer_map_names=Mage::getModel('qixol/Customergrouspmap')->getCollection();
        $list_customer_integration_codes=array();
        foreach ($list_customer_map_names as $list_map)
        {
            $list_customer_integration_codes[$list_map->getCustomerGroupName()]=$list_map->getIntegrationCode();
        }
        // end mapping array

        if (isset($list_customer_integration_codes[$customergroupName]))
        {
            $customergroupName=$list_customer_integration_codes[$customergroupName];
        }

        //get mapping    
        $list_store_integration_codes=Mage::getModel('qixol/Storesmap')->getCollection();

        $list_store_integration_codes_exists=array();

        foreach ($list_store_integration_codes as $list_map)
        {
            $list_store_integration_codes_exists[$list_map->getWebsite()][$list_map->getStoreGroup()][$list_map->getStoreName()]=$list_map->getIntegrationCode();
        }
        // end mapping array

        //get mapping    
        $list_shipping_map_names=Mage::getModel('qixol/Shippingmap')->getCollection();

        $list_shipping_integration_codes=array();

        foreach ($list_shipping_map_names as $list_map)
        {
            $list_shipping_integration_codes[$list_map->getCarrier()][$list_map->getStoreGroup()][$list_map->getShippingName()]=$list_map->getIntegrationCode();
        }
        // end mapping array

        $store_integration_code = $list_store_integration_codes_exists[Mage::app()->getStore()->getWebsite()->getName()][Mage::app()->getStore()->getGroup()->getName()][Mage::app()->getStore()->getName()];
        
        if ($data_products!='')
        {
            //echo "call promotions";
            $basketTotal = $cart->getSubtotal();
            if ($shipping_price_exists > 0)
            {
                $basketTotal += (float)$shipping_price_exists;
            }
            $basket = '<basket id="';
            //$basket .= /*Mage::getSingleton("core/session")->getEncryptedSessionId();*/
            $basket .= $_SESSION['qixol_quoted_items']['cart_session_id'];
            $basket .= '" companykey="';
            $basket .= Mage::getStoreConfig('qixol/integration/companykey');
            $basket .= '" baskettotal="';
            $basket .= $basketTotal;
            $basket .= '" basketdate="';
            $basket .= date("Y-m-d\TH:i:s",strtotime("+ 1 DAY"));
            $basket .= '" channel="';
            $basket .= Mage::app()->getStore()->getWebsite()->getName();
            $basket .= '" storegroup="';
            $basket .= Mage::app()->getStore()->getGroup()->getName();
            $basket .= '" store="';
            if (!empty($store_integration_code))
            {
                $basket .= $store_integration_code;
            }
            else
            {
                $basket .= Mage::app()->getStore()->getName();
            }
            $basket .= '"';
            
            // /*Mage::getStoreConfig('qixol/syhchronized/channel')*/.
            if ($shipping_method_exists != '')
            {
                $basket .= ' deliverymethod="';
                $basket .= $shipping_method_exists;
                $basket .= '"';
            }

            if ($shipping_price_exists > 0)
            {
                $basket .= ' deliveryprice="';
                $basket .= $shipping_price_exists;
                $basket .= '"';
            }

            $basket .= ' customergroup="';
            $basket .= $customergroupName;
            $basket .= '"';
            
            $basket .= ' currencycode="';
            $basket .= Mage::app()->getStore()->getCurrentCurrencyCode();
            $basket .= '"';
            
            if ($set_confirmed)
            {
                $basket .= ' confirmed="true"';
            }
            
            $show_missed_promotions = Mage::getStoreConfig('qixol/missed_promotions/show_missed_promotions');
            if (($show_missed_promotions === "1") && $get_missed_promotions)
            {
                $basket .= ' get_missed_promotions="true"';
            }

            $basket .= '>';

            $basket .= $coupons_applyed;
            
            $basket .= '<items>';
            $basket .= $data_products;
            $basket .= '</items></basket>';

            error_log($basket);
            $result = $this->promoService->BasketValidate($basket);
            error_log($result->message);
            
            if ($result->success)
            {
                $new_cart_structure = $this->parseBasketXml($result->message, $cart);
            }
        }

        if (isset($new_cart_structure) && count($new_cart_structure)>0)
        {
            return $new_cart_structure;
        }
    }

    function parseBasketXml($basketResponseXml, $cart)
    {
        $basketResponseXml = simplexml_load_string($basketResponseXml);
        if (!$basketResponseXml)
        {
            return false;
        }

        $basketResponse = [];
        
        $attributes_cart = $basketResponseXml->attributes();
        $basketResponse['cart_data'] = [];
        $basketResponse['cart_data']['id']=(isset($attributes_cart['id'])?(string)$attributes_cart['id']:0);
        $basketResponse['cart_data']['manualdiscount']=(isset($attributes_cart['manualdiscount'])?(float)$attributes_cart['manualdiscount']:0);
        $basketResponse['cart_data']['basketdiscount']=(isset($attributes_cart['basketdiscount'])?(float)$attributes_cart['basketdiscount']:0);
        $basketResponse['cart_data']['linestotaldiscount']=(isset($attributes_cart['linestotaldiscount'])?(float)$attributes_cart['linestotaldiscount']:0);
        $basketResponse['cart_data']['totaldiscount']=(isset($attributes_cart['totaldiscount'])?(float)$attributes_cart['totaldiscount']:0);
        $basketResponse['cart_data']['baskettotal']=(isset($attributes_cart['baskettotal'])?(float)$attributes_cart['baskettotal']:0);
        $basketResponse['cart_data']['originalbaskettotal']=(isset($attributes_cart['originalbaskettotal'])?(float)$attributes_cart['originalbaskettotal']:0);
        $basketResponse['cart_data']['deliverymanualdiscount']=(isset($attributes_cart['deliverymanualdiscount'])?(float)$attributes_cart['deliverymanualdiscount']:0);
        $basketResponse['cart_data']['deliveryprice']=(isset($attributes_cart['deliveryprice'])?(float)$attributes_cart['deliveryprice']:0);
        $basketResponse['cart_data']['deliverypromotiondiscount']=(isset($attributes_cart['deliverypromotiondiscount'])?(float)$attributes_cart['deliverypromotiondiscount']:0);
        $basketResponse['cart_data']['deliverytotaldiscount']=(isset($attributes_cart['deliverytotaldiscount'])?(float)$attributes_cart['deliverytotaldiscount']:0);
        $basketResponse['cart_data']['deliveryoriginalprice']=(isset($attributes_cart['deliveryoriginalprice'])?(float)$attributes_cart['deliveryoriginalprice']:0);

        if ($basketResponseXml instanceof SimpleXMLElement)
        {
            $basketResponse['items'] = $this->parseBasketItemsXml($basketResponseXml->items, $cart);
            $basketResponse['coupons'] = $this->parseBasketCouponsXml($basketResponseXml->coupons);
            $basketResponse['summary'] = $this->parseBasketSummaryXml($basketResponseXml->summary);
            $basketResponse['missedpromotions'] = $this->parseBasketMissedPromotionsXml($basketResponseXml->missedpromotions, $cart);
        }
        
        return $basketResponse;
    }

    function parseBasketItemsXml($itemsXml, $cart)
    {
        $basketResponseItems = [];
        foreach ($itemsXml->item as $xml_items_sub)
        {
            $item_attributes=$xml_items_sub->attributes();

            $basketResponseItems[(int)$item_attributes['id']]=array();

            $basketResponseItems[(int)$item_attributes['id']]['data']['quoteid']=(int)$item_attributes['id'];
            $basketResponseItems[(int)$item_attributes['id']]['data']['productcode']=(isset($item_attributes['productcode'])?(string)$item_attributes['productcode']:'');
            $basketResponseItems[(int)$item_attributes['id']]['data']['variantcode']=(isset($item_attributes['variantcode'])?(string)$item_attributes['variantcode']:'');
            $basketResponseItems[(int)$item_attributes['id']]['data']['price']=(isset($item_attributes['price'])?(float)$item_attributes['price']:0.0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['manualdiscount']=(isset($item_attributes['manualdiscount'])?(float)$item_attributes['manualdiscount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['quantity']=(isset($item_attributes['quantity'])?(float)$item_attributes['quantity']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['linepromotiondiscount']=(isset($item_attributes['linepromotiondiscount'])?(float)$item_attributes['linepromotiondiscount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['totaldiscount']=(isset($item_attributes['totaldiscount'])?(float)$item_attributes['totaldiscount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['originalprice']=(isset($item_attributes['originalprice'])?(float)$item_attributes['originalprice']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['originalquantity']=(isset($item_attributes['originalquantity'])?(float)$item_attributes['originalquantity']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['originalamount']=(isset($item_attributes['originalamount'])?(float)$item_attributes['originalamount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['appliedpromotioncount']=(isset($item_attributes['appliedpromotioncount'])?(float)$item_attributes['appliedpromotioncount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['isdelivery']=(isset($item_attributes['isdelivery'])&&strtolower((string)$item_attributes['isdelivery'])=='true'?true:false);
            $basketResponseItems[(int)$item_attributes['id']]['data']['totalissuedpoints']=(isset($item_attributes['totalissuedpoints'])?(int)$item_attributes['totalissuedpoints']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['splitfromlineid']=(isset($item_attributes['splitfromlineid'])?(int)$item_attributes['splitfromlineid']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['alwaysexcluded']=(isset($item_attributes['alwaysexcluded'])?(int)$item_attributes['alwaysexcluded']:0);
            $basketResponseItems[(int)$item_attributes['id']]['data']['generated']=(isset($item_attributes['generated'])&&strtolower($item_attributes['generated'])=='true'?(true):false);
            $basketResponseItems[(int)$item_attributes['id']]['data']['appliedpromotioncount']=(isset($item_attributes['appliedpromotioncount'])?(float)$item_attributes['appliedpromotioncount']:0);
            $basketResponseItems[(int)$item_attributes['id']]['description']=(isset($xml_items_sub->description)?(string)$xml_items_sub->description:'');
            $basketResponseItems[(int)$item_attributes['id']]['data']['lineamount']=(isset($item_attributes['lineamount'])?(float)$item_attributes['lineamount']:0);

            //get cart item by cart item iD returned from validation

            $item_not_found=true;

            foreach ($cart->getAllVisibleItems() as $item)
            {
                if ($item->getId()==(int)$item_attributes['id'])
                {
                    $item_not_found=false;
                    $cart_item=$item;
                    unset($product_search_tmp_sku);
                    if ($cart_item->getProductType()=='configurable')
                    {
                        $product_search_tmp = Mage::getModel('catalog/product')->load($cart_item->getProductId());
                        $product_search_tmp_sku=$product_search_tmp->getSku();
                    }
                    $basketResponseItems[(int)$item_attributes['id']]['updated_qty']=false;
                    $basketResponseItems[(int)$item_attributes['id']]['updated_price']=false;
                    $product_updated=false;

                    if ((!$cart_item->isDeleted() && !$cart_item->getParentItemId()/*check is visible*/)&&($cart_item->getProductType()=='configurable'&&(string)$item_attributes['productcode']==$product_search_tmp_sku&&(string)$item_attributes['variantcode']==$cart_item->getSku()) 
                                || ((string)$item_attributes['variantcode']==''&&(string)$item_attributes['productcode']==$cart_item->getSku()))
                    {
                        if ($item_attributes['quantity']!=$cart_item->getQty())
                        {
                            $basketResponseItems[(int)$item_attributes['id']]['updated_qty']=true;
                            /* $cart_item->setQty($item_attributes['quantity']);
                                $product_updated=true;
                            */
                        }
                        if ((float)$item_attributes['lineamount']!=($cart_item->getPrice()*$cart_item->getQty()))
                        {
                            $basketResponseItems[(int)$item_attributes['id']]['updated_price']=true;
                        }
                    }
                    else
                    {
                        if ((int)Mage::getStoreConfig('qixol/advanced/separateitem')>0)
                        {
                            $basketResponseItems[(int)$item_attributes['id']]['new']=true;
                        }
                        else
                        {
                            $basketResponseItems[(int)$item_attributes['id']]['updated_qty']=true;
                        }
                    }
                }
            }
            $is_splitted_line=false;
            if ($item_not_found)
            {
                if (!isset($item_attributes['splitfromlineid'])||(int)$item_attributes['splitfromlineid']==0)
                {
                    if ((int)Mage::getStoreConfig('qixol/advanced/separateitem')>0)
                    {
                        $basketResponseItems[(int)$item_attributes['id']]['new']=true;
                    }
                    else
                    {
                    $check_exists_in_cart=false; 
                    foreach ($basketResponseItems as $current_item_cart_position => $cart_item_to_check)
                    {
                        if ($current_item_cart_position == (int)$item_attributes['id'])
                        {
                            continue;
                        }

                        if ($cart_item_to_check['data']['productcode']==(string)$item_attributes['productcode']&&$cart_item_to_check['data']['variantcode']==(string)$item_attributes['variantcode'])
                        {
                            $basketResponseItems[$current_item_cart_position]['updated_qty']=true;
                            $basketResponseItems[$current_item_cart_position]['free_added']=(isset($item_attributes['quantity'])?(float)$item_attributes['quantity']:0);
                            //$basketResponseItems[$current_item_cart_position]['updated_price']=true;
                            //$basketResponseItems[$current_item_cart_position]['data']['price']+=(isset($item_attributes['price'])?(float)$item_attributes['price']:0.0);
                            $basketResponseItems[$current_item_cart_position]['data']['quantity']+=(isset($item_attributes['quantity'])?(float)$item_attributes['quantity']:0);
                            //$basketResponseItems[$current_item_cart_position]['data']['originalamount']+=(isset($item_attributes['originalamount'])?(float)$item_attributes['originalamount']:0.0);
                            //$basketResponseItems[$current_item_cart_position]['data']['totaldiscount']=(isset($item_attributes['originalamount'])?(float)$item_attributes['originalamount']:0.0);
                            $check_exists_in_cart=true;
                        }
                    }
                    if (!$check_exists_in_cart)
                    {
                        $basketResponseItems[(int)$item_attributes['id']]['new']=true;
                        if ($basketResponseItems[(int)$item_attributes['id']]['data']['lineamount']==0)
                        {
                            $basketResponseItems[$current_item_cart_position]['free_added']=(isset($item_attributes['quantity'])?(float)$item_attributes['quantity']:0);
                        }
                    }

                }
                }
                elseif((int)$item_attributes['splitfromlineid']>0)
                {
                    $basketResponseItems[(int)$item_attributes['splitfromlineid']]['updated_qty']=0;
                    $basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['totaldiscount']+=(float)$item_attributes['totaldiscount'];
                    $calcualted_discount=$basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['totaldiscount']/$basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['originalquantity'];
                    $basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['price']=$basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['originalprice']-$calcualted_discount;
                    $basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['lineamount']=($basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['price']*$basketResponseItems[(int)$item_attributes['splitfromlineid']]['data']['originalquantity']);
                    $is_splitted_line=true;
                }
            }
            $basketResponseItems[(int)$item_attributes['id']]['promotions']=array();
            foreach ($xml_items_sub->promotions->promotion as $promotion)
            {
                $promotion_attributes=$promotion->attributes();
                $basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]=array();
                //print_r($promotion_attributes);
                $basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]['id']=(int)$promotion_attributes['id'];

                //$basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]['id']=(isset($promotion_attributes['discountamount'])?(float)$promotion_attributes['discountamount']:0);
                $basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]['instance']=(isset($promotion_attributes['instance'])?(int)$promotion_attributes['instance']:0);
                $basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]['basketlevel']=(isset($promotion_attributes['basketlevel'])&&strtolower($promotion_attributes['basketlevel'])=='true'?(true):false);
                $basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']]['discountamount']=(isset($promotion_attributes['discountamount'])?(float)$promotion_attributes['discountamount']:0);
                if ($is_splitted_line)
                { //check is promotion exists in main linea
                    if (!isset($basketResponseItems[(int)$item_attributes['splitfromlineid']]['promotions'][(int)$promotion_attributes['id']]))
                    {
                        $basketResponseItems[(int)$item_attributes['splitfromlineid']]['promotions'][(int)$promotion_attributes['id']]=$basketResponseItems[(int)$item_attributes['id']]['promotions'][(int)$promotion_attributes['id']];        
                    }
                }
            }
        }
        
        return $basketResponseItems;
    }

    function parseBasketCouponsXml($couponsXml)
    {
        $basketResponseCoupons = [];

        foreach ($couponsXml->coupon as $item_key=>$xml_items_sub)
        {
            if ($item_key=='coupon')
            {
                $coupon_attributes=$xml_items_sub->attributes();
                if (!(strtolower((string)$coupon_attributes['issued'])=='true'))
                {
                    if(isset($coupon_attributes['code'])&&(string)$coupon_attributes['code']!='')
                    {
                        $basketResponseCoupons[(string)$coupon_attributes['code']]['issued']=false;
                        $basketResponseCoupons[(string)$coupon_attributes['code']]['code']=(string)$coupon_attributes['code'];
                        $basketResponseCoupons[(string)$coupon_attributes['code']]['description']=(isset($xml_items_sub->couponname)&&(string)$xml_items_sub->couponname!=''?(string)$xml_items_sub->couponname:(string)$coupon_attributes['code']);
                    }
                    else
                    {
                        unset($_SESSION['qixol_quoted_items']['coupons'][(string)$coupon_attributes['code']]);
                    }
                }
                else
                {
                    //!!!!!!!!!!!!!!! get valid to for coupon
                    $validtill='0000-00-00 00:00:00';

                    $result = $this->promoService->CouponCodeValidate((string)$coupon_attributes['code']);
                    if ($result->success)
                    {
                        $update_item=false;
                        $xml_coupon_code_validated = $result->message;
                        if (strlen($xml_coupon_code_validated)>10)
                        {
                            $xml_coupon_object = simplexml_load_string($xml_coupon_code_validated);
                            foreach ($xml_coupon_object as $xml_coupon_object_root_key=>$xml_coupon_object_object_sub)
                            {
                                if ($xml_coupon_object_root_key=='coupon')
                                {
                                    foreach ($xml_coupon_object_object_sub as $xml_coupon_object_coupon_key=>$xml_coupon_object_object_coupon)
                                    {
                                        if ($xml_coupon_object_coupon_key=='codes')
                                        {
                                            foreach ($xml_coupon_object_object_coupon as $xml_coupon_object_object_coupon_obj)
                                            {
                                                $xml_coupon_object_object_coupon_attributes=$xml_coupon_object_object_coupon_obj->attributes();
                                                $validtill=date("Y-m-d H:i:s",strtotime((string)$xml_coupon_object_object_coupon_attributes['validto']));
                                            }                                            
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //!!!!!!!!!!!!!!! end valid to for coupon
                    $basketResponseCoupons[(string)$coupon_attributes['code']]['description']=(string)$xml_items_sub->couponname;//(string)$coupon_attributes['reportingcode']
                    $basketResponseCoupons[(string)$coupon_attributes['code']]['validtill']=($validtill=='1970-01-01 00:00:00'?"0000-00-00 00:00:00":$validtill);//(string)$coupon_attributes['code']
                    $basketResponseCoupons[(string)$coupon_attributes['code']]['issued']=true;

                }
            }
        }
        
        return $basketResponseCoupons;
    }

    function parseBasketSummaryXml($basketSummaryXml)
    {
        $basketResponseSummary = [];

        foreach ($basketSummaryXml->promotions->promotion as $promotion)
        {
            $promotion_attributes = $promotion->attributes();
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['type'] = (string)$promotion_attributes['type'];
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['display'] = (string)$promotion_attributes['display'];
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['display_text']=(isset($promotion->displaytext)?(string)$promotion->displaytext:'');
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['discountamount']=(isset($promotion_attributes['discountamount'])?(float)$promotion_attributes['discountamount']:0);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['basketlevel']=(isset($promotion_attributes['basketlevel'])&&strtolower($promotion_attributes['basketlevel'])=='true'?(true):false);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['deliverylevel']=(isset($promotion_attributes['deliverylevel'])&&strtolower($promotion_attributes['deliverylevel'])=='true'?(true):false);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['issuedpoints']=(isset($promotion_attributes['issuedpoints'])?((int)$promotion_attributes['issuedpoints']):0);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['issuedcoupon']=(isset($promotion_attributes['issuedcoupon'])&&strtolower($promotion_attributes['issuedcoupon'])=='true'?(true):false);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['unpublished']=(isset($promotion_attributes['unpublished'])&&strtolower($promotion_attributes['unpublished'])=='true'?(true):false);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['issuedproduct']=(isset($promotion_attributes['issuedproduct'])&&strtolower($promotion_attributes['issuedproduct'])=='true'?(true):false);
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['description']=(isset($promotion->description)?(string)$promotion->description:'');
            $basketResponseSummary[(int)$promotion_attributes['id']]['data']['name']=(isset($promotion->name)?(string)$promotion->name:'');
        }
            
//        foreach ($basketSummaryXml->messages as $message)
//        {
//            // TODO - ???
//            $new_cart_structure['messages'] = array();
//        }
        
        return $basketResponseSummary;
    }

    function parseBasketMissedPromotionsXml($missedPromotions, $cart)
    {
        $basketResponseMissedPromotions = [];
        foreach ($missedPromotions->promotion as $promotion)
        {
            $promotion_attributes = $promotion->attributes();
            $promotion_type = (string)$promotion_attributes['type'];
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['name'] = (isset($promotion->name) ? (string)$promotion->name : '');
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['type'] = $promotion_type;
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['display'] = (string)$promotion_attributes['display'];
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['displaytext'] = (isset($promotion->displaytext) ? (string)$promotion->displaytext : '');
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['matcheditems'] = [];
            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['missingproducts'] = [];
            
            foreach($promotion->criteria->criteriaitems as $criteriaitems)
            {
                $$criteriaitems_attributes = $criteriaitems->attributes();
                $fullymatched = (bool)$$criteriaitems_attributes['fullymatched'];
                if ($fullymatched)
                {
                    // find matched cart items
                    foreach($criteriaitems_attributes->criteriaitem as $criteriumitem)
                    {
                        foreach($criteriumitem->matcheditems->item as $item)
                        {
                            $basketResponseMissedPromotions[(int)$promotion_attributes['id']]['matcheditems'][] = $this->getMatchedCartItems($item, $cart);
                        }
                    }
                }
                else
                {
                    // find matched cart items (if matched) and missing items (whether matched or !matched)
                }
            }
        }
        
        return $basketResponseMissedPromotions;
    }
    
    private function getMatchedCartItems($item, $cart)
    {
        $matchedCartItems = [];
        
        
        
        return $matchedCartItems;
    }
}
