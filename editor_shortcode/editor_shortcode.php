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
                <textarea name="editor" placeholder="Enter or paste your text here"></textarea><br/>
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
        <h3>Results:</h3>
        <div id="results"></div>
    <?php
    return ob_get_clean();
}
function register_editor_shortcode_css(){
    wp_register_style('editor_shortcode', plugins_url('editor_shortcode.css', __FILE__), array(), '1.0.12');
}
function register_editor_shortcode_js(){
    wp_register_script('editor_shortcode', plugins_url( 'editor_shortcode.js', __FILE__), array('jquery'), '0.9.10');
    wp_localize_script('editor_shortcode', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
}
add_action( 'wp_ajax_parse_text', 'editor_parse_text');
add_action( 'wp_ajax_nopriv_parse_text', 'editor_parse_text');
function editor_parse_text(){
    global $wpdb;
    $text = $_POST['text'];
    $words = explode(" ", $text);
    $output = [];
    $parity = 0;
    foreach( $words as $word ){
        $isOdd = $parity % 2 === 1;
        $output[] = array( 'warn' => $isOdd, 'word' => $word );
        $parity = 1 - $parity;
    }
    $response = array( 'output' => $output, 'hardest' => 'antidisciplinarianestablishmentism', 'hard_level'=>65);
    wp_send_json( $response );
    wp_die();
}