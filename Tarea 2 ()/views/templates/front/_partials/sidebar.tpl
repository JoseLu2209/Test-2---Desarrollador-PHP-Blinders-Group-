{block name="right_column"}
    <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
        {* <div class="db_blog_search">
            <form role="search" method="get" class="db_blog_search_widget" action=""> 
                <input id="dbSearch" type="search" placeholder="Buscar" name="s" class="db_blog_search_input"> 
            </form>
        </div> *}

        {if $isCategory == 1 && $subcategories|count > 0}
        <div class="db_subcategorie_sidebar --card-blog">
            <span class="db_title_h4 bck_title">{l s='Subcategorías' mod='dbblog'}</span>
            <ul class="subcategories_sidebar">
                {foreach from=$subcategories item=category}
                <li>
                    <a href="{$category.url}">{$category.title}</a>
                </li>
                {/foreach}
            </ul>
        </div> 
        {/if}

        {if $more_views}
        <div class="db_more_views_sidebar --card-blog">
            <span class="db_title_h4 bck_title">{l s='Más visto' mod='dbblog'}</span>
            <ul class="post_list_sidebar">
                {foreach from=$more_views item=post}
                <li>
                    <a href="{$post.url}">
                        <img src="{$post.img}" alt="{$post.title}">
                    </a>
                    <div class="title">
                        <a href="{$post.url}">{$post.title}</a>
                        <div class="post-meta">
                            <span class="updated"><img class="visibilidad" src="{$path_img}icons/visibilidad.svg" alt="{l s='Visualizaciones' mod='dbblog'}"> {$post.views}</span> | <a href="{$post.url_category}"">{$post.title_category}</a>
                        </div>
                    </div>
                    </a>
                </li>
                {/foreach}
            </ul>
        </div> 
        {/if}

        {if $rrss == 1}
        <div class="db_rrss --card-blog">
            <span class="db_title_h4 bck_title">{l s='Síguenos' mod='dbblog'}</span>
            <ul>
                {if !empty($twitter)}
                    <li><a href="{$twitter}" target="_blank"><img src="{$path_img}icons/twitter.svg" alt="Twitter"> {l s='Twitter' mod='dbblog'}</a></li>
                {/if}
                {if !empty($facebook)}
                    <li><a href="{$facebook}" target="_blank"><img src="{$path_img}icons/facebook.svg" alt="Facebook"> {l s='Facebook' mod='dbblog'}</a></li>
                {/if}
                {if !empty($linkedin)}
                    <li><a href="{$linkedin}" target="_blank"><img src="{$path_img}icons/linkedin.svg" alt="Linkedin"> {l s='Linkedin' mod='dbblog'}</a></li>
                {/if}
                {if !empty($youtube)}
                    <li><a href="{$youtube}" target="_blank"><img src="{$path_img}icons/youtube.svg" alt="YouTube"> {l s='YouTube' mod='dbblog'}</a></li>
                {/if}
                {if !empty($instagram)}
                    <li><a href="{$instagram}" target="_blank"><img src="{$path_img}icons/instagram.svg" alt="Instagram"> {l s='Instagram' mod='dbblog'}</a></li>
                {/if}
            </ul>
        </div>
        {/if}

        {if $authors && ($isHome == 1 || $isCategory == 1)}
        <div class="db_authors --card-blog">
            <span class="db_title_h4 bck_title">{l s='Nuestros Autores' mod='dbblog'}</span>
            <ul>
                {foreach from=$authors item=author}
                    <li>
                        <a class="side_author" href="{$author.url}">
                            <img src="{$author.imagen}" alt="{$author.name}">
                            <div class="side_info_author">
                                <span class="name">{$author.name} <small>{$author.comments_author} {l s='Posts' mod='dbblog'}</span>
                                <span class="profession">{$author.profession}</span>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        {/if}

        {if $last_posts && $isAuthor == 0}
        <div class="db_more_views_sidebar --card-blog">
            <span class="db_title_h4 bck_title">{l s='Últimos artículos' mod='dbblog'}</span>
            <ul class="post_list_sidebar">
                {foreach from=$last_posts item=post}
                <li>
                    <a href="{$post.url}">
                        <img src="{$post.img}" alt="{$post.title}">
                    </a>
                    <div class="title">
                        <a href="{$post.url}">{$post.title}</a>
                        <div class="post-meta">
                            <span class="updated">{$post.date}</span> | <a href="{$post.url_category}"">{$post.title_category}</a>
                        </div>
                    </div>
                    </a>
                </li>
                {/foreach}
            </ul>
        </div> 
        {/if}
    </div>
{/block}