<?php
namespace Zabba\Module\ZBulkAddToCart\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
//use Joomla\CMS\Filesystem\File;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use VirtuemartControllerConfig;
use VirtueMartCart;
use stdClass;
use OPCmini;
use OPCAddToCartAsLink;

class ZBulkAddToCartHelper
{
 /**
	 * Retrieve foo test
	 *
	 * @param   Registry        $params  The module parameters
	 * @param   CMSApplication  $app     The application
	 *
	 * @return  array
	 */
    public static function setCart()
    {
        $cartData = self::importData();
        if ($cartData[0]['sku']) return $cartData;
        if ($cartData[0]['virtuemart_product_id']) 
        {    
            $importovanoPolozek = self::loadCartData($cartData); 
            $txt = 'Úspěšně bylo importováno '.(int)$importovanoPolozek.' položek';
            return self::returnHandler($txt);
        }
    }
    
    private static function importData() 
    {
        $o=1;
//        $id="";
        $qty="";
  //      $url="";
        $nezarazeno=array();
        $input = Factory::getApplication()->getInput();
//        if ($input->exists('p1'))
        if ($input -> exists('odesli'))
        {
            while($o<16)
            {
                $sku = $input->get('id'.$o);
                $qty = $input->get('pocet'.$o);
                //validate sku
                if (!empty($sku) && !empty($qty)) 
                {
                    $db = Factory::getContainer()->get(DatabaseInterface::class);
                    $query = $db->getQuery(true)
                        ->select ($db->quoteName('virtuemart_product_id'))
                        ->from ($db->quoteName('#__virtuemart_products'))
                        ->where ($db->quoteName('product_sku'). ' = \''.$db->escape($sku).'\''); 
                    $db->setQuery($query); 
                    $virtuemart_product_id = (int)$db->loadResult();
                    if (empty($virtuemart_product_id))
                    {
                        $db = Factory::getContainer()->get(DatabaseInterface::class);
                        $query = $db->getQuery(true)
                            ->select ($db->quoteName('virtuemart_product_id'))
                            ->from ($db->quoteName('#__virtuemart_products'))
                            ->where ($db->quoteName('product_gtin'). ' = \''.$db->escape($sku).'\'');
                        $db->setQuery($query); 
                        $virtuemart_product_id = (int)$db->loadResult(); 
                    }
                    if (!empty($virtuemart_product_id)) 
                    {
                        $cart_row['virtuemart_product_id'] = (int)$virtuemart_product_id; 
                        //validate qty
                        $cart_row['quantity'] = 0; 
                        if (preg_match('/^[0-9]+$/', $qty)) 
                        {
                            if (is_numeric($qty)) 
                            {
                                $cart_row['quantity'] = (int)$qty; 
                            }
                        }
                        $cart_row['customProductData'] = array(); 
                        $cartProductsData[] = $cart_row; 
                    }
                    else
                    {
                        $nezarazeno[$o]=(string)$sku;
                    }    
                }	
                $o++;
            }
            if (!empty($nezarazeno))
            {
                $saveItems = self::getControl($nezarazeno,$cartProductsData);
                return $saveItems;
            }
            else
                return $cartProductsData;
            }
    }

    private static function loadCartData($cartData) {
	self::loadVM(); 
	$cart = VirtuemartCart::getCart(); 
	$cart->cartData = array();
	$cart->products = array();
	$obj = new \stdClass(); 
	$obj->cart =& $cart; 
	foreach ($cartData as $ind=>$p) {
            $add_id = array(); 
            $qadd = array(); 
            $other = array(); 
            $add_id[$ind] = $p['virtuemart_product_id']; 
            $qadd[$ind] = $p['quantity']; 
            foreach($p as $key=>$val) 
            {
                if (in_array($key, $items ??[])) continue; 
                    $other[$ind][$key] = $val; 
            }
    /*
	link_type: 
	0 -> feature disabled
	1 -> deletect cart and set link products
	2 -> do not increment quantity and do not delete cart
	3 -> increment quantity and do not delete cart
    */
            require_once(JPATH_ROOT.'/components/com_onepage/helpers/mini.php'); 
            require_once(JPATH_ROOT.'/components/com_onepage/helpers/addtocartaslink.php'); 
            OPCAddToCartAsLink::addtocartaslink($obj,$add_id, $qadd, $other, false, 3); 
	}
    	$cartX = OPCmini::getCart(); 
	return count($cartData);
	}

    public static function loadVM() 
    {
	if (!class_exists('VmConfig'))	  
	{
            require (JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php'); 
	}
	\VmConfig::loadConfig(); 
	if (!class_exists('VirtueMartCart'))
        {
            require(JPATH_SITE.'/components/com_virtuemart/helpers/cart.php');
        }
	Factory::getLanguage()->load('com_virtuemart'); 
  
    }

    private static function returnHandler($txt='') 
    {
//        $return = Request::getVar('return', ''); 
        $return = Factory::getApplication()->getInput()->get('return', '');
        $redirecttocart = (int)self::getParams()->get('redirecttocart', 0); 
        if ($redirecttocart === 0) 
        {
            $url = Route::_('index.php?option=com_virtuemart&view=cart'); 
            if (empty($txt)) 
            {
                Factory::getApplication()->redirect($url); 
            }
            else 
            {
                Factory::getapplication()->enqueueMessage($txt, 'notice'); 
                Factory::getApplication()->redirect($url); 
            }
        }
        if (!empty($return)) 
        {
            $url = base64_decode($return); 
            if (empty($txt)) 
            {
                Factory::getApplication()->redirect($url); 
            }
            else 
            {
                Factory::getapplication()->enqueueMessage($txt, 'notice'); 
                Factory::getApplication()->redirect($url); 
            }
        }
    }
	
    private static function getParams() 
    {
        static $params; 
	if (!empty($params)) return $params; 
        $module_id = (int)Factory::getApplication()->getInput()->get('module_id', 0);
	if (!empty($module_id)) 
        {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select ($db->quoteName('params'))
                ->from ($db->quoteName('#__modules'))
                ->where ($db->quoteName('id'). ' = '.(int)$module_id); 
            $db->setQuery($query); 
            $params_txt = $db->loadResult();
            if (!empty($params_txt)) 
            {
                $params = new Registry($params_txt); 
		return $params; 
            }
	}
	else
	{
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select ($db->quoteName('params'))
                ->from ($db->quoteName('#__modules'))
                ->where ($db->quoteName('module'). ' = '.'\'mod_virtuemart_zbulkaddtocart\'')
                ->where ($db->quoteName('published'). ' = '.(int)1);                            
            $db->setQuery($query); 
            $params_txt = $db->loadResult();
            if (!empty($params_txt))
            {
                $params = new Registry($params_txt); 
		return $params; 
            }
	}
	return new Registry(''); 
    }
    
    public static function getControl($nezarazeno, $cartProductsData)
    {
        $urlcart = $urlcart." ".Text::_('MOD_VIRTUEMART_BULKADDTOCART_NO_INSERT')."  <br /> ";
        $urlcart = $urlcart.Text::_('MOD_VIRTUEMART_BULKADDTOCART_CORRECT_ITEMS')." <br />";
       	foreach ($nezarazeno as $ind=>$p) 
        {
            $skuNezarazeno = $p; 
            $urlcart = $urlcart.Text::_('MOD_VIRTUEMART_BULKADDTOCART_ITEM')." ".$skuNezarazeno." ".Text::_('MOD_VIRTUEMART_BULKADDTOCART_ITEM_INVALID')."  <br /> ";
        }
        Factory::getapplication()->enqueueMessage($urlcart, 'warning'); 
            if($cartProductsData)
            {
                foreach ($cartProductsData as $ind=>$p)
                {
                    $db = Factory::getContainer()->get(DatabaseInterface::class);
                    $query = $db->getQuery(true)
                        ->select ($db->quoteName('product_sku'))
                        ->from ($db->quoteName('#__virtuemart_products'))
                        ->where ($db->quoteName('virtuemart_product_id'). ' = '.$p['virtuemart_product_id']);
                    $db->setQuery($query); 
                    $virtuemart_product_sku = (string)$db->loadResult(); 
                    $saveItems[$ind]['sku'] = $virtuemart_product_sku;
                    $saveItems[$ind]['qty'] = $p['quantity'];
                }
                return $saveItems;
            }
    }	
    
    public static function getClear()
    {
//      if (!empty($_POST['zrus']))
    $clear_values = 0;
    $input = Factory::getApplication()->getInput();
    if ($input -> exists('zrus'))
      {
        $clear_values = 1;

    }
    return $clear_values;	
    }	
}

 
