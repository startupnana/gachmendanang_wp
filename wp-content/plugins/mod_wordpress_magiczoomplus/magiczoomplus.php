<?php
/*

Copyright 2008 MagicToolbox (email : support@magictoolbox.com)
Plugin Name: Magic Zoom Plus
Plugin URI: http://www.magictoolbox.com/magiczoomplus/
Description: Make your images look stunning. Magic Zoom Plus adds a sophisticated zoom and phenomenal full-screen mode. Choose <a href="admin.php?page=WordPressMagicZoomPlus-config-page">your options</a>. For inspiration, view <a target="_blank" href="https://www.magictoolbox.com/magiczoomplus/examples/">popular examples </a> or browse <a target="_blank" href="https://www.magictoolbox.com/magiczoomplus/integration/">all the options</a>.
Version: 6.0.17
Author: MagicToolbox
Author URI: http://www.magictoolbox.com/

*/

/*
    WARNING: DO NOT MODIFY THIS FILE!

    NOTE: If you want change Magic Zoom Plus settings
            please go to plugin page
            and click 'Magic Zoom Plus Configuration' link in top navigation sub-menu.
*/

if(!function_exists('magictoolbox_WordPress_MagicZoomPlus_init')) {
    /* Include MagicToolbox plugins core funtions */
    require_once(dirname(__FILE__)."/magiczoomplus/plugin.php");
}

//MagicToolboxPluginInit_WordPress_MagicZoomPlus ();
register_activation_hook( __FILE__, 'WordPress_MagicZoomPlus_activate');

register_deactivation_hook( __FILE__, 'WordPress_MagicZoomPlus_deactivate');

magictoolbox_WordPress_MagicZoomPlus_init();
?>