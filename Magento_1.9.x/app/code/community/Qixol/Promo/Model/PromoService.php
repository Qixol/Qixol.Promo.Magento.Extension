<?php

require('iPromoService.php');
require_once('config.php');

abstract class PromoService implements iPromoService
{
    public abstract function CustomerGroupExport($data);
    public abstract function ShippingMethodsExport($data);
    public abstract function CurrenciesExport($data);
    public abstract function StoresExport($data);
    public abstract function ProductsExport($data);

    public abstract function PromotionsForProducts($data);
    public abstract function PromotionsForBaskets($data);
    
    public abstract function BasketValidate($data);
    public abstract function CouponCodeValidate($couponCode);
    public abstract function RetrieveValidatedBasket($basketRef);
    public abstract function BasketCheck();

    public function parsePromotionsForBaskets($promotion_new_xml='')
    {
        $xml_object = simplexml_load_string($promotion_new_xml);
        if ($xml_object instanceof SimpleXMLElement)
        {     
            foreach ($xml_object as $xml_root_key=>$xml_object_sub)
            {
                if ($xml_root_key=='promotions')
                {
                    foreach ($xml_object_sub as $xml_promotions)
                    {
                        unset($promotion_model);
                        $attributes=$xml_promotions->attributes();
                        $promotion_model=Mage::getModel('qixol/promotions')->load((int)$attributes['id']);
                        if (count($promotion_model->getData()) > 0)
                        {
                            $promotion_model->setUpdateTime(date("Y-m-d H:i:s"));
                        }
                        else
                        {
                            unset($promotion_model);
                            $promotion_model=Mage::getModel('qixol/promotions');
                            $promotion_model->setCreatedTime(date("Y-m-d H:i:s"));
                            $promotion_model->setUpdateTime('0000-00-00 00:00:00');
                        }

                        $promotion_model->setPromotionId((int)$attributes['id']);
                        $promotion_model->setPromotionType(isset($attributes['type'])?(string)$attributes['type']:"");

                        $promotion_model->setDiscountpercent(isset($attributes['discountpercent'])?(double)$attributes['discountpercent']:0);
                        $promotion_model->setDiscountamount(isset($attributes['discountamount'])?(double)$attributes['discountamount']:0);
                        $promotion_model->setHascouponrestrictions(isset($attributes['hascouponrestrictions'])?(int)$attributes['hascouponrestrictions']:0);

                        $validFrom = '0001-01-01 00:00:00';
                        if (isset($attributes['validfrom']))
                        {
                            $validFrom = '0001-01-01 ' . $attributes['validfrom'];
                        }
                        $promotion_model->setFromDate($validFrom);

                        $validTo = '9999-12-31 23:59:59';
                        if (isset($attributes['validto']))
                        {
                            $validTo = '9999-12-31 ' . $attributes['validto'];
                        }
                        $promotion_model->setTillDate($validTo);

                        $promotion_model->setPromotionName(isset($xml_promotions->name)?(string)$xml_promotions->name:"");
                        $promotion_model->setPromotionText(isset($xml_promotions->displaytext)?(string)$xml_promotions->displaytext:"");

                        try
                        {
                            $promotion_model->save();
                        }
                        catch(Exception $e)
                        {
                            print_r($e);
                        }
                    }
                }
            }
            //delete not updated simple way
            $promotion_has_products=Mage::getResourceModel('qixol/promotions');
            $promotion_has_products->removeOldDayPromotion();
        }
    }

    function parsePromotionsForProducts($promotion_new_xml='')
    {
        $active_promotions=array();
        $new_promotions=array();
        $promotions_list=Mage::getModel('qixol/Promotions')->getCollection();
        foreach ($promotions_list as $current_promotion)
        {
            $active_promotions[$current_promotion->getPromotionId()]=$current_promotion->getPromotionType();
        }
        $xml_object = simplexml_load_string($promotion_new_xml);

        if ($xml_object instanceof SimpleXMLElement)
        {
            foreach ($xml_object->promotions->promotion as $xml_promotions)
            {
                unset($promotion_model);
                $attributes=$xml_promotions->attributes();
                $promotion_model=Mage::getModel('qixol/promotions')->load((int)$attributes['id']);
                if (count($promotion_model->getData())>0)
                {
                    $promotion_model->setUpdateTime(date("Y-m-d H:i:s"));
                }
                else
                {
                    unset($promotion_model);
                    $promotion_model=Mage::getModel('qixol/promotions');
                    $promotion_model->setCreatedTime(date("Y-m-d H:i:s"));
                    $promotion_model->setUpdateTime('0000-00-00 00:00:00');
                }

                $promotion_model->setPromotionId((int)$attributes['id']);
                $promotion_model->setIsForProduct(1);
                $promotion_model->setPromotionType(isset($attributes['type'])?(string)$attributes['type']:"");
                $new_promotions[$promotion_model->getPromotionId()]=$promotion_model->getPromotionType();

                $promotion_model->setDiscountamount(isset($attributes['discountamount'])?(double)$attributes['discountamount']:0);

                $promotion_model->setDiscountpercent(isset($attributes['discountpercent'])?(double)$attributes['discountpercent']:0);
                $promotion_model->setYourref(isset($attributes['yourref'])?(string)$attributes['yourref']:"");

                $promotion_model->setBundleprice(isset($attributes['bundleprice'])?(double)$attributes['bundleprice']:0);
                $promotion_model->setPromotionName(isset($xml_promotions->name)?(string)$xml_promotions->name:"");
                $promotion_model->setPromotionText(isset($xml_promotions->displaytext)?(string)$xml_promotions->displaytext:"");

                $validFrom = '0001-01-01 00:00:00';
                if (isset($attributes['validfrom']))
                {
                    $validFrom = '0001-01-01 ' . $attributes['validfrom'];
                }
                $promotion_model->setFromDate($validFrom);

                $validTo = '9999-12-31 23:59:59';
                if (isset($attributes['validto']))
                {
                    $validTo = '9999-12-31 ' . $attributes['validto'];
                }
                $promotion_model->setTillDate($validTo);

                /*
                if (isset($xml_promotions->availabletimes)&&isset($xml_promotions->availabletimes->availabletime))
                {
                    foreach ($xml_promotions->availabletimes->availabletime as $availabletime)
                    {
                        $time_attributes=$availabletime->attributes();
                        if ((string)$time_attributes['start']!='')
                        {
                            $promotion_model->setFromDate('2000-01-01 '.(string)$time_attributes['start']);
                        }
                        if ((string)$time_attributes['start']!='')
                        {
                            $promotion_model->setTillDate('2000-01-01 '.(string)$time_attributes['end']);
                        }
                    }
                }
                */
                try
                {
                    $promotion_model->save();
                }
                catch(Exception $e)
                {
                    print_r($e);
                }
            }

            foreach ($xml_object->products->product as $xml_products)
            {
                //print_r($xml_products);
                $attributes=$xml_products->attributes();
                if (isset($xml_products->promotions)&&!isset($xml_products->promotions->promotion[0]))
                {
                    $ttemp_data=$xml_products->promotions->promotion;
                    unset($xml_products->promotions->promotion);
                    $xml_products->promotions->promotion[0]=$ttemp_data;
                    unset($ttemp_data);
                }
                
                foreach  ($xml_products->promotions->promotion as $idx_name=>$xml_promotion_data)
                {
                    $xml_promotion_data_attributes=$xml_promotion_data->attributes();
                    $promotion_to_product_array=array();
                    $promotion_to_product_array['created_time']=date("Y-m-d H:i:s");
                    $promotion_to_product_array['update_time']=date("Y-m-d H:i:s");

                    //get product id
                    $promotion_to_product_array['product_id']=0;
                    $product_search_tmp = Mage::getModel('catalog/product');              
                    if ($product_id_tmp=$product_search_tmp->getIdBySku(((string)$attributes['variantcode']!=''?(string)$attributes['variantcode']:(string)$attributes['productcode'])))
                    {
                        $promotion_to_product_array['product_id']=(int)$product_id_tmp;
                    }
                    //get parent_id
                    $promotion_to_product_array['parent_product_id']=0;
                    if ((string)$attributes['variantcode']!='')
                    {
                        $product_search_tmp = Mage::getModel('catalog/product');              
                        if ($product_id_tmp=$product_search_tmp->getIdBySku((string)$attributes['productcode']))
                        {
                            $promotion_to_product_array['parent_product_id']=(int)$product_id_tmp;
                        }
                    }

                    $promotion_to_product_array['promotion_id']=(int)$xml_promotion_data_attributes['id'];;
                    $promotion_to_product_array['parentsku']=((string)$attributes['variantcode']!=''?(string)$attributes['productcode']:'');
                    $promotion_to_product_array['sku']=((string)$attributes['variantcode']!=''?(string)$attributes['variantcode']:(string)$attributes['productcode']);

                    $promotion_to_product_array['requiredqty']=(isset($xml_promotion_data_attributes['requiredqty'])?(int)$xml_promotion_data_attributes['requiredqty']:0);
                    $promotion_to_product_array['multipleproductrestrictions']=isset($xml_promotion_data_attributes['multipleproductrestrictions'])?(int)$xml_promotion_data_attributes['multipleproductrestrictions']:0;
                    $promotion_has_products=Mage::getResourceSingleton('qixol/promotions');
                    $promotion_has_products->updatePromotionProduct($promotion_to_product_array);
                }
            }

            //delete not updated simple way
            $promotion_has_products=Mage::getResourceModel('qixol/promotions');
            $promotion_has_products->removeOldPromotion();
            $promotion_has_products->removeOldPromotedProduct();
            /*
            $condition=$promotion_has_products->_getWriteAdapter()->quoteInto('(update_time <= ?)', "(now() - interval 1 hour)");
            $promotion_has_products->_getWriteAdapter()->delete($this->getTable('promotions'), $condition);
            */

            /*
            foreach ($active_promotions as $p_id=>$p_type)
            {
                if (!isset($new_promotions[$p_id]))
                {
                    //delete
                }
                elseif($new_promotions[$p_id]!=$p_type)
                {
                    //promotion type changed ???
                }
            }
            */
        }
    }
}
