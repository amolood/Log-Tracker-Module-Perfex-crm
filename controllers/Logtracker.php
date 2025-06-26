<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logtracker extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('logtracker_model');
        $this->load->helper('logtracker');
    }
    /**
     * @return void
     */
    public function index()
    {
        if (!has_permission('logtracker', '', 'view')) {
            access_denied('logtracker');
        }

        $viewData['title'] = _l('logtracker_dashboard');
        $viewData['summary_per_date'] = getLogData("logDate");
        $viewData['summary_per_level'] = getLogData("logType");
        
        $this->load->view('logtracker/manage', $viewData);
    }

    /**
     * @param $date
     * @return void
     */
    public function view($date)
    {
        try {
            if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
                throw new Exception("Access denied");
            }

            if (empty($date)) {
                throw new Exception("Date parameter is empty");
            }

            $viewData['title'] = _l('log') . '[' . $date . ']';
            $logData = getLogData("logDate");

            if (empty($logData['data'][$date])) {
                set_alert('danger', _l('no_details_found'));
                redirect(admin_url('logtracker'));
            }

            $viewData['log_data'] = $logData['data'][$date];
            $viewData['selected_date'] = $date;
            $this->load->view('logtracker/view', $viewData);
        } catch (Exception $e) {
            // Handle exceptions
            // You can log the error, display a user-friendly message, etc.
            // For now, let's log the error and redirect to a generic error page
            log_message('error', $e->getMessage());
            set_alert('danger', _l('error_occurred'));
            redirect(admin_url('error_page'));
        }
    }


    /**
     * @param $tableName
     * @param $date
     * @return void
     */
    public function get_table_data($tableName, $date = "")
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }

        $this->app->get_table_data(module_views_path(LOGTRACKER_MODULE, 'tables/' . $tableName), ['date' => $date]);
    }

    /**
     * @param $fileName
     * @param $zip
     * @return void
     */
    public function downloadLogFile($fileName, $zip = false)
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }

        $folderPath = !empty($folderPath) ? $folderPath : APPPATH . '/logs';
        $filePath = $folderPath . '/' . $fileName;

        if (file_exists($filePath . '.php')) {
            if ($zip) {
                $zipFilePath = $this->createZip($filePath);

                if ($zipFilePath !== false) {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
                    header('Content-Length: ' . filesize($zipFilePath));
                    readfile($zipFilePath);

                    unlink($zipFilePath);
                    exit;
                }
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath . '.txt') . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath . '.php'));
            readfile($filePath . '.php');
            exit;
        }
    }

    private function createZip($filePath)
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }
        $zip = new ZipArchive();
        $zipFilePath = $filePath . '.zip';

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($filePath . '.php', basename($filePath . '.txt'));
            $zip->close();
            return $zipFilePath;
        } else {
            return false;
        }
    }

    /**
     * @param $fileName
     * @return void
     */
    public function deleteLogFileUsingAjax($fileName)
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }

        $folderPath = !empty($folderPath) ? $folderPath : APPPATH . '/logs';
        $filePath = $folderPath . '/' . $fileName . '.php';

        if (file_exists($filePath)) {
            $fileDeleted = unlink($filePath);
            echo json_encode(['type' => 'danger', 'message' => $fileDeleted ? _l('log_file_deleted') : _l('something_went_wrong')]);
        }
    }

    public function deleteLogFile($fileName)
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }
        $folderPath = !empty($folderPath) ? $folderPath : APPPATH . '/logs';
        $filePath = $folderPath . '/' . $fileName . '.php';

        if (file_exists($filePath)) {
            $fileDeleted = unlink($filePath);
            set_alert('danger', $fileDeleted ? _l('log_file_deleted') : _l('something_went_wrong'));
            redirect(admin_url('logtracker'));
        }
    }

    public function sendErroLogMail()
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }
        $postData = $this->input->post();

        if (!isset($postData['email_to']) || !isset($postData['error_level']) || !isset($postData['error_time']) || !isset($postData['error_message'])) {
            echo json_encode(['type' => 'danger', 'message' => _l('something_went_wrong')]);
            exit;
        }
        $sendMail = send_mail_template('SendError', LOGTRACKER_MODULE, $postData['email_to'], $postData['error_level'], $postData['error_time'], $postData['error_message']);

        // Send to Telegram
        $telegramMessage = "🚨 <b>Log Error</b>\n"
            . "<b>Level:</b> " . $postData['error_level'] . "\n"
            . "<b>Time:</b> " . $postData['error_time'] . "\n"
            . "<b>Message:</b> " . $postData['error_message'];
        send_telegram_message($telegramMessage);

        echo json_encode([
            'type' => $sendMail ? 'success' : 'danger',
            'message' => $sendMail ? _l('mail_sent_success') : _l('mail_was_not_sent')
        ]);
    }

    public function sendErrorLogTelegram()
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'view')) {
            access_denied();
        }
        $postData = $this->input->post();

        if (!isset($postData['error_level']) || !isset($postData['error_time']) || !isset($postData['error_message'])) {
            echo json_encode(['type' => 'danger', 'message' => _l('something_went_wrong')]);
            exit;
        }

        $telegramMessage = "🚨 <b>Log Error</b>\n"
            . "<b>Level:</b> " . $postData['error_level'] . "\n"
            . "<b>Time:</b> " . $postData['error_time'] . "\n"
            . "<b>Message:</b> " . $postData['error_message'];
        
        $result = send_telegram_message($telegramMessage);
        
        if ($result !== false) {
            echo json_encode(['type' => 'success', 'message' => _l('telegram_message_sent_success')]);
        } else {
            echo json_encode(['type' => 'danger', 'message' => _l('telegram_message_sent_failed')]);
        }
    }

    public function clear_all_logs()
    {
        if (!has_permission('logtracker', get_staff_user_id(), 'delete')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => _l('invalid_request')]);
            return;
        }
        $deleted = $this->logtracker_model->clearAllLogs();
        if ($deleted > 0) {
            echo json_encode(['success' => true, 'message' => _l('all_logs_deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('no_logs_to_delete')]);
        }
    }

    public function test_telegram()
    {
        if (!has_permission('logtracker', '', 'view')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $message = "✅ *Logtracker - Telegram Test Message*\n" .
                   "This is a test message from your Perfex CRM Logtracker module.\n" .
                   "If you received this, your settings are correct!";
        
        $result = send_telegram_message($message);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => _l('telegram_test_message_sent_success')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('telegram_test_message_sent_failed')]);
        }
    }
}