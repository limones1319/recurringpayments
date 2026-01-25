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
{if isset($new_sub)}
	<div class="panel well">
		<h3>{l s='New Recurring Payment' mod='an_recurringpayments'}</h3>
		{foreach from=$new_sub item=sub}
			{assign var=payment_data value=$sub->getPaymentData()}
			<b>{$sub->getProduct()->name|escape:'html':'UTF-8'}</b> {$sub->getDesignation()|escape:'html':'UTF-8'}<br/><br/>
			<b>{l s='Periodicity:' mod='an_recurringpayments'}</b> {$sub->getPeriod()->name|escape:'html':'UTF-8'} <br/>
			<b>{l s='Next Payment:' mod='an_recurringpayments'}</b> {$payment_data.payment|escape:'html':'UTF-8'}<br/>
			<b>{l s='Next Delivery:' mod='an_recurringpayments'}</b> {$payment_data.delivery|escape:'html':'UTF-8'}
			<br/><br/>
		{/foreach}
	</div>
{/if}
{if isset($an_sub)}
	<div class="panel well">
		<h3>{l s='Products ordered by Recurring Payments' mod='an_recurringpayments'}</h3>
		{foreach from=$an_sub item=sub}
			<b>{$sub->getProduct()->name|escape:'html':'UTF-8'}</b> {$sub->getDesignation()|escape:'html':'UTF-8'}<br/>
			<b>{l s='Periodicity:' mod='an_recurringpayments'}</b> {$sub->getPeriod()->name|escape:'html':'UTF-8'} 
			({l s='Delivery:' mod='an_recurringpayments'} {date('Y-m-d', strtotime('+'|cat:$sub->getPeriod()->require_payment_before|cat:'day', $order_date_add))|escape:'html':'UTF-8'})
			<br/><br/>
		{/foreach}
	</div>
{/if}
