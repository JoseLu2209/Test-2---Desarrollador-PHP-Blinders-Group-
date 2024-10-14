<?php

class dbblogAjaxModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();        
    }
    
 
    public function displayAjax()
    {
        $result = 'Error';
        $action = Tools::getValue('action');
        if($action == 'send_comment'){

            // Crear el comentario
            $id_post = (int)Tools::getValue('id_post');
            $id_comment = (int)Tools::getValue('id_comment');
            $nombre = Tools::getValue('nombre');
            $comentario = Tools::getValue('comentario');
            $rating = (int)Tools::getValue('rating');
            if($rating == 0){
                $rating = 5;
            }
            $id_comment_parent = Tools::getValue('id_comment_parent');

            // reCAPTCHA
            $recaptcha_enable = Configuration::get('DBBLOG_RECAPTCHA_ENABLE');
            $recaptcha = Configuration::get('DBBLOG_RECAPTCHA');
            $recaptcha_private = Configuration::get('DBBLOG_RECAPTCHA_PRIVATE');
            if($recaptcha_enable == 1 && !empty($recaptcha) && !empty($recaptcha_private)){
                $recaptcha = Tools::getValue('recaptcha');
                $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_private."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
                if ($response['success'] == false) {
                    die(json_encode(array('result' => 'reCAPTCHA')));
                }
            }

            if($id_post > 0){
                $date_add = date('Y-m-d H:i:s');

                $comment = new DbBlogComment();
                $comment->id_post = $id_post;
                $comment->id_comment_parent = $id_comment;
                $comment->name = $nombre;
                $comment->comment = $comentario;
                $comment->rating = $rating;
                $comment->approved = 0;
                $comment->moderator = 0;
                $comment->date_add = $date_add;
                if($comment->save()){
                    $result = 'OK';
                }

                die(json_encode(array('result' => $result)));
            }

        }

        if($action == 'infinite_scroll'){

            // Scroll Infinito
            $id_lang = Context::getContext()->language->id;
            $page = (int)Tools::getValue('page');
            $id_category = (int)Tools::getValue('id_category');

            if($page > 0){

                $id_lang = Context::getContext()->language->id;
                if($id_category > 0) {
                    $posts = DbBlogCategory::getPostsById($id_category, $id_lang, $page);
                } else {
                    $posts = DbBlogPost::getPostHome($id_lang, $page);
                }

                $list_post = $this->module->renderScroll($posts);
                
                $result = 'OK';
                die(json_encode(array(
                    'result' => $result,
                    'list_post' => $list_post,
                    'sum' => (int)count($posts),
                )));
            }
        }
            
        if($action == 'infinite_scroll_author'){

            // Scroll Infinito
            $id_lang = Context::getContext()->language->id;
            $page = (int)Tools::getValue('page');
            $id_author = Tools::getValue('id_author');

            if($page > 0){

                $id_lang = Context::getContext()->language->id;
                $posts = DbBlogAuthor::getPosts($id_author, $id_lang, $page);
                $list_post = $this->module->renderScroll($posts);
                
                $result = 'OK';
                die(json_encode(array(
                    'result' => $result,
                    'list_post' => $list_post,
                    'sum' => (int)count($posts),
                )));

            }
        }

        if($action == 'form_respond'){

            // Form Comment
            $id_lang = Context::getContext()->language->id;
            $id_comment = (int)Tools::getValue('id_comment');
            $id_post = (int)Tools::getValue('id_post');

            if($id_comment > 0 && $id_post > 0){

                $form = $this->module->renderFormRespond($id_comment, $id_post);
                
                $result = 'OK';
                die(json_encode(array(
                    'result' => $result,
                    'form' => $form,
                )));

            }
        }
        die(json_encode(array('result' => $result)));
    }
 
}