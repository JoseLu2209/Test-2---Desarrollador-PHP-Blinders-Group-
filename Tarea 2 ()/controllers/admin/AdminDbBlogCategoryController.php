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


class AdminDbBlogCategoryController extends ModuleAdminController
{
    protected $_defaultOrderBy = 'position';
    protected $_defaultOrderWay = 'ASC';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'dbblog_category';
        $this->className = 'DbBlogCategory';
        $this->lang = true;
        //$this->multishop_context = Shop::CONTEXT_ALL;
        $this->position_identifier = 'position';
        $this->_where = 'AND a.`id_parent` = 0';
        $this->_orderWay = $this->_defaultOrderWay;

        parent::__construct();

        $this->fields_list = array(
            'id_dbblog_category' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'width' => 30
            ),
            'title' => array(
                'title' => $this->trans('Nombre', array(), 'Admin.Global'),
            ),
            'short_desc' => array(
                'title' => $this->trans('Descripción', array(), 'Admin.Global'),
                'callback' => 'cleanHtml',
                'width' => 500,
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
                'width' => 40,
            ),
            'active' => array(
                'title' => 'Activo',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false,
                'search' => true,
                'width' => 25,
            ),
        );

        if($this->module->premium == 1) {
            $this->fields_list['index'] = DbBlogPremium::renderListProduct();
        }

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
        if (Tools::getIsset('status'.$this->table))
        {
            DbBlogCategory::isToggleStatus((int)Tools::getValue('id_dbblog_category'));
            return;
        }

        if (Tools::getIsset('index'.$this->table))
        {
            DbBlogCategory::isToggleIndex((int)Tools::getValue('id_dbblog_category'));
            return;
        }

        return parent::initProcess();
    }

    public function renderList()
    {
        // removes links on rows
        $this->list_no_link = true;

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND b.`id_shop` = '.(int)Context::getContext()->shop->id;
        }

        // adds actions on rows
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        
        return parent::renderList();
    }

    public function renderView()
    {
        // gets necessary objects
        $id_dbblog_category = (int)Tools::getValue('id_dbblog_category');       

        // return parent::renderView();

        if (($id_dbblog_category)) {
            $this->_where = 'AND a.`id_parent` = '.(int)$id_dbblog_category;
        }
        $this->position_identifier = 'position';
        $this->_orderWay = $this->_defaultOrderWay;

        // removes links on rows
        $this->list_no_link = true;

        // adds actions on rows
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        // Sets the title of the toolbar
        if (Tools::isSubmit('add'.$this->table)) {
            $this->toolbar_title = $this->l('Crear nueva categoría');
        } else {
            $this->toolbar_title = $this->l('Actualizar categoría');
        }

        $categories = DbBlogCategory::getCategories($this->context->language->id, true, false);
        array_unshift($categories, array('id' => 0, 'title' => $this->l('Ninguno')));
        foreach ($categories as $key => $category) {
            if (isset($obj->id) && $obj->id) {
                if ($category['id'] == $obj->id_simpleblog_category) {
                    unset($category[$key]);
                }
            }
        }


        // Sets the fields of the form
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Categoría'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_dbblog_category',
                ),

                array(
                    'type' => 'select',
                    'label' => $this->l('Categoría padre'),
                    'name' => 'id_parent',
                    'required' => true,
                    'options' => array(
                        'id' => 'id',
                        'query' => $categories,
                        'name' => 'title'
                        )
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Nombre'),
                    'name' => 'title',
                    'required' => true,
                    'lang' => true,
                    'id' => 'name',
                    'class' => 'copy2friendlyUrl',
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Descripcion corta'),
                    'name' => 'short_desc',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Descripción'),
                    'name' => 'large_desc',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Url'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                ),


                
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Active', array(), 'Admin.Global'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                ),
                
            ),
        );

        if($this->module->premium == 1) {
            $this->fields_form['input'][] = DbBlogPremium::renderFormCategory();
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        $this->fields_form['buttons'] = array(
            'save-and-stay' => array(
                'title' => $this->trans('Guardar y permanecer', array(), 'Admin.Actions'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
            )
        );

        return parent::renderForm();
    }

    public function processDelete()
    {
        // Comprobamos antes de borrar si hay subcategorias y posts asociados
        $obj = $this->loadObject();
        $id_dbblog_category = $obj->id_dbblog_category;
        $sql = "SELECT count(*) as total
            FROM "._DB_PREFIX_."dbblog_category 
            WHERE id_parent = '$id_dbblog_category'";
        $subcats = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if((int)$subcats > 0){
            $this->errors[] = $this->l('Tienes subcategorías asociadas a esta categoría, primero tienes que borrar las subcategorías');
        }
        $sql = "SELECT count(*) as total
            FROM "._DB_PREFIX_."dbblog_post 
            WHERE id_dbblog_category = '$id_dbblog_category'";
        $posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if((int)$posts > 0){
            $this->errors[] = $this->l('Tienes posts asociadas a esta categoría como principal, primero tienes que cambiar la asociación de los posts a otra categoría principal');
        }
        if(count($this->errors) > 0){
            return;
        }

        // Borramos si la comprobacion es correcta
        $object = parent::processDelete();
        return $object;
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id_dbblog_category = (int)(Tools::getValue('id'));
        $positions = Tools::getValue('dbblog_category');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            $id_dbblog_category = (int)$pos[2];

            if ((int)$id_dbblog_category > 0) {
                if ($DbBlogCategory = new DbBlogCategory($id_dbblog_category)) {
                    $DbBlogCategory->position = $position+1;
                    if ($DbBlogCategory->update()) {
                        echo 'Posicion '.(int)$position.' para la categoria '.(int)$DbBlogCategory->id.' actualizada\r\n';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This category ('.(int)$id_dbblog_category.') cant be loaded"}';
                }

            }
        }
    }

    public function cleanHtml($html)
    {
        return strip_tags(stripslashes($html));
    }
}