<?php
add_shortcode('decody_editor', 'decody_editor');

function decody_editor( $atts )
{
    ob_start();
    $tags = get_tags( array(
            'taxonomy' => 'schema',
            'orderby' => 'name'
    ))
    ?>
    <textarea name="editor" placeholder="Enter or paste your text here"></textarea>
    <button type="button" onclick="update()">check it out</button>
    <select name="schema">
        <option value="">Select Schema</option>
        <?php
        foreach( $tags as $tag ){
            ?><option value="<?=$tag->name?>"><?=$tag->name?></option><?php
        }
        ?>
    </select>
    <?php
    return ob_get_clean();
}