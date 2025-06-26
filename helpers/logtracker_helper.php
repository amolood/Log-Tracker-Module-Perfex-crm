<?php
if (!function_exists('getLogData')) {
	function getLogData($primaryDataKey = "logType")
	{
		$secondaryDataKey = ($primaryDataKey == "logType") ? "logDate" : "logType";

		$folderPath = get_instance()->config->item('log_path');
		$folderPath = !empty($folderPath) ? $folderPath : APPPATH . '/logs';

		$extension = get_instance()->config->item('log_file_extension');
		$extension = !empty($extension) ? $extension : 'php';

		$files = directory_map($folderPath);
		$files = array_reverse($files);

		$logData = ['count' => 0, 'data' => []];

		foreach ($files as $file) {
			$filePath = $folderPath . "/" . $file;
			$ext = pathinfo($filePath, PATHINFO_EXTENSION);

			if (is_file($filePath) && $ext === $extension) {
				$content = file_get_contents($filePath);

				preg_match_all('/^(.*?) - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> (.*)$/m', $content, $matches, PREG_SET_ORDER);
				$matches = array_reverse($matches);

				foreach ($matches as $match) {
					[$fullMatch, $logType, $logTime, $logMsg] = $match;
					$logDate = substr($logTime, 0, 10); // Extract date from log_time

					// Increment counts
					$logData['count']++;
					$logData['data'][${$primaryDataKey}]['count'] = ($logData['data'][${$primaryDataKey}]['count'] ?? 0) + 1;
					$logData['data'][${$primaryDataKey}]['data'][${$secondaryDataKey}]['count'] = ($logData['data'][${$primaryDataKey}]['data'][${$secondaryDataKey}]['count'] ?? 0) + 1;

					// Store data
					$logData['data'][${$primaryDataKey}]['data'][${$secondaryDataKey}]['data'][] = (object) ["message" => $logMsg, "time" => $logTime, 'level' => $logType];
				}
			}
		}

		return $logData;
	}
}

if (!function_exists('getColorByCategory')) {
	function getColorByCategory($category)
	{
		$color = !empty(get_option($category . '_color')) ? get_option($category . '_color') : '#64748b';
		return $color;
	}
}

if (!function_exists('displayEnvironmentMessage')) {
	function displayEnvironmentMessage()
	{
		$message = new app\services\messages\DevelopmentEnvironment();

		$errorHtml = '';
		if (ENVIRONMENT == 'development') {
			$errorHtml = '<div class="alert alert-warning">';
			$errorHtml .= $message->getMessage();
			$errorHtml .= '</div>';
		}

		return $errorHtml;
	}
}

if (!function_exists('send_telegram_message')) {
	function send_telegram_message($message) {
		// Check if Telegram is enabled
		if (get_option('telegram_enabled') != '1') {
			return false;
		}
		
		$botToken = get_option('telegram_bot_token');
		$chatIds = get_option('telegram_chat_ids');
		
		// If no bot token or chat IDs configured, return false
		if (empty($botToken) || empty($chatIds)) {
			return false;
		}
		
		// Convert comma-separated chat IDs to array
		$chatIdsArray = array_map('trim', explode(',', $chatIds));
		$success = true;
		
		// Send message to all configured chat IDs
		foreach ($chatIdsArray as $chatId) {
			if (!empty($chatId)) {
				$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
				$data = [
					'chat_id' => $chatId,
					'text' => $message,
					'parse_mode' => 'HTML'
				];
				$options = [
					'http' => [
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data),
						'timeout' => 5,
					],
				];
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				
				// If any message fails, mark as unsuccessful
				if ($result === false) {
					$success = false;
				}
			}
		}
		
		return $success;
	}
}
