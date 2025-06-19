<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseFactory;
use Zabba\Module\ZSearchSphinx\Site\Helper\ZSearchSphinxHelper;

class ModVirtuemartZBulkAddToCartHelper
{
    public static function getAjax() 
    {
        $input = Factory::getApplication()->getInput();
        if ($input -> exists('query'))
        {
            $q  = $input->post->get('query', '', 'string');
            $condition = preg_replace('/[^A-Za-z0-9\- ]/', '', $q);
            $options = ZSearchSphinxHelper::pripojDatabazi('sphinx');
            $database = new DatabaseFactory();
            $db = $database->getDriver('mysql', $options);
            $stmt = $db->getQuery(true);
            $aq = explode(' ',$q);
            if(strlen($aq[count($aq)-1])<3)
            {
        	$query = $q;
            }
            else
            {
                $query = $q.'*';
            }
            $stmt
                ->select($db->quoteName("product_name"))
                ->from($db->quoteName("#__sphinx_test1"))
                ->where("MATCH"."(".$db->quote($query).")"." ORDER BY product_in_stock DESC LIMIT  0,10 OPTION ranker=sph04");

            $db->setQuery($stmt);
            $results = $db->loadObjectList();
            $replace_string = '<b>'.$condition.'</b>';
            if($results)
            {
                foreach($results as $row)
                {
                    $data[] = array(
                        'product_name'		=>	str_ireplace($condition, $replace_string, $row->product_name)
                    );
                }
            echo json_encode($data);
            }
            else{
                echo json_encode('');
            }
        }
        $input = Factory::getApplication()->getInput()->json;
        if ($input -> exists('search_query'))
        {
            $post_data = json_decode(file_get_contents('php://input'), true);
            $data = array(
		':search_query'		=>	$post_data['search_query']
            );
             $name = (string) preg_replace('/[^\p{L}\d\s]/u', ' ', $data[':search_query']);
            $options = ZSearchSphinxHelper::pripojDatabazi('sphinx');
            $database = new DatabaseFactory();
            $db = $database->getDriver('mysql', $options);
            $query = $db->getQuery(true);
            $query
                ->select($db->quoteName(array("product_sku", "product_name")))
                ->from($db->quoteName("#__sphinx_test1"))
                ->where("MATCH"."(".$db->quote($name).")");
/*            ->select($db->quoteName(array('product_sku', 'product_name')))
            ->from($db->quoteName('#__sphinx_test1'))
            ->where('MATCH(product_name) AGAINST(' . $db->quote($name) . ' IN BOOLEAN MODE)'); */

            $db->setQuery($query);
            $result = $db->loadObject();
            if($result)
            {

            $output = array(
		'product_sku'	=>	$result->product_sku,
                'product_name'	=>	$result->product_name
            );
            echo json_encode($output);
            }
            else{
                echo json_encode('');
            }

        }
    }
}
