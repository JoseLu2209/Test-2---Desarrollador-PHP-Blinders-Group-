<form method="post" class="form_comment_post" action="">
    {if isset($id_post)}
        <input type="hidden" name="id_post" value="{$id_post}">
    {else}
        <input type="hidden" name="id_post" value="{$post.id}">
    {/if}
    {if isset($id_comment)}
        <input type="hidden" name="id_comment" value="{$id_comment}">
    {else}
        <input type="hidden" name="id_comment" value="0">
    {/if}
    <label>{l s='Nombre *' mod='dbblog'}</label>
    {if $customer_login}
        <input type="hidden" name="nombre" value="{$customer_name}">
        <span>{l s='Logueado como'} {$customer_name}</span>
    {else}
        <input type="text" name="nombre" class="input" placeholder="Tú nombre" required>
    {/if}
    <label>{l s='Comentario *' mod='dbblog'}</label>
    <textarea name="comentario" rows="8"required></textarea>

    {*{if isset($id_comment) && $id_comment <= 0}
        <div class="rating">
            <label class="label">{l s='Tu valoración:' mod='dbblog'}</label>
            <span class="texto" id="texto-valoracion">{l s='¿Qué te ha parecido?' mod='dbblog'}</span>
            <div class="selector--starts">
                <input type="radio" name="rating" id="valoracion-1" class="valoracion" value="1"><label for="valoracion-1"></label>
                <input type="radio" name="rating" id="valoracion-2" class="valoracion" value="2"><label for="valoracion-2"></label>
                <input type="radio" name="rating" id="valoracion-3" class="valoracion" value="3"><label for="valoracion-3"></label>
                <input type="radio" name="rating" id="valoracion-4" class="valoracion" value="4"><label for="valoracion-4"></label>
                <input type="radio" name="rating" id="valoracion-5" class="valoracion" value="5" checked="checked"><label for="valoracion-5"></label>
            </div>
        </div>
    {else}
        <input type="hidden" name="rating" value="0">
    {/if}*}

    {if $recaptcha_enable == 1 && !empty($recaptcha) && !empty($recaptcha_private)}
        <div class="g-recaptcha" data-sitekey="{$recaptcha}"></div><br />
    {/if}

    <button type="button" class="btn btn-secondary send_comment" disabled>{l s='Enviar comentario' mod='dbblog'}</button>

    <span class="custom-checkbox">
        <label class="politica_privacidad_message">
            <input id="politica_privacidad" class="politica_privacidad" name="politica_privacidad" type="checkbox" value="1">
            <span>
                <svg class="checkbox-checked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
            </span>
            <span>{l s='He leído y acepto la' mod='dbblog'} <a href="{$link_privacity}" target="_blank">{l s='política de privacidad' mod='dbblog'}</a></span>
        </label>
    </span>


    <div class="legal">
        {$rgpd_text nofilter}
    </div>
</form>