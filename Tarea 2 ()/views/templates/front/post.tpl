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
{extends file='page.tpl'}

{block name='head_microdata'}
{/block}

{block name='hook_extra'}
    <meta property="og:url" content="{$url_post}" />
    <meta property="og:title" content="{$post.title}" />
    <meta property="og:description" content="{$post.short_desc|strip_tags}" />
    <meta property="og:image" content="{_PS_BASE_URL_}{$post.img}" />
    {$json_ld nofilter}
{/block}

{block name="notifications" append}
    {include file='module:dbblog/views/templates/front/_partials/header.tpl'}
{/block}

{block name="content_wrapper"}

    <div id="content-wrapper" class="content-only">
        <div class="dbblog_content_top">
            <div class="dbblog_top_post">
                <h1 class="name">{$post.title}</h1>
                <div class="description">{$post.short_desc nofilter}</div>
            </div>
        </div>

        <div class="dbblog_content_columns">
            <div class="content_left">
                <section class="section_post --card">
                    <div class="info_post">
                        <div class="info_author">
                            {if $post.author.imagen.webp_small == 1}
                                <picture>
                                    <source srcset="{$path_img_author}{$post.author.imagen.small}.webp" type="image/webp">
                                    <source srcset="{$path_img_author}{$post.author.imagen.small}" type="image/jpeg">
                                    <img class="img_author" src="{$path_img_author}{$post.author.imagen.small}" alt="{$post.author.name}" loading="lazy" width="48" height="48">
                                </picture>
                            {else}
                                <img class="img_author" src="{$path_img_author}{$post.author.imagen.small}" alt="{$post.author.name}" loading="lazy" width="48" height="48">
                            {/if}
                            <div class="content_author">
                                <a class="link_author" href="{$post.author.url}">{$post.author.name}</a>
                                <div class="more_info">
                                    <span class="profession">{$post.author.profession}</span>
                                    <span class="expert">{$post.author.tag}</span>
                                </div>
                            </div>
                        </div>
                        <div class="info_post">
                            <span class="updated">{l s='Actualizado:' mod='dbblog'} {$post.date_upd}</span>
                            <span class="views">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM432 256c0 79.5-64.5 144-144 144s-144-64.5-144-144s64.5-144 144-144s144 64.5 144 144zM288 192c0 35.3-28.7 64-64 64c-11.5 0-22.3-3-31.6-8.4c-.2 2.8-.4 5.5-.4 8.4c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-2.8 0-5.6 .1-8.4 .4c5.3 9.3 8.4 20.1 8.4 31.6z"></path></svg>
                                {$post.views}
                            </span>
                        </div>
                    </div>

                    {if $post.image.webp_big == 1}
                        <picture>
                            <source srcset="{$path_img_posts}{$post.image.big}.webp" type="image/webp">
                            <source srcset="{$path_img_posts}{$post.image.big}" type="image/jpeg">
                            <img class="img_primary_post" src="{$path_img_posts}{$post.image.big}" alt="{$post.title}" loading="lazy" width="800" height="450">
                        </picture>
                    {else}
                        <img class="img_primary_post" src="{$path_img_posts}{$post.image.big}" alt="{$post.title}" loading="lazy" width="800" height="450">
                    {/if}

                    <div class="post_share">
                        <span>{l s='Compartir' mod='dbblog'}:</span>
                        <span class="share_link datatext" datatext="{$share_facebook|base64_encode}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"/></svg>
                        </span>
                        {*<span class="share_link datatext" datatext="{$share_instagram|base64_encode}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                        </span>*}
                        <span class="share_link datatext" datatext="{$share_twitter|base64_encode}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
                        </span>
                        <span class="share_link datatext" datatext="{$share_pinterest|base64_encode}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"/></svg>
                        </span>
                        <span class="share_link datatext" datatext="{$share_linkedin|base64_encode}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"/></svg>
                        </span>
                    </div>

                    <div class="large_desc">{$desc nofilter}</div>
                </section>

                {if $more_posts_author|count > 0}
                    <div class="more_posts_author --card">
                        <span class="title_sidebar">{l s='Más del autor' mod='dbblog'}</span>
                        <ul class="post_list_horizontal">
                            {foreach from=$more_posts_author item=post}
                                <li>
                                    <div class="img_post datatext" datatext="{$post.url|base64_encode}">
                                        <img src="{$post.img}" alt="{$post.title}" alt="{$post.title}" width="126" height="71" loading="lazy">
                                    </div>
                                    <div class="content_post">
                                        <span class="category">{$post.title_category}</span>
                                        <a class="name_post truncate_2" href="{$post.url}">{$post.title}</a>
                                        <div class="post-meta">
                                            <span class="author">{$post.author.name}</span>
                                            <span class="views">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM432 256c0 79.5-64.5 144-144 144s-144-64.5-144-144s64.5-144 144-144s144 64.5 144 144zM288 192c0 35.3-28.7 64-64 64c-11.5 0-22.3-3-31.6-8.4c-.2 2.8-.4 5.5-.4 8.4c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-2.8 0-5.6 .1-8.4 .4c5.3 9.3 8.4 20.1 8.4 31.6z"></path></svg>
                                                {$post.views}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div>

            <div class="content_right">
                {include file='module:dbblog/views/templates/front/_partials/sidebar_more_views.tpl'}
                {include file='module:dbblog/views/templates/front/_partials/sidebar_rrss.tpl'}
                {include file='module:dbblog/views/templates/front/_partials/sidebar_last_posts.tpl'}
            </div>
        </div>

        <div class="dbblog_content_bottom">
            {if $active_comments == 1}
                <div class="dbblog_comments --card">
                    <span class="title_sidebar">{$comments|count} {l s='comentarios' mod='dbblog'}</span>
                    <section class="form_comments">
                        <p class="title_comment">{l s='Escribe un comentario' mod='dbblog'}</p>
                        {include file='module:dbblog/views/templates/front/_partials/form_comment.tpl'}
                    </section>
                </div>

                    {if $comments|count > 0}
                        <section class="list_comments">
                            <div class="comentarios_users">
                                <div class="append_comments">
                                    {foreach from=$comments item=comment}
                                        <div class='comentario --card'>
                                            <span class="name">{$comment.name}</span>
                                            <span class="date">{$comment.date_add}</span>
                                            <p class="text_comment">{$comment.comment|strip_tags:true}</p>
                                            <button type="button" class="btn btn_form_respond" data-id-post="{$post.id}" data-id-comment="{$comment.id_dbblog_comment}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M205 34.8c11.5 5.1 19 16.6 19 29.2v64H336c97.2 0 176 78.8 176 176c0 113.3-81.5 163.9-100.2 174.1c-2.5 1.4-5.3 1.9-8.1 1.9c-10.9 0-19.7-8.9-19.7-19.7c0-7.5 4.3-14.4 9.8-19.5c9.4-8.8 22.2-26.4 22.2-56.7c0-53-43-96-96-96H224v64c0 12.6-7.4 24.1-19 29.2s-25 3-34.4-5.4l-160-144C3.9 225.7 0 217.1 0 208s3.9-17.7 10.6-23.8l160-144c9.4-8.5 22.9-10.6 34.4-5.4z"/></svg>
                                            </button>

                                            <div id="form_respond" class="form_respond_{$comment.id_dbblog_comment}"></div>
                                        </div>

                                        {if isset($comment.children)}
                                            {foreach from=$comment.children item=comment2}
                                                <div class='comentario --card padding_1'>
                                                    <span class="name">{$comment2.name}</span>
                                                    <span class="date">{$comment2.date_add}</span>
                                                    <p class="text_comment">{$comment2.comment|strip_tags:true}</p>
                                                    {*<button type="button" class="btn btn_form_respond" data-id-post="{$post.id}" data-id-comment="{$comment.id_dbblog_comment}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M205 34.8c11.5 5.1 19 16.6 19 29.2v64H336c97.2 0 176 78.8 176 176c0 113.3-81.5 163.9-100.2 174.1c-2.5 1.4-5.3 1.9-8.1 1.9c-10.9 0-19.7-8.9-19.7-19.7c0-7.5 4.3-14.4 9.8-19.5c9.4-8.8 22.2-26.4 22.2-56.7c0-53-43-96-96-96H224v64c0 12.6-7.4 24.1-19 29.2s-25 3-34.4-5.4l-160-144C3.9 225.7 0 217.1 0 208s3.9-17.7 10.6-23.8l160-144c9.4-8.5 22.9-10.6 34.4-5.4z"/></svg>
                                                    </button>*}
{*                                                    <div id="form_respond" class="form_respond_{$comment2.id_dbblog_comment}"></div>*}
                                                </div>

                                                {if isset($comment2.children)}
                                                    {foreach from=$comment2.children item=comment3}
                                                        <div class='comentario --card padding_2'>
                                                            <div class="comment_info">
                                                                <div class="other_info">
                                                                    <span class="name">{$comment3.name}</span>
                                                                </div>
                                                                <span><small>{$comment3.date_add}</small></span>
                                                            </div>
                                                            <div class="comment_desc">
                                                                <p>{$comment3.comment|strip_tags:true}</p>
                                                            </div>
                                                        </div>
                                                    {/foreach}
                                                {/if}
                                            {/foreach}
                                        {/if}
                                    {/foreach}

                                </div>
                            </div>
                        </section>
                    {/if}

            {/if}
        </div>
    </div>
{/block}