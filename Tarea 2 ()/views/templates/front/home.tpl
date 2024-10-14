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
    {$json_ld nofilter}
{/block}

{block name="notifications" append}
    {include file='module:dbblog/views/templates/front/_partials/header.tpl'}
{/block}

{block name="content_wrapper"}

    <div id="content-wrapper" class="content-only">

        <div class="dbblog_content_top">
            {if $destacados|count > 0}
                <div class="home_destacado_principal">
                    {foreach from=$destacados item=post name=dest}
                        {if $smarty.foreach.dest.first}
                            {include file='module:dbblog/views/templates/front/_partials/post_mini.tpl' large='xl'}
                        {/if}
                    {/foreach}
                </div>
            {/if}

            {if $destacados|count > 1}
                <div id="splide_dbblog_destacados" class="home_destacados splide">
                    <div class="splide__track">
                        <div class="splide__list">
                            {foreach from=$destacados item=post name=dest}
                                {if !$smarty.foreach.dest.first}
                                    <div class="splide__slide">
                                        {include file='module:dbblog/views/templates/front/_partials/post_mini.tpl' large='m'}
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener( 'DOMContentLoaded', function () {
                        new Splide( '#splide_dbblog_destacados', {
                            perPage     : 4,
                            pagination: false,
                            lazyLoad: 'sequential',
                            arrows: true,
                            gap: '16px',
                            breakpoints: {
                                575: {
                                    perPage: 1,
                                    padding: {
                                        right: '30%',
                                    },
                                    arrows: false,
                                },
                                767: {
                                    perPage: 2,
                                    padding: {
                                        right: '15%',
                                    },
                                    arrows: false,
                                },
                                992: {
                                    perPage: 2,
                                    padding: {
                                        right: '10%',
                                    },
                                    arrows: false,
                                },
                                1200: {
                                    perPage: 3,
                                }
                            },
                        } ).mount();
                    } );
                </script>
            {/if}


            {if $short_desc|count_characters > 1}
                <div class="db__short-desc --card">
                    {$short_desc nofilter}
                </div>
            {/if}
        </div>

        <div class="dbblog_content_columns">
            <div class="content_left">
                {if $last_posts_home}
                    <div class="dbblog_grid">
                        <p class="title_list">{l s='Últimos posts' mod='dbblog'}</p>
                        <div class="dbblog_list">
                            {foreach from=$last_posts_home item=post}
                                {include file='module:dbblog/views/templates/front/_partials/post_mini.tpl' large='l'}
                            {/foreach}
                        </div>
                    </div>

                    {include file='module:dbblog/views/templates/front/_partials/pagination.tpl'}
                {/if}
            </div>

            <div class="content_right">
                {include file='module:dbblog/views/templates/front/_partials/sidebar_more_views.tpl'}
                {include file='module:dbblog/views/templates/front/_partials/sidebar_rrss.tpl'}
                {include file='module:dbblog/views/templates/front/_partials/sidebar_authors.tpl'}
            </div>
        </div>

        <div class="dbblog_content_bottom">
            {if $large_desc|count_characters > 1}
                <div class="db__large-desc --card">
                    {$large_desc nofilter}
                </div>
            {/if}
        </div>

    </div>

{/block}