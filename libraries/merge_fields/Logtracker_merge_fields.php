<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Logtracker_merge_fields extends App_merge_fields
{
	public function build()
	{
		return [
			[
				'name' => 'Error Level',
				'key' => '{error_level}',
				'available' => [
					'logtracker'
				],
				'templates' => []
			],
			[
				'name' => 'Error Time',
				'key' => '{error_time}',
				'available' => [
					'logtracker'
				],
				'templates' => []
			],
			[
				'name' => 'Error Message',
				'key' => '{error_message}',
				'available' => [
					'logtracker'
				],
				'templates' => []
			]
		];
	}

	public function format($errorLevel = '', $errorTime = '', $errorMessage = '')
	{
		$fields = [];

		$fields['{error_level}'] = $errorLevel;
		$fields['{error_time}'] = $errorTime;
		$fields['{error_message}'] = $errorMessage;

		return $fields;
	}
}
