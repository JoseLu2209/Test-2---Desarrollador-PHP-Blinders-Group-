<?php

class DbBlogCategory extends ObjectModel
{

    public $id;
    public $id_shop;
    public $id_dbblog_category;
    public $title;
    public $active = 1;
    public $index = 1;
    public $short_desc;
    public $large_desc;
    public $link_rewrite;
    public $meta_title;
    public $meta_description;
    public $date_add;
    public $date_upd;
    public $position;
    public $id_parent;

    public static $definition = array(
        'table' => 'dbblog_category',
        'primary' => 'id_dbblog_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'active' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_parent' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'index' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>		array('type' => self::TYPE_DATE),
            'date_upd' =>		array('type' => self::TYPE_DATE),
            
            // Lang fields
            'short_desc' =>	        array('type' => self::TYPE_HTML, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 4000),
            'large_desc' =>	        array('type' => self::TYPE_HTML, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml'),
			'title' =>			    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true , 'validate' => 'isCleanHtml', 'size' => 128),
            'link_rewrite' =>	    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 128),
            'meta_title' =>	        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 128),
            'meta_description' =>	array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 255),
        ),
    );

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $default_language_id = Configuration::get('PS_LANG_DEFAULT');
        $this->position = DbBlogCategory::getNewLastPosition();
        foreach ( $this->title as $k => $value ) {
            if ( preg_match( '/^[1-9]\./', $value ) ) {
                $this->title[ $k ] = '0' . $value;
            }
            if(empty($value)) {
                $this->title[$k] = $this->title[$default_language_id];
            }
        }
        foreach ( $this->link_rewrite as $k => $value ) {
            if(empty($value)) {
                $this->link_rewrite[$k] = Tools::link_rewrite($this->title[$k]);
            }
        }
        $ret = parent::add($autodate, $null_values);
        return $ret;
    }

    public function update( $null_values = false ) {

        foreach ( $this->title as $k => $value ) {
            if ( preg_match( '/^[1-9]\./', $value ) ) {
                $this->title[ $k ] = '0' . $value;
            }
        }
        foreach ( $this->link_rewrite as $k => $value ) {
            if(empty($value)) {
                $this->link_rewrite[$k] = Tools::link_rewrite($this->title[$k]);
            }
        }
        return parent::update( $null_values );
    }

    public static function getNewLastPosition()
    {
        return (Db::getInstance()->getValue('
            SELECT IFNULL(MAX(position),0)+1
            FROM `'._DB_PREFIX_.'dbblog_category`'
        ));
    }

    public static function getCategories($id_lang, $active = true, $parent = 0)
    {
        if (!Validate::isBool($active))
            die(Tools::displayError());

        $id_shop = (int)Context::getContext()->shop->id;
        
        $sql = "SELECT * 
            FROM "._DB_PREFIX_."dbblog_category c 
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON c.id_dbblog_category = cl.id_dbblog_category
                    AND cl.id_shop = '$id_shop'
            WHERE cl.id_lang = '$id_lang' 
            ".($active ? 'AND `active` = 1' : '');
        if($parent >= 0){
            $sql .= " AND id_parent = '$parent'";
        }
        $sql .= " ORDER BY c.position ASC";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $categories = array();

        foreach ($result as $row)
        {
            $categories[$row['id_dbblog_category']]['id_dbblog_category'] = $row['id_dbblog_category'];
            $categories[$row['id_dbblog_category']]['title'] = $row['title'];
            $categories[$row['id_dbblog_category']]['url'] = self::getLink($row['link_rewrite'], $id_lang);
            $categories[$row['id_dbblog_category']]['id'] = $row['id_dbblog_category'];
            $categories[$row['id_dbblog_category']]['link_rewrite'] = $row['link_rewrite'];
            $categories[$row['id_dbblog_category']]['is_child'] = $row['id_parent'] > 0 ? true : false;
            if(sizeof(self::getChildrens($row['id_dbblog_category'])))
                $categories[$row['id_dbblog_category']]['childrens'] = self::getChildrens($row['id_dbblog_category']);
        }
            
        return $categories;
    }

    public static function getCategoriesSelected($id_post)
    {
        $sql = "SELECT id_dbblog_category
            FROM "._DB_PREFIX_."dbblog_category_post 
            WHERE id_dbblog_post = '$id_post'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $categories = [];
        foreach ($result as $row){
            $categories[] = $row['id_dbblog_category'];
        }
            
        return $categories;
    }

    public static function getLink($rewrite, $id_lang = null, $id_shop = null)
    {
        return Context::getContext()->link->getModuleLink('dbblog', 'dbcategory', array('rewrite' => $rewrite));
    }

    public static function getChildrens($id_parent)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        $child_categories = DB::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'dbblog_category` c
            INNER JOIN `'._DB_PREFIX_.'dbblog_category_lang` cl
                ON c.`id_dbblog_category` = cl.`id_dbblog_category` 
                    AND cl.`id_lang` = '.(int)$id_lang.' AND cl.`id_shop` = '.$id_shop.'
            WHERE c.`id_parent` = '.(int)$id_parent.' AND c.active = 1
            ORDER BY c.`position` ASC
        ');

        foreach($child_categories as $key => $child_categorie) {
            $child_categories[$key]['url'] = self::getLink($child_categorie['link_rewrite'], $id_lang);
        }

        return $child_categories;
    }

    public static function getCategory($rewrite)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        $result = DB::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'dbblog_category` c
            INNER JOIN `'._DB_PREFIX_.'dbblog_category_lang` cl
                ON c.`id_dbblog_category` = cl.`id_dbblog_category` 
                    AND cl.`id_lang` = '.(int)$id_lang.' AND cl.`id_shop` = '.$id_shop.'
            WHERE cl.link_rewrite = "'.$rewrite.'"
            LIMIT 1
        ');

        $category = array();

        foreach ($result as $row)
        {
            $category['id_dbblog_category'] = $row['id_dbblog_category'];
            $category['title'] = $row['title'];
            $category['url'] = self::getLink($row['link_rewrite'], $id_lang);
            $category['id'] = $row['id_dbblog_category'];
            $category['short_desc'] = $row['short_desc'];
            $category['large_desc'] = $row['large_desc'];
            $category['id_parent'] = $row['id_parent'];
            $category['meta_title'] = $row['meta_title'];
            $category['meta_description'] = $row['meta_description'];
            $category['index'] = $row['index'];
            $category['active'] = $row['active'];
        }
            
        return $category;
    }

    public static function getCategoryById($id_category)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        $result = DB::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'dbblog_category` c
            INNER JOIN `'._DB_PREFIX_.'dbblog_category_lang` cl
                ON c.`id_dbblog_category` = cl.`id_dbblog_category` 
                    AND cl.`id_lang` = '.(int)$id_lang.' AND cl.`id_shop` = '.$id_shop.'
            WHERE c.id_dbblog_category = "'.$id_category.'"
            LIMIT 1
        ');

        $category = array();

        foreach ($result as $row)
        {
            $category['title'] = $row['title'];
            $category['url'] = self::getLink($row['link_rewrite'], $id_lang);
            $category['id'] = $row['id_dbblog_category'];
            $category['short_desc'] = $row['short_desc'];
            $category['large_desc'] = $row['large_desc'];
            $category['id_parent'] = $row['id_parent'];
            $category['meta_title'] = $row['meta_title'];
            $category['meta_description'] = $row['meta_description'];
            $category['index'] = $row['index'];
            $category['active'] = $row['active'];
        }

        return $category;
    }

    public static function getPosts($link_rewrite, $id_lang, $page = 0)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $limit = (int) Configuration::get('DBBLOG_POSTS_PER_PAGE');
        if($limit == 0){
            $limit = 10;
        }
        $offset = $page * $limit;

        $sql = "SELECT id_dbblog_category FROM "._DB_PREFIX_."dbblog_category_lang WHERE link_rewrite = '$link_rewrite'";
        $id_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category AND pl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            LEFT JOIN "._DB_PREFIX_."dbblog_category_post cp ON cp.id_dbblog_post = p.id_dbblog_post 
            WHERE p.active = 1 AND (p.id_dbblog_category = '$id_category' OR cp.id_dbblog_category = '$id_category')
            GROUP BY p.id_dbblog_post
            ORDER BY p.date_add DESC
            LIMIT ".$offset.",".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if($comments['total'] == 0){
                $rating = 0;
            } else {
                $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 0);
            }

            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_upd']), 'd/m/Y');
            $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_.'dbblog/views/img/post/'.$row['image'];
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
        }
        return $posts;    
    }

    public static function getPostsById($id_category, $id_lang, $page = 0)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $limit = Configuration::get('DBBLOG_POSTS_PER_PAGE');
        $offset = $page * $limit;

        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            LEFT JOIN "._DB_PREFIX_."dbblog_category_post cp ON cp.id_dbblog_post = p.id_dbblog_post 
            WHERE p.active = 1 AND (p.id_dbblog_category = '$id_category' OR cp.id_dbblog_category = '$id_category')
            GROUP BY p.id_dbblog_post
            ORDER BY p.date_add DESC
            LIMIT ".$offset.",".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if($comments['total'] == 0){
                $rating = 0;
            } else {
                $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 1);
            }

            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_upd']), 'd/m/Y');
            if(!empty($row['image'])) {
                $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $row['image'];
            } else {

            }
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
        }

        return $posts;    
    }

    public static function totalPosts($link_rewrite, $id_lang, $active = 1)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT id_dbblog_category 
                FROM "._DB_PREFIX_."dbblog_category_lang 
                WHERE link_rewrite = '$link_rewrite' AND id_lang = '$id_lang' AND id_shop = '$id_shop'";
        $id_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $sql = "SELECT COUNT(DISTINCT p.id_dbblog_post) as total
                FROM "._DB_PREFIX_."dbblog_post p
                INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                    ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
                LEFT JOIN "._DB_PREFIX_."dbblog_category_post cp ON cp.id_dbblog_post = p.id_dbblog_post
                WHERE p.active = 1 AND (p.id_dbblog_category = '$id_category' OR cp.id_dbblog_category = '$id_category')";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        return $result;
            
    }

    public static function getPostsViews($id_lang, $link_rewrite = NULL, $id_author = NULL, $id_post = NULL, $limit = 4)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        //$limit = 4;
        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'";
        $sql .= " WHERE p.active = 1 ";
        if($link_rewrite != NULL){
            $sql .= " AND cl.link_rewrite = '$link_rewrite'";
        }
        if($id_author != NULL){
            $sql .= " AND p.author = '$id_author'";
        }
        if($id_post != NULL){
            $sql .= " AND p.id_dbblog_post != '$id_post'";
        }
        $sql .= " GROUP BY p.id_dbblog_post
            ORDER BY views DESC
            LIMIT ".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['views'] = $row['views'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_add']), 'd/m/Y');
            if(!empty($row['image'])) {
                $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $row['image'];
            } else {
                $posts[$row['id_dbblog_post']]['img'] = '';
            }
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
        }
        return $posts;     
    }

    public static function getPostsLast($id_lang, $link_rewrite = NULL, $id_author = NULL, $id_post = NULL, $limit = 4)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        //$limit = 4;
        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'";
        $sql .= " WHERE p.active = 1";
        if($link_rewrite != NULL){
            $sql .= " AND cl.link_rewrite = '$link_rewrite'";
        }
        if($id_author != NULL){
            $sql .= " AND p.author = '$id_author'";
        }
        if($id_post != NULL){
            $sql .= " AND p.id_dbblog_post != '$id_post'";
        }
        $sql .= " ORDER BY date_add DESC
            LIMIT ".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if($comments['total'] == 0){
                $rating = 0;
            } else {
                $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 1);
            }

            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_add']), 'd/m/Y');
            if(!empty($row['image'])) {
                $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $row['image'];
            } else {
                $posts[$row['id_dbblog_post']]['img'] = '';
            }
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
        }
        return $posts;     
    }

    public function isToggleStatus($id_category){
        $sql = "SELECT active FROM "._DB_PREFIX_."dbblog_category WHERE id_dbblog_category = '$id_category'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_category SET active = '$active' WHERE id_dbblog_category = '$id_category'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

    public function isToggleIndex($id_category){
        $sql = "SELECT `index` FROM "._DB_PREFIX_."dbblog_category WHERE id_dbblog_category = '$id_category'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_category SET `index` = '$active' WHERE id_dbblog_category = '$id_category'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

    public static function getAuthors($limit = 0, $id_category)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        if((int)$limit == 0) {
            $limit = (int)Configuration::get('DBBLOG_SIDEBAR_AUTHOR');
        }
        $sql = "SELECT 
                    dbal.name, 
                    dba.id_dbaboutus_author, 
                    dbal.link_rewrite, 
                    dbal.profession,
                    (SELECT count(*) as total 
                    FROM "._DB_PREFIX_."dbblog_post a
                    LEFT JOIN "._DB_PREFIX_."dbblog_category_post b 
                        ON a.id_dbblog_post = b.id_dbblog_post
                    WHERE a.author = dba.id_dbaboutus_author AND a.active = 1 AND (a.id_dbblog_category = '$id_category' OR b.id_dbblog_category = '$id_category')) as posts
                FROM "._DB_PREFIX_."dbaboutus_author dba
                INNER JOIN "._DB_PREFIX_."dbaboutus_author_lang dbal 
                    ON dba.id_dbaboutus_author = dbal.id_dbaboutus_author 
                        AND dbal.id_lang = '$id_lang' AND dbal.id_shop = '$id_shop'
                INNER JOIN "._DB_PREFIX_."dbblog_post p ON p.author = dba.id_dbaboutus_author
                LEFT JOIN "._DB_PREFIX_."dbblog_category_post cp ON cp.id_dbblog_post = p.id_dbblog_post 
                WHERE dba.active = 1 AND p.active = 1 AND (p.id_dbblog_category = '$id_category' OR cp.id_dbblog_category = '$id_category')
                GROUP BY dba.id_dbaboutus_author
                ORDER BY posts DESC
                LIMIT ".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $authors = array();
        foreach ($result as $key => $row) {
            $authors[$row['id_dbaboutus_author']]['name'] = $row['name'];
            $authors[$row['id_dbaboutus_author']]['id'] = $row['id_dbaboutus_author'];
            $authors[$row['id_dbaboutus_author']]['profession'] = $row['profession'];
            $authors[$row['id_dbaboutus_author']]['comments_author'] = (int)$row['posts'];
            $authors[$row['id_dbaboutus_author']]['imagen'] = DbBlogPost::getImage($row['id_dbaboutus_author']);
            $authors[$row['id_dbaboutus_author']]['url'] = DbBlogPost::getLink_author($row['link_rewrite']);
        }
        return $authors;
    }

    public static function getPostsDestacados($id_category)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            LEFT JOIN "._DB_PREFIX_."dbblog_category_post cp ON cp.id_dbblog_post = p.id_dbblog_post 
            WHERE p.active = 1 AND p.id_dbblog_category = '$id_category' AND featured = 1
            GROUP BY p.id_dbblog_post
            ORDER BY p.date_add DESC";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if($comments['total'] == 0){
                $rating = 0;
            } else {
                $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 1);
            }

            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_upd']), 'd/m/Y');
            if(!empty($row['image'])) {
                $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $row['image'];
            } else {

            }
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
        }

        return $posts;
    }

}