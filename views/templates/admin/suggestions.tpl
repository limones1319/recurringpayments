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

{$contact_us = '#'}
{$rate_url = '#'}
{$anvanto_url = 'https://bit.ly/2TH0AJ6'}

{if isset($theme.url_contact_us)}
{$contact_us = $theme.url_contact_us}
{/if}

{if isset($theme.url_rate)}
{$rate_url = $theme.url_rate}
{/if}

<link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700&display=swap" rel="stylesheet">
<style>
.an_panel {
    border-radius: 5px;
    margin: 0 4px 39px;
    font-family: 'Ubuntu', sans-serif;
}
.an_panel-link {
    text-decoration: underline!important;
}

.an_panel_info {
    font-family: 'Ubuntu', sans-serif;
    display: flex;
    margin-bottom: 0px;
}
.an_panel_info-item {
    background: #fff;
    box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    padding: 12px 16px 12px 16px;
    max-width: 330px;
    width: 100%;
    margin-right: 20px;
    margin-bottom: 20px;
}
.an_panel_info-item:last-child {
    margin-right: 0;
}
.an_panel_info-item-contact {
    border-left: 3px solid #21a6cb;
}
.an_panel_info-item-rate {
    border-left: 3px solid #fed500;
}
.an_panel_info-item-docs {
    border-left: 3px solid #e56b93;
}
.an_panel_info-item-ad {
    border-left: 3px solid #0ca300;
}
.an_panel_info-item h2 {
    font-size: 16px;
    font-family: 'Ubuntu', sans-serif;
	font-weight: bold;
    margin: 0 0 4px;
}
.an_panel_info-item p {
    font-size: 14px;
    line-height: 20px;
    margin: 0;
}
.an_panel_info .grade {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    margin-top: 13px;
}
.an_panel_info-stars {
    transition: all .2s;
    padding-right: 3px;
}
.an_panel_info-stars path {
    fill: #e0e0e0;
}
.an_panel_info-stars:nth-of-type(1):hover path,
.an_panel_info-stars:nth-of-type(2):hover path,
.an_panel_info-stars:nth-of-type(3):hover path,
.an_panel_info-stars:nth-of-type(4):hover path,
.an_panel_info-stars:nth-of-type(5):hover path,
.an_panel_info-stars:nth-of-type(1):hover ~ .an_panel_info-stars:nth-of-type(n+1) path,
.an_panel_info-stars:nth-of-type(2):hover ~ .an_panel_info-stars:nth-of-type(n+2) path,
.an_panel_info-stars:nth-of-type(3):hover ~ .an_panel_info-stars:nth-of-type(n+3) path,
.an_panel_info-stars:nth-of-type(4):hover ~ .an_panel_info-stars:nth-of-type(n+4) path,
.an_panel_info-stars:nth-of-type(5):hover ~ .an_panel_info-stars:nth-of-type(n+5) path {
    fill: #fed500;
}
@media (max-width: 1366px) {
    .an_panel_info-item {
        max-width: 50%;
    }
}
@media (max-width: 767px) {
    .an_panel_info-item {
        max-width: 100%;
        margin-right: 0;
    }
    .an_panel_info {
        flex-direction: column;
    }
}
@media (max-width: 480px) {
    .an_panel_info-item {
        margin-right: 0;
    }
    .an_panel_modules-item {
        flex-direction: column;
        padding: 20px 0;
        position: relative;
    }
    .an_panel_modules-item-title {
        position: static;
    }
    .an_panel_modules-disabled-flag {
        top: 20px;
    }
}
</style>

<div class="an_panel_info">
    <div class="an_panel_info-item an_panel_info-item-contact">
        <h2>Contact Us</h2>
        <p><a class="an_panel-link" href="{$contact_us}" target="_blank">Contact us</a> on any question or problem with the module</p>
    </div>
	{if $rate_url <> ''}
    <div class="an_panel_info-item an_panel_info-item-rate">
        <h2>Rate{if isset($theme.name) AND $theme.name != ''} "{$theme.name}"{/if}</h2>
        <div class="grade">
            <a href="{$rate_url}" target="_blank" id="star5" class="an_panel_info-stars">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="21px" height="20px">
                   <path fill-rule="evenodd"  fill="rgb(254, 213, 0)"
                    d="M20.495,9.068 C20.889,8.667 21.028,8.079 20.858,7.532 C20.688,6.985 20.245,6.595 19.701,6.512 L14.860,5.778 C14.654,5.746 14.476,5.611 14.384,5.416 L12.220,0.833 C11.977,0.318 11.484,-0.002 10.934,-0.002 C10.386,-0.002 9.892,0.318 9.649,0.833 L7.485,5.416 C7.393,5.612 7.214,5.746 7.008,5.778 L2.168,6.513 C1.624,6.595 1.181,6.986 1.011,7.532 C0.841,8.079 0.980,8.667 1.373,9.068 L4.875,12.635 C5.025,12.787 5.093,13.006 5.058,13.220 L4.232,18.257 C4.158,18.700 4.270,19.131 4.544,19.472 C4.971,20.002 5.716,20.163 6.312,19.836 L10.640,17.458 C10.821,17.359 11.049,17.359 11.229,17.458 L15.558,19.836 C15.769,19.952 15.993,20.010 16.225,20.010 C16.648,20.010 17.049,19.814 17.325,19.472 C17.600,19.131 17.711,18.699 17.638,18.257 L16.811,13.220 C16.776,13.006 16.844,12.787 16.993,12.635 L20.495,9.068 Z"/>
                </svg>
            </a>
            <a href="{$contact_us}" target="_blank" id="star4" class="an_panel_info-stars">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="21px" height="20px">
                   <path fill-rule="evenodd"  fill="rgb(254, 213, 0)"
                    d="M20.495,9.068 C20.889,8.667 21.028,8.079 20.858,7.532 C20.688,6.985 20.245,6.595 19.701,6.512 L14.860,5.778 C14.654,5.746 14.476,5.611 14.384,5.416 L12.220,0.833 C11.977,0.318 11.484,-0.002 10.934,-0.002 C10.386,-0.002 9.892,0.318 9.649,0.833 L7.485,5.416 C7.393,5.612 7.214,5.746 7.008,5.778 L2.168,6.513 C1.624,6.595 1.181,6.986 1.011,7.532 C0.841,8.079 0.980,8.667 1.373,9.068 L4.875,12.635 C5.025,12.787 5.093,13.006 5.058,13.220 L4.232,18.257 C4.158,18.700 4.270,19.131 4.544,19.472 C4.971,20.002 5.716,20.163 6.312,19.836 L10.640,17.458 C10.821,17.359 11.049,17.359 11.229,17.458 L15.558,19.836 C15.769,19.952 15.993,20.010 16.225,20.010 C16.648,20.010 17.049,19.814 17.325,19.472 C17.600,19.131 17.711,18.699 17.638,18.257 L16.811,13.220 C16.776,13.006 16.844,12.787 16.993,12.635 L20.495,9.068 Z"/>
                </svg>
            </a>
            <a href="{$contact_us}"  target="_blank" class="an_panel_info-stars">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="21px" height="20px">
                   <path fill-rule="evenodd"  fill="rgb(254, 213, 0)"
                    d="M20.495,9.068 C20.889,8.667 21.028,8.079 20.858,7.532 C20.688,6.985 20.245,6.595 19.701,6.512 L14.860,5.778 C14.654,5.746 14.476,5.611 14.384,5.416 L12.220,0.833 C11.977,0.318 11.484,-0.002 10.934,-0.002 C10.386,-0.002 9.892,0.318 9.649,0.833 L7.485,5.416 C7.393,5.612 7.214,5.746 7.008,5.778 L2.168,6.513 C1.624,6.595 1.181,6.986 1.011,7.532 C0.841,8.079 0.980,8.667 1.373,9.068 L4.875,12.635 C5.025,12.787 5.093,13.006 5.058,13.220 L4.232,18.257 C4.158,18.700 4.270,19.131 4.544,19.472 C4.971,20.002 5.716,20.163 6.312,19.836 L10.640,17.458 C10.821,17.359 11.049,17.359 11.229,17.458 L15.558,19.836 C15.769,19.952 15.993,20.010 16.225,20.010 C16.648,20.010 17.049,19.814 17.325,19.472 C17.600,19.131 17.711,18.699 17.638,18.257 L16.811,13.220 C16.776,13.006 16.844,12.787 16.993,12.635 L20.495,9.068 Z"/>
                </svg>
            </a>
            <a href="{$contact_us}"  target="_blank" class="an_panel_info-stars">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="21px" height="20px">
                   <path fill-rule="evenodd"  fill="rgb(254, 213, 0)"
                    d="M20.495,9.068 C20.889,8.667 21.028,8.079 20.858,7.532 C20.688,6.985 20.245,6.595 19.701,6.512 L14.860,5.778 C14.654,5.746 14.476,5.611 14.384,5.416 L12.220,0.833 C11.977,0.318 11.484,-0.002 10.934,-0.002 C10.386,-0.002 9.892,0.318 9.649,0.833 L7.485,5.416 C7.393,5.612 7.214,5.746 7.008,5.778 L2.168,6.513 C1.624,6.595 1.181,6.986 1.011,7.532 C0.841,8.079 0.980,8.667 1.373,9.068 L4.875,12.635 C5.025,12.787 5.093,13.006 5.058,13.220 L4.232,18.257 C4.158,18.700 4.270,19.131 4.544,19.472 C4.971,20.002 5.716,20.163 6.312,19.836 L10.640,17.458 C10.821,17.359 11.049,17.359 11.229,17.458 L15.558,19.836 C15.769,19.952 15.993,20.010 16.225,20.010 C16.648,20.010 17.049,19.814 17.325,19.472 C17.600,19.131 17.711,18.699 17.638,18.257 L16.811,13.220 C16.776,13.006 16.844,12.787 16.993,12.635 L20.495,9.068 Z"/>
                </svg>
            </a>
            <a href="{$contact_us}"  target="_blank" class="an_panel_info-stars">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="21px" height="20px">
                   <path fill-rule="evenodd"  fill="rgb(254, 213, 0)"
                    d="M20.495,9.068 C20.889,8.667 21.028,8.079 20.858,7.532 C20.688,6.985 20.245,6.595 19.701,6.512 L14.860,5.778 C14.654,5.746 14.476,5.611 14.384,5.416 L12.220,0.833 C11.977,0.318 11.484,-0.002 10.934,-0.002 C10.386,-0.002 9.892,0.318 9.649,0.833 L7.485,5.416 C7.393,5.612 7.214,5.746 7.008,5.778 L2.168,6.513 C1.624,6.595 1.181,6.986 1.011,7.532 C0.841,8.079 0.980,8.667 1.373,9.068 L4.875,12.635 C5.025,12.787 5.093,13.006 5.058,13.220 L4.232,18.257 C4.158,18.700 4.270,19.131 4.544,19.472 C4.971,20.002 5.716,20.163 6.312,19.836 L10.640,17.458 C10.821,17.359 11.049,17.359 11.229,17.458 L15.558,19.836 C15.769,19.952 15.993,20.010 16.225,20.010 C16.648,20.010 17.049,19.814 17.325,19.472 C17.600,19.131 17.711,18.699 17.638,18.257 L16.811,13.220 C16.776,13.006 16.844,12.787 16.993,12.635 L20.495,9.068 Z"/>
                </svg>
            </a>
        </div>
    </div>
	{/if}
    <div class="an_panel_info-item an_panel_info-item-docs">
        <h2><a class="an_panel-link" href="{$anvanto_url}" target="_blank">What's next?</a></h2>
        <p>Find out how to improve your shop with <a href="{$anvanto_url}" class="suggestions-link"  target="_blank">other modules and themes</a> made by Anvanto.</p>
    </div>
	
	{*
     <div class="an_panel_info-item an_panel_info-item-ad">
        <h2><a class="an_panel-link" href="{$theme.url_doc}" target="_blank">New 4th block</a></h2>
        <p>New next for this block</p>
     </div>	
	*}
</div>