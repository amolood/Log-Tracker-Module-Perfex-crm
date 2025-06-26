"use strict";

var log_tracker_chart = $('#log_tracker_chart');
if (log_tracker_chart.length > 0) {
    new Chart(log_tracker_chart, {
        type: 'doughnut',
        data: log_tracker_chart.data("json"),
        options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
                onChartClickRedirect(evt, this);
            }
        }
    });
}

function deleteLogFile(fileName) {
    if (confirm_delete()) {
        $.ajax({
            url: `${admin_url}logtracker/deleteLogFileUsingAjax/${fileName}`,
            type: 'POST',
            dataType: 'json'
        }).done(function (res) {
            alert_float(res.type, res.message);
            $('.table-logtracker').DataTable().ajax.reload();
        });
    }
}

function setLevel(level) {
    $('input[name="level"]').val(level).trigger('change');
}

function openMailPopup(instance) {
    const mailPopup = $('#error_log_mail');
    const errorLevel = $(instance).closest("tr").find("td:eq(0)").text();
    const errorTime = $(instance).closest("tr").find("td:eq(1)").text();
    const errorMessage = $(instance).closest("tr").find("td:eq(2)").text();

    mailPopup.find('input:not([name="csrf_token_name"])').val('');

    mailPopup.modal('show');

    mailPopup.find('input[name="error_level"]').val(errorLevel);
    mailPopup.find('input[name="error_time"]').val(errorTime);
    mailPopup.find('input[name="error_message"]').val(errorMessage);
}
