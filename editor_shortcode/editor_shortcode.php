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
        <div id="decody_editor">
            <textarea id="editor" placeholder="Enter or paste your text here"></textarea><br/>
            <select id="schema">
                <option value="">Select Schema</option>
                <?php
                foreach( $tags as $tag ){
                    ?><option value="<?=$tag->term_id?>"><?=$tag->name?></option><?php
                }
                ?>
            </select>
            <input id="target_level" type="number" placeholder="Target level"><span>Leave blank for no target</span>
            <button type="button" onclick="update()">check it out</button><br/>
        </div>
        <h3>Results:</h3>
        <div id="decody_results">
            <div id="decody_output"></div>
            <div>
                <ul>
                    <li><span class="no-level">larger words</span> could not be found in this schema</li>
                    <li><span class="warn">red words</span> exceed the level chosen</li>
                </ul>
            </div>
        </div>
    <?php
    return ob_get_clean();
}
function register_editor_shortcode_css(){
    wp_register_style('editor_shortcode', plugins_url('editor_shortcode.css', __FILE__), array(), '1.0.12');
}
function register_editor_shortcode_js(){
    wp_register_script('editor_shortcode', plugins_url( 'editor_shortcode.js', __FILE__), array('jquery'), '0.9.11');
    wp_localize_script('editor_shortcode', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
}
add_action( 'wp_ajax_parse_text', 'editor_parse_text');
add_action( 'wp_ajax_nopriv_parse_text', 'editor_parse_text');
function editor_parse_text(){
	global $wpdb;
    $schema = $_POST['schema'];
    $sql = $wpdb->prepare( "SELECT `name` FROM $wpdb->terms WHERE term_id=%d", $schema);
    $term_name = $wpdb->get_var( $sql );
    $sql = $wpdb->prepare( "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=%d", $schema );
    $term_taxonomy = $wpdb->get_row( $sql );
    $term_taxonomy_id = $term_taxonomy->term_taxonomy_id;
    $taxonomy_name = $term_taxonomy->taxonomy;
    $text = $_POST['text'];
    $words = explode(" ", $text);
    $output = [];
    foreach( $words as $word ){
        $sql = $wpdb->prepare( "SELECT post_excerpt, post_type FROM wp_posts p " .
            "WHERE `post_title`=%s " .
            "AND `post_type` IN ('word_pgc', 'word_structure')",
            $word);
        $properties = $wpdb->get_results( $sql );
        $pgcs = [];
        $structure = false;
        foreach( $properties as $property ){
            switch( $property->post_type ) {
                case 'word_pgc':
                    $pgcs[] = $property->post_excerpt;
                    break;
                case 'word_structure':
                    $structure = $property->post_excerpt;
                    break;
            }
        }
        // hfw
        $sql = $wpdb->prepare( "SELECT post_title FROM wp_posts p " .
            "LEFT JOIN wp_term_relationships r ON r.object_id=p.`ID` AND r.`term_taxonomy_id`=%d " .
            "WHERE post_type='schema_hfw' AND post_excerpt=%s AND r.object_id IS NOT NULL;",
            $term_taxonomy_id, $word);
        $hfw_level = (int) $wpdb->get_var( $sql );
        $pgc_level = false;
        $structure_level = false;
        if( count($pgcs)) {
	        $sql        = "SELECT post_title FROM wp_posts p " .
	                      "LEFT JOIN wp_term_relationships r ON r.object_id=p.`ID` AND r.`term_taxonomy_id`=%d " .
	                      "WHERE post_type='schema_pgc' " .
	                      "AND post_excerpt IN (" . implode( ",", array_fill( 0, count( $pgcs ), '%s' ) ) . ") " .
	                      "AND r.object_id IS NOT NULL;";
	        $query      = $wpdb->prepare( $sql, $term_taxonomy_id, ...$pgcs );
	        $pgc_level  = 0;
	        $pgc_levels = $wpdb->get_results( $query );
	        foreach ( $pgc_levels as $p ) {
		        $pgc_level = max( $pgc_level, (int) $p->post_title );
	        }
        }
        if( $structure ) {
	        $sql             = $wpdb->prepare( "SELECT post_title FROM wp_posts p " .
	                                           "LEFT JOIN wp_term_relationships r ON r.object_id=p.`ID` AND r.`term_taxonomy_id`=%d " .
	                                           "WHERE post_type='schema_structure' AND post_excerpt=%s " .
	                                           "AND r.object_id IS NOT NULL;",
		        $term_taxonomy_id, $structure );
	        $structure_level = (int) $wpdb->get_var( $sql );
	        $level           = max( $pgc_level, $structure_level );
        }
        if( ! ( $pgc_level && $structure_level )) $level = false;
        $output[] = array( 'level' => ($hfw_level ? $hfw_level : $level ), 'isHFW' =>boolval( $hfw_level), 'word' => $word, 'structure_level' => $structure_level, 'pgc_level' => $pgc_level );
    }
    $response = array( 'output' => $output, 'hardest' => 'antidisciplinarianestablishmentism', 'hard_level'=>65);
    wp_send_json( $response );
    wp_die();
}