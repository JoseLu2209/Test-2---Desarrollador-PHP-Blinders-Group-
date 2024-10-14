<?php

class DbblogDbCategoryModuleFrontController extends ModuleFrontController
{
    public $home;
    public $parents = [];

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        $id_lang = Context::getContext()->language->id;

        parent::initContent();

        // Detectamos en el caso de tener idiomas que la url tenga la url con el idioma
        $languages = Language::getLanguages();
        if (count($languages) > 1) {
            $path_language = $_SERVER['REQUEST_URI'];
            $iso_code = Language::getIsoById($this->context->language->id);
            $route_prefix = $iso_code . '/';
            if (strpos($path_language, $route_prefix) == false) {
                header("HTTP/1.0 404 Not Found");
                $this->setTemplate('errors/404.tpl');
                return;
            }
        }

        // Rewrite
        $rewrite = Tools::getValue('rewrite');
        $category = DbBlogCategory::getCategory($rewrite);

        // Redireccionamos a 404
        if((int)$category['id'] == 0 || $category['active'] == 0){
            Tools::redirect('index.php?controller=404');
        }

        // Cabecera
        $title_blog = Configuration::get('DBBLOG_TITLE', $id_lang);
        $categories = DbBlogCategory::getCategories($id_lang, true, false);
        $subcategories = DbBlogCategory::getCategories($id_lang, true, $category['id']);
        $url_home = Context::getContext()->link->getModuleLink('dbblog', 'dbhome', array());

        // Descripciones
        $short_desc = Configuration::get('DBBLOG_HOME_SHORT_DESC', $id_lang);
        $large_desc = Configuration::get('DBBLOG_HOME_LARGE_DESC', $id_lang);

        // Listado de posts
        $list_cat = DbBlogCategory::getPosts($rewrite, $id_lang);
        $total_posts = DbBlogCategory::totalPosts($rewrite, $id_lang);
        $posts_per_page = Configuration::get('DBBLOG_POSTS_PER_PAGE');
        $pagination = 1;
        if($total_posts <= $posts_per_page){
            $pagination = 0;
            $posts_per_page = $total_posts;
        }
        if($total_posts > 0) {
            $percent_view = round($posts_per_page * 100 / $total_posts, 0);
        } else {
            $percent_view = 100;
        }

        // Destacados categoria
        $destacados = DbBlogCategory::getPostsDestacados($category['id']);

        // Posts views
//        $post_extract = Configuration::get('DBBLOG_POST_EXTRACT');
        $post_readmore = Configuration::get('DBBLOG_POST_READMORE');

        // Authors
        //$authors = DbBlogPost::getAuthors($category['id']);
        $authors = DbBlogCategory::getAuthors(0, $category['id']);

        // Redes sociales
        $rrss = 0;
        $twitter = Configuration::get('DBBLOG_TWITTER');
        $facebook = Configuration::get('DBBLOG_FACEBOOK');
        $linkedin = Configuration::get('DBBLOG_LINKEDIN');
        $youtube = Configuration::get('DBBLOG_YOUTUBE');
        $instagram = Configuration::get('DBBLOG_INSTAGRAM');
        if(!empty($twitter) || !empty($facebook) || !empty($linkedin) || !empty($youtube)
            || !empty($instagram)){
                $rrss = 1;
        }

        // Mas vistos Sidebar
        $more_views = DbBlogCategory::getPostsViews($id_lang, $rewrite, NULL, NULL, Configuration::get('DBBLOG_SIDEBAR_VIEWS'));

        // Ultimos Sidebar
//        $last_posts = DbBlogCategory::getPostsLast($id_lang, $rewrite, NULL, NULL, Configuration::get('DBBLOG_SIDEBAR_LAST'));

        $json_ld = $this->module->generateBreadcrumbJsonld($this->getBreadcrumbLinks());

        $this->context->smarty->assign(array(
            'title_blog'    => $title_blog,
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'isHome'        => 0,
            'isCategory'    => 1,
            'isAuthors'     => 0,
            'isAuthor'      => 0,
            'isPost'        => 0,
            'url_home'      => $url_home,
            'json_ld'       => $json_ld,

            'category'          => $category,
            'list_cat'          => $list_cat,
            'total_posts'       => $total_posts,
            'posts_per_page'    => $posts_per_page,
            'pagination'        => $pagination,
            'percent_view'      => $percent_view,
            'rewrite'           => $rewrite,
//            'post_extract'      => $post_extract,
            'post_readmore'     => $post_readmore,
            'destacados'        => $destacados,

            'authors'       => $authors,
            'rrss'          => $rrss,
            'twitter'       => $twitter,
            'facebook'      => $facebook,
            'linkedin'      => $linkedin,
            'youtube'       => $youtube,
            'instagram'     => $instagram,
            'path_img'      => _MODULE_DIR_.'dbblog/views/img/',
            'path_img_posts' => _MODULE_DIR_.'dbblog/views/img/post/',
            'path_img_author' => _MODULE_DIR_.'dbaboutus/views/img/author/',
            'more_views'    => $more_views,
//            'last_posts'    => $last_posts,
        ));

        $this->setTemplate('module:dbblog/views/templates/front/category.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $id_lang = Context::getContext()->language->id;
        $title_blog = Configuration::get('DBBLOG_TITLE', $id_lang);
        $url_home = Context::getContext()->link->getModuleLink('dbblog', 'dbhome', array());

        $breadcrumb['links'][] = [
            'title' => $title_blog,
            'url' => $url_home,
        ];

        // Categoria
        $rewrite = Tools::getValue('rewrite');
        $category = DbBlogCategory::getCategory($rewrite);

        // Categorias superiores
        $parents = $this->getParentCategories((int)$category['id_parent']);
        if(!empty($parents) && is_array($parents)) {
            foreach ($parents as $parent) {
                $breadcrumb['links'][] = $parent;
            }
        }

        $breadcrumb['links'][] = [
            'title' => $category['title'],
            'url'   => $category['url'],
        ];

        return $breadcrumb;
    }

    public function getParentCategories($id_parent)
    {
        if($id_parent > 0){
            $category = DbBlogCategory::getCategoryById($id_parent);
            $parents[] = [
                'title' => $category['title'],
                'url'   => $category['url'],
            ];
            if((int)$category['id_parent'] > 0){
                $this->getParentCategories((int)$category['id_parent']);
            } else {
                return $parents;
            }
        } else {
            return;
        }
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $rewrite = Tools::getValue('rewrite');
        $category = DbBlogCategory::getCategory($rewrite);
        $robots = 'index,follow';
        if((int)$category['index'] == 0) {
            $robots = 'noindex,follow';
        }
        $url = Context::getContext()->link->getModuleLink('dbblog', 'dbcategory', array('rewrite' => $rewrite));


        $page['meta']['title'] = $category['meta_title'];
        $page['meta']['description'] = $category['meta_description'];
        $page['meta']['robots'] = $robots;
        $page['canonical'] = $url;

        return $page;
    }

    public function setMedia()
    {
        parent::setMedia();

        if(!Module::isEnabled('dbthemecustom')){
            $this->context->controller->addCSS(array(
                $this->module->getLocalPath() . 'views/css/splide/splide.min.css',
                $this->module->getLocalPath() . 'views/css/splide/themes/splide-default.min.css',
            ));
            $this->context->controller->addJS(array(
                $this->module->getLocalPath() . 'views/js/splide.min.js',
            ));
        }

        $this->context->controller->addCSS(array(
            $this->module->getLocalPath() . 'views/css/dbblog.css',
        ));

        $this->context->controller->addJS(array(
            $this->module->getLocalPath() . 'views/js/dbblog.js',
        ));

        Media::addJsDef(array(
            'dbblog_ajax' => Context::getContext()->link->getModuleLink('dbblog', 'ajax', array()),
        ));
    }

    public function getTemplateVarUrls()
    {
        $urls = parent::getTemplateVarUrls();

        $languages = Language::getLanguages();
        if (count($languages) > 1) {
            $rewrite = Tools::getValue('rewrite');
            $category = DbBlogCategory::getCategory($rewrite);
            $id_dbblog_category = $category['id_dbblog_category'];
            foreach ($urls['alternative_langs'] as $locale => $href_lang) {
                $id_lang = (int)Language::getIdByLocale($locale);
                if ($id_lang > 0) {
                    $sql = "SELECT link_rewrite
                    FROM "._DB_PREFIX_."dbblog_category_lang al 
                    WHERE al.id_lang = '$id_lang' AND al.id_dbblog_category = '$id_dbblog_category'";
                    $link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

                    $blog_slug = Configuration::get('DBBLOG_SLUG', $id_lang);
                    $iso_code = Language::getIsoById($id_lang);
                    $urls['alternative_langs'][$locale] = $urls['base_url'].$iso_code.'/'.$blog_slug.'/'.$link_rewrite.'/';

                }
            }
        }

        return $urls;
    }
}