<?php



function fallen_crown_admin_page() {
    add_menu_page(
        'Fallen Crown', // Page Title
        'FC', // Menu Title
        'manage_options', // Capability
        'fallen-crown', // Menu Slug
        'fc_admin_page', // Callback Function
        'dashicons-image-filter', // Icon URL
        101 // Position
    );
}
add_action( 'admin_menu', 'fallen_crown_admin_page' );



function fc_admin_page() {
    ob_start();
    ?>
<h1>Fallen Crown Assets</h1>
    <?php
    $output = ob_get_clean();
    echo $output;
}
