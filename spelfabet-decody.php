<?php
/**
 * Plugin Name: Spelfabet Cody
 * Plugin URI: http://www.cbdweb.net
 * Description: Assess texts using Phoneme-Grapheme correspondences
 * Version: 0.9
 * Author: Nik Dow, CBDWeb
 * License: GPL2
 */

function spelfabet_cody_enqueue_scripts(){
    wp_enqueue_style('spelfabet_cody', plugins_url('spelfabet-cody/css/spelfabet-cody.css' ) );
}
add_action( 'admin_enqueue_scripts', 'spelfabet_cody_enqueue_scripts' );
/*
 * https://wordpress.stackexchange.com/questions/110562/is-it-possible-to-add-custom-post-type-menu-as-another-custom-post-type-sub-menu
 */
add_action('admin_menu', 'add_to_menu');
function add_to_menu(){
    add_menu_page( 'Spelfabet Cody', 'Spelfabet Cody', 'publish_posts', 'spelfabet_cody', 'spelfabet_cody', 'dashicons-clipboard', 20 );
    add_submenu_page( 'spelfabet_cody', 'Word PGC', 'Word PGC', 'publish_posts', 'edit.php?post_type=word_pgc', null, 20);
    add_submenu_page( 'spelfabet_cody', 'Word Structure', 'Word Structure', 'publish_posts', 'edit.php?post_type=word_structure', null, 30);
    add_submenu_page( 'spelfabet_cody', 'Teaching Level PGC', 'Schema PGC', 'publish_posts', 'edit.php?post_type=schema_pgc', null, 40);
    add_submenu_page( 'spelfabet_cody', 'Teaching Level Structure', 'Schema Structure', 'publish_posts', 'edit.php?post_type=schema_structure', null, 50);
    add_submenu_page( 'spelfabet_cody', 'Teaching Level HFW', 'Schema HFW', 'publish_posts', 'edit.php?post_type=schema_hfw', null, 60);
    add_submenu_page( 'spelfabet_cody', 'Teaching Level Descriptions', 'Schema Descriptions', 'publish_posts', 'edit.php?post_type=schema_levels', null, 70);
}
require_once plugin_dir_path( __FILE__ ) . 'cody-includes.php';
require_once plugin_dir_path( __FILE__ ) . 'uploads.php';
function spelfabet_cody(){
    ?>
    <div class="wrap">
        <h1>Hello Spelfabet Cody</h1>
        <P>Go to the Uploads menu (under Spelfabet Cody) to upload files.</P>
        <P>Each Schema is one teaching method</P>
        <P>When viewing Schema files, if you have uploaded more than one schema, you need to select a "tag" to view just one schema, then click on "filter".</P>
        <P></P>
    </div>
    <?php
}

add_action( 'init', 'create_spelfabet_cody');
function create_spelfabet_cody(){
    require_once plugin_dir_path( __FILE__ ) . 'post-types.php';
}
/*
 * default column ordering
 */
is_admin() && add_action( 'pre_get_posts', 'order_cody');
function order_cody( $query ){
    if( ! $query->is_main_query() ) return;
    switch ($query->get('post_type')){
        case 'schema_pgc':
        case 'schema_structure':
        case 'schema_hfw':
        case 'schema_levels':
            $query->set('orderby', ['ABS(title)','excerpt']);
            $query->set( 'order', 'ASC');
            break;
        case 'word_pgc':
        case 'word_structure':
            $query->set('orderby', 'title'); // adding 'excerpt' here breaks the sort order ???
            $query->set( 'order', 'ASC');
            break;
    }

}
