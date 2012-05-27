<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Mailer_Driver_Mailgun implements Mailer_Driver {
	
	public function deliver(Mailer $mailer, $options = array()) {
		Kohana::$log->add(Log::INFO, 'Sending email via mailgun email driver. Email details: :mailer', array(':mailer' => print_r($mailer, TRUE)));
		$url = $options['url'];
		$apikey = $options['apikey'];

		$fields = array(
			'from' => $mailer->from,
			'to' => $mailer->to,
			'subject' => $mailer->subject,
		);
		foreach ($mailer->formats as $format) {
			$fields[$format] = $mailer->content[$format];
		}
		
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_USERPWD, $apikey);
		curl_setopt($request, CURLOPT_POSTFIELDS, $fields);
		
		// Additional curl request options.
		curl_setopt($request, CURLOPT_MAXREDIRS, 3);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_VERBOSE, 0);
		curl_setopt($request, CURLOPT_HEADER, 1);
		curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($request);
		
		curl_close($request);
	}
}