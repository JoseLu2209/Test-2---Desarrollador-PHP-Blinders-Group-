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

{if $last_posts}
    <div class="db_last_posts_sidebar --card">
        <span class="title_sidebar">{l s='Últimos artículos' mod='dbblog'}</span>
        <ul class="post_list_sidebar">
            {$number = 1}
            {foreach from=$last_posts item=post}
                <li>
                    <span class="number_sidebar">{$number}</span>
                    <div class="content">
                        <a class="name_post truncate_3" href="{$post.url}" title="{$post.title}">{$post.title}</a>
                    </div>
                </li>
                {$number = $number+1}
            {/foreach}
        </ul>
    </div>
{/if}