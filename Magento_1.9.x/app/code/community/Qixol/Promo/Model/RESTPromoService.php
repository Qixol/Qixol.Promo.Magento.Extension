<?php

require_once ('config.php');
require_once('PromoService.php');

abstract class ServiceType
{
    const BASKET_SERVICE = 0;
    const IMPORT_SERVICE = 1;
    const EXPORT_SERVICE = 2;   
}

class RESTPromoService extends PromoService implements iPromoService
{
    private function callQixolPromoRestService($method, $url, $data = false)
    {
        $curl = curl_init();
        
        switch ($method)
        {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                
                if ($data)
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
                
            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            
            default:
                if ($data)
                {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }
        
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "username:password");
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($curl);
        
        curl_close($curl);
        
        return $result;
    }

    private function restServiceUrl(ServiceType $serviceType)
    {
        $evaluationServicesUrl = 'https://evaluation.qixolpromo.com/';
        switch ($serviceType)
        {
            case ServiceType::BASKET_SERVICE:
                $liveServicesUrl = 'https://basketmanager.qixolpromo.com/';
                break;
            case ServiceType::IMPORT_SERVICE:
                $liveServicesUrl = 'https://datamanager.qixolpromo.com/';
                break;
            case ServiceType::EXPORT_SERVICE:
                $liveServicesUrl = 'https://datamanager.qixolpromo.com/';
                break;
            default:
                break;
        }

        switch (Mage::getStoreConfig('qixol/integration/services')) {
          case 'evaluation':
            $restServiceUrl = $evaluationServicesUrl;
              break;
          case 'live':
            $restServiceUrl = $liveServicesUrl;
              break;
          case 'custom':
              $restServiceUrl = Mage::getStoreConfig('qixol/integration/restServiceAddress');
              // TODO: check a string exists and is a valid URL?
              if (substr_compare(substr($restServiceUrl, -1), '/', 0) != 0)
              {
                  $restServiceUrl .= '/';
              }
              break;
          default:
            $restServiceUrl = $evaluationServicesUrl;
            break;
      }
        return $restServiceUrl;
    }
    
    public function CustomerGroupExport($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/import/entityvalues';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);
            
            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function ShippingMethodsExport($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/import/entityvalues';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function CurrenciesExport($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/import/entityvalues';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function StoresExport($data)
    {
        // TODO: /import/entityvalues
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/import/hierarchyvalues';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }          
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function ProductsExport($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/import/products';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Products - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }

    public function PromotionsForProducts($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/export/promotionsforproducts';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }

    public function PromotionsForBaskets($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/export/promotionsforbaskets';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function CouponCodesImport()
    {
        $returnValue->message = 'NOT IMPLEMENTED';
        $returnValue->success = false;
        return $returnValue;
    }
    
    public function BasketValidate($data)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/basket/validate';
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $data);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }

    public function CouponCodeValidate($couponCode)
    {
        $url = $this->restServiceUrl().'api/'.Mage::getStoreConfig('qixol/integration/companykey').'/basket/validatecouponcode/'.$couponCode;
        try
        {
            $result = $this->callQixolPromoRestService('POST', $url, $couponCode);

            if (empty($result))
            {
                $returnValue->messsage = 'Stores - no response';
                $returnValue->success = false;
            }
            else
            {
                $returnValue->message = $result;
                $returnValue->success = true;
            }
            
            return $returnValue;
        }
        catch(Exception $e)
        {
            $returnValue->message = print_r($e->faultstring);
            $returnValue->success = false;
            
            return $returnValue;
        }
    }
    
    public function RetrieveValidatedBasket($basketRef)
    {
        $returnValue->message = 'NOT IMPLEMENTED';
        $returnValue->success = false;
        return $returnValue;
    }
    
    public function BasketCheck()
    {
        $returnValue->message = 'NOT IMPLEMENTED';
        $returnValue->success = false;
        return $returnValue;
    }
}
