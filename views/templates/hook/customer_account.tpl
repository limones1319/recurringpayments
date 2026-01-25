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
{if $an_new}
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$link->getModuleLink('an_recurringpayments', 'account')|escape:'html':'UTF-8'}" title="{l s='My Recurring Payments' mod='an_recurringpayments'}">
    <span class="link-item">
      <i class="material-icons">&#xE916;</i>
        {l s='My Recurring Payments' mod='an_recurringpayments'}
    </span>
    </a>
{/if}

{if !$an_new}
<li class="referralprogram">
    <a href="{$link->getModuleLink('an_recurringpayments', 'account')|escape:'html':'UTF-8'}" title="{l s='My Recurring Payments' mod='an_recurringpayments'}" rel="nofollow"><i class="icon-cogs"></i>
        <span>{l s='My Recurring Payments' mod='an_recurringpayments'}</span>
    </a>
</li>
{/if}
