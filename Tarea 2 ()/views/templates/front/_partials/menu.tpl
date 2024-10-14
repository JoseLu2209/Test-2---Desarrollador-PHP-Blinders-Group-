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

<span class="dbblog_menu" data-toggle="modal" data-target="#dbblog_menu">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM192 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H160c17.7 0 32 14.3 32 32z"/></svg>
    <span class="name_menu">{l s='Categorías' mod='dbblog'}</span>
</span>
<div class="modal fade right" id="dbblog_menu" tabindex="-1" role="dialog" aria-labelledby="dbblog_menu_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="dbblog_menu_modal_Label">{l s='Categorías' mod='dbblog'}</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg>
                    </span>
                </button>
            </div>
            <div class="modal-body">

                {* Items secundarios *}
                {foreach from=$categories item=$category}
                    {include file='module:dbblog/views/templates/front/_partials/submenus.tpl' linkback=$category.title}
                {/foreach}

                {* Items primarios *}
                <div class="dbblog_primary">
                    <div class="dbblog_category">
                        {foreach from=$categories item=category key=key}
                            {if !isset($category.childrens)}
                                <a class="item_primary" href="{$category.url}" title="{$category.title}">{$category.title}</a>
                            {else}
                                <span class="item_primary open_subitems" data-subitem="subitems_{$category.id_dbblog_category}">
                                    {$category.title}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path d="M89.45 87.5l143.1 152c4.375 4.625 6.562 10.56 6.562 16.5c0 5.937-2.188 11.87-6.562 16.5l-143.1 152C80.33 434.1 65.14 434.5 55.52 425.4c-9.688-9.125-10.03-24.38-.9375-33.94l128.4-135.5l-128.4-135.5C45.49 110.9 45.83 95.75 55.52 86.56C65.14 77.47 80.33 77.87 89.45 87.5z"/></svg>
                                </span>
                            {/if}
                        {/foreach}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>