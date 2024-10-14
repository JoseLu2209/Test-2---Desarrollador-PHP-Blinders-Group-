<?php
/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class Dbblog extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        require_once(dirname(__FILE__).'/classes/DbBlogCategory.php');
        require_once(dirname(__FILE__).'/classes/DbBlogPost.php');
        require_once(dirname(__FILE__).'/classes/DbBlogComment.php');

        if(file_exists(dirname(__FILE__).'/premium/DbPremium.php')){
            require_once(dirname(__FILE__).'/premium/DbPremium.php');
            $this->premium = 1;
        } else {
            $this->premium = 0;
        }

        $this->name = 'dbblog';
        $this->tab = 'front_office_features';
        $this->version = '2.0.3';
        $this->author = 'DevBlinders';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DB Blog');
        $this->description = $this->l('Blog para PrestaShop optimizado para SEO');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        if($this->id && !$this->isRegisteredInHook('moduleRoutes')) {
            $this->registerHook('moduleRoutes');
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if(!Module::isInstalled('dbdatatext')){
            $this->rcopy(dirname(__FILE__).'/dependencies/dbdatatext/', _PS_MODULE_DIR_.'/dbdatatext/');
            $dbdatatext = Module::getInstanceByName('dbdatatext');
            $dbdatatext->install();
        } else {
            if(!Module::isEnabled('dbdatatext')){
                $dbdatatext = Module::getInstanceByName('dbdatatext');
                $dbdatatext->enable();
            }
        }

        if(!Module::isInstalled('dbaboutus')){
            $this->rcopy(dirname(__FILE__).'/dependencies/dbaboutus/', _PS_MODULE_DIR_.'/dbaboutus/');
            $dbdatatext = Module::getInstanceByName('dbaboutus');
            $dbdatatext->install();
        } else {
            if(!Module::isEnabled('dbaboutus')){
                $dbdatatext = Module::getInstanceByName('dbaboutus');
                $dbdatatext->enable();
            }
        }

	    if(!Module::isEnabled('dbaboutus')){
            $this->_errors[] = $this->l('Debe de tener instalado y activo el módulo dbaboutus y el módulo dbdatatext');
            return false;
        }

        // Settings
        include(dirname(__FILE__).'/sql/install.php');
        $this->createTabs();

        // Config general
        Configuration::updateValue('DBBLOG_SLUG', 'blog');
        Configuration::updateValue('DBBLOG_AUTHOR_SLUG', 'author');
        Configuration::updateValue('DBBLOG_POSTS_PER_PAGE', '12');
        Configuration::updateValue('DBBLOG_POSTS_PER_HOME', '12');
        Configuration::updateValue('DBBLOG_POSTS_PER_AUTHOR', '4');
        // Colores
        Configuration::updateValue('DBBLOG_COLOR_TEXT', '#7a7a7a');
        Configuration::updateValue('DBBLOG_COLOR', '#24b9d7');
        Configuration::updateValue('DBBLOG_COLOR_BACKGROUND', '#F6F6F6');
        // Sidebar Blog
        Configuration::updateValue('DBBLOG_SIDEBAR_VIEWS', '4');
        Configuration::updateValue('DBBLOG_SIDEBAR_AUTHOR', '3');
        Configuration::updateValue('DBBLOG_SIDEBAR_LAST', '4');
        // Config Comments
        Configuration::updateValue('DBBLOG_COMMENTS', '1');
        Configuration::updateValue('DBBLOG_RECAPTCHA_ENABLE', '0');
        // Config Post
        Configuration::updateValue('DBBLOG_POST_EXTRACT', '0');
        Configuration::updateValue('DBBLOG_POST_READMORE', '0');
        Configuration::updateValue('DBBLOG_POST_RELATED', '4');
        Configuration::updateValue('DBBLOG_POST_AUTHOR', '4');
        // Home PrestaShop
        Configuration::updateValue('DBBLOG_POST_FEATURED_HOMEPS', '0');
        Configuration::updateValue('DBBLOG_POST_VIEWS_HOMEPS', '0');
        // Sidebar PrestaShop
        Configuration::updateValue('DBBLOG_POST_VIEWS_SIDEBARPS', '0');
        Configuration::updateValue('DBBLOG_POST_LAST_SIDEBARPS', '0');


        return parent::install() &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayLeftColumn');
    }

    public function uninstall()
    {
//        include(dirname(__FILE__).'/sql/uninstall.php');
        $this->deleteTabs();
        Configuration::deleteByName('DBBLOG_SLUG');
        Configuration::deleteByName('DBBLOG_AUTHOR_SLUG');

        return parent::uninstall();
    }

    /**
     * Function to Copy folders and files
     */
    function rcopy($src, $dst) {
        if (file_exists ( $dst )) {
            return;
        }
        if (is_dir ( $src )) {
            mkdir ( $dst );
            $files = scandir ( $src );
            foreach ( $files as $file ) {
                if ($file != "." && $file != "..") {
                    $this->rcopy("$src/$file", "$dst/$file");
                }
            }
        } else if (file_exists ( $src )) {
            copy($src, $dst);
        }
    }

    /**
     * Create Tabs
     */
    public function createTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlog');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogCategory');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogPost');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogComment');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogSetting');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        // Tabs
        if (!Tab::getIdFromClassName('AdminDevBlinders')) {
            $parent_tab = new Tab();
            $parent_tab->name = array();
            foreach (Language::getLanguages(true) as $lang)
                $parent_tab->name[$lang['id_lang']] = $this->l('DevBlinders');

            $parent_tab->class_name = 'AdminDevBlinders';
            $parent_tab->id_parent = 0;
            $parent_tab->module = $this->name;
            $parent_tab->add();

            $id_full_parent = $parent_tab->id;
        } else {
            $id_full_parent = Tab::getIdFromClassName('AdminDevBlinders');
        }

        $parent = new Tab();
        $parent->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $parent->name[$lang['id_lang']] = $this->l('Blog');

        $parent->class_name = 'AdminDbBlog';
        $parent->id_parent = $id_full_parent;
        $parent->module = $this->name;
        $parent->icon = 'assignment';
        $parent->add();

        // Categorias
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Categorías');

        $tab_config->class_name = 'AdminDbBlogCategory';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Posts
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Posts');

        $tab_config->class_name = 'AdminDbBlogPost';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Comentarios
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Comentarios');

        $tab_config->class_name = 'AdminDbBlogComment';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Configuración
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Configuración');

        $tab_config->class_name = 'AdminDbBlogSetting';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Responder comentario
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Responder');

        $tab_config->class_name = 'AdminDbBlogRespond';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->active = 0;
        $tab_config->add();
    }

    /**
     * Delete Tabs
     */
    public function deleteTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlog');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogCategory');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogPost');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogComment');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogSetting');
        $idTabs[] = Tab::getIdFromClassName('AdminDbBlogRespond');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminDbBlogSetting'){
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . '/views/js/back.js');
            $this->context->controller->addCSS($this->_path . '/views/css/back.css');
        }
        Media::addJsDef(array(
            'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'ps_force_friendly_product' => (int)Configuration::get('PS_FORCE_FRIENDLY_PRODUCT'),
        ));
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        // Colores configurables
        $inline = "<style>
                    :root {
                        --dbblog-texto1: ".Configuration::get('DBBLOG_COLOR_TEXT').";
                        --dbblog-primary: ".Configuration::get('DBBLOG_COLOR').";
                        --dbblog-background: ".Configuration::get('DBBLOG_COLOR_BACKGROUND').";
                    }
                </style>";
        if(Tools::getValue('fc') == 'module' && Tools::getValue('module') == 'dbblog') {
            return $inline;
        }

        // Css hooks adicionales
        if($this->premium == 1) {
            $controller = Context::getContext()->controller->php_self;
            if ($controller == 'index') {
                $featured_home = (int)Configuration::get('DBBLOG_POST_FEATURED_HOMEPS');
                $views_home = (int)Configuration::get('DBBLOG_POST_VIEWS_HOMEPS');
                if($featured_home > 0 || $views_home > 0) {
                    $this->context->controller->addCSS(array(
                        $this->getLocalPath() . 'views/css/dbblog.css',
                    ));
                }
            } elseif($controller == 'category' || $controller == 'manufacturer') {
                $featured_home = (int)Configuration::get('DBBLOG_POST_LAST_SIDEBARPS');
                $views_home = (int)Configuration::get('DBBLOG_POST_VIEWS_SIDEBARPS');
                if($featured_home > 0 || $views_home > 0) {
                    $this->context->controller->addCSS(array(
                        $this->getLocalPath() . 'views/css/dbblog.css',
                    ));
                }
            }

            if(Tools::getValue('fc') == 'module' && Tools::getValue('module') == 'dbaboutus') {
                $this->context->controller->addCSS(array(
                    $this->getLocalPath() . 'views/css/dbblog.css',
                ));
                return $inline;
            }
        }
    }
    
    /**
     * Add Routes
     */
    public function hookModuleRoutes($params)
    {

        $context = Context::getContext();
        $controller = Tools::getValue('controller', 0);
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;
        $blog_slug = Configuration::get('DBBLOG_SLUG', $id_lang);

        $my_routes = array(
            // Home
            'module-dbblog-dbhome' => array(
                'controller' => 'dbhome',
                'rule' => $blog_slug.'/',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'dbblog',
                ),
            ),

            // Category
            'module-dbblog-dbcategory' => array(
                'controller' => 'dbcategory',
                'rule' =>       $blog_slug.'/{rewrite}/',
                'keywords' => array(
                    'rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'dbblog',
                ),
            ),

            // Post
            'module-dbblog-dbpost' => array(
                'controller' => 'dbpost',
                'rule' =>       $blog_slug.'/{rewrite}.html',
                'keywords' => array(
                    'rewrite' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'dbblog',
                ),
            ),  

        );

        return $my_routes;
    }

    public function renderPremiumTpl($tpl)
    {
        if($this->premium == 1) {
            return $this->display(__FILE__, 'premium/' . $tpl);
        }

        return;
    }

    public function hookdisplayLeftColumn($params)
    {
        if($this->premium == 1) {
            $num_views = Configuration::get('DBBLOG_POST_VIEWS_SIDEBARPS');
            $num_last = Configuration::get('DBBLOG_POST_LAST_SIDEBARPS');
            $data = DbBlogPremium::hookdisplayLeftColumn($params);
            $more_views = $data['more_views'];
            $last_posts = $data['last_posts'];
            if ($num_views > 0 || $last_posts > 0) {
                $this->context->smarty->assign(array(
                    'path_img' => _MODULE_DIR_ . 'dbblog/views/img/',
                    'more_views' => $more_views,
                    'last_posts' => $last_posts,
                ));
                return $this->fetch('module:dbblog/views/templates/hook/sidebar.tpl');
            }
        }
    }

    public function hookdisplayHome($params)
    {
        if($this->premium == 1){
            $data = DbBlogPremium::hookdisplayHome($params);

            $limit_views_home = $data['limit_views_home'];
            $more_views = $data['more_views'];
            $limit_last_home = $data['limit_last_home'];
            $last_posts = $data['last_posts'];

            if($limit_views_home > 0 || $limit_last_home > 0) {
                Context::getContext()->smarty->assign(array(
                    'more_views' => $more_views,
                    'last_posts' => $last_posts,
                    'limit_views' => $limit_views_home,
                    'limit_last' => $limit_last_home,
                    'path_img_posts' => _MODULE_DIR_.'dbblog/views/img/post/',
                ));
                return $this->fetch('module:dbblog/views/templates/hook/homeps.tpl');
            }
        }
    }

    public function renderScroll($posts)
    {
        $this->smarty->assign(array(
            'list_post' => $posts,
            'path_img_posts' => _MODULE_DIR_.'dbblog/views/img/post/',
        ));

        return $this->fetch('module:dbblog/views/templates/front/_partials/infinite_scroll.tpl');
    }

    public function renderFormRespond($id_comment, $id_post)
    {
        // Customer
        $customer_login = $this->context->customer->isLogged();
        $customer_name = '';
        if($customer_login){
            $customer_name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
        }

        $link = new Link();
        $id_lang = $this->context->language->id;
        $rgpd_text = Configuration::get('DBBLOG_RGPD', $id_lang);
        $link_privacity = $link->getCMSLink(Configuration::get('DBBLOG_PRIVACITY'));
        $recaptcha_enable = Configuration::get('DBBLOG_RECAPTCHA_ENABLE');
        $recaptcha = Configuration::get('DBBLOG_RECAPTCHA');
        $recaptcha_private = Configuration::get('DBBLOG_RECAPTCHA_PRIVATE');

        $this->smarty->assign(array(
            'id_comment' => $id_comment,
            'customer_name' => $customer_name,
            'customer_login' => $customer_login,
            'id_post' => $id_post,
            'recaptcha_enable' => $recaptcha_enable,
            'link_privacity' => $link_privacity,
            'rgpd_text' => $rgpd_text,
            'recaptcha' => $recaptcha,
            'recaptcha_private' => $recaptcha_private,
        ));

        return $this->fetch('module:dbblog/views/templates/front/_partials/form_comment.tpl');
    }

    public function shortCodes($desc)
    {

        if($this->premium == 1) {
            return DbBlogPremium::shortCodes($desc);
        }

        return $desc;
    }

    public function getProductSC($id_product)
    {
        $product = new Product($id_product, null, $this->context->language->id);
        if(!empty($product->link_rewrite) && $product->link_rewrite != '') {
            $product = array(
                'id_product' => $id_product,
            );
            $assembler = new ProductAssembler($this->context);

            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );

            $product_for_template = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($product),
                $this->context->language
            );

            return $product_for_template;
        }

        return;
    }

    public function getProductsSC($id_category = null, $orderby = null, $way = null, $num = null)
    {
        $id_lang = $this->context->language->id;
        $category = new Category($id_category, $id_lang);
        if($orderby != 'seller' && $orderby != 'id_product' && $orderby != 'date_add' && $orderby != 'date_upd' && $orderby != 'name'
            && $orderby != 'manufacturer' && $orderby != 'position' && $orderby != 'price'){
            $orderby = 'seller';
        }
        if($way != 'asc' && $way != 'desc'){
            $way = 'DESC';
        }
        if($num < 1){ $num = 4; }
        if(!empty($category->link_rewrite) && $category->link_rewrite != '') {
            if($orderby == 'seller'){
                $products = $this->getProductsSeller($id_category, $way, $num);
            } else {
                $products = $category->getProducts($id_lang, 1, $num, $orderby);
            }

            $products_for_template = [];
            $assembler = new ProductAssembler($this->context);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
            foreach ($products as $rawProduct)
            {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }

            return $products_for_template;
        }
    }

    public function getProductsSeller($id_category, $way = null, $num = null, $id_lang)
    {
        if($way == null){ $way = DESC; }
        $sql = 'SELECT p.id_product, 
                    SUM(od.product_quantity) as sellers
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p
					ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p')
            . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od
				    ON p.`id_product` = od.`product_id`
				WHERE product_shop.`id_shop` = ' . (int) $this->context->shop->id
            . (' AND product_shop.`active` = 1')
            . ' AND product_shop.`visibility` IN ("both", "catalog")
                    AND (stock.out_of_stock = 1 OR stock.quantity > 0)';
        if($id_category > 0){
            $categories = implode(',', $this->getAllChildrens([], $id_category, $id_lang));
            $sql .= ' AND cp.id_category IN ('.$categories.')';
        }
        $sql .= ' GROUP BY p.id_product
                ORDER BY sellers '.$way.'
                LIMIT '.$num;

        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);

        return $products;
    }

    public function checkWebp() {
        if($this->premium == 0) {
            return false;
        }

        $gd_extensions = get_extension_funcs("gd");
        if (in_array("imagewebp", $gd_extensions)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getNewImg($img) {

        // Cuando no hay imagen guardada en base de datos, le damos la default
        if(empty($img)) {
            $image = [];
            $image['webp_small'] = 0;
            $image['webp_big'] = 0;
            $image['small'] = 'sin-imagen-small.jpg';
            $image['big'] = 'sin-imagen-big.jpg';
            return $image;
        }

        $dir_img = dirname(__FILE__).'/views/img/post/';
        $type = Tools::strtolower(Tools::substr(strrchr($img, '.'), 1));
        $extensions = array('.jpg', '.gif', '.jpeg', '.png', '.webp');
        $name_without_extension = str_replace($extensions, '', $img);
        $img_small = $name_without_extension.'-small.'.$type;
        $img_big = $name_without_extension.'-big.'.$type;
        $img_small_webp = $img_small.'.webp';
        $img_big_webp = $img_big.'.webp';

        $image = [];
        $image['webp_small'] = 0;
        $image['webp_big'] = 0;
        // Imagen small
        if (file_exists($dir_img.$img_small_webp)) {
            $image['small'] = $img_small;
            $image['webp_small'] = 1;
        } elseif(file_exists($dir_img.$img_small)) {
            $image['small'] = $img_small;
        } elseif(file_exists($dir_img.$img)) {
            $image['small'] = $img;
        } else {
            $image['small'] = 'sin-imagen-small.jpg';
        }
        // Imagen big
        if (file_exists($dir_img.$img_big_webp)) {
            $image['big'] = $img_big;
            $image['webp_big'] = 1;
        } elseif(file_exists($dir_img.$img_big)) {
            $image['big'] = $img_big;
        } elseif(file_exists($dir_img.$img)) {
            $image['big'] = $img;
        } else {
            $image['big'] = 'sin-imagen-big.jpg';
        }

        return $image;
    }

    public function generateBreadcrumbJsonld($breadcrumb)
    {
        $itemListElement = [];
        $position = 1;
        foreach($breadcrumb['links'] as $bc) {
            (object)$bread = new stdClass();
            $bread->{'@type'} = 'ListItem';
            $bread->position = $position;
            $bread->name = $bc['title'];
            $bread->item = $bc['url'];
            $itemListElement[] = $bread;
            $position++;
        }

        (object)$json = new stdClass();
        $json->{'@context'} = 'https://schema.org';
        $json->{'@type'} = 'BreadcrumbList';
        $json->itemListElement = $itemListElement;

        $json_ld = json_encode($json, JSON_UNESCAPED_UNICODE);
        $script_json = '<script type="application/ld+json">'.$json_ld.'</script>';

        return $script_json;
    }

}
