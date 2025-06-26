<?php

defined('BASEPATH') or exit('No direct script access allowed');
get_instance()->load->helper('logtracker');
$log_data = getLogData("logDate")['data'][$date];

$result = collect($log_data['data'])->pluck('data')->collapse()->all();
$data = get_instance()->input->post();

// Levels filters
$filtered_data = array_filter($result, function ($value, $key) use ($data) {
    if ($data['level'] !== 'all') {
        return $value->level == strtoupper($data['level']);
    } else {
        return true;
    }
}, ARRAY_FILTER_USE_BOTH);

// Searching
$filtered_data = array_filter($filtered_data, function ($value) use ($data) {
    foreach ($value as $property => $v) {
        if (stripos($v, $data['search']['value']) !== false) {
            return true;
        }
    }
    return false;
});

// Sorting
usort($filtered_data, function ($a, $b) use ($data) {
    $dir = ($data['order'][0]['dir'] == 'asc') ? 1 : -1; // Determine a sorting direction

    $column = $data['order'][0]['column'];
    switch ($column) {
        case '0':
            return $dir * strcmp($a->level, $b->level);
        case '1':
            return $dir * strcmp($a->time, $b->time);
        case '2':
            return $dir * strcmp($a->message, $b->message);
        default:
            return 0; // Default case: no change in order
    }
});

$output_data = array_slice($filtered_data, $data['start'], $data['length']);

$result = [
    'rResult' => $output_data,
    'output' => [
        'draw' => $data['draw'] ? intval($data['draw']) : 0,
        'iTotalRecords' => count($filtered_data),
        'iTotalDisplayRecords' => count($filtered_data),
        'aaData' => []
    ]
];

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $color = getColorByCategory($aRow->level);
    $level = '<span class="label" style="color:' . $color . ';border:1px solid ' . adjust_hex_brightness($color, 0.4) . ';background: ' . adjust_hex_brightness($color, 0.04) . ';">';
    $level .= $aRow->level;
    $level .= '</span>';
    $row[] = $level;
    $row[] = $aRow->time;
    $row[] = $aRow->message;

    $actions = '-';
    if (has_permission('logtracker', '', 'email')) {
        $actions = '';
        $actions .= '<div class="tw-flex tw-items-center tw-space-x-3">
			<a href="javascript:void(0)" class="text-success" data-toggle="tooltip" data-title="' . _l('send_mail') . '" onclick="openMailPopup(this)">
				<i class="fa-solid fa-envelope fa-lg"></i>
			</a>
			<a href="javascript:void(0)" class="text-info" data-toggle="tooltip" data-title="' . _l('send_to_telegram') . '" onclick="sendToTelegram(this)">
				<i class="fa-brands fa-telegram fa-lg"></i>
			</a>
		</div>';
    }
    $row[] = $actions;

    $output['aaData'][] = $row;
}
