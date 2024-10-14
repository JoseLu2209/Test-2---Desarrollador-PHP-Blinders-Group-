{block name='breadcrumb'}
    <nav class="breadcrumb_blog">
        <ol>
            {foreach from=$breadcrumb.links item=path name=breadcrumb}
                {block name='breadcrumb_item'}
                <li>
                    {if not $smarty.foreach.breadcrumb.last}<a href="{$path.url}">{/if}
                        <span>{$path.title}</span>
                    {if not $smarty.foreach.breadcrumb.last}</a>{/if}
                </li>
                {/block}
            {/foreach}
        </ol>
    </nav>
{/block}