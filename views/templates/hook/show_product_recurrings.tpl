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
	<table style="border: solid 1pt black; padding:0 10pt">
		<tr><td></td><td></td></tr>
		<tr><td><b>{l s='New Recurring Payment' mod='an_recurringpayments'} </b></td><td></td></tr>
		<tr><td></td><td></td></tr>
		{foreach from=$new_sub item=sub}
            {assign var=payment_data value=$sub->getPaymentData()}
			<tr><td><b>{$sub->getProduct()->name|escape:'html':'UTF-8'}</b></td><td>{$sub->getDesignation()|escape:'html':'UTF-8'}</td></tr>
			<tr><td><b>{l s='Periodicity:' mod='an_recurringpayments'}</b></td><td>{$sub->getPeriod()->name|escape:'html':'UTF-8'}</td></tr>
			<tr><td><b>{l s='Next Payment:' mod='an_recurringpayments'}</b></td><td>{$payment_data.payment|escape:'html':'UTF-8'}</td></tr>
			<tr><td><b>{l s='Next Delivery:' mod='an_recurringpayments'}</b></td><td>{$payment_data.delivery|escape:'html':'UTF-8'}</td></tr>
		{/foreach}
		<tr><td></td><td></td></tr>
	</table>
{/if}

{if isset($an_sub)}
	<table style="border: solid 1pt black; padding:0 10pt">
		<tr><td></td><td></td></tr>
		<tr><td><b>{l s='New Recurring Payment' mod='an_recurringpayments'} </b></td><td></td></tr>
		<tr><td></td><td></td></tr>
		{foreach from=$an_sub item=sub}
			<tr><td><b>{$sub->getProduct()->name|escape:'html':'UTF-8'}</b></td><td>{$sub->getDesignation()|escape:'html':'UTF-8'}</td></tr>
			<tr><td><b>{l s='Periodicity:' mod='an_recurringpayments'}</b></td><td>{$sub->getPeriod()->name|escape:'html':'UTF-8'}</td></tr>
			<tr><td><b>{l s='Delivery:' mod='an_recurringpayments'}</b></td><td>{date('Y-m-d', strtotime('+'|cat:$sub->getPeriod()->require_payment_before|cat:'day', $order_date_add))|escape:'html':'UTF-8'}</td></tr>
		{/foreach}
		<tr><td></td><td></td></tr>
	</table>
{/if}
