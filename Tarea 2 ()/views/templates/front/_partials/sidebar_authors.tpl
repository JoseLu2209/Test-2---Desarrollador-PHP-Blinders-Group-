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

{if $authors && ($isHome == 1 || $isCategory == 1)}
    <div class="db_authors_sidebar --card">
        <span class="title_sidebar">{l s='Nuestros Autores' mod='dbblog'}</span>
        <ul class="authors_list_sidebar">
            {foreach from=$authors item=author}
                <li>
                    {if $author.imagen.webp_small == 1}
                        <picture>
                            <source srcset="{$path_img_author}{$author.imagen.small}.webp" type="image/webp">
                            <source srcset="{$path_img_author}{$author.imagen.small}" type="image/jpeg">
                            <img class="img_author" src="{$path_img_author}{$author.imagen.small}" alt="{$author.name}" loading="lazy" width="90" height="90">
                        </picture>
                    {else}
                        <img class="img_author" src="{$path_img_author}{$author.imagen.small}" alt="{$author.name}" loading="lazy" width="90" height="90">
                    {/if}
                    <div class="info_author">
                        <a class="name truncate_2" href="{$author.url}">{$author.name}</a>
                        <span class="profession">{$author.profession}</span>
                        <span class="posts">{$author.comments_author} {l s='Posts' mod='dbblog'}</span>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}