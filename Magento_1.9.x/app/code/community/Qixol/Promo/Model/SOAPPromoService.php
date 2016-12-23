<?php

require_once ('config.php');
require_once('PromoService.php');

class SOAPPromoService extends PromoService implements iPromoService
{

    private function importServiceUrl() {
        $evaluationImportServicesUrl = 'https://evaluation.qixolpromo.com/ImportService.svc';
        $liveImportServicesUrl = 'https://datamanager.qixolpromo.com/ImportService.svc';

        switch (Mage::getStoreConfig('qixol/integration/services')) {
              case 'evaluation':
                $importServiceUrl = $evaluationImportServicesUrl;
                  break;
              case 'live':
                $importServiceUrl = $liveImportServicesUrl;
                  break;
              case 'custom':
                  $importServiceUrl = Mage::getStoreConfig('qixol/integration/importManagerServiceAddress');
                  break;
              default:
                $importServiceUrl = $evaluationImportServicesUrl;
                break;
          }
        return $importServiceUrl;
    }
    
    private function exportServiceUrl() {
        $evaluationExportServicesUrl = 'https://evaluation.qixolpromo.com/ExportService.svc';
        $liveExportServicesUrl = 'https://datamanager.qixolpromo.com/ExportService.svc';

        switch (Mage::getStoreConfig('qixol/integration/services')) {
              case 'evaluation':
                $exportServiceUrl = $evaluationExportServicesUrl;
                  break;
              case 'live':
                $exportServiceUrl = $liveExportServicesUrl;
                  break;
              case 'custom':
                  $exportServiceUrl = Mage::getStoreConfig('qixol/integration/exportManagerServiceAddress');
                  break;
              default:
                $exportServiceUrl = $evaluationExportServicesUrl;
                break;
          }
        return $exportServiceUrl;
    }

    private function basketServiceUrl() {

        $evaluationBasketServicesUrl = 'https://evaluation.qixolpromo.com/BasketService.svc';
        $liveBasketServicesUrl = 'https://basketmanager.qixolpromo.com/BaskettService.svc';

        switch (Mage::getStoreConfig('qixol/integration/services')) {
          case 'evaluation':
            $basketServiceUrl = $evaluationBasketServicesUrl;
              break;
          case 'live':
            $basketServiceUrl = $liveBasketServicesUrl;
              break;
          case 'custom':
              $basketServiceUrl = Mage::getStoreConfig('qixol/integration/basketManagerServiceAddress');
              break;
          default:
            $basketServiceUrl = $evaluationBasketServicesUrl;
            break;
      }
        return $basketServiceUrl;
    }
    
    function CustomerGroupExport($data)
    {
        $importServiceUrl = $this->importServiceUrl();
        $importServiceUrlWsdlRequest = $importServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($importServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                                      'location'  => $importServiceUrl));
        $types_array = $soapclient->__getTypes();
        $functions_array = $soapclient->__getFunctions();

        try {
            $result = $soapclient->__soapCall('ImportEntityAttributeValues', array('ImportEntityAttributeValues' => array('xmlToImport' => $data)));
            $returnValue->message = $result->ImportEntityAttributeValuesResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }
    }
    
    function ShippingMethodsExport($data)
    {
        $importServiceUrl = $this->importServiceUrl();
        $importServiceUrlWsdlRequest = $importServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($mportServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                                      'location'  => $importServiceUrl));
        $types_array = $soapclient->__getTypes();
        $functions_array = $soapclient->__getFunctions();
        try {
          $result = $soapclient->__soapCall('ImportEntityAttributeValues', array('ImportEntityAttributeValues' => array('xmlToImport' => $data)));
            $returnValue->message = $result->ImportEntityAttributeValuesResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }
    }

    function CurrenciesExport($data)
    {
        $importServiceUrl = $this->importServiceUrl();
        $importServiceUrlWsdlRequest = $importServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($importServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                                      'location'  => $importServiceUrl));
        $types_array = $soapclient->__getTypes();
        $functions_array = $soapclient->__getFunctions();
        try {
            $result = $soapclient->__soapCall('ImportEntityAttributeValues', array('ImportEntityAttributeValues' => array('xmlToImport' => $data)));
            $returnValue->message = $result->ImportEntityAttributeValuesResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }
    }

    function StoresExport($data)
    {
        $importServiceUrl = $this->importServiceUrl();
        $importServiceUrlWsdlRequest = $importServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($importServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                                      'location'  => $importServiceUrl));
        $types_array = $soapclient->__getTypes();
        $functions_array = $soapclient->__getFunctions();
        try {
            $result = $soapclient->__soapCall('ImportEntityAttributeValues', array('ImportEntityAttributeValues' => array('xmlToImport' => $data)));
            $returnValue->message = $result->ImportEntityAttributeValuesResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }

    }
    
    function ProductsExport($data)
    {
        $importServiceUrl = $this->importServiceUrl();
        $importServiceUrlWsdlRequest = $importServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($importServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                                            'location'  => $importServiceUrl));
        $types_array = $soapclient->__getTypes();
        $functions_array = $soapclient->__getFunctions();
            try {
            $result = $soapclient->__soapCall('ImportProducts', array('ImportProducts' => array('xmlToImport' => $data)));
            $returnValue->message = $result->ImportProductsResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }
    }
    
    public function PromotionsForProducts($data)
    {
        $exportServiceUrl = $this->exportServiceUrl();
        $exportServiceUrlWsdlRequest = $exportServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($exportServiceUrlWsdlRequest, array(    'trace'     => 1,
                                                                                        'location'  => $exportServiceUrl));
        try {
            $result = $soapclient->__soapCall('RetrievePromotionsForProducts', array('RetrievePromotionsForProducts' => array('xmlExportRequest' => $data)));
            $returnValue->message = $result->RetrievePromotionsForProductsResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $returnValue->message = $e->faultstring;
            $returnValue->success = false;
            return $returnValue;
        }
    }

    public function PromotionsForBaskets($data)
    {
        $exportServiceUrl = $this->exportServiceUrl();
        $exportServiceUrlWsdlRequest = $exportServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($exportServiceUrlWsdlRequest, array(  'trace'     => 1,
                                                                              'location'  => $exportServiceUrl));

        try
        {
            $result = $soapclient->__soapCall('RetrievePromotionsForBaskets', array('RetrievePromotionsForBaskets' => array('xmlExportRequest' => $data)));
            $returnValue->message = $result->RetrievePromotionsForBasketsResult;
            $returnValue->success = true;
            return $returnValue;
        }
        catch (SoapFault $e)
        {
            $returnValue->message = $e->faultstring;
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
        $basketServiceUrl = $this->basketServiceUrl();
        $basketServiceUrlWsdlRequest = $basketServiceUrl . '?singleWsdl';
        $soapclient = new soapclient($basketServiceUrlWsdlRequest, array(    'trace' => 1,
                                                                          'location' => $basketServiceUrl));

        try
        {
            $result = $soapclient->__soapCall('ValidateBasket', array('ValidateBasket' => array('basketXml' => $data)));
            $returnValue->message = $result->ValidateBasketResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            $message = $e->faultstring . '\n';
            $message .= "REQUEST:\n" . $soapclient->__getLastRequestHeaders();
            $message .= $soapclient->__getLastRequest() . "\n";
            $message .= "RESPONSE:\n" . $soapclient->__getLastResponseHeaders();
            $message .= $soapclient->__getLastResponse() . "\n";

            $returnValue->message = $message;
            $returnValue->success = false;
            return $returnValue;
        }
    }

    public function CouponCodeValidate($couponCode)
    {
        $basketServiceUrl = $this->basketServiceUrl();
        $basketServiceUrlWsdlRequest = $basketServiceUrl . '?singleWsdl';
        $soapclient_coupon = new soapclient($basketServiceUrlWsdlRequest, array( 'trace'     => 1,
                                                                                'location'  => $basketServiceUrl));
        
        try
        {
            $soapclient_coupon->__soapCall('ValidateCouponCode', array('ValidateCouponCode' =>
                                                                array('companyKey' =>
                                                                    Mage::getStoreConfig('qixol/integration/companykey'),
                                                                'couponCode' => $couponCode)));
            $returnValue->message = $result->ValidateCouponCodeResult;
            $returnValue->success = true;
            return $returnValue;
        } catch (SoapFault $e) {
            //$message = $e->faultstring . '\n';
            //$message .= "REQUEST:\n" . $soapclient->__getLastRequestHeaders();
            //$message .= $soapclient->__getLastRequest() . "\n";
            //$message .= "RESPONSE:\n" . $soapclient->__getLastResponseHeaders();
            //$message .= $soapclient->__getLastResponse() . "\n";

            $returnValue->message = $message;
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
