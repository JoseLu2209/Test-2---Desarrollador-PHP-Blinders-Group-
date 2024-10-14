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

{if isset($category.childrens)}
    <div class="subitems subitems_{$category.id_dbblog_category}">
        <p class="dbblog_back" data-subitem="subitems_{$category.id_dbblog_category}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path d="M166.5 424.5l-143.1-152c-4.375-4.625-6.562-10.56-6.562-16.5c0-5.938 2.188-11.88 6.562-16.5l143.1-152c9.125-9.625 24.31-10.03 33.93-.9375c9.688 9.125 10.03 24.38 .9375 33.94l-128.4 135.5l128.4 135.5c9.094 9.562 8.75 24.75-.9375 33.94C190.9 434.5 175.7 434.1 166.5 424.5z"/></svg>
            {l s='Volver' mod='dbblog'}
        </p>

        <div class="content_subitems">
            <a class="item_viewall subitem" href="{$category.url}">
                {l s='Ver' mod='dbblog'} {$category.title}
            </a>
            {foreach from=$category.childrens key=$tipo item=dropdown}
                {if isset($dropdown.childrens)}
                    <span class="item_primary open_subitems" data-subitem="subitems_{$tipo}">
                        {$dropdown.title}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path d="M89.45 87.5l143.1 152c4.375 4.625 6.562 10.56 6.562 16.5c0 5.937-2.188 11.87-6.562 16.5l-143.1 152C80.33 434.1 65.14 434.5 55.52 425.4c-9.688-9.125-10.03-24.38-.9375-33.94l128.4-135.5l-128.4-135.5C45.49 110.9 45.83 95.75 55.52 86.56C65.14 77.47 80.33 77.87 89.45 87.5z"/></svg>
                    </span>
                    {assign var=menu value=$dropdown}
                    {include file='module:dbblog/views/templates/hook/subitems.tpl' linkback=$dropdown.title}
                {else}
                    <a class="subitem" href="{$dropdown.url}">
                        {$dropdown.title}
                    </a>
                {/if}
            {/foreach}
        </div>
    </div>
{/if}
