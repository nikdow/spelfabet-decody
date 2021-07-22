<?php
add_shortcode('decody_editor', 'decody_editor');

function decody_editor( $atts )
{
    ob_start();
    ?>
    <h2>Spelfabet Decody edit area</h2>
    <?php
    return ob_get_clean();
}