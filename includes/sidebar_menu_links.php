<?php

/*
 * Inject sidebar menu and links for logtracker module
 */
hooks()->add_action('admin_init', 'logtracker_module_init_menu_items');
function logtracker_module_init_menu_items()
{
    if (!has_permission('logtracker', '', 'view')) {
        return;
    }
    get_instance()->app_menu->add_sidebar_menu_item('logtracker', [
        'slug' => 'logtracker',
        'name' => _l('logtracker'),
        'icon' => 'fa fa-bug',
        'position' => 20,
    ]);
    get_instance()->app_menu->add_sidebar_children_item('logtracker', [
        'slug' => 'logtracker_dashboard',
        'name' => _l('dashboard_string'),
        'href' => admin_url('logtracker'),
        'position' => 1,
    ]);

    get_instance()->app_menu->add_sidebar_children_item('logtracker', [
        'slug' => 'logtracker_settings',
        'name' => _l('logtracker_settings'),
        'href' => admin_url('settings?group=logtracker_settings'),
        'position' => 2,
    ]);
    get_instance()->app_menu->add_sidebar_children_item('logtracker', [
        'slug' => 'logtracker_author',
        'name' => _l('logtracker_author'),
        'href' => 'https://amolood.com',
        'position' => 2,
    ]);

    get_instance()->app->add_settings_section_child('other', 'logtracker_settings', [
        'name' => _l('logtracker_settings'),
        'title' => _l('logtracker'),
        'view' => 'logtracker/settings',
        'icon' => 'fa fa-bug',
        'position' => 5,
    ]);
}

