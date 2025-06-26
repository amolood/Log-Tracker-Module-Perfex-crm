<?php

/*
 * Inject Css file for logtracker module
 */
hooks()->add_action('app_admin_head', 'logtracker_load_css');
function logtracker_load_css()
{
    if (get_instance()->app_modules->is_active(LOGTRACKER_MODULE)) {
        echo '<link href="' . module_dir_url(LOGTRACKER_MODULE, 'assets/css/logtracker.css') . '?v=' . get_instance()->app_scripts->core_version() . '" rel="stylesheet" type="text/css"></link>';
    }
}

/*
 * Inject Javascript file for logtracker module
 */
hooks()->add_action('app_admin_footer', function () {
    if (get_instance()->app_modules->is_active('logtracker')) {
        echo '<script src="'.module_dir_url('logtracker', 'assets/js/logtracker.js').'?v='.get_instance()->app_scripts->core_version().'" ></script>';
    }
});


