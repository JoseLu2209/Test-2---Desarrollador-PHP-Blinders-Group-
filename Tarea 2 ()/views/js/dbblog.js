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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

/*$(document).on("click", ".datatext", function(e) {
    window.location.href = b64_to_utf8($(this).attr("datatext"));
});*/

function b64_to_utf8(str) {
    return decodeURIComponent(escape(window.atob(str)));
}

$( document ).ready(function() {

    // Activar boton comentarios
    $(document).on('click', '.politica_privacidad', function(){
        var nombre = $('input[name=nombre]', $(this).parents("form")).length;
        if( $('#politica_privacidad').prop('checked')) {
            $(".send_comment", $(this).parents("form")).removeAttr("disabled");
        } else {
            $(".send_comment", $(this).parents("form")).attr( "disabled", "disabled" );
        }
    });

    // Enviar Comentario
    $(document).on('click', '.send_comment', function(){
        var id_post = $('input[name=id_post]', $(this).parents("form")).val();
        var id_comment = $('input[name=id_comment]', $(this).parents("form")).val();
        var nombre = $('input[name=nombre]', $(this).parents("form")).val();
        var comentario = $('textarea[name=comentario]', $(this).parents("form")).val();
        var rating = $('input[name=rating]:checked', $(this).parents("form")).val();
        // var url_comment = $('.form_comment_post').attr('action');

        if(nombre.length == 0){
            alert('Debes rellenar el nombre');
            return;
        }

        if(comentario.length == 0){
            alert('Debes rellenar el comentario');
            return;
        }

        var checked = $("input[name=politica_privacidad]:checked", $(this).parents("form")).length;
        if(checked == 0){
            alert('Debe de aceptar la politica de privacidad para comentar');
            return;
        }

        requestData = {
            id_post: id_post,
            id_comment: id_comment,
            nombre: nombre,
            comentario: comentario,
            rating: rating,
            action: 'send_comment',
        };

        $.ajax({
            url: dbblog_ajax,
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if(response.result == 'OK'){
                    modal = '<div id="comment_created" class="modal fade" role="dialog" style="padding-right: 15px; display: block;" aria-modal="true"> <div class="modal-dialog"><div class="modal-content"> <div class="modal-header"> <h5 class="modal-title" style="width: 100%;">Comentario creado<button type="button" class="close pull-right" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">×</span></button> </h5> </div> <div class="modal-body">Se ha enviado el comentarios para su revisión, cuando lo apruebe el administrador será mostrado.</div> </div> </div></div>';    
                    $('input[name=nombre]').val('');
                    $('textarea[name=comentario]').val('');
                } else {
                    modal = '<div id="comment_created" class="modal fade" role="dialog" style="padding-right: 15px; display: block;" aria-modal="true"> <div class="modal-dialog"><div class="modal-content"> <div class="modal-header"> <h5 class="modal-title" style="width: 100%;">Error crear comentario<button type="button" class="close pull-right" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">×</span></button> </h5> </div> <div class="modal-body">No se ha podido crear el comentario, intenteló más tarde</div> </div> </div></div>';
                }

                $('body').append(modal);
                $('#comment_created').modal('show');
            }
        });
    });

    // Scroll infinito
    $(document).on('click', '.btn_db_inifinitescroll', function(){
        var page = document.getElementById('btn_db_inifinitescroll').dataset.pag;
        var id_category = $('.btn_db_inifinitescroll').data('category');

        requestData = {
            page: page,
            id_category: id_category,
            action: 'infinite_scroll',
        };

        $.ajax({
            url: dbblog_ajax,
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if(response.result == 'OK'){
                    // Calculamos la pagina siguiente
                    page_plus = parseInt(page) + 1;
                    $('.btn_db_inifinitescroll').attr('data-pag', page_plus); 

                    // Sacamos los post vistos hasta el momento
                    sum_infinite_from = document.getElementById('sum_infinite_from').dataset.sumin;
                    sum_infinite = parseInt(sum_infinite_from) + parseInt(response.sum);
                    $('.sum_infinite_from').html(sum_infinite);
                    $('.sum_infinite_from').attr('data-sumin', sum_infinite); 

                    // Calculamos el porcentaje de progreso
                    total_posts = $('.total_posts').data('total');
                    porcent = (sum_infinite * 100 / total_posts).toFixed(2);
                    $('.dbblog_infinitescroll .progress-bar').css('width', porcent+'%');

                    // Pintamos los posts llamados por ajax
                    $('.dbblog_list').append(response.list_post);

                    // Oculatmos el boton si ya no hay mas posts
                    if(total_posts <= sum_infinite){
                        $('#btn_db_inifinitescroll').hide();
                    }
                }
            }
        });
    });

    // Scroll infinito Author
    $(document).on('click', '.btn_db_inifinitescroll_author', function(){
        var page = document.getElementById('btn_db_inifinitescroll_author').dataset.pag;
        var id_author = $('.btn_db_inifinitescroll_author').data('author');

        requestData = {
            page: page,
            id_author: id_author,
            action: 'infinite_scroll_author',
        };

        $.ajax({
            url: dbblog_ajax,
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if(response.result == 'OK'){
                    // Calculamos la pagina siguiente
                    page_plus = parseInt(page) + 1;
                    $('.btn_db_inifinitescroll_author').attr('data-pag', page_plus); 

                    // Sacamos los post vistos hasta el momento
                    sum_infinite_from = document.getElementById('sum_infinite_from').dataset.sumin;
                    sum_infinite = parseInt(sum_infinite_from) + parseInt(response.sum);
                    $('.sum_infinite_from').html(sum_infinite);
                    $('.sum_infinite_from').attr('data-sumin', sum_infinite); 

                    // Calculamos el porcentaje de progreso
                    total_posts = $('.total_posts').data('total');
                    porcent = (sum_infinite * 100 / total_posts).toFixed(2);
                    $('.progress-bar').css('width', porcent+'%');

                    // Pintamos los posts llamados por ajax
                    $('.post__grid').append(response.list_post);

                    // Oculatmos el boton si ya no hay mas posts
                    if(total_posts <= sum_infinite){
                        $('#btn_db_inifinitescroll_author').hide();
                    }
                }
            }
        });
    });

    // Formulario respuesta
    $(document).on('click', '.btn_form_respond', function(){

        var id_comment = $(this).data('id-comment');
        var id_post = $(this).data('id-post');

        if ($('.form_respond_' + id_comment + ':has(*)').length) {
            $('.form_respond_' + id_comment).empty();
            return;
        }

        requestData = {
            id_comment: id_comment,
            id_post: id_post,
            action: 'form_respond',
        };

        $.ajax({
            url: dbblog_ajax,
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if(response.result == 'OK'){
                    $('.form_respond_' + id_comment).append(response.form);
                }
            }
        });
    });

    // Mostrar menu mobile
    $(document).on('click', '.dbblog_menu_mobile', function(){
        $('.menu .list_category').css('opacity', '1');
        $('.menu .list_category').css('visibility', 'visible');
        $('.menu .menub__overlay').css('opacity', '1');
        $('.menu .menub__overlay').css('visibility', 'visible');
        $('body').css('overflow', 'hidden');
    });
    // Ocultar menu mobile
    $(document).on('click', '.close_menub', function(){
        $('.menu .list_category').css('opacity', '0');
        $('.menu .list_category').css('visibility', 'hidden');
        $('.menu .menub__overlay').css('opacity', '0');
        $('.menu .menub__overlay').css('visibility', 'hidden');
        $('body').css('overflow', 'auto');
    });

});

// Menu nuevo
$(document).ready(function() {
    var BlogdondeEstoy = ["BlogMenuPrincipal"];
    var BlogmenuPrincipalTitulo = "#dbblog_menu .modal-body .menu_header";
    var BlogmenuPrincipalCuerpo = "#dbblog_menu .modal-body .dbblog_primary";
    var BlogcategoriasSelTxt = "#dbblog_menu .modal-body .";
    var BlogcategoriasBack = "#dbblog_menu .modal-body .dbblog_back";

    /* Sobre las categorias padres del menú */
    var BlogMenuPrincipal = function(accion){
        if (accion == "mostrar"){
            /* Mostramos el menú Principal */
            $(BlogmenuPrincipalTitulo).show('400');
            $(BlogmenuPrincipalCuerpo).show('400');
        } else if (accion =="ocultar") {
            $(BlogmenuPrincipalTitulo).hide('400');
            $(BlogmenuPrincipalCuerpo).hide('400');
        }
    }

    var BlogMenuHijoOcultar = function(selector) {
        $(selector).css("height", "0px");
        $(selector).css("overflow-y", "hidden");
    };

    var BlogMenuHijoMostrar = function(selector) {
        $(selector).css("height", "100vh");
        $(selector).css("overflow-y", "scroll");
    };

    var BlogMenusHijos = function(accion, esteObjeto){
        claseOcultar = BlogdondeEstoy[BlogdondeEstoy.length - 1];
        if (accion == "irAtras") {
            // Mostramos el padre o el menu principal si procede
            selectorOcultar = BlogcategoriasSelTxt + claseOcultar;
            BlogMenuHijoOcultar(selectorOcultar);
            if ( BlogdondeEstoy.length == 2){
                // Si el padre es el menu principal
                BlogMenuPrincipal("mostrar");
            } else {
                // el padre es otro hijo, por lo que lo abrimos
                claseMostrar = BlogdondeEstoy[BlogdondeEstoy.length - 2];
                selectorMostrar = BlogcategoriasSelTxt + claseMostrar;
                BlogMenuHijoMostrar(selectorMostrar);
            }
            BlogdondeEstoy.pop();
        } else if (accion == "irAHijo") {
            // Mostramos el menu hijo de este
            var BlogcategoriaMostrar = $(esteObjeto).attr('data-subitem');
            selectorMostrar = BlogcategoriasSelTxt + BlogcategoriaMostrar;
            selectorOcultar = BlogcategoriasSelTxt + claseOcultar;

            if ( BlogdondeEstoy.length == 1){
                BlogMenuPrincipal("ocultar");
            } else {
                BlogMenuHijoOcultar(selectorOcultar);
            }
            BlogMenuHijoMostrar(selectorMostrar);
            BlogdondeEstoy.push(BlogcategoriaMostrar);
        }
    };

    $("#dbblog_menu .modal-body .open_subitems").click(function() {
        /* Vamos a una categoría hija */
        BlogMenusHijos("irAHijo", this);
        return false;
    });
    $(BlogcategoriasBack).click(function(){
        /* Vamos a la Categoría Padre */
        BlogMenusHijos("irAtras", this);
        return false;
    });
});