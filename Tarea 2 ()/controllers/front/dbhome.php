<?php

class DbblogDbHomeModuleFrontController extends ModuleFrontController
{
    public $home;

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

        // Cabecera
        $title_blog = Configuration::get('DBBLOG_TITLE', $id_lang);
        $categories = DbBlogCategory::getCategories($id_lang, true);

        // Descripciones
        $short_desc = Configuration::get('DBBLOG_HOME_SHORT_DESC', $id_lang);
        $large_desc = Configuration::get('DBBLOG_HOME_LARGE_DESC', $id_lang);

        // Listado de posts
        $total_posts = DbBlogPost::getTotalPosts($id_lang);
        $posts_per_home = Configuration::get('DBBLOG_POSTS_PER_HOME');
        $pagination = 1;

        if($total_posts <= $posts_per_home){
            $pagination = 0;
            $posts_per_home = $total_posts;
        }
        if($total_posts > 0) {
            $percent_view = round($posts_per_home * 100 / $total_posts, 0);
        } else {
            $percent_view = 100;
        }

        // Posts Destacados
        $destacados = DbBlogPost::getPostFeatures($id_lang);
        
        // Posts views
//        $post_extract = Configuration::get('DBBLOG_POST_EXTRACT');
        $post_readmore = Configuration::get('DBBLOG_POST_READMORE');

        // Authors
        $authors = DbBlogPost::getAuthors(0, 0);

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
        $more_views = DbBlogCategory::getPostsViews($id_lang, NULL, NULL, NULL, Configuration::get('DBBLOG_SIDEBAR_VIEWS'));

        // Ultimos Sidebar
//        $last_posts = DbBlogCategory::getPostsLast($id_lang, NULL, NULL, NULL, Configuration::get('DBBLOG_SIDEBAR_LAST'));
        $last_posts_home = DbBlogCategory::getPostsLast($id_lang, NULL, NULL, NULL, Configuration::get('DBBLOG_POSTS_PER_HOME'));

        $json_ld = $this->module->generateBreadcrumbJsonld($this->getBreadcrumbLinks());

        $this->context->smarty->assign(array(
            'title_blog'    => $title_blog,
            'categories'    => $categories,
            'isHome'        => 1,
            'isCategory'    => 0,
            'isAuthors'     => 0,
            'isAuthor'      => 0,
            'isPost'        => 0,
            'json_ld'       => $json_ld,

            'short_desc'    => $short_desc,
            'large_desc'    => $large_desc,
            'destacados'    => $destacados,
            'path_img_posts' => _MODULE_DIR_.'dbblog/views/img/post/',
            'path_img_author' => _MODULE_DIR_.'dbaboutus/views/img/author/',
//            'post_extract'      => $post_extract,
            'post_readmore'     => $post_readmore,

            'authors'       => $authors,
            'rrss'          => $rrss,
            'twitter'       => $twitter,
            'facebook'      => $facebook,
            'linkedin'      => $linkedin,
            'youtube'       => $youtube,
            'instagram'     => $instagram,
            'path_img'      => _MODULE_DIR_.'dbblog/views/img/',
            'more_views'    => $more_views,
//            'last_posts'    => $last_posts,
            'last_posts_home'    => $last_posts_home,
            'percent_view' => $percent_view,
            'posts_per_page' => $posts_per_home,
            'total_posts' => $total_posts,
            'pagination' => $pagination,
        ));

        $this->setTemplate('module:dbblog/views/templates/front/home.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $id_lang = Context::getContext()->language->id;
        $title_blog = Configuration::get('DBBLOG_TITLE', $id_lang);
        $url_home = Context::getContext()->link->getModuleLink('dbblog', 'dbhome', array());

        $breadcrumb['links'][] = [
            'title' => $title_blog,
            'url'   => $url_home,
        ];

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $url_home = Context::getContext()->link->getModuleLink('dbblog', 'dbhome', array());
        $meta_title = Configuration::get('DBBLOG_META_TITLE', $this->context->language->id);
        $meta_description = Configuration::get('DBBLOG_META_DESCRIPTION', $this->context->language->id);

        $page['meta']['title'] = $meta_title;
        $page['meta']['description'] = $meta_description;
        $page['meta']['robots'] = 'index,follow';
        $page['canonical'] = $url_home;

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
            foreach ($urls['alternative_langs'] as $locale => $href_lang) {
                $id_lang = (int)Language::getIdByLocale($locale);
                if ($id_lang > 0) {
                    $blog_slug = Configuration::get('DBBLOG_SLUG', $id_lang);
                    $iso_code = Language::getIsoById($id_lang);
                    $urls['alternative_langs'][$locale] = $urls['base_url'].$iso_code.'/'.$blog_slug.'/';
                }
            }
        }

        return $urls;
    }
}