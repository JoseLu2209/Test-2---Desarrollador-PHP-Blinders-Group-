<script type="application/ld+json">{ 
    "@context" : "http://schema.org",  
    "@type" : "Article",
    "url" : "{$post.url}",
    "mainEntityOfPage": "{$post.short_desc|strip_tags}",
    "articleSection": "{$post.title_category}",
    "wordCount": {$post.large_desc|count_words},
    "headline": "{$post.title}",
    "alternativeHeadline": "{$post.meta_title} - {$title_blog}",
    "commentCount": {$post.comments.total},
    "creator": "{$post.author.name}",
    "dateCreated": "{$post.date_add}",
    "dateModified": "{$post.date_upd}",
    "datePublished": "{$post.date_add}",
    "inLanguage": "es", 
    "image": "{$post.img}",
    "thumbnailUrl": "{$post.img}",
    "comment": [
        {foreach from=$comments item=comment name=foo}
        {
            "@type": "comment",
            "author": {
                "@type": "Person",
                "name": "{$comment.name}" 
            },
            "datePublished": "{$comment.date_add}",
            "comment": "{$comment.comment}"
        }{if !$smarty.foreach.foo.last},{/if}
        {if $comment.children}
            {foreach from=$comment.children item=comment name=foo}
            {
                "@type": "comment",
                "author": {
                    "@type": "Person",
                    "name": "{$comment.name}" 
                },
                "datePublished": "{$comment.date_add}",
                "comment": "{$comment.comment}"
            },
            {/foreach}
        {/if}
        {/foreach}
    ],
    
    "author": {
        "@type" : "Person",
        "name": "{$post.author.name}"
    }, 
    "publisher": {
        "@type": "Organization",
        "name": "{Configuration::get('PS_SHOP_NAME')}",
        "logo": {
            "@type" : "ImageObject",
            "url": "{$logo_shop}"
        }        
    }    
}
</script>