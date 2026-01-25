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
<script type="text/javascript">
    var an_recurringpayments_url = '{$link->getModuleLink('an_recurringpayments', 'front', [], false)|escape:'html':'UTF-8'}';
    var an_translate = {
        buttonText: '{l s='Subscribe!' mod='an_recurringpayments'}',
    };

    {if isset($recurringpayments)}
    {literal}
    document.addEventListener("DOMContentLoaded", function(){
        $(document).ready(function(){
    {/literal}
            {foreach from=$recurringpayments item=_item}
            {if $_item.id_product|intval == 0}
            var elem = $('.cart-summary .card-block')[0];
            elem.append($('</br><div style="max-width:200px;float:right"><small style  class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small></div>'));
            {/if}
            var elem = $('.cart_item_recc[id^="product_{$_item.id_product|intval}_{$_item.id_product_attribute|intval}"]');
            if (elem.length) {
                var line = elem.closest('.product-line-grid');

                if (line.length){
                    var desc = line.find('.product-line-grid-body');
                    desc.append('<small class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small>');
                } else if(!line.length) {
                    line = elem.closest('.cart_item');
                    var desc = line.find('.product-line-grid-body');
                    desc.append('<small class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small>');
                } else {
                    elem.append('</br><div style="max-width:200px;float:right"><small style  class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small></div>');
                }
            }
            {/foreach}
            {literal}
        });
        $(document).ajaxComplete(function (event, xhr, settings) {
            if (settings.hasOwnProperty('url')
                && typeof settings.url != 'undefined'
                && settings.url != null
                && settings.url.indexOf('ajax') != -1
                && (settings.url.indexOf('refresh') != -1 || settings.url.indexOf('add') != -1)
            ) {
                setTimeout(function(){
            {/literal}
                    {foreach from=$recurringpayments item=_item}
                    var elem = $('.cart_item_recc[id^="product_{$_item.id_product|intval}_{$_item.id_product_attribute|intval}"]');
                    if (elem.length) {
                        var line = elem.closest('.product-line-grid');
                        if (line.length){
                            var desc = line.find('.product-line-grid-body');
                            desc.append('<small class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small>');
                        } else {
                            elem.append('</br><div style="max-width:200px;float:right"><small style  class="recurringpayment_cart">{l s='Recurring Payment:' mod='an_recurringpayments'} <b>{$_item.period->name|escape:'html':'UTF-8'}</b>; {l s='First Delivery:' mod='an_recurringpayments'} <b>{$_item.displayDate|escape:'html':'UTF-8'}</b></small></div>');
                        }
                    }
                    {/foreach}
    {literal}
                }, 50);
            }
        });
    });
    {/literal}
    {/if}
</script>
