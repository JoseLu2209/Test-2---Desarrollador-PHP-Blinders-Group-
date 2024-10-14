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
 *  @author    DevBlinders <info@devblinders.com>
 *  @copyright 2007-2020 DevBlinders
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


class AdminDbBlogRespondController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->name = 'AdminDbBlogRespondController';

        parent::__construct();

    }

    public function initProcess()
    {
        parent::initProcess();


        if(Tools::getValue('submitDbblogRespond')) {
            $id_author = (int) Tools::getValue('id_dbaboutus_author');
            $autor = new DbAboutUsAuthor($id_author, $this->context->language->id);

            $comment = new DbBlogComment();
            $comment->id_comment_parent = (int) Tools::getValue('id_comment_parent');
            $comment->id_post = (int) Tools::getValue('id_post');
            $comment->name = $autor->name;
            $comment->comment = Tools::getValue('content');
            $comment->approved = (int) Tools::getValue('active');
            $comment->rating = 0;
            $comment->moderator = 1;
            $comment->date_add = date('Y-m-d');
            $comment->add();

            $redirect = $this->context->link->getAdminLink('AdminDbBlogComment', true, [], []);
            Tools::redirect($redirect);
            die();
        }

        return $this->getContent();
    }

    public function getContent()
    {

        $autores = new DbAboutUsAuthor();
        $authors = $autores->getAuthors();
        $options = [];
        foreach($authors as $key => $author){
            $options[$key]['id_option'] = $author['id_dbaboutus_author'];
            $options[$key]['name'] = $author['name'];
        }

        $fields_values = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Respuesta'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_post',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_comment_parent',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Autor'),
                        'name' => 'id_dbaboutus_author',
                        'required' => true,
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'col' => 8,
                        'type' => 'textarea',
                        'desc' => $this->l('Escribe tu respuesta'),
                        'name' => 'content',
                        'label' => $this->l('Respuesta'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $id_comment = Tools::getValue('id_dbblog_comment');
        $comment = new DbBlogComment($id_comment);
        $config_values = array(
            'id_comment_parent' => $id_comment,
            'id_post' => $comment->id_post,
            'id_dbaboutus_author' => '',
            'content' => '',
            'active' => 1,
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDbblogRespond';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminDbBlogRespond', true, [], ['id_dbblog_comment' => $id_comment]);
        //$helper->token = Tools::getAdminTokenLite('AdminDbBlogRespond');

        $helper->tpl_vars = array(
            'fields_value' => $config_values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $formulario = $helper->generateForm(array($fields_values));

        $this->context->smarty->assign('formulario', $formulario);
        $this->content .= $this->module->display(_PS_MODULE_DIR_.$this->module->name, '/views/templates/admin/respond.tpl');
    }

}
