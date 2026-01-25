{*
* 2021 Anvanto
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
*
*  @author Anvanto <anvantoco@gmail.com>
*  @copyright  2021 Anvanto
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of Anvanto
*}
<div id="anHider" style="display: {$anHide_display|escape:'html':'UTF-8'}">
	<div class="product_attributes_">
		<input type="hidden" name="saverecurringpayment" value="1" />
		<p id="">
			<label{if version_compare($smarty.const._PS_VERSION_, '1.7.3.0', '>=')} style="float: left"{/if}>{l s='Recurring Payment:' mod='an_recurringpayments'}</label>
			<select name="recurringpayment_period" class="form-control">
				{if $recurringpayment.only_recurringpayment neq 1}
					<option data-mindate="" data-daysids="" value="-1">{l s='None' mod='an_recurringpayments'}</option>
				{/if}
				{foreach from=$recurringpayment.periods item=row}
					<option data-mindate="{$row->getMinDate()|escape:"htmlall"}" data-daysids="{$row->getDaysIds()|escape:"htmlall"}" value="{$row->id|intval}" data-needstartdate="{$row->can_define_start_date|intval}">{$row->name|escape:"htmlall"}</option>
				{/foreach}
			</select>
		</p>
		<p class="recurringpayment_start_date">
			<label{if version_compare($smarty.const._PS_VERSION_, '1.7.3.0', '>=')} style="float: left"{/if}>{l s='First Delivery:' mod='an_recurringpayments'}</label>
			<input type="text" name="recurringpayment_start_date" class="text form-control" value="">
		</p>
	</div>
</div>
