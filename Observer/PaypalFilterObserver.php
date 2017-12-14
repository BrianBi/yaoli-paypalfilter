<?php 

namespace Yaoli\Paypalfilter\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class PaypalFilterObserver implements ObserverInterface
{
	protected $_objectManager;

	/** @var CheckoutSession */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_response;

	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		CheckoutSession $checkoutSession, 
		ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResponseFactory $_response,
        \Magento\Framework\UrlInterface $_urlInterFace
	)
	{
		$this->_objectManager  = $objectManager;
		$this->checkoutSession = $checkoutSession;
		$this->scopeConfig 	   = $scopeConfig;
		$this->_urlInterface   = $_urlInterFace;
		$this->_response	   = $_response;
	}

	/**
     * @param Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$items = $this->checkoutSession->getQuote()->getAllItems();

    	if ($this->scopeConfig->getValue('yaoli_paypalfilter/general/enable'))
    	{
    		$_limitcate = $this->scopeConfig->getValue('yaoli_paypalfilter/general/category_id');
	    	$flags 		= false;

	    	foreach ($items as $item)
	    	{
	    		$_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
	    		$_cateIds = $_product->getCategoryIds();

	    		if (in_array($_limitcate, $_cateIds))
	    		{
	    			$flags = true;
	    			break;
	    		} else {
	    			continue;
	    		}
	    	}

	    	if ($flags)
	    	{
	    		$url = $this->_urlInterface->getUrl('/');
	    		$this->_response->create()->setRedirect($url)->sendResponse();
	    		exit('123');
	    	}
    	}

    	return $this;
    }
}