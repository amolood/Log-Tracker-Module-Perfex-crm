<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
get_instance()->load->helper('logtracker');
$total_logs = $summary_per_level['count'] ?? 0;
$error_logs = $summary_per_level['data']['ERROR']['count'] ?? 0;
$debug_logs = $summary_per_level['data']['DEBUG']['count'] ?? 0;
$info_logs = $summary_per_level['data']['INFO']['count'] ?? 0;
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Summary Stats -->
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="widget-dragger"></div>
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo _l('total_logs'); ?></h4>
                        <h3 class="tw-mt-4 tw-font-bold tw-text-4xl"><?php echo $total_logs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="widget-dragger"></div>
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-danger-700"><?php echo _l('error_logs'); ?></h4>
                        <h3 class="tw-mt-4 tw-font-bold tw-text-4xl"><?php echo $error_logs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="widget-dragger"></div>
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-primary-700"><?php echo _l('debug_logs'); ?></h4>
                        <h3 class="tw-mt-4 tw-font-bold tw-text-4xl"><?php echo $debug_logs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="widget-dragger"></div>
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-info-700"><?php echo _l('info_logs'); ?></h4>
                        <h3 class="tw-mt-4 tw-font-bold tw-text-4xl"><?php echo $info_logs; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                <span><i class="fa-solid fa-list-alt"></i>
                                    <?php echo _l('log_summary_by_date') // A new language string might be needed ?>
                                </span>
                            </h4>
                            <?php if (has_permission('logtracker', '', 'delete')): ?>
                                <button id="clearAllLogsBtn" class="btn btn-danger">
                                    <i class="fa fa-trash"></i> <?php echo _l('clear_all_logs'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <hr class="hr-panel-heading">
                        <table class="table table-logtracker">
                            <thead>
                                <tr>
                                    <th><?php echo _l('date'); ?></th>
                                    <th><?php echo _l('total_logs'); ?></th>
                                    <th><?php echo _l('error'); ?></th>
                                    <th><?php echo _l('debug'); ?></th>
                                    <th><?php echo _l('info'); ?></th>
                                    <th><?php echo _l('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary_per_date['data'] as $key => $value) {
                                $date = '<span>' . _d($key) . '</span>';
                                $total = '<span class="label label-default">' . $value['count'] . '</span>';
                                $error = '<span class="label label-danger">' . ($value['data']["ERROR"]['count'] ?? 0) . '</span>';
                                $debug = '<span class="label label-primary">' . ($value['data']["DEBUG"]['count'] ?? 0) . '</span>';
                                $info = '<span class="label label-info">' . ($value['data']["INFO"]['count'] ?? 0) . '</span>';
                                
                                $actions = '<div class="tw-flex tw-items-center tw-space-x-3">';
                                $actions .= '<a href="' . admin_url('logtracker/view/') . $key . '" class="text-info" data-toggle="tooltip" data-title="' . _l('view_log_details') . '">
										<i class="fa-solid fa-eye fa-lg"></i>
									</a>';
                                if (has_permission('logtracker', '', 'download')) {
                                    $actions .= '<a href="' . admin_url('logtracker/downloadLogFile/') . 'log-' . $key . '" class="text-success" data-toggle="tooltip" data-title="' . _l('download_log_file') . '">
											<i class="fa-solid fa-file-arrow-down fa-lg"></i>
										</a>';
                                    $actions .= '<a href="' . admin_url('logtracker/downloadLogFile/') . 'log-' . $key . '/true' . '" class="text-warning" data-toggle="tooltip" data-title="' . _l('download_log_file_as_a_zip') . '">
											<i class="fa-solid fa-file-zipper fa-lg"></i>
										</a>';
                                }
                                if (has_permission('logtracker', '', 'delete')) {
                                    $actions .= '<a href="javascript:void(0)" class="text-danger" onclick="deleteLogFile(\'' . 'log-' . $key . '\')" data-toggle="tooltip" data-title="' . _l('delete_log_file') . '">
											<i class="fa-regular fa-trash-can fa-lg"></i>
										</a>';
                                }
                                $actions .= '</div>';
                                ?>
                                <tr>
                                    <td><?php echo $date; ?></td>
                                    <td><?php echo $total; ?></td>
                                    <td><?php echo $error; ?></td>
                                    <td><?php echo $debug; ?></td>
                                    <td><?php echo $info; ?></td>
                                    <td><?php echo $actions; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    "use strict";

    $(function () {
        initDataTableInline('.table-logtracker');
        $('#clearAllLogsBtn').on('click', function () {
            if (confirm('<?php echo _l('clear_logs_confirm_warning'); ?>')) {
                $.ajax({
                    url: admin_url + 'logtracker/clear_all_logs',
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert_float('success', response.message);
                            location.reload();
                        } else {
                            alert_float('warning', response.message);
                        }
                    },
                    error: function () {
                        alert_float('danger', 'An error occurred while clearing logs.');
                    }
                });
            }
        });
    });

    function deleteLogFile(fileName) {
        if (confirm('<?php echo _l('clear_logs_confirm_warning'); ?>')) {
            $.ajax({
                url: admin_url + 'logtracker/deleteLogFileUsingAjax/' + fileName,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    alert_float(response.type, response.message);
                    $('.table-logtracker').DataTable().row($('a[onclick="deleteLogFile(\'' + fileName + '\')"]').closest('tr')).remove().draw();
                },
                error: function () {
                    alert_float('danger', 'An error occurred while deleting the log file.');
                }
            });
        }
    }
</script>