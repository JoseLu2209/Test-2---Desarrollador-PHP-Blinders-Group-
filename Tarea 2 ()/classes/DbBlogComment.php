<?php

class DbBlogComment extends ObjectModel
{

    public $id;
    public $id_dbblog_comment;
    public $id_comment_parent = 0;
    public $id_post;
    public $name;
    public $comment;
    public $rating;
    public $approved = 0;
    public $moderator = 0;

    public static $definition = array(
        'table' => 'dbblog_comment',
        'primary' => 'id_dbblog_comment',
        'multilang' => false,
        'fields' => array(
            'id_comment_parent' =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_post' =>            array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' =>               array('type' => self::TYPE_STRING, 'required' => true , 'validate' => 'isCleanHtml', 'size' => 128),
            'comment' =>            array('type' => self::TYPE_STRING, 'required' => false),
            'rating' =>	            array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'approved' =>           array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'moderator' =>          array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>		    array('type' => self::TYPE_DATE),
        ),
    );

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
    }

    public static function getComments($id_post)
    {        
        $sql = "SELECT * 
            FROM "._DB_PREFIX_."dbblog_comment
            WHERE approved = 1 AND id_post = '$id_post' 
            ORDER BY date_add ASC";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $tree = self::buildTree($result);
            
        return $tree;
    }

    public static function buildTree(array $elements, $parentId = 0) {
        $branch = array();
    
        foreach ($elements as $element) {
            if ($element['id_comment_parent'] == $parentId) {
                $children = self::buildTree($elements, $element['id_dbblog_comment']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
    
        return $branch;
    }

    public static function getTotalCommentsByPost($id_post)
    {
        $sql = "SELECT count(*) as total, SUM(rating) as suma
            FROM "._DB_PREFIX_."dbblog_comment
            WHERE approved = 1 AND id_post = '$id_post' AND id_comment_parent = 0";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            
        return $result;
    }

    public static function getTotalCommentsByAuthor($id_author)
    {
        // if (!Validate::isBool($active))
        //     die(Tools::displayError());
        
        $sql = "SELECT count(*) as total, SUM(rating) as suma
            FROM "._DB_PREFIX_."dbblog_comment c
            INNER JOIN "._DB_PREFIX_."dbblog_post p ON c.id_post = p.id_dbblog_post AND p.author = '$id_author'
            WHERE approved = 1";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            
        return $result;
    }

    public function isToggleApproved($id_comment){
        $sql = "SELECT approved FROM "._DB_PREFIX_."dbblog_comment WHERE id_dbblog_comment = '$id_comment'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbblog_comment SET approved = '$active' WHERE id_dbblog_comment = '$id_comment'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(json_encode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

}