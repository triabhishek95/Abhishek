<?php

namespace Abhishek\HidePrice\Pricing\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Render\FinalPriceBox as OriginalFinalPriceBox;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Config\ScopeConfigInterface;

class FinalPriceBox extends OriginalFinalPriceBox{

	public function __construct(
		Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null,
        HttpContext $httpContext,
        ScopeConfigInterface $scopeConfig,
        CustomerContext $customerContext
    ){
    	parent::__construct($context, $saleableItem, $price, $rendererPool, $data, $salableResolver, $minimalPriceCalculator);
    	$this->_httpContext = $httpContext;
    	$this->_scopeConfig = $scopeConfig;
    	$this->_customerContext = $customerContext;
	}

	Const ModuleStatus = 'hideprice/general_settings/enabled';

	Const BaseURL = 'web/unsecure/base_url';

	/**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    	$moduleStatus = $this->_scopeConfig->getValue(self::ModuleStatus, $storeScope);

    	if($moduleStatus == 1){

	    	$isLoggedin = $this->_httpContext->getValue($this->_customerContext::CONTEXT_AUTH);

	    	$baseUrl = $this->_scopeConfig->getValue(self::BaseURL, $storeScope);

	    	if($isLoggedin){
	    		return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
	            'data-role="priceBox" ' .
	            'data-product-id="' . $this->getSaleableItem()->getId() . '" ' .
	            'data-price-box="product-id-' . $this->getSaleableItem()->getId() . '"' .
	            '>' . $html . '</div>';	
	    	} 
	        else{
	        	return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
	            'data-role="priceBox" ' .
	            'data-product-id="' . $this->getSaleableItem()->getId() . '" ' .
	            'data-price-box="product-id-' . $this->getSaleableItem()->getId() . '"' .
	            ' style="width:50%;"> <a class="btn btn-primary" href="'.$baseUrl.'customer/account/login" >Please Login to See the Price </a> </div>';
	        }
    	}else{
    		return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
	            'data-role="priceBox" ' .
	            'data-product-id="' . $this->getSaleableItem()->getId() . '" ' .
	            'data-price-box="product-id-' . $this->getSaleableItem()->getId() . '"' .
	            '>' . $html . '</div>';
    	}
    }
}