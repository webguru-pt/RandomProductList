<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Webguru\RandomProductList\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

/**
 * Product list
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
	protected $_urlInterface;
	protected $logger;
	
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ToolbarModel $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param ProductList $productListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Framework\UrlInterface $urlInterface,
		\Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
		$this->_urlInterface = $urlInterface;
		$this->logger = $logger;
        parent::__construct($context, $catalogSession, $catalogConfig, $toolbarModel, $urlEncoder, $productListHelper, $postDataHelper, $data);
    }
	
    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if (($this->getCurrentOrder()) == 'position') {
                $this->_collection->addAttributeToSort(
                    $this->getCurrentOrder(),
                    $this->getCurrentDirection()
                );
            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
		
		$this->logger->debug($this->_urlInterface->getCurrentUrl());
		if (strpos($this->_urlInterface->getCurrentUrl(),'catalogsearch') !== false) {
			$this->logger->debug("No Randomize...");
		}
		else {
			$this->logger->debug("Randomize...");
			$seed = date("Ymd");
			$this->_collection->getSelect()->order('rand('.$seed.')');
		}
		
        return $this;
    }
}
