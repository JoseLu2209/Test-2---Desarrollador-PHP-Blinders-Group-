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


class AdminDbBlogPostController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'dbblog_post';
        $this->className = 'DbBlogPost';
        $this->lang = true;
        //$this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->fields_list = array(
            'id_dbblog_post' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'width' => 30
            ),
            'image' => array(
                'title' => $this->trans('Imagen', array(), 'Admin.Global'),
                'width' => 150,
                'orderby' => false,
                'search' => false,
                'callback' => 'getImg',
            ),
            'title' => array(
                'title' => $this->trans('Nombre', array(), 'Admin.Global'),
            ),
            'short_desc' => array(
                'title' => $this->trans('Descripción', array(), 'Admin.Global'),
                'callback' => 'cleanHtml',
                'width' => 500,
            ),
            'featured' => array(
                'title' => 'Destacado',
                'active' => 'featured',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false,
                'search' => true,
                'width' => 25,
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
            DbBlogPost::isToggleStatus((int)Tools::getValue('id_dbblog_post'));
            return;
        }

        if (Tools::getIsset('index'.$this->table))
        {
            DbBlogPost::isToggleIndex((int)Tools::getValue('id_dbblog_post'));
            return;
        }

        if (Tools::getIsset('featured'.$this->table))
        {
            DbBlogPost::isToggleFeatured((int)Tools::getValue('id_dbblog_post'));
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
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        
        return parent::renderList();
    }

    public function renderView()
    {
        // gets necessary objects
        $id_dbblog_post = (int)Tools::getValue('id_dbblog_post');
        return parent::renderView();
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);

        // Sets the title of the toolbar
        if (Tools::isSubmit('add'.$this->table)) {
            $this->toolbar_title = $this->l('Crear nuevo post');
        } else {
            $this->toolbar_title = $this->l('Actualizar post');
        }

        $categories = DbBlogCategory::getCategories($this->context->language->id, true, -1);
        $categories_selected = DbBlogCategory::getCategoriesSelected($obj->id_dbblog_post);
        $authors = DbBlogPost::getAuthors(999);

        // Imagen cuando editamos
        $image = '';
        if(isset($obj->id)) {
            if (file_exists(_PS_MODULE_DIR_ . 'dbblog/views/img/post/'.$obj->image[1]) && !empty($obj->image[1])) {
                $image_url = ImageManager::thumbnail(_PS_MODULE_DIR_ . 'dbblog/views/img/post/'.$obj->image[1], 'dbblog_'.$obj->image[1], 350, 'jpg', false);
                $image = '<div class="col-lg-6">' . $image_url . '</div>';
            } else {
                $image = '';
            }

            $this->fields_value = array(
                'image_old' => $obj->image[1],
                'category_post[]' => $categories_selected,
                'publish_date' => $obj->publish_date // Cargar la fecha de publicación
            );
        } else {
            $this->fields_value = array(
                'category_post[]' => $categories_selected,
                'publish_date' => '' // Inicializar en vacío
            );
        }

        // Sets the fields of the form
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Post'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(
                // Incluido para el ejercicio 2
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Fecha de publicación'),
                    'name' => 'publish_date',
                    'desc' => $this->l('Establezca una fecha y hora para que se publique la publicación.'),
                    'required' => false,
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_dbblog_category',
                ),

                array(
                    'type' => 'select',
                    'label' => $this->l('Categoría'),
                    'name' => 'category_post',
                    'multiple' => true,
                    'required' => true,
                    'desc' => $this->l('Selecciona todas las categorias donde quieres que aparezca el post'),
                    'options' => array(
                        'id' => 'id',
                        'query' => $categories,
                        'name' => 'title'
                    )
                ),

                array(
                    'type' => 'select',
                    'label' => $this->l('Categoría principal'),
                    'name' => 'id_dbblog_category',
                    'multiple' => false,
                    'required' => true,
                    'desc' => $this->l('Será la categoría principal del post'),
                    'options' => array(
                        'id' => 'id',
                        'query' => $categories,
                        'name' => 'title'
                    )
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Título'),
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
                    'type' => 'file',
                    'label' => $this->l('Imagen'),
                    'name' => 'image',
                    'required' => false,
                    'desc' => $this->l('Formato permitido: .gif, .jpg, .png')
                ),
            ),

            'submit' => array(
                'title' => $this->l('Guardar'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $this->tpl_view_vars['image'] = $image;

        return parent::renderForm();
    }
    //Realizado para el segundo ejercicio
    //Necesario para guardar la fecha de publicacion
    public function processAdd()
    {
        $object = parent::processAdd();

        if ($object->id > 0) {
            // Imagen
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $image_name = $this->saveImg($object);
                $object->image = $image_name;
                $object->update();
            }

            // Guardar la fecha de publicación
            $publish_date = Tools::getValue('publish_date');
            if ($publish_date) {
                $object->publish_date = $publish_date; // Asigna la fecha de publicación
                $object->update(); // Actualiza el objeto
            }

            // Categorías Asociadas
            if (!empty(Tools::getValue('category_post')) && count(Tools::getValue('category_post')) > 0) {
                foreach (Tools::getValue('category_post') as $id_category) {
                    $id_post = $object->id;
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "dbblog_category_post VALUES ('$id_category', '$id_post')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                }
            }
        }

        return $object;
    }
    //Realizado para el segundo ejercicio
    //Necesario para actualizar la fecha de publicacion
    public function processUpdate()
    {
        $object = parent::processUpdate();

        if ($object != false && $object->id_dbblog_post > 0) {
            // Imagen
            $image_name = $this->saveImg($object);
            $object->image = $image_name;
            $object->update();

            // Guardar la fecha de publicación
            $publish_date = Tools::getValue('publish_date');
            if ($publish_date) {
                $object->publish_date = $publish_date; // Asigna la fecha de publicación
                $object->update(); // Actualiza el objeto
            }

            // Categorías Asociadas
            Db::getInstance(_DB_PREFIX_ . "dbblog_category_post")->delete("dbblog_category_post", "id_dbblog_post = '" . (int)$object->id_dbblog_post . "'");
            if (is_array(Tools::getValue('category_post'))) {
                foreach (Tools::getValue('category_post') as $id_category) {
                    $id_post = $object->id_dbblog_post;
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "dbblog_category_post VALUES ('$id_category', '$id_post')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                }
            }
        }

        return $object;
    }

    public function getImg($img, $tr)
    {
        if ($img) {
            return '<img src="' . _MODULE_DIR_ . 'dbblog/views/img/post/' . $img . '" style="width:50px; height:50px;" />';
        }

        return '';
    }

    public function cleanHtml($html)
    {
        return strip_tags($html);
    }

    public function saveImg($object)
    {
        // Save the image logic here
        // You might want to handle file uploads and validations accordingly
        if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $image_name = 'post_' . time() . '.' . $ext;
                $target_path = _PS_MODULE_DIR_ . 'dbblog/views/img/post/' . $image_name;
                move_uploaded_file($file['tmp_name'], $target_path);
                return $image_name;
            }
        }

        return false;
    }
}
