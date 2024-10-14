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

<article class="post --card-blog --card-blog-{$large}">
    <div datatext="{$post.url|base64_encode nofilter}" class="img_card datatext">
        {if $large == 'xl'}
            {if $post.image.webp_big == 1}
                <picture>
                    <source srcset="{$path_img_posts}{$post.image.big}.webp" type="image/webp">
                    <source srcset="{$path_img_posts}{$post.image.big}" type="image/jpeg">
                    <img src="{$path_img_posts}{$post.image.big}" alt="{$post.title}" loading="lazy" width="800" height="450">
                </picture>
            {else}
                <img src="{$path_img_posts}{$post.image.big}" alt="{$post.title}" loading="lazy" width="800" height="450">
            {/if}
        {else}
            {if $post.image.webp_small == 1}
                <picture>
                    <source srcset="{$path_img_posts}{$post.image.small}.webp" type="image/webp">
                    <source srcset="{$path_img_posts}{$post.image.small}" type="image/jpeg">
                    <img src="{$path_img_posts}{$post.image.small}" alt="{$post.title}" loading="lazy" width="400" height="250">
                </picture>
            {else}
                <img src="{$path_img_posts}{$post.image.small}" alt="{$post.title}" loading="lazy" width="400" height="250">
            {/if}
        {/if}
    </div>
    <div class="card_content">
        <span class="category_post">{$post.title_category}</span>
        {if $large == 'xl'}
            <a class="name_post truncate_1" href="{$post.url}" title="{$post.title}">{$post.title}</a>
        {else}
            <a class="name_post truncate_2" href="{$post.url}" title="{$post.title}">{$post.title}</a>
        {/if}
        <div class="datos_adicionales">
            <span class="author_post">{$post.author.name}</span>
            <span class="date_post">{$post.date}</span>
        </div>
        {* Deprecated version 2.0.0
        {if isset($post_extract) && $post_extract == 1}
            {$post.short_desc nofilter}
        {/if}*}
        {if isset($post_readmore) && $post_readmore == 1}
            <span datatext="{$post.url|base64_encode nofilter}" class="btn btn-secondary read_more datatext">{l s='Leer más'}</span>
        {/if}
    </div>
</article>