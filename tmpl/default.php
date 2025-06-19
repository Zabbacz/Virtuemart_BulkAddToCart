<?php
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
//use Zabba\Module\ZBulkAddToCart\Site\Helper\ZBulkAddToCartHelper;

$app = Factory::getApplication();

//$wa->getRegistry()->addExtensionRegistryFile('mod_virtuemart_zbulkaddtocart');
/*$wa->registerScript('mod_virtuemart_zbulkaddtocart.search', 'mod_virtuemart_zbulkaddtocart/bulk_search.js');
$wa->useScript('mod_virtuemart_zbulkaddtocart.bulk_search');
$wa->registerStyle('mod_virtuemart_zbulkaddtocart.style', 'mod_virtuemart_zbulkaddtocart/styl.css');
$wa->useStyle('mod_virtuemart_zbulkaddtocart.style');
*/
$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_virtuemart_zbulkaddtocart');
$wa->useScript('mod_virtuemart_zbulkaddtocart.bulk_search');
$wa->useStyle('mod_virtuemart_zbulkaddtocart.style');


//JHtml::stylesheet($root.'./modules/mod_virtuemart_bulkaddtocart/css/styl.css')    ;
?>
<form method="post">
<div class="container">
<fieldset class="container">
	<br /><p class="chyba"><?=$urlcart?></p>
		<div>
                    <label for="bulkhledatkochech">
                    <input type="checkbox" id="bulkHledatkoCheck" name="bulkHledatkoCheck">
                    Zapnout pomocníka při hledání kódů (pokud zapnete, můžete do pole objednací číslo zadávat kód produktu, název nebo EAN)
                    </label><br />
			__<input type='text' class='inputtable' value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_PRODUCT_SKU'); ?>"  readonly> 
			<input type='text' class='inputtable' value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_QTY'); ?>"  readonly> <br/>
		</div>
<?php
$c = 1;
if (isset($clear_values) && $clear_values === 1) {
    unset($return_values);
}
do {
	$value_sku=$return_values[$c-1]['sku'];
	$value_qty=$return_values[$c-1]['qty'];
?>
		<div>
			<?= $c ?>.
			<input type='text' id='inputBulk<?= $c ?>' class='inputtable' name='id<?= $c ?>' value='<?=$value_sku?>' 
                               minlength="3" maxlength="64" autocomplete="off"><!--16-->
                         <span class='searchSpan' id="searchResultBulkAddToCart<?= $c ?>"></span>
			<input type='number' class='inputtable' id ='pocetBulk<?= $c ?>' name='pocet<?= $c ?>' value='<?=$value_qty?>' min="1" max="9999">
                        <p><span class='nameSpanBulk' id="nameSpanBulk<?= $c ?>"></span></p>
		</div>	

<?php
$c++;
}
while($c<16);
?>
</fieldset>
<input type="submit" name="odesli"  class="btn btn-primary" value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_ADD_TO_CART'); ?>" >
<!--		<input type="submit" name="kontrola" class="kontrolni_tlacitko" value="Kontrola"> -->
<input type="submit" name="zrus"  class="btn btn-danger" onclick="if(confirm('Smazat položky z formuláře ?')){}else{return false;};" value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_CLEAR_FORM'); ?>" > 


</div>			
</form>
