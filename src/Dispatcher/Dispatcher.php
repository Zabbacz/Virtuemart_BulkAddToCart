<?php

namespace Zabba\Module\ZBulkAddToCart\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
//use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Zabba\Module\ZBulkAddToCart\Site\Helper\ZBulkAddToCartHelper;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    protected function getLayoutData()
    {
        $params = new Registry($this->module->params);
        $data    = parent::getLayoutData();
        $helperName    = 'ZBulkAddToCartHelper';
//        $data['urlcart'] = $this->getHelperFactory()->getHelper($helperName)->setCart();
//        $data['return_values'] = $this->getHelperFactory()->getHelper($helperName)->getControl();
        $data['return_values'] = $this->getHelperFactory()->getHelper($helperName)->setCart();
        $data['clear_values'] = $this->getHelperFactory()->getHelper($helperName)->getClear();
    return $data;

    }
}