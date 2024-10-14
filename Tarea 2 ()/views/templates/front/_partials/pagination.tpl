{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="dbblog_infinitescroll">
    <p class="sum_infinite">{l s='Has visto' mod='dbblog'} <span id="sum_infinite_from" class="sum_infinite_from" data-sumin="{$posts_per_page}">{$posts_per_page}</span> {l s='de' mod='dbblog'} <span class="total_posts" data-total="{$total_posts}">{$total_posts}</span> {l s='posts' mod='dbblog'}</p>

    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: {$percent_view}%" aria-valuenow="{$percent_view}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    {if $pagination == 1}
        <div id="btn_db_inifinitescroll" class="btn btn-secondary btn_db_inifinitescroll" data-category="{if $isHome == 0}{$category.id}{else}0{/if}" data-pag="1">{l s='Cargar más' mod='dbblog'}</div>
    {/if}
</div>