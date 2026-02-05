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

{extends file='page.tpl'}

{block name='page_title'}
	{l s='My Recurring Payments' mod='an_recurringpayments'}
{/block}

{block name='page_content'}
{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" rel="nofollow" title="{l s='My Account' mod='an_recurringpayments'}">{l s='My Account' mod='an_recurringpayments'}</a>
	<span class="navigation-pipe">&gt;</span>
	{l s='My Recurring Payments' mod='an_recurringpayments'}
{/capture}


<div id="an_recurringpayments_block_account">
	{if isset($an_error)}<p class="error">{l s='Something went wrong!' mod='an_recurringpayments'}</p>{/if}
	{if count($myrecurringpayments)}
		<table id="recurringpayments-list" class="table table-bordered footab tablet breakpoint footable-loaded footable">
			<thead>
				<tr>
					<th class="first_item">{l s='Product' mod='an_recurringpayments'}</th>
					<th class="item">{l s='Price' mod='an_recurringpayments'}</th>
					<th class="item">{l s='Qty' mod='an_recurringpayments'}</th>
					<th class="item">{l s='Periodicity' mod='an_recurringpayments'}</th>
					<th class="item">{l s='Status' mod='an_recurringpayments'}</th>
					<th class="item">{l s='First Delivery' mod='an_recurringpayments'}</th>
					<th class="item">{l s='Next Payment' mod='an_recurringpayments'}</th>
					<th class="last_item" style="width:65px"></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$myrecurringpayments item=sub}
					{assign var=paymentData value=$sub->getPaymentData()}
					<tr>
						<td>
							<a href="{$link->getProductLink($sub->getProduct())|escape:'html':'UTF-8'}"><b>{$sub->getProduct()->name|escape:'htmlall':'UTF-8'}</b></a>
							<br><small>{$sub->getDesignation()|escape:'htmlall':'UTF-8'}</small>
						</td>
						<td>{Tools::displayPrice($sub->getProduct()->getPrice())|escape:'htmlall':'UTF-8'}</td>
						<td>
							<form action="{$link->getModuleLink('an_recurringpayments', 'account', ['action' => 'saveQty', 'id' => $sub->id])|escape:'html':'UTF-8'}" method="POST">
								<input type="text" name="qty" value="{$sub->qty|escape:'htmlall':'UTF-8'}" style="width:50%"/>
								<a href="javascript:;" class="edit-qty btn btn-default button button-small"><span>{l s='Save' mod='an_recurringpayments'}</span></a>
							</form>
						</td>
						<td>{$sub->getPeriod()->name|escape:'htmlall':'UTF-8'}</td>
						<td>
							{if $sub->vps_is_suspended}
								<span class="label" style="background-color:#dc3545; border-color:#dc3545;">{l s='Suspended' mod='an_recurringpayments'}</span>
							{else}
								{if $sub->status}
									<span class="label" style="background-color:#32CD32; border-color:#32CD32;">{l s='Active' mod='an_recurringpayments'}</span>
								{else}
									<span class="label" style="background-color:#4169E1; border-color:#4169E1;">{l s='Cancelled' mod='an_recurringpayments'}</span>
								{/if}
							{/if}
						</td>
						<td>{Tools::displayDate($sub->start_date)|escape:'htmlall':'UTF-8'}</td>
						<td>{Tools::displayDate($paymentData.payment)|escape:'htmlall':'UTF-8'}</td>
						<td>
							<a href="{$link->getModuleLink('an_recurringpayments', 'account', ['action' => 'delete', 'id' => $sub->id])|escape:'html':'UTF-8'}" onclick="return confirm('{l s='Are you sure?' mod='an_recurringpayments'}')" class="btn btn-default button button-small"><span>{l s='Cancel' mod='an_recurringpayments'}</span></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		<script>
		document.addEventListener("DOMContentLoaded", function(){
			$('#recurringpayments-list .edit-qty').click(function(){
				var tr = $(this).parents('tr:first');
				var input = tr.find('input');
				input.parents('form:first').submit();
			});
		});
		</script>
	{else}
		<p class="warning">{l s='You don\'t have recurring payments' mod='an_recurringpayments'}</p>
	{/if}
	<ul class="footer_links">
		<li class="fleft">
			<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
				<span>
					<i class="icon-chevron-left"></i>
					{l s='Back to your account' mod='an_recurringpayments'}
				</span>
			</a>
		</li>
	</ul>
</div>


{/block}
{block name='page_footer'}
  {block name='my_account_links'}
	{include file='customer/_partials/my-account-links.tpl'}
  {/block}
{/block}
