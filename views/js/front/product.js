/**
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
 */


$(document).ready(function () {
    $(document).ajaxSend(function (event, jqxhr, settings ) {
        if (
            settings.hasOwnProperty('data')
            && typeof settings.data != 'undefined'
            && settings.data != null
            && typeof settings.data.indexOf === 'function'
            && settings.data.indexOf('saverecurringpayment') == -1
            && $('select[name="recurringpayment_period"]').length > 0
        ) {
            var period = ($('select[name="recurringpayment_period"]').val() || 0);
            var start_date = $('input[name="recurringpayment_start_date"]').val();

            if ($('select[name="recurringpayment_period"] option[value="-1"]').length == 0 && period == 0) {
                jqxhr.abort();
                $('select[name="recurringpayment_period"]').focus();
                return;
            }

            if (
                $('input[name="recurringpayment_start_date"]:visible').length > 0
                && $('input[name="recurringpayment_start_date"]').val() == ''
            ) {
                jqxhr.abort();
                $('input[name="recurringpayment_start_date"]').focus();
                return;
            }

            if (period > 0 && (start_date != null || start_date != '')) {
                settings.data += '&saverecurringpayment=1&recurringpayment_period='+period+'&recurringpayment_start_date='+start_date;
            }
        }
    });

    $('.product-add-to-cart').before($('#anHider').show());
    $('select[name="recurringpayment_period"]').change(function () {
        var option = $(this).find('option[value="'+$(this).val()+'"]');
        selectrecurringpayment($(this).val(), option.data('mindate'), option.data('daysids'), option.data('needstartdate'));
    }).change();
});
