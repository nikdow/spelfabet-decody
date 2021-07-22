<?php
/* general */
add_filter( "manage_edit-word_pgc_sortable_columns", 'cody_sort_columns');
function cody_sort_columns( $columns ){
    return [];
}
add_filter('manage_posts_custom_column', 'excerpt_column', 10, 2);
function excerpt_column($column_name, $post_ID){
    if( $column_name == 'excerpt'){
        echo get_the_excerpt( $post_ID );
    }
}
/*
 * label for title field on custom posts
 */
add_filter('enter_title_here', 'spelfabet_cody_enter_title');
function spelfabet_cody_enter_title( $input ) {
    global $post_type;
    switch ( $post_type ){
        case 'word_pgc':
        case 'word_structure':
            return __( 'Enter word' );
            break;
        case 'schema_pgc':
        case 'schema_structure':
            return __( 'Enter Teaching Level (number 1,2,3,...)');
            break;
    }
    return $input;
}
add_filter( 'gettext', 'wpse22764_gettext', 10, 2 );
function wpse22764_gettext( $translation, $original )
{
    if ( 'Excerpt' == $original ) {
        global $post_type;
        switch ($post_type) {
            case 'word_pgc':
            case 'schema_pgc':
                return 'grapheme:exemplar word';
            case 'word_structure':
            case 'schema_structure':
                return 'Syllable Structure';
        }
    }else{
        $pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
        if ($pos !== false) {
            return  '';
        }
    }
    return $translation;
}
/*
 * word-pgc
 */
$labels = array(
    'name' => _x('Word: Phoneme Grapheme Correspondence', 'post type general name'),
    'singular_name' => _x('word-PGC', 'post type singular name'),
    'add_new' => _x('Add New', 'events'),
    'add_new_item' => __('Add New Word'),
    'edit_item' => __('Edit Word'),
    'new_item' => __('New Word'),
    'view_item' => __('View Word'),
    'search_items' => __('Search Words'),
    'not_found' =>  __('No words found'),
    'not_found_in_trash' => __('No words found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'word_pgc',
    array(
        'label'=>__('Words'),
        'labels' => $labels,
        'description' => 'Each post is one Word-PGC.',
        'public' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-word_pgc_columns", "word_pgc_edit_columns" );
function word_pgc_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />', // otherwise, no checkbox in the list of posts
        "title" => "Word",
        "excerpt" => "PGC"
    );
    return $columns;
}
/*
 * word-structure
 */
$labels = array(
    'name' => _x('Word Structure', 'post type general name'),
    'singular_name' => _x('word-structure', 'post type singular name'),
    'add_new' => _x('Add New', 'events'),
    'add_new_item' => __('Add New Word Structure'),
    'edit_item' => __('Edit Word Structure'),
    'new_item' => __('New Word Structure'),
    'view_item' => __('View Word Structure'),
    'search_items' => __('Search Word Structures'),
    'not_found' =>  __('No words found'),
    'not_found_in_trash' => __('No words found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'word_structure',
    array(
        'label'=>__('Word Structures'),
        'labels' => $labels,
        'description' => 'Each post is one Word-structure.',
        'public' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-word_structure_columns", "word_structure_edit_columns" );
function word_structure_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />',
        "title" => "Word",
        "excerpt" => "Syllable Structure"
    );
    return $columns;
}
/*
 * schema-pgc
 */
$labels = array(
    'name' => _x('Schema PGC', 'post type general name'),
    'singular_name' => _x('Schema PGC', 'post type singular name'),
    'add_new' => _x('Add New', 'events'),
    'add_new_item' => __('Add New Schema PGC'),
    'edit_item' => __('Edit Schema PGC'),
    'new_item' => __('New Schema PGC'),
    'view_item' => __('View Schema PGC'),
    'search_items' => __('Search Schema PGC'),
    'not_found' =>  __('No Schema PGCs found'),
    'not_found_in_trash' => __('No Schema PGCs found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'schema_pgc',
    array(
        'label'=>__('Schema PGC'),
        'labels' => $labels,
        'description' => 'Each post is one Schema-PGC.',
        'public' => true,
        'exclude_from_search' => true,
        'taxonomies' => [ 'schema' ],
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
            'can_export' => false,
            'query_var' => false,
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-schema_pgc_columns", "schema_pgc_edit_columns" );
function schema_pgc_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />',
        "title" => "Teaching Level",
        "excerpt" => "Phoneme Grapheme correspondence",
        "schema" => 'Schema'
    );
    return $columns;
}
add_action( 'manage_schema_pgc_posts_custom_column', 'add_column', 10, 2);
/*
 * schema-structure
 */
$labels = array(
    'name' => _x('Schema Structure', 'post type general name'),
    'singular_name' => _x('Schema Structure', 'post type singular name'),
    'add_new' => _x('Add New', 'events'),
    'add_new_item' => __('Add New Schema Structure'),
    'edit_item' => __('Edit Schema Structure'),
    'new_item' => __('New Schema Structure'),
    'view_item' => __('View Schema Structure'),
    'search_items' => __('Search Schema Structure'),
    'not_found' =>  __('No Schema Structures found'),
    'not_found_in_trash' => __('No Schema Structures found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'schema_structure',
    array(
        'label'=>__('Schema Structures'),
        'labels' => $labels,
        'description' => 'Each post is one Schema-Structure.',
        'public' => true,
        'exclude_from_search' => true,
        'taxonomies' => [ 'schema' ],
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
            'can_export' => false,
            'query_var' => false,
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-schema_structure_columns", "schema_structure_edit_columns" );
function schema_structure_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />',
        "title" => "Teaching Level",
        "excerpt" => "Syllable Structure",
        "schema" => 'Schema'
    );
    return $columns;
}
add_action( 'manage_schema_structure_posts_custom_column', 'add_column', 10, 2);
/*
 * schema-hfw
 */
$labels = array(
    'name' => _x('Schema High Frequency Words', 'post type general name'),
    'singular_name' => _x('Schema HFW', 'post type singular name'),
    'add_new' => _x('Add New', 'schema HFW'),
    'add_new_item' => __('Add New Schema HFW'),
    'edit_item' => __('Edit Schema HFW'),
    'new_item' => __('New Schema HFW'),
    'view_item' => __('View Schema HFW'),
    'search_items' => __('Search Schema HFW'),
    'not_found' =>  __('No Schema HFWs found'),
    'not_found_in_trash' => __('No Schema HFWs found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'schema_hfw',
    array(
        'label'=>__('Schema High Frequency Words'),
        'labels' => $labels,
        'description' => 'Each post is one Schema High Frequency Word.',
        'public' => true,
        'exclude_from_search' => true,
        'taxonomies' => [ 'schema' ],
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
            'can_export' => false,
            'query_var' => false,
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-schema_hfw_columns", "schema_hfw_edit_columns" );
function schema_hfw_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />',
        "title" => "Teaching Level",
        "excerpt" => "High Frequency Word",
        "schema" => 'Schema'
    );
    return $columns;
}
add_action( 'manage_schema_hfw_posts_custom_column', 'add_column', 10, 2);
/*
 * schema-levels
 */
$labels = array(
    'name' => _x('Schema Level Descriptions', 'post type general name'),
    'singular_name' => _x('Schema Levels', 'post type singular name'),
    'add_new' => _x('Add New', 'schema HFW'),
    'add_new_item' => __('Add New Schema Level Description'),
    'edit_item' => __('Edit Schema Level Description'),
    'new_item' => __('New Schema Description'),
    'view_item' => __('View Schema Description'),
    'search_items' => __('Search Schema Description'),
    'not_found' =>  __('No Schema Descriptions found'),
    'not_found_in_trash' => __('No Schema Descriptions found in Trash'),
    'parent_item_colon' => '',
);
register_post_type( 'schema_levels',
    array(
        'label'=>__('Schema Level Descriptions'),
        'labels' => $labels,
        'description' => 'Each post is one Schema Descriptions.',
        'public' => true,
        'exclude_from_search' => true,
        'taxonomies' => [ 'schema' ],
        'has_archive' => false,
        'show_ui' => true,
        'capabilities' =>array(
            'edit_post'=>'publish_posts',
            'edit_posts'=>'publish_posts',
            'edit_others_posts'=>'publish_posts',
            'publish_posts'=>'publish_posts',
            'delete_post' =>'publish_posts',
            'delete_published_posts' => 'publish_posts',
            'delete_posts' => 'publish_posts',
            'can_export' => false,
            'query_var' => false,
        ),
        'menu_icon' => "dashicons-controls-repeat",
        'hierarchical' => false,
        'rewrite' => false,
        'supports'=> array('title', 'excerpt' ),
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'query_var' => false,
    )
);
add_filter ( "manage_edit-schema_levels_columns", "schema_levels_edit_columns" );
function schema_levels_edit_columns($columns) {
    $columns = array(
        "cb" => '<input type="checkbox" />',
        "title" => "Teaching Level",
        "excerpt" => "Description",
        "schema" => 'Schema'
    );
    return $columns;
}
add_action( 'manage_schema_levels_posts_custom_column', 'add_column', 10, 2);
/*
 * used in all schema post types:
 */
function add_column( $column, $post_id ){
    if( $column === "schema" ){
        $terms = get_the_terms( $post_id, 'schema' );
        if( ! empty( $terms ) ){
            echo( $terms[0]->name );
        }
    }
}