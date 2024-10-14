<?php

class DbBlogPost extends ObjectModel
{

    public $id;
    public $id_dbblog_post;
    public $id_dbblog_category;
    public $type = 1;
    public $author;
    public $featured = 0;
    public $views = 0;
    public $active = 1;
    public $index = 1;
    
    public $title;
    public $short_desc;
    public $large_desc;
    public $image;
    public $link_rewrite;
    public $meta_title;
    public $meta_description;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'dbblog_post',
        'primary' => 'id_dbblog_post',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_dbblog_category' =>	    array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'active' =>			        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'type' =>		            array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'author' =>		            array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'featured' =>		        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'views' =>		            array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'index' =>			        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>		        array('type' => self::TYPE_DATE),
            'date_upd' =>		        array('type' => self::TYPE_DATE),
            
            // Lang fields
            'short_desc' =>	        array('type' => self::TYPE_HTML, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 4000),
            'large_desc' =>	        array('type' => self::TYPE_HTML, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml'),
			'title' =>			    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true , 'validate' => 'isCleanHtml', 'size' => 128),
            'link_rewrite' =>	    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 128),
            'meta_title' =>	        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 128),
            'meta_description' =>	array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 255),
            'image' =>			    array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 128),
        ),
    );

    public function __construct($id_dbblog_post = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_dbblog_post, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $default_language_id = Configuration::get('PS_LANG_DEFAULT');
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

    public static function getAuthors($limit = 0, $all = 1)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        if($limit == 0) {
            $limit = (int)Configuration::get('DBBLOG_SIDEBAR_AUTHOR');
        }
        $sql = "SELECT 
                    dbal.name, dba.id_dbaboutus_author, 
                    dbal.link_rewrite, 
                    dbal.profession,
                    (SELECT count(*) as total FROM "._DB_PREFIX_."dbblog_post WHERE author = dba.id_dbaboutus_author AND active = 1) as posts
                FROM "._DB_PREFIX_."dbaboutus_author dba
                INNER JOIN "._DB_PREFIX_."dbaboutus_author_lang dbal 
                    ON dba.id_dbaboutus_author = dbal.id_dbaboutus_author 
                        AND dbal.id_lang = '$id_lang' AND dbal.id_shop = '$id_shop'
                WHERE dba.active = 1
                ORDER BY posts DESC";
        if($all == 0) {
            $sql .= " LIMIT ".$limit;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $authors = array();
        foreach ($result as $key => $row) {
            $authors[$row['id_dbaboutus_author']]['name'] = $row['name'];
            $authors[$row['id_dbaboutus_author']]['id'] = $row['id_dbaboutus_author'];
            $authors[$row['id_dbaboutus_author']]['profession'] = $row['profession'];
            $authors[$row['id_dbaboutus_author']]['comments_author'] = (int)$row['posts'];
            $authors[$row['id_dbaboutus_author']]['imagen'] = self::getImage($row['id_dbaboutus_author']);
            $authors[$row['id_dbaboutus_author']]['url'] = self::getLink_author($row['link_rewrite']);
        }
        return $authors;
    }

    public static function getAuthorById($id_author)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT dba.*, dbal.*, dbatl.name as name_tag
            FROM "._DB_PREFIX_."dbaboutus_author dba
            INNER JOIN "._DB_PREFIX_."dbaboutus_author_lang dbal 
                ON dba.id_dbaboutus_author = dbal.id_dbaboutus_author 
                    AND dbal.id_lang = '$id_lang' AND dbal.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbaboutus_tag_lang dbatl
                ON dba.id_tag = dbatl.id_dbaboutus_tag
                    AND dbal.id_lang = '$id_lang' AND dbal.id_shop = '$id_shop'
            WHERE dba.id_dbaboutus_author = '$id_author'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        $author = array();
        $author['name'] = $result['name'];
        $author['link_rewrite'] = $result['link_rewrite'];
        $author['url'] = self::getLink_author($author['link_rewrite']);
        $author['id'] = $id_author;
        $author['profession'] = $result['profession'];
        $author['description'] = $result['short_desc'];
        $author['tag'] = $result['name_tag'];
        $author['twitter'] = $result['twitter'];
        $author['facebook'] = $result['facebook'];
        $author['linkedin'] = $result['linkedin'];
        $author['youtube'] = $result['youtube'];
        $author['instagram'] = $result['instagram'];
        $author['imagen'] = self::getImage($result['id_dbaboutus_author']);
        return $author;
    }

    public static function getPostsMoreViews($id_dbblog_author, $id_lang, $id_post, $limit = 4)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post 
                    AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category
                    AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            WHERE p.active = 1 AND p.author = '$id_dbblog_author' AND p.id_dbblog_post != '$id_post'
            GROUP BY p.id_dbblog_post
            ORDER BY p.views DESC
            LIMIT ".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if($comments['total'] == 0){
                $rating = 0;
            } else {
                $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 0);
            }

            $posts[$row['id_dbblog_post']]['author'] = self::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = DbBlogPost::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['views'] = $row['views'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_upd']), 'd/m/Y');
            $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_.'dbblog/views/img/post/'.$row['image'];
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogPost::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
            $posts[$row['id_dbblog_post']]['active'] = $row['active'];
        }
        return $posts;
    }

    public static function getImage($id_employee)
    {
        $image = $id_employee.'.jpg';
        $img = Dbaboutus::getNewImg($image);

        return $img;
    }

    public static function getPostFeatures($id_lang, $limit = 10)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN  "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post 
                    AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category
                    AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            WHERE p.active = 1 AND p.featured = 1
            GROUP BY p.id_dbblog_post
            ORDER BY views DESC
            LIMIT ".$limit;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $posts[$row['id_dbblog_post']]['author'] = DbBlogPost::getAuthorById($row['author']);
            $posts[$row['id_dbblog_post']]['id'] = $row['id_dbblog_post'];
//            $posts[$row['id_dbblog_post']]['image'] = $row['image'];
            $posts[$row['id_dbblog_post']]['image'] = Dbblog::getNewImg($row['image']);
            $posts[$row['id_dbblog_post']]['url'] = self::getLink($row['link_rewrite'], $id_lang);
            $posts[$row['id_dbblog_post']]['title'] = $row['title'];
            $posts[$row['id_dbblog_post']]['short_desc'] = $row['short_desc'];
            $posts[$row['id_dbblog_post']]['date'] = date_format(date_create($row['date_upd']), 'd/m/Y');
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogPost::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['active'] = $row['active'];
        }
        return $posts;
            
    }

    public static function getLink($rewrite, $id_lang = null, $id_shop = null)
    {
        return Context::getContext()->link->getModuleLink('dbblog', 'dbpost', array('rewrite' => $rewrite));
    }

    public static function getLink_author($rewrite, $id_lang = null, $id_shop = null)
    {
        return Context::getContext()->link->getModuleLink('dbaboutus', 'author', array('rewrite' => $rewrite));
    }

    public static function getPost($id_lang, $rewrite)
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
            FROM "._DB_PREFIX_."dbblog_post p
            INNER JOIN  "._DB_PREFIX_."dbblog_post_lang pl 
                ON p.id_dbblog_post = pl.id_dbblog_post 
                    AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
            INNER JOIN "._DB_PREFIX_."dbblog_category_lang cl 
                ON p.id_dbblog_category = cl.id_dbblog_category
                    AND cl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
            WHERE p.active = 1 AND pl.link_rewrite = '$rewrite'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        $post = array();
        $post['author'] = DbBlogPost::getAuthorById($result['author']);
        $post['id'] = $result['id_dbblog_post'];
        $post['image'] = Dbblog::getNewImg($result['image']);
        if(!empty($post['image'])) {
            $post['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $result['image'];
        } else {
            $post['img'] = '';
        }
        $post['url'] = self::getLink($result['link_rewrite'], $id_lang);
        $post['title'] = $result['title'];
        $post['short_desc'] = $result['short_desc'];
        $post['large_desc'] = $result['large_desc'];
        $post['meta_title'] = $result['meta_title'];
        $post['meta_description'] = $result['meta_description'];
        $post['index'] = $result['index'];
        $post['views'] = (int)$result['views'] + 1;
        $post['date_add'] = date_format(date_create($result['date_add']), 'd/m/Y');
        $post['date_upd'] = date_format(date_create($result['date_upd']), 'd/m/Y');
        $post['date_add_json'] = $result['date_add'];
        $post['date_upd_json'] = $result['date_upd'];
        $post['title_category'] = $result['title_category'];
        $post['url_category'] = DbBlogCategory::getLink($result['link_category'], $id_lang);
        $post['link_rewrite_category'] = $result['link_category'];
        $post['active'] = $result['active'];

        $comments = DbBlogComment::getTotalCommentsByPost($result['id_dbblog_post']);
        if($comments['total'] == 0){
            $rating = 0;
            $avg_rating = 0;
        } else {
            $rating = round($comments['suma'] * 100 / ($comments['total'] * 5), 1);
            $avg_rating = round($comments['suma'] * 5 / ($comments['total'] * 5), 1);
        }
        $post['comments'] = $comments;
        $post['rating'] = $rating;
        $post['avg_rating'] = $avg_rating;

        return $post;   
    }


    public static function getTotalPosts($id_lang, $page = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;


        $sql = "SELECT DISTINCT COUNT(p.id_dbblog_post) as total
                FROM "._DB_PREFIX_."dbblog_post p 
                INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl 
                    ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
                WHERE p.active = 1";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        return $result;
    }


    public static function getPostHome($id_lang, $page = 0)
    {

        $id_shop = (int)Context::getContext()->shop->id;
        $limit = Configuration::get('DBBLOG_POSTS_PER_HOME');
        if ($limit == 0) {
            $limit = 10;
        }
        $offset = $page * $limit;

        $sql = "SELECT p.*, pl.*, cl.title as title_category, cl.link_rewrite as link_category
                FROM " . _DB_PREFIX_ . "dbblog_post p
                INNER JOIN " . _DB_PREFIX_ . "dbblog_post_lang pl 
                    ON p.id_dbblog_post = pl.id_dbblog_post AND pl.id_lang = '$id_lang' AND pl.id_shop = '$id_shop'
                INNER JOIN " . _DB_PREFIX_ . "dbblog_category_lang cl 
                    ON p.id_dbblog_category = cl.id_dbblog_category AND pl.id_lang = '$id_lang' AND cl.id_shop = '$id_shop'
                WHERE p.active = 1
                GROUP BY p.id_dbblog_post
                ORDER BY p.date_add DESC
                LIMIT " . $offset . "," . $limit;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $posts = array();
        foreach ($result as $row) {
            $comments = DbBlogComment::getTotalCommentsByPost($row['id_dbblog_post']);
            if ($comments['total'] == 0) {
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
            $posts[$row['id_dbblog_post']]['img'] = _MODULE_DIR_ . 'dbblog/views/img/post/' . $row['image'];
            $posts[$row['id_dbblog_post']]['title_category'] = $row['title_category'];
            $posts[$row['id_dbblog_post']]['url_category'] = DbBlogCategory::getLink($row['link_category'], $id_lang);
            $posts[$row['id_dbblog_post']]['total_comments'] = $comments['total'];
            $posts[$row['id_dbblog_post']]['rating'] = $rating;
        }
        return $posts;

    }

    public static function sumView($id_post)
    {
        $sql = "UPDATE "._DB_PREFIX_."dbblog_post SET views = views + 1 WHERE id_dbblog_post = '$id_post'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }

    public function isToggleStatus($id_post){
        $sql = "SELECT active FROM "._DB_PREFIX_."dbblog_post WHERE id_dbblog_post = '$id_post'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_post SET active = '$active' WHERE id_dbblog_post = '$id_post'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

    public function isToggleIndex($id_post){
        $sql = "SELECT `index` FROM "._DB_PREFIX_."dbblog_post WHERE id_dbblog_post = '$id_post'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_post SET `index` = '$active' WHERE id_dbblog_post = '$id_post'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

    public function isToggleFeatured($id_post){
        $sql = "SELECT `featured` FROM "._DB_PREFIX_."dbblog_post WHERE id_dbblog_post = '$id_post'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_post SET `featured` = '$active' WHERE id_dbblog_post = '$id_post'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }
}