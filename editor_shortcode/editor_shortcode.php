<?php

add_action('wp_enqueue_scripts', 'register_editor_shortcode_css');
add_action('wp_enqueue_scripts', 'register_editor_shortcode_js');
add_shortcode('decody_editor', 'decody_editor');

function decody_editor( $atts )
{
    $tags = get_tags( array(
            'taxonomy' => 'schema',
            'orderby' => 'name'
    ));
    wp_enqueue_style('editor_shortcode');
    wp_enqueue_script('editor_shortcode');
    ob_start();
    ?>
        <form name="decody_editor">
            <div id="decody_editor">
                <div contenteditable="true" name="editor" placeholder="Enter or paste your text here"></div><br/>
                <select name="schema">
                    <option value="">Select Schema</option>
                    <?php
                    foreach( $tags as $tag ){
                        ?><option value="<?=$tag->name?>"><?=$tag->name?></option><?php
                    }
                    ?>
                </select>
                <button type="button" onclick="update()">check it out</button><br/>
            </div>
        </form>
    <?php
    return ob_get_clean();
}
function register_editor_shortcode_css(){
    wp_register_style('editor_shortcode', plugins_url('editor_shortcode.css', __FILE__), array(), '1.0.3');
}
function register_editor_shortcode_js(){
    wp_register_script('editor_shortcode', plugins_url( 'editor_shortcode.js', __FILE__), array(), '0.9.2');
}