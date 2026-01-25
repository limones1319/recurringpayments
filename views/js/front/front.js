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
(function ($) {
    $(document).ready(function () {
        $(document).ajaxComplete(function (event, xhr, settings) {
            if (
                typeof settings.data != 'undefined'
                && settings.data != null
                && settings.data.indexOf('quickview') != -1
            ) {
                $('select[name="recurringpayment_period"]').change(function () {
                    var option = $(this).find('option[value="'+$(this).val()+'"]');
                    selectrecurringpayment($(this).val(), option.data('mindate'), option.data('daysids'), option.data('needstartdate'));
                }).change();
            }
        });

        if ($('.product_list').length) {
            var recurringpaymentProductListInit = function () {
                var ids = [];
                $('.ajax_add_to_cart_button').each(function () {
                    if ($(this).next().hasClass('recurringpayment')) {
                        return;
                    }
                    var id = parseInt($(this).data('id-product'));
                    (id > 0 && ids.push(id));
                });
                if (ids.length) {
                    $.ajax({
                        url: an_recurringpayments_url,
                        data: {'action': 'hasrecurringpayment', 'ids': ids},
                        dataType: 'json',
                        success: function (json) {
                            if (json.length) {
                                for (var i = 0; i < json.length; i++) {
                                    var elem = 'a.ajax_add_to_cart_button[data-id-product="' + json[i].id_product + '"]';
                                    if (json[i].only_recurringpayment == '1') {
                                        $(elem).unbind('click')
                                            .attr('href', $(elem).parents('li').find('a.lnk_view').attr('href'));
                                        $(elem).html('<span>'+an_translate.buttonText+'</span>')
                                            .removeClass('ajax_add_to_cart_button').addClass('lnk_view');
                                    } else {
                                        $(elem).after(
                                            $('<a><span>'+an_translate.buttonText+'</span></a>').attr({
                                                'href': $(elem).parents('li').find('a.lnk_view').attr('href'),
                                                'class': 'recurringpayment button btn btn-default lnk_view'
                                            })
                                        );
                                        $('a.recurringpayment').after('<br><br>');
                                        $(elem).parents('li').find('a.lnk_view:last').hide();
                                    }
                                }
                            }
                        }
                    });
                }
            };

            recurringpaymentProductListInit();

            if ($('#layered_block_left').lenght) {
                setInterval(recurringpaymentProductListInit, 500);
            }
        }
    });
})(jQuery);

   function parseISO8601(dateStringInRange)
   {
       var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
        date = new Date(NaN), month,
        parts = isoExp.exec(dateStringInRange);
       if (parts) {
           month = +parts[2];
           date.setFullYear(parts[1], month - 1, parts[3]);
           if (month != date.getMonth() + 1) {
               date.setTime(NaN);
            }
        }
        return date;
    }

    function selectrecurringpayment(id, minDate, dayIds, needStartDate)
    {
        if (needStartDate == 1) {
            $('p.recurringpayment_start_date').show();
        } else {
            $('p.recurringpayment_start_date').hide();
        }

        var min_date = new Date(minDate);
        dayIds = dayIds.toString();
        if (min_date == 'NaN') {
            min_date = parseISO8601(minDate);
        }

        $('input[name="recurringpayment_start_date"]').datepicker('destroy');
        $('input[name="recurringpayment_start_date"]').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: min_date,
            beforeShowDay: function (date) {
                for (i=0; i<dayIds.length; i++) {
                    if (date.getDay() == dayIds.charAt(i)) {
                        return [true, ''];
                    }
                }

                return [false, ''];
            }
        });
    }
