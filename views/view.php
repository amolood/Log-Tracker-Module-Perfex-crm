<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                                    <?php echo _l('log') . '[' . $selected_date . ']' ?>
                                </h4>
                            </div>
                        </div>
                        <hr class="hr-panel-heading">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="panel ctl-panel-default">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading tw-font-semibold"><i class="fa-solid fa-flag"></i>
                                        <?php echo _l('levels') ?>
                                    </div>
                                    <!-- List group -->
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <?php $allColor = getColorByCategory('all'); ?>
                                            <?php echo '<a href="javascript:void(0)" onclick="setLevel(\'all\')" class="label" style="color:' . $allColor . ';border:1px solid ' . adjust_hex_brightness($allColor, 0.4) . ';background: ' . adjust_hex_brightness($allColor, 0.04) . ';">' . _l('all') . '</a>'; ?>
                                            <?php echo '<span class="label pull-right" style="color:' . $allColor . ';border:1px solid ' . adjust_hex_brightness($allColor, 0.4) . ';background: ' . adjust_hex_brightness($allColor, 0.04) . ';">' . $log_data['count'] . '</span>'; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <?php $errorColor = getColorByCategory('error'); ?>
                                            <?php $errorCount = (isset($log_data['data']['ERROR'])) ? $log_data['data']['ERROR']['count'] : 0; ?>
                                            <?php echo '<a href="javascript:void(0)" onclick="setLevel(\'error\')" class="label" style="color:' . $errorColor . ';border:1px solid ' . adjust_hex_brightness($errorColor, 0.4) . ';background: ' . adjust_hex_brightness($errorColor, 0.04) . ';">' . _l('error') . '</a>'; ?>
                                            <?php echo '<span class="label pull-right" style="color:' . $errorColor . ';border:1px solid ' . adjust_hex_brightness($errorColor, 0.4) . ';background: ' . adjust_hex_brightness($errorColor, 0.04) . ';">' . $errorCount . '</span>'; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <?php $debugColor = getColorByCategory('debug'); ?>
                                            <?php $debugCount = (isset($log_data['data']['DEBUG'])) ? $log_data['data']['DEBUG']['count'] : 0; ?>
                                            <?php echo '<a href="javascript:void(0)" onclick="setLevel(\'debug\')" class="label" style="color:' . $debugColor . ';border:1px solid ' . adjust_hex_brightness($debugColor, 0.4) . ';background: ' . adjust_hex_brightness($debugColor, 0.04) . ';">' . _l('debug') . '</a>'; ?>
                                            <?php echo '<span class="label pull-right" style="color:' . $debugColor . ';border:1px solid ' . adjust_hex_brightness($debugColor, 0.4) . ';background: ' . adjust_hex_brightness($debugColor, 0.04) . ';">' . $debugCount . '</span>'; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <?php $infoColor = getColorByCategory('info'); ?>
                                            <?php $infoCount = (isset($log_data['data']['INFO'])) ? $log_data['data']['INFO']['count'] : 0; ?>
                                            <?php echo '<a href="javascript:void(0)" onclick="setLevel(\'info\')" class="label" style="color:' . $infoColor . ';border:1px solid ' . adjust_hex_brightness($infoColor, 0.4) . ';background: ' . adjust_hex_brightness($infoColor, 0.04) . ';">' . _l('info') . '</a>'; ?>
                                            <?php echo '<span class="label pull-right" style="color:' . $infoColor . ';border:1px solid ' . adjust_hex_brightness($infoColor, 0.4) . ';background: ' . adjust_hex_brightness($infoColor, 0.04) . ';">' . $infoCount . '</span>'; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <?php $folderPath = get_instance()->config->item('log_path');
                                $folderPath = !empty($folderPath) ? $folderPath : APPPATH . '/logs';

                                $extension = get_instance()->config->item('log_file_extension');
                                $extension = !empty($extension) ? $extension : 'php'; ?>
                                <div class="panel ctl-panel-default">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading"><span class="tw-font-semibold"><i
                                                class="fa-solid fa-circle-info"></i>
                                            <?php echo _l('log_information') ?>:
                                        </span>
                                        <div class="pull-right">
                                            <?php if (has_permission('logtracker', '', 'download')) { ?>
                                                <a href="<?php echo admin_url('logtracker/downloadLogFile/') . 'log-' . $selected_date ?>"
                                                   class="btn btn-success btn-xs"><i class="fa-solid fa-download"></i>
                                                    <?php echo _l('download') ?>
                                                </a>
                                            <?php } ?>
                                            <?php if (has_permission('logtracker', '', 'delete')) { ?>
                                                <a href="<?php echo admin_url('logtracker/deleteLogFile/') . 'log-' . $selected_date ?>"
                                                   class="btn btn-danger btn-xs _delete"><i class="fa-solid fa-trash"></i>
                                                    <?php echo _l('delete') ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <!-- List group -->
                                    <ul class="list-group">
                                        <div class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <span class="tw-font-semibold">
                                                        <?php echo _l('file_path') ?>:
                                                    </span>
                                                </div>
                                                <div class="col-md-11">
                                                    <?php echo $folderPath . "/log-" . $selected_date . "." . $extension; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="row">
                                                <?php $info = $log_data['count']; ?>
                                                <div class="col-md-3"><span class="tw-font-semibold">
                                                        <?php echo _l('log_entries') ?>:
                                                    </span>
                                                    <span class="label label-info">
                                                        <?php echo $info ?>
                                                    </span>
                                                </div>
                                                <?php $info = bytesToSize($folderPath . "/log-" . $selected_date . "." . $extension); ?>
                                                <div class="col-md-3"><span class="tw-font-semibold">
                                                        <?php echo _l('size') ?>:
                                                    </span><span class="label label-info">
                                                        <?php echo $info ?>
                                                    </span></div>
                                                <?php $info = filectime($folderPath . "/log-" . $selected_date . "." . $extension); ?>
                                                <div class="col-md-3"><span class="tw-font-semibold">
                                                        <?php echo _l('created_at') ?>:
                                                    </span>
                                                    <span class="label label-info">
                                                        <?php echo _dt(date("Y-m-d H:i:s", $info)); ?>
                                                    </span>
                                                </div>
                                                <?php $info = filemtime($folderPath . "/log-" . $selected_date . "." . $extension); ?>
                                                <div class="col-md-3"><span class="tw-font-semibold">
                                                        <?php echo _l('updated_at') ?>:
                                                    </span>
                                                    <span class="label label-info">
                                                        <?php echo _dt(date("Y-m-d H:i:s", $info)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </ul>
                                </div>
                                <hr />
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <?php echo render_datatable([
                                            _l('level'),
                                            _l('time'),
                                            _l('message'),
                                            _l('action')
                                        ], 'logtracker_date_view') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="error_log_mail" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open('', ['id' => 'error_log_mail_form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('send_error_log_mail'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php echo form_hidden('error_level'); ?>
                <?php echo form_hidden('error_time'); ?>
                <?php echo form_hidden('error_message'); ?>
                <?php
                echo render_input('email_to', 'email_to', '', 'email');
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-success">
                    <?php echo _l('send'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>

<?php echo form_hidden('level', 'all') ?>

<script>
    "use strict";

    function sendErroLogMail(form) {
        $(form).find('button[type="submit"]').attr('data-loading-text', '<?php echo _l('wait_text'); ?>').prop('disabled', true);
        $.ajax({
            url: `${admin_url}logtracker/sendErroLogMail`,
            type: 'post',
            dataType: 'json',
            data: $(form).serialize()
        }).done(function (res) {
            $(form).find('button[type="submit"]').prop('disabled', false);
            alert_float(res.type, res.message);
            $('#error_log_mail').modal('hide');
        });
    }

    function sendToTelegram(element) {
        var row = $(element).closest('tr');
        var data = $('.table-logtracker_date_view').DataTable().row(row).data();
        
        var errorLevel = data[0].replace(/<[^>]*>/g, '').trim();
        var errorTime = data[1];
        var errorMessage = data[2];
        
        $.ajax({
            url: `${admin_url}logtracker/sendErrorLogTelegram`,
            type: 'post',
            dataType: 'json',
            data: {
                error_level: errorLevel,
                error_time: errorTime,
                error_message: errorMessage
            }
        }).done(function (res) {
            alert_float(res.type, res.message);
        });
    }

    $(function () {
        $('input[name="level"]').on('change', function () {
            $('.table-logtracker_date_view').DataTable().ajax.reload();
        });

        var fnServerParams = {
            'level': '[name="level"]'
        };

        initDataTable('.table-logtracker_date_view', `${admin_url}logtracker/get_table_data/logtracker_date_view/<?php echo $selected_date ?>`, undefined, undefined, fnServerParams);

        appValidateForm($('#error_log_mail_form'), {
            email_to: 'required'
        }, sendErroLogMail);
    });
</script>