<?php
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$wa->registerStyle('mod_virtuemart_zbulkaddtocart.style', 'mod_virtuemartzbulkaddtocart/styl.css');
$wa->useStyle('mod_virtuemart_zbulkaddtocart.style');

//JHtml::stylesheet($root.'./modules/mod_virtuemart_bulkaddtocart/css/styl.css')    ;
?>
<form method="post">
<div class="container">
<fieldset class="container">
	<br /><p class="chyba"><?=$urlcart?></p>
		<div>
			__<input type='text' class='inputtable' value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_PRODUCT_SKU'); ?>"  readonly> 
			<input type='text' class='inputtable' value="<?php echo Text::_('MOD_VIRTUEMART_BULKADDTOCART_QTY'); ?>"  readonly> <br/>
		</div>
<?php
$c = 1;
 if($clear_values==1) unset($return_values);
do {
	$value_sku=$return_values[$c-1]['sku'];
	$value_qty=$return_values[$c-1]['qty']
?>
		<div>
			<?= $c ?>.
			<input type='text' class='inputtable' name='id<?= $c ?>' value='<?=$value_sku?>' minlength="3" maxlength="16" '>
			<input type='number' class='inputtable' name='pocet<?= $c ?>' value='<?=$value_qty?>' min="1" max="9999">
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
