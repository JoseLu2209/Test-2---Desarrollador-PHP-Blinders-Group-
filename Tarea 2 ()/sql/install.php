<?php
/**
 * 2007-2021 PrestaShop
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
 * @author    DevBlinders <soporte@devblinders.com>
 * @copyright Copyright (c) DevBlinders
 * @license   Commercial license
 */
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_category` (
            `id_dbblog_category` int(11) NOT NULL AUTO_INCREMENT,
            `id_parent` int(10) NOT NULL DEFAULT \'0\',
            `position` int(10) NOT NULL DEFAULT \'0\',
            `index` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
            `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_dbblog_category`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_category_lang` (
            `id_dbblog_category` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `title` varchar(128) NOT NULL,
            `short_desc` varchar(4000) NOT NULL,
            `large_desc` text NOT NULL,
            `link_rewrite` varchar(128) NOT NULL,
            `meta_title` varchar(128) NOT NULL,
            `meta_description` varchar(255) NOT NULL,
            PRIMARY KEY (`id_dbblog_category`, `id_lang`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_category_post` (
            `id_dbblog_category` int(11) NOT NULL,
            `id_dbblog_post` int(11) NOT NULL,
            PRIMARY KEY (`id_dbblog_category`, `id_dbblog_post`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_post` (
            `id_dbblog_post` int(11) NOT NULL AUTO_INCREMENT,
            `id_dbblog_category` int(11) NOT NULL,
            `type` int(11) NOT NULL DEFAULT \'1\',
            `author` int(11) NOT NULL,
            `featured` tinyint(1) NOT NULL DEFAULT \'0\',
            `index` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
            `views` int(11) unsigned NOT NULL DEFAULT \'0\',
            `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `publish_date` datetime NOT NULL,  -- Nuevo campo para la fecha de publicación
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_dbblog_post`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_post_lang` (
            `id_dbblog_post` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `title` varchar(128) NOT NULL,
            `short_desc` varchar(4000) NOT NULL,
            `large_desc` text NOT NULL,
            `image` varchar(255) NOT NULL,
            `link_rewrite` varchar(128) NOT NULL,
            `meta_title` varchar(128) NOT NULL,
            `meta_description` varchar(255) NOT NULL,
            PRIMARY KEY (`id_dbblog_post`, `id_lang`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dbblog_comment` (
            `id_dbblog_comment` int(11) NOT NULL AUTO_INCREMENT,
            `id_comment_parent` int(11) unsigned NOT NULL DEFAULT \'0\',
            `id_post` int(11) NOT NULL,
            `name` varchar(128) NOT NULL,
            `comment` text NOT NULL,
            `rating` int(1) NOT NULL,
            `approved` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `moderator` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_dbblog_comment`, `id_post`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
