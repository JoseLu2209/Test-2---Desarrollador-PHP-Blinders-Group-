<?php

class DbblogDbPostModuleFrontController extends ModuleFrontController
{
    public $home;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        $id_lang = Context::getContext()->language->id;
        $link = new Link();

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
        $post = DbBlogPost::getPost($id_lang, $rewrite);
        $desc = $this->module->shortCodes($post['large_desc']);

        // Redireccionamos a 404
        if((int)$post['id'] == 0 || $post['active'] == 0){
            Tools::redirect('index.php?controller=404');
        }

        // Sumamos como visto
        DbBlogPost::sumView($post['id']);

        // Cabecera
        $title_blog = Configuration::get('DBBLOG_TITLE', $id_lang);
        $baseurl = substr(Context::getContext()->shop->getBaseURL(true), 0, -1);
        $logo_shop = _PS_IMG_.Configuration::get('PS_LOGO');
        $categories = DbBlogCategory::getCategories($id_lang, true, false);
        $url_home = Context::getContext()->link->getModuleLink('dbblog', 'dbhome', array());
        $c_active = Configuration::get('DBBLOG_COMMENTS');

        // Authors
//        $authors = DbBlogPost::getAuthors();

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
        $url_post = Context::getContext()->link->getModuleLink('dbblog', 'dbpost', array('rewrite' => $rewrite));
        $share_facebook = 'https://www.facebook.com/sharer.php?u='.$url_post;
        $share_twitter = 'https://twitter.com/intent/tweet?text='.$post['title'].'&url='.$url_post;
        $share_pinterest = '//pinterest.com/pin/create/link/?url='.$url_post.'&description='.$post['title'];
        $share_linkedin = 'https://www.linkedin.com/shareArticle?url='.$url_post.'&title='.$post['title'];

        // Mas vistos relacionados de la categoria principal
        $more_views = DbBlogCategory::getPostsViews($id_lang, $post['link_rewrite_category'], NULL, $post['id'], Configuration::get('DBBLOG_SIDEBAR_VIEWS'));
//        $more_views_post = DbBlogCategory::getPostsViews($id_lang, $post['link_rewrite_category'], NULL, $post['id'], Configuration::get('DBBLOG_POST_RELATED'));

        // Mas vistos relacionados de la categoria principal
        $more_posts_author = DbBlogPost::getPostsMoreViews($post['author']['id'], $id_lang, $post['id'], Configuration::get('DBBLOG_POST_AUTHOR'));

        // Ultimos Sidebar
        $last_posts = DbBlogCategory::getPostsLast($id_lang, $post['link_rewrite_category'], NULL, $post['id'], Configuration::get('DBBLOG_SIDEBAR_LAST'));

        // Customer
        $customer_login = $this->context->customer->isLogged();
        $customer_name = '';
        if($customer_login){
            $customer_name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
        }

        $comments = DbBlogComment::getComments($post['id']);
        $active_comments = Configuration::get('DBBLOG_COMMENTS');
        $rgpd_text = Configuration::get('DBBLOG_RGPD', $id_lang);
        $link_privacity = $link->getCMSLink(Configuration::get('DBBLOG_PRIVACITY'));
        $recaptcha_enable = Configuration::get('DBBLOG_RECAPTCHA_ENABLE');
        $recaptcha = Configuration::get('DBBLOG_RECAPTCHA');
        $recaptcha_private = Configuration::get('DBBLOG_RECAPTCHA_PRIVATE');

        // Generar Json+ld del post
        $json_ld = $this->module->generateBreadcrumbJsonld($this->getBreadcrumbLinks());
        if($this->module->premium == 1) {
            $json_ld .= PHP_EOL.DbBlogPremium::generateJsonld($post, $comments, $baseurl, $logo_shop);
        }

        $this->context->smarty->assign(array(
            'title_blog'        => $title_blog,
            'baseurl'           => $baseurl,
            'logo_shop'         => $logo_shop,
            'categories'        => $categories,
            'isHome'            => 0,
            'isCategory'        => 0,
            'isAuthor'          => 0,
            'isAuthors'         => 0,
            'isPost'            => 1,
            'url_home'          => $url_home,
            'c_active'          => $c_active,
            'url_post'          => $url_post,
            'share_facebook'    => $share_facebook,
            'share_twitter'     => $share_twitter,
            'share_pinterest'    => $share_pinterest,
            'share_linkedin'     => $share_linkedin,
            'json_ld'          => $json_ld,

            'comments'          => $comments,
            'active_comments'   => $active_comments,
            'rgpd_text'         => $rgpd_text,
            'link_privacity'    => $link_privacity,
            'recaptcha_enable'  => $recaptcha_enable,
            'recaptcha'         => $recaptcha,
            'recaptcha_private' => $recaptcha_private,
            'post'              => $post,
            'desc'              => $desc,
            'customer_login'    => $customer_login,
            'customer_name'     => $customer_name,
            'url_comment'       => Context::getContext()->link->getModuleLink('dbblog', 'ajax', array()),
            'more_posts_author' => $more_posts_author,
//            'more_views_post'   => $more_views_post,
            'id_comment'        => 0,

//            'authors'       => $authors,
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
            'last_posts'    => $last_posts,

            'premium'       => $this->module->premium,
        ));

        $this->setTemplate('module:dbblog/views/templates/front/post.tpl');
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
        $post = DbBlogPost::getPost($id_lang, $rewrite);
        $category = DbBlogCategory::getCategory($post['link_rewrite_category']);

        $breadcrumb['links'][] = [
            'title' => $category['title'],
            'url'   => $category['url'],
        ];

        $breadcrumb['links'][] = [
            'title' => $post['title'],
            'url'   => $post['url'],
        ];

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $rewrite = Tools::getValue('rewrite');
        $id_lang = Context::getContext()->language->id;
        $post = DbBlogPost::getPost($id_lang, $rewrite);
        $robots = 'index,follow';
        if((int)$post['index'] == 0) {
            $robots = 'noindex,follow';
        }
        $url = Context::getContext()->link->getModuleLink('dbblog', 'dbpost', array('rewrite' => $rewrite));


        $page['meta']['title'] = $post['meta_title'];
        $page['meta']['description'] = $post['meta_description'];
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
            $post = DbBlogPost::getPost(Context::getContext()->language->id, $rewrite);
            $id_dbblog_post= $post['id'];
            foreach ($urls['alternative_langs'] as $locale => $href_lang) {
                $id_lang = (int)Language::getIdByLocale($locale);
                if ($id_lang > 0) {
                    $sql = "SELECT link_rewrite
                    FROM "._DB_PREFIX_."dbblog_post_lang al 
                    WHERE al.id_lang = '$id_lang' AND al.id_dbblog_post = '$id_dbblog_post'";
                    $link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

                    $blog_slug = Configuration::get('DBBLOG_SLUG', $id_lang);
                    $iso_code = Language::getIsoById($id_lang);
                    $urls['alternative_langs'][$locale] = $urls['base_url'].$iso_code.'/'.$blog_slug.'/'.$link_rewrite.'.html';

                }
            }
        }

        return $urls;
    }

}