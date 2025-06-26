<?php

/*
 * Inject permissions Feature and Capabilities for logtracker module
 */
hooks()->add_filter('staff_permissions', function ($permissions) {
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'view' => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    $permissions['logtracker'] = [
        'name' => _l('logtracker'),
        'capabilities' => $allPermissionsArray,
    ];
    return $permissions;
});


