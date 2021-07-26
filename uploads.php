<?php
/*
 * Spelfabet Decody uploads page
 */
add_action( 'admin_menu', 'spelfabet_decody_menu');

// https://developer.wordpress.org/reference/functions/_wp_handle_upload/

function spelfabet_decody_menu(){
    add_submenu_page( 'spelfabet_decody', "Spelfabet Decody Uploads", 'Uploads', 'publish_posts', 'spelfabet_decody_uploads', 'spelfabet_decody_uploads', 55  );
}

function spelfabet_decody_uploads(){

    ?>
    <div class="spelfabet_decody_uploads">
        <h1>Spelfabet Decody Uploads page</h1>
        <form name="upload_CSV" enctype="multipart/form-data" method="post" action="/wp-admin/admin-post.php">
            <input name="action" type="hidden" value="decody_upload"/>
            <label for="upload_CSV">Upload Data File</label>
            <input id="upload_CSV" type="file" name="upload_CSV" />
            <br/>
            Only the first two columns of the spreadsheet are used.<br/>
            The first row is ignored (you can put column headings there, but we don't look at them).
            <br/>
            <ul class="vertical">
                <li><input type="radio" name="csv_type" value="word_pgc">Word PGC</li>
                <li><input type="radio" name="csv_type" value="word_structure">Word Structure</li>
                <li><label for="schema">Schema title</label> <input name="schema"/> <strong>Important</strong>: be consistent with this field as you enter all the CSV files below.</li>
                <li><input type="radio" name="csv_type" value="schema_pgc">Schema PGC</li>
                <li><input type="radio" name="csv_type" value="schema_structure">Schema Structure</li>
                <li><input type="radio" name="csv_type" value="schema_hfw">Schema HFW</li>
                <li><input type="radio" name="csv_type" value="schema_levels">Schema Levels</li>
            </ul>
            <P><input type="checkbox" name="delete"> Delete entries based on uploaded file, i.e. "undo"</P>
            <?php submit_button( "upload CSV file")?>
        </form>
        <table class="bordered">
            <tr>
                <th>Data type</th>
                <th>Columns</th>
            </tr>
            <tr>
                <td>Word PGC</td>
                <td>word, PGC as "t:tap"</td>
            </tr>
            <tr>
                <td>Word Structure</td>
                <td>word, structure as "VCC"</td>
            </tr>
            <tr>
                <td style="border:0;" colspan="2">Schema files:</td>
            </tr>
            <tr>
                <td>Schema PGC</td>
                <td>level as integer, PGC as "t:tap" </td>
            </tr>
            <tr>
                <td>Schema level</td>
                <td>level as integer, structure as "VCC"</td>
            </tr>
            <tr>
                <td>Schema High Frequency Words</td>
                <td>level as integer, HFW</td>
            </tr>
            <tr>
                <td>Schema Levels</td>
                <td>level as integer, description of level</td>
            </tr>
        </table>
    </div>
    <?php
}
/*
 *  file upload stuff
 */
add_filter( 'mime_types', 'wpse_mime_types' );
function wpse_mime_types( $existing_mimes ) {
    // Add csv to the list of allowed mime types
    $existing_mimes['csv'] = 'text/csv';
    return $existing_mimes;
}
require_once( ABSPATH . 'wp-admin/includes/file.php');
require_once( ABSPATH . 'wp-includes/capabilities.php');
require_once( ABSPATH . 'wp-includes/pluggable.php');
require_once( ABSPATH . 'wp-admin/includes/taxonomy.php');
require_once plugin_dir_path( __FILE__ ) . 'decody_includes.php';
add_action( 'admin_post_decody_upload', 'handle_upload' );
function handle_upload()
{
    $error = new WP_Error();
    if( empty($_FILES['upload_CSV']['name']) ) $error->add('nofile', 'No file uploaded');
    if( ! current_user_can('publish_posts') ) $error->add( 'privilege', 'Role publish_posts is required to upload a Decody spreadsheet');
    if( ! $error->has_errors() ) {
        $upload = wp_handle_upload($_FILES['upload_CSV'], ['test_form' => false]);
        if (isset($upload['error'])) {
            $error->add('bad upload', 'Error uploading file');
        }
    }
    if( ! $error->has_errors() ) {
        $post_type = $_POST['csv_type'];
        /*
         * schema if relevant
         */
        $schema = $_POST['schema'];
        if (!$schema && 0 === stripos($post_type, 'schema')) {
            $error->add('noschema', "Must specify schema for this type of upload");
        }
    }
    if( $error->has_errors() ){ ?>
        <h2>Errors:</h2>
        <ul class="vertical">
            <li>
                <?=implode('</li><li>', $error->get_error_messages())?>
            </li>
        </ul>
        <P>
            <a href="/wp-admin/admin.php?page=spelfabet_decody_uploads">Click here to try again</a>
        </P>
        <?php
        return;
    }
    $delete = array_key_exists( 'delete', $_POST ) && !! $_POST['delete'];
    /*
     * read the file
     */
    $handle = fopen($upload['file'], 'r');
    $headings = fgetcsv($handle); // discarded
    $lines_read = 0;
    while ($fields = fgetcsv($handle)) {
        if( Count($fields) < 2) continue;
        if( trim($fields[0]) === "" ) continue;
        if( trim($fields[1]) === "" ) continue;
        switch ($post_type) {
            case "word_pgc":
            case "word_structure":
                $word = $fields[0];
                $payload = $fields[1];
                switch ( $post_type ){
                    case "word_pgc":
                        $posts = get_posts(['numberposts' => 10, 'post_type' => $post_type, 'exact' => true, 'title' => $word]);
                        $post_filtered = array_filter($posts, function ($post) use ($payload) {
                            return strtolower($post->post_excerpt) === strtolower($payload);
                        });
                        if( $delete && count($post_filtered) > 0 ){
                            $lines_read++;
                            foreach( $post_filtered as $post ){
                                wp_delete_post( $post->ID, true );
                            }
                            continue 2;
                        } else {
                            if (count($post_filtered) === 1) { // exists, so ignore
                                continue 2;
                            }
                        }
                        break;
                    case "word_structure":
                        $posts = get_posts(['numberposts' => 1, 'post_type' => $post_type, 'exact' => true, 'title' => $word]);
                        if (count($posts) === 1) { // existing post, update
                            $lines_read++;
                            if( $delete ){
                                wp_delete_post( $posts[0]->ID, true );
                                continue 2;
                            }
                            $post = $posts[0];
                            $post->post_excerpt = $payload;
                            wp_update_post($post);
                            continue 2;
                        }
                        break;
                }
                $post = ['post_title' => $word, 'post_excerpt' => $payload, 'post_type' => $post_type, 'post_status' => 'publish'];
                wp_insert_post($post, false, false);
                $lines_read++;
                break;
            case "schema_pgc":
            case "schema_structure":
            case "schema_hfw":
            case "schema_levels":
                $level = $fields[0];
                $payload = $fields[1];
                if( $post_type === 'schema_pgc'){ // canonicalize
                    preg_match('/([a-z]+):\s?([a-z]+)/', trim($payload), $matches);
                    if( count( $matches ) < 3 ) {
                        $payload = "";
                    } else {
	                    $payload = $matches[1] . ":" . $matches[2];
                    }
                }
                $posts = get_posts(['numberposts' => 100, 'post_type' => $post_type, 'exact' => true, 'title' => $level]);
                $post_filtered = array_filter($posts, function ($post) use ($payload, $schema) {
                    $term = get_the_terms( $post, 'schema');
                    return strtolower($post->post_excerpt) === strtolower($payload) && $schema === $term[0]->name ;
                });
                if (count($post_filtered) === 1) {
                    if( $delete ) {
                        $lines_read++;
                        foreach ( $post_filtered as $post ) { // index although numeric may not start at 0
                            wp_delete_post($post->ID, true);
                        }
                    }
                    continue;
                }
                if( ! $delete ) {
                    $post = ['post_title' => trim($level), 'post_excerpt' => trim($payload), 'post_type' => $post_type, 'post_status' => 'publish', 'tax_input' => ['schema' => $schema]];
                    wp_insert_post($post, false, false);
                    $lines_read++;
                }
                break;
        }
    }
    ?>
    <h2>Successfully <?=$delete ? 'deleted' : 'uploaded'?> <?=$lines_read?> rows</h2>
    <a href="/wp-admin/edit.php?post_type=<?=$post_type . ($schema ? "&schema=" . str_replace(" ", "-", $schema) : "")?>&filter_action=Filter&paged=1">View your upload</a>
    <?php
}