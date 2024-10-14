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


class AdminDbBlogCommentController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'dbblog_comment';
        $this->className = 'DbBlogComment';
        $this->lang = false;
        //$this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->fields_list = array(
            'id_dbblog_comment' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'width' => 30
            ),
            'id_post' => array(
                'title' => $this->trans('ID Post', array(), 'Admin.Global'),
            ),
            'name_post' => array(
                'title' => $this->trans('Post', array(), 'Admin.Global'),
            ),
            'name' => array(
                'title' => $this->trans('Nombre', array(), 'Admin.Global'),
            ),
            'comment' => array(
                'title' => $this->trans('Comentario', array(), 'Admin.Global'),
                'width' => 500,
            ),
            /*'rating' => array(
                'title' => $this->trans('Puntuación', array(), 'Admin.Global'),
            ),*/
            'approved' => array(
                'title' => 'Aprobado',
                'active' => 'approved',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false,
                'search' => true,
                'width' => 25,
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function initProcess()
    {
        $isStatusAction = Tools::getIsset('approved'.$this->table);
        if ($isStatusAction)
        {
            DbBlogComment::isToggleApproved((int)Tools::getValue('id_dbblog_comment'));
            return;
        }

        return parent::initProcess();
    }

    public function renderList()
    {
        // removes links on rows
        $this->list_no_link = true;

        $this->_select = 'pl.title as name_post';
        $this->_join = "INNER JOIN "._DB_PREFIX_."dbblog_post_lang pl ON a.id_post = pl.id_dbblog_post AND pl.id_lang = ".(int)Context::getContext()->language->id;

        // adds actions on rows
        $this->addRowAction('edit');
        $this->addRowAction('respond');
        $this->addRowAction('delete');
        
        return parent::renderList();
    }

    public function renderView()
    {
        // gets necessary objects
        $id_dbblog_comment = (int)Tools::getValue('id_dbblog_comment');
        return parent::renderView();
    }

    public function renderForm()
    {

        // Sets the title of the toolbar
        $this->toolbar_title = $this->l('Comentario');

        // Sets the fields of the form
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Comentario'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_dbblog_comment',
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Nombre'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => false,
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Comentario'),
                    'name' => 'comment',
                    'lang' => false,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Aprobado'),
                    'name' => 'approved',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                
            ),
        );


        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        return parent::renderForm();
    }

    public function displayRespondLink($token = null, $id)
    {
        $link = $this->context->link->getAdminLink('AdminDbBlogRespond', true, [], ['id_dbblog_comment' => $id]);
        $button = '<a href="'.$link.'" class="edit"><i class="material-icons">question_answer</i> Responder</a>';
        return $button;
    }

}