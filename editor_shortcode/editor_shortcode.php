<?php

add_action('wp-enqueue_scripts', 'register_css');
add_shortcode('decody_editor', 'decody_editor');

function decody_editor( $atts )
{
    $tags = get_tags( array(
            'taxonomy' => 'schema',
            'orderby' => 'name'
    ));
    wp_enqueue_style('editor_shortcode');
    ob_start();
    ?>
        <div id="decody_editor">
            <textarea name="editor" placeholder="Enter or paste your text here"></textarea><br/>
            <select name="schema">
                <option value="">Select Schema</option>
                <?php
                foreach( $tags as $tag ){
                    ?><option value="<?=$tag->name?>"><?=$tag->name?></option><?php
                }
                ?>
            </select><br/>
            <button type="button" onclick="update()">check it out</button><br/>
        </div>
    <div>
        [plugins_url = <?=plugins_url('editor_shortcode.css', __FILE__)?>]
    </div>
    <?php
    return ob_get_clean();


}
function register_css(){
    wp_register_style('editor_shortcode', plugins_url('editor_shortcode.css', __FILE__), array(), '1.0.0', 'screen' );
}