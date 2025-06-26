<?php

// create logtracker model class

defined('BASEPATH') or exit('No direct script access allowed');

class Logtracker_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getLogData($orderBy = "logDate")
    {
        $logData = [];
        $logFiles = $this->getLogFiles();
        $logData['total'] = count($logFiles);
        $logData['data'] = [];
        $logData['summary'] = [];
        $logData['summary']['total'] = 0;
        $logData['summary']['error'] = 0;
        $logData['summary']['debug'] = 0;
        $logData['summary']['info'] = 0;
        $logData['summary']['critical'] = 0;
        $logData['summary']['warning'] = 0;
        $logData['summary']['notice'] = 0;
        $logData['summary']['alert'] = 0;
        $logData['summary']['emergency'] = 0;
        $logData['summary']['unknown'] = 0;

        foreach ($logFiles as $logFile) {
            $logData['data'][$logFile] = $this->getLogDataFromFile($logFile);
            $logData['summary']['total'] += count($logData['data'][$logFile]);
            foreach ($logData['data'][$logFile] as $log) {
                $logData['summary'][$log['level']]++;
            }
        }

        if ($orderBy == "logDate") {
            krsort($logData['data']);
        }

        return $logData;
    }

    public function getLogFiles()
    {
        $logFiles = [];
        $files = glob(APPPATH . 'logs/log-*.php');
        if ($files) {
             $logFiles = array_map('basename', $files);
        }
        return $logFiles;
    }

    public function getLogDataFromFile($logFile)
    {
        $logData = [];
        $logFile = APPPATH . '/logs/' . $logFile;
        $logData = file($logFile);
        $logData = array_map('trim', $logData);
        $logData = array_filter($logData);
        $logData = array_map(function ($log) {
            $logData = [];
            $logData['date'] = substr($log, 0, 19);
            $logData['level'] = substr($log, 21, 7);
            $logData['message'] = substr($log, 29);
            return $logData;
        }, $logData);
        return $logData;
    }

    public function getLogDataByDate($date)
    {
        $logData = [];
        $logFiles = $this->getLogFiles();
        $logData['total'] = count($logFiles);
        $logData['data'] = [];
        $logData['summary'] = [];
        $logData['summary']['total'] = 0;
        $logData['summary']['error'] = 0;
        $logData['summary']['debug'] = 0;
        $logData['summary']['info'] = 0;
        $logData['summary']['critical'] = 0;
        $logData['summary']['warning'] = 0;
        $logData['summary']['notice'] = 0;
        $logData['summary']['alert'] = 0;
        $logData['summary']['emergency'] = 0;
        $logData['summary']['unknown'] = 0;

        foreach ($logFiles as $logFile) {
            $logData['data'][$logFile] = $this->getLogDataFromFile($logFile);
            $logData['summary']['total'] += count($logData['data'][$logFile]);
            foreach ($logData['data'][$logFile] as $log) {
                $logData['summary'][$log['level']]++;
            }
        }

        krsort($logData['data']);
        return $logData;
    }

    public function getLogDataByLevel($level)
    {
        $logData = [];
        $logFiles = $this->getLogFiles();
        $logData['total'] = count($logFiles);
        $logData['data'] = [];
        $logData['summary'] = [];
        $logData['summary']['total'] = 0;
        $logData['summary']['error'] = 0;
        $logData['summary']['debug'] = 0;
        $logData['summary']['info'] = 0;
        $logData['summary']['critical'] = 0;
        $logData['summary']['warning'] = 0;
        $logData['summary']['notice'] = 0;
        $logData['summary']['alert'] = 0;
        $logData['summary']['emergency'] = 0;
        $logData['summary']['unknown'] = 0;

        foreach ($logFiles as $logFile) {
            $logData['data'][$logFile] = $this->getLogDataFromFile($logFile);
            $logData['summary']['total'] += count($logData['data'][$logFile]);
            foreach ($logData['data'][$logFile] as $log) {
                if ($log['level'] == $level) {
                    $logData['summary'][$log['level']]++;
                }
            }
        }

        krsort($logData['data']);
        return $logData;
    }

    public function clearAllLogs()
    {
        $logFiles = $this->getLogFiles();
        $deleted = 0;
        foreach ($logFiles as $logFile) {
            $filePath = APPPATH . 'logs/' . $logFile;
            if (is_file($filePath) && unlink($filePath)) {
                $deleted++;
            }
        }
        return $deleted;
    }
}