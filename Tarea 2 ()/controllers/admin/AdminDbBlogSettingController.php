<?php

class AdminDbBlogSettingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;

        parent::__construct();

    }

    public function initProcess()
    {

        if(Tools::getIsset('submitDbblogModule')){
            $this->saveData();
        }

        return parent::initProcess();
    }

    public function renderList()
    {
        $list = parent::renderList();

        $this->context->smarty->assign('name_module', $this->module->name);
        $this->context->smarty->assign('premium', $this->module->premium);

        $iframe = $this->module->display(_PS_MODULE_DIR_.$this->module->name, '/views/templates/admin/iframe.tpl');
        $iframe_bottom = $this->module->display(_PS_MODULE_DIR_.$this->module->name, '/views/templates/admin/iframe_bottom.tpl');

        return $iframe.$list.$this->renderForm().$iframe_bottom;
    }

    /*public function initContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=dbblog&tab_module=dbblog&module_name=dbblog');
    }*/

    public function renderForm()
    {
        $general_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_TITLE',
                        'label' => $this->l('Título Blog'),
                        'desc' => $this->l('El titulo del blog'),
                        'lang'  => true,
                    ),

                    array(
                        'type' => 'textarea',
                        'name' => 'DBBLOG_HOME_SHORT_DESC',
                        'label' => $this->l('Descripción corta'),
                        'desc' => $this->l('Descripción corta en la home del blog'),
                        'autoload_rte' => true,
                        'rows' => 5,
                        'cols' => 40,
                        'lang'  => true,
                    ),

                    array(
                        'type' => 'textarea',
                        'name' => 'DBBLOG_HOME_LARGE_DESC',
                        'label' => $this->l('Descripción larga'),
                        'desc' => $this->l('Descripción larga en la home del blog'),
                        'autoload_rte' => true,
                        'rows' => 5,
                        'cols' => 40,
                        'lang'  => true,
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POSTS_PER_PAGE',
                        'label' => $this->l('Nº Posts por categoría'),
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POSTS_PER_HOME',
                        'label' => $this->l('Nº Posts en Home'),
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POSTS_PER_AUTHOR',
                        'label' => $this->l('Nº Posts en página de autor'),
                        'desc' => $this->l('Número de últimos post en la página del autor'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $seo_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('SEO'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_SLUG',
                        'label' => $this->l('URL del blog'),
                        'lang'  => true,
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_META_TITLE',
                        'label' => $this->l('Meta Título Blog'),
                        'desc' => $this->l('El meta Description del blog'),
                        'lang'  => true,
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_META_DESCRIPTION',
                        'label' => $this->l('Meta Description Blog'),
                        'desc' => $this->l('La metadescription del blog'),
                        'lang'  => true,
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $color_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Colores'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'color',
                        'name' => 'DBBLOG_COLOR_TEXT',
                        'label' => $this->l('Texto'),
                        'desc' => $this->l('Color del texto general'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'color',
                        'name' => 'DBBLOG_COLOR',
                        'label' => $this->l('Primario'),
                        'desc' => $this->l('Color primario o destacado del blog'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'color',
                        'name' => 'DBBLOG_COLOR_BACKGROUND',
                        'label' => $this->l('Fondo'),
                        'desc' => $this->l('Color de fondo del blog'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $redes_blog = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Redes'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_FACEBOOK',
                        'label' => $this->l('Facebook'),
                        'desc' => $this->l('Url Facebook'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_INSTAGRAM',
                        'label' => $this->l('Instagram'),
                        'desc' => $this->l('Url Instagram'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_TWITTER',
                        'label' => $this->l('Twitter'),
                        'desc' => $this->l('Url Twitter'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_LINKEDIN',
                        'label' => $this->l('Linkedin'),
                        'desc' => $this->l('Url Linkedin'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_YOUTUBE',
                        'label' => $this->l('Youtube'),
                        'desc' => $this->l('Url Youtube'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $post_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Posts'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    /*array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar extracto listado'),
                        'name' => 'DBBLOG_POST_EXTRACT',
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
                        'disabled' => true,
                        'class' => 'disabled',
                    ),*/

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar leer más'),
                        'name' => 'DBBLOG_POST_READMORE',
                        'desc' => $this->l('Mostrar botón de leer más en los listados de posts'),
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
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    /*array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_RELATED',
                        'label' => $this->l('Nº Post relacionados'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),*/

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_AUTHOR',
                        'label' => $this->l('Nº Post del mismo autor'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $list_cms = CMSCore::listCms(Context::getContext()->language->id);
        $comment_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Comentarios'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Permitir comentarios'),
                        'name' => 'DBBLOG_COMMENTS',
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

                    array(
                        'type' => 'textarea',
                        'name' => 'DBBLOG_RGPD',
                        'label' => $this->l('Texto RGPD'),
                        'autoload_rte' => true,
                        'rows' => 5,
                        'cols' => 40,
                        'lang'  => true,
                    ),

                    array(
                        'type' => 'select',
                        'lang' => true,
                        'label' => $this->l('Política de privacidad'),
                        'name' => 'DBBLOG_PRIVACITY',
                        'desc' => $this->l('Seleccionar la página de de política de privacidad'),
                        'options' => array(
                            'query' => $list_cms,
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activar Recaptcha V2'),
                        'name' => 'DBBLOG_RECAPTCHA_ENABLE',
                        'desc' => $this->l('Insertar recaptcha de google antibots'),
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
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_RECAPTCHA',
                        'label' => $this->l('reCAPTCHA clave pública'),
                        'desc' => $this->l('Introducir la clave publica de reCAPTCHA de Google'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_RECAPTCHA_PRIVATE',
                        'label' => $this->l('reCAPTCHA clave privada'),
                        'desc' => $this->l('Introducir la clave privada de reCAPTCHA de Google'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $sidebar_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Sidebar Blog'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_SIDEBAR_VIEWS',
                        'label' => $this->l('Nº Posts más vistos'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_SIDEBAR_LAST',
                        'label' => $this->l('Nº Últimos posts'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_SIDEBAR_AUTHOR',
                        'label' => $this->l('Nº de autores'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $home_ps_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Home PrestaShop'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_FEATURED_HOMEPS',
                        'label' => $this->l('Nº Últimos post'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_VIEWS_HOMEPS',
                        'label' => $this->l('Nº Post más vistos'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $sidebar_ps_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Sidebar PrestaShop'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_VIEWS_SIDEBARPS',
                        'label' => $this->l('Nº Post más vistos'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'DBBLOG_POST_LAST_SIDEBARPS',
                        'label' => $this->l('Nº Últimos posts'),
                        'desc' => $this->l('0 para desactivarlos'),
                        'disabled' => true,
                        'class' => 'disabled',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $array_form = array($general_options, $seo_options, $color_options, $redes_blog, $post_options, $comment_options, $sidebar_options, $home_ps_options, $sidebar_ps_options);
        if($this->module->premium == 1) {
            $array_form = DbBlogPremium::getConfigForm();
        }

        $helper = new HelperForm();
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'submitDbblogModule';
        $helper->token = Tools::getAdminTokenLite('AdminDbBlogSetting');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($array_form);
    }

    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $values = array();

        foreach ($languages as $lang){
            $values['DBBLOG_TITLE'][$lang['id_lang']] = Configuration::get('DBBLOG_TITLE', $lang['id_lang']);
            $values['DBBLOG_HOME_SHORT_DESC'][$lang['id_lang']] = Configuration::get('DBBLOG_HOME_SHORT_DESC', $lang['id_lang']);
            $values['DBBLOG_HOME_LARGE_DESC'][$lang['id_lang']] = Configuration::get('DBBLOG_HOME_LARGE_DESC', $lang['id_lang']);
            $values['DBBLOG_SLUG'][$lang['id_lang']] = Configuration::get('DBBLOG_SLUG', $lang['id_lang']);
            $values['DBBLOG_META_TITLE'][$lang['id_lang']] = Configuration::get('DBBLOG_META_TITLE', $lang['id_lang']);
            $values['DBBLOG_META_DESCRIPTION'][$lang['id_lang']] = Configuration::get('DBBLOG_META_DESCRIPTION', $lang['id_lang']);
            $values['DBBLOG_RGPD'][$lang['id_lang']] = Configuration::get('DBBLOG_RGPD', $lang['id_lang']);
        }

        $values['DBBLOG_COLOR'] = Configuration::get('DBBLOG_COLOR');
        $values['DBBLOG_COLOR_TEXT'] = Configuration::get('DBBLOG_COLOR_TEXT');
        $values['DBBLOG_COLOR_BACKGROUND'] = Configuration::get('DBBLOG_COLOR_BACKGROUND');
        $values['DBBLOG_FACEBOOK'] = Configuration::get('DBBLOG_FACEBOOK');
        $values['DBBLOG_INSTAGRAM'] = Configuration::get('DBBLOG_INSTAGRAM');
        $values['DBBLOG_TWITTER'] = Configuration::get('DBBLOG_TWITTER');
        $values['DBBLOG_LINKEDIN'] = Configuration::get('DBBLOG_LINKEDIN');
        $values['DBBLOG_YOUTUBE'] = Configuration::get('DBBLOG_YOUTUBE');
        $values['DBBLOG_POSTS_PER_PAGE'] = Configuration::get('DBBLOG_POSTS_PER_PAGE');
        $values['DBBLOG_POSTS_PER_HOME'] = Configuration::get('DBBLOG_POSTS_PER_HOME');
        $values['DBBLOG_POSTS_PER_AUTHOR'] = Configuration::get('DBBLOG_POSTS_PER_AUTHOR');
        $values['DBBLOG_SIDEBAR_VIEWS'] = Configuration::get('DBBLOG_SIDEBAR_VIEWS');
        $values['DBBLOG_SIDEBAR_LAST'] = Configuration::get('DBBLOG_SIDEBAR_LAST');
        $values['DBBLOG_SIDEBAR_AUTHOR'] = Configuration::get('DBBLOG_SIDEBAR_AUTHOR');
        $values['DBBLOG_COMMENTS'] = Configuration::get('DBBLOG_COMMENTS');
        $values['DBBLOG_PRIVACITY'] = Configuration::get('DBBLOG_PRIVACITY');
        $values['DBBLOG_RECAPTCHA_ENABLE'] = Configuration::get('DBBLOG_RECAPTCHA_ENABLE');
        $values['DBBLOG_RECAPTCHA'] = Configuration::get('DBBLOG_RECAPTCHA');
        $values['DBBLOG_RECAPTCHA_PRIVATE'] = Configuration::get('DBBLOG_RECAPTCHA_PRIVATE');
        $values['DBBLOG_POST_RELATED'] = Configuration::get('DBBLOG_POST_RELATED');
        $values['DBBLOG_POST_EXTRACT'] = Configuration::get('DBBLOG_POST_EXTRACT');
        $values['DBBLOG_POST_READMORE'] = Configuration::get('DBBLOG_POST_READMORE');
        $values['DBBLOG_POST_AUTHOR'] = Configuration::get('DBBLOG_POST_AUTHOR');
        $values['DBBLOG_POST_FEATURED_HOMEPS'] = Configuration::get('DBBLOG_POST_FEATURED_HOMEPS');
        $values['DBBLOG_POST_VIEWS_HOMEPS'] = Configuration::get('DBBLOG_POST_VIEWS_HOMEPS');
        $values['DBBLOG_POST_VIEWS_SIDEBARPS'] = Configuration::get('DBBLOG_POST_VIEWS_SIDEBARPS');
        $values['DBBLOG_POST_LAST_SIDEBARPS'] = Configuration::get('DBBLOG_POST_LAST_SIDEBARPS');

        return $values;
    }

    protected function saveData()
    {
        $form_values = $this->getConfigFormValues();
        $languages = Language::getLanguages(false);
        $id_shop_group = (int)Context::getContext()->shop->id_shop_group;
        $id_shop = (int)Context::getContext()->shop->id;

        foreach ($form_values as $name => $key) {
            if(is_array($key)){
                $values = array();
                foreach ($languages as $lang){
                    $values[$lang['id_lang']] = Tools::getValue($name . '_'.(int)$lang['id_lang']);
                }
                Configuration::updateValue($name, $values, true, $id_shop_group, $id_shop);
            } else {
                Configuration::updateValue($name, Tools::getValue($name), true, $id_shop_group, $id_shop);
            }
        }
    }

}