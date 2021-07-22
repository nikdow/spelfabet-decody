<?php
/*
 * taxonomy for teaching methods
 * each method (schema) corresponds to one term in this taxonomy
 */
add_action( 'init', 'schema_tax');
function schema_tax(){
    register_taxonomy('schema', ['schema_pgc', 'schema_structure', 'schema_hfw','schema_levels'], [
        'description' => 'each term defines one teaching method (schema)',
        'public' => true,
        'show_in_menu' => false,
        'show_in_nav_menus' => true, // in case this allows selection of this taxononmy in post_list -> required
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false,
        'hierarchical' => false,
        'capabilities' => ['manage_terms' => 'publish_posts', 'edit_terms' => 'publish_posts', 'delete_terms' => 'publish_posts', 'assign_terms' => 'publish_posts'],
    ]);
    register_taxonomy_for_object_type( 'schema', ['schema_pgc', 'schema_structure', 'schema_hfw', 'schema_levels']);
// https://wordpress.stackexchange.com/questions/346257/how-to-show-the-category-filter-thats-shown-on-the-all-post-pages-on-a-custom
// $which (the position of the filters form) is either 'top' or 'bottom'
    add_action('restrict_manage_posts', function ($post_type, $which) {
        if ('top' === $which && in_array($post_type, ['schema_pgc', 'schema_structure', 'schema_hfw', 'schema_levels'])) {
            $taxonomy = 'schema';
            $tax = get_taxonomy($taxonomy);            // get the taxonomy object/data
            $cat = filter_input(INPUT_GET, $taxonomy); // get the selected category slug

            echo '<label class="screen-reader-text" for="schema">Filter by ' .
                esc_html($tax->labels->singular_name) . '</label>';

            wp_dropdown_categories([
                'show_option_all' => $tax->labels->all_items,
                'hide_empty' => 0, // include categories that have no posts
                'hierarchical' => $tax->hierarchical,
                'show_count' => 0, // don't show the category's posts count
                'orderby' => 'name',
                'selected' => $cat,
                'taxonomy' => $taxonomy,
                'name' => $taxonomy,
                'value_field' => 'slug',
            ]);
        }
    }, 10, 2);
}