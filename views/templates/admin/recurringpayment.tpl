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
<div class="panel clearfix">
	<div class="panel-heading">
		<i class="icon-credit-card"></i>
		{l s='View Recurring Payment' mod='an_recurringpayments'}
	</div>
	<div class="form-horizontal">
		<div class="row">
			<label class="control-label col-lg-3">{l s='Customer:' mod='an_recurringpayments'} </label>
			<div class="col-lg-9">
				<p class="form-control-static">
					{if $an_obj->getCustomer()->id}
						<a href="{$link->getAdminLink('AdminCustomers', true)|escape:'html':'UTF-8'}&id_customer={$an_obj->getCustomer()->id|intval}&viewcustomer">
							{$an_obj->getCustomer()->firstname|escape:'htmlall':'UTF-8'} {$an_obj->getCustomer()->lastname|escape:'htmlall':'UTF-8'}
						</a>
					{else}
						{l s='Guest' mod='an_recurringpayments'}
					{/if}
				</p>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="row">
			<label class="control-label col-lg-3">{l s='Product:' mod='an_recurringpayments'} </label>	
			<div class="col-lg-9">
				<p class="form-control-static">
					<a href="{$link->getAdminLink('AdminProducts', true)|escape:'html':'UTF-8'}&id_product={$an_obj->getProduct()->id|intval}&updateproduct">
						{$an_obj->getProduct()->name|escape:'htmlall':'UTF-8'}
					</a>
				</p>
			</div>
			<div class="clear"></div>
		</div>
		{if $an_obj->getDesignation()}
		<div class="row">
			<label class="control-label col-lg-3">{l s='Attributes:' mod='an_recurringpayments'} </label>	
			<div class="col-lg-9">
				<p class="form-control-static">
					{$an_obj->getDesignation()|escape:'htmlall':'UTF-8'}
				</p>
			</div>
			<div class="clear"></div>
		</div>
		{/if}				
		<div class="row">
			<label class="control-label col-lg-3">{l s='Quantity:' mod='an_recurringpayments'} </label>	
			<div class="col-lg-9">
				<p class="form-control-static">
					{$an_obj->qty|intval}
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
								
		<div class="row">
			<label class="control-label col-lg-3">{l s='Period:' mod='an_recurringpayments'} </label>
			<div class="col-lg-9">
				<p class="form-control-static">
					<b>{$an_obj->getperiod()->name|escape:'htmlall':'UTF-8'}</b>
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
								
		<div class="row">
			<label class="control-label col-lg-3">{l s='Status:' mod='an_recurringpayments'} </label>
			<div class="col-lg-9">
				<p class="form-control-static">
					{if $an_obj->status eq 1}
						<span class="label" style="background-color:#32CD32; border-color:#32CD32;">{l s='Active' mod='an_recurringpayments'}</span>
					{else}
						<span class="label" style="background-color:#4169E1; border-color:#4169E1;">{l s='Disabled' mod='an_recurringpayments'}</span>
					{/if}
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
							
		<div class="row">
			<label class="control-label col-lg-3">{l s='First Delivery:' mod='an_recurringpayments'} </label>	
			<div class="col-lg-9">
				<p class="form-control-static">
					{Tools::displayDate($an_obj->start_date)|escape:'htmlall':'UTF-8'}
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
		
		{assign var=paymentData value=$an_obj->getPaymentData()}							
		<div class="row">
			<label class="control-label col-lg-3">{l s='Next Payment:' mod='an_recurringpayments'} </label>
			<div class="col-lg-9">
				<p class="form-control-static">
				{if isset($paymentData.payment)}
					{Tools::displayDate($paymentData.payment)|escape:'htmlall':'UTF-8'}
				{/if}
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
								
		<div class="row">
			<label class="control-label col-lg-3">{l s='Next Delivery:' mod='an_recurringpayments'} </label>
			<div class="col-lg-9">
				<p class="form-control-static">
				{if isset($paymentData.delivery)}
					{Tools::displayDate($paymentData.delivery)|escape:'htmlall':'UTF-8'}
				{/if}
				</p>
			</div>							
			<div class="clear"></div>								
		</div>
		<div class="clear"></div>
	</div>
</div>
