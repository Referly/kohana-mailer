<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Mailer_Driver_Mailgun implements Mailer_Driver {
	
	public function deliver(Mailer $mailer, $options = array()) {
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
		
		if (isset($mailer->campaign)) { $fields['o:campaign'] = $mailer->campaign; } // allows for campaign tracking
		if (isset($mailer->tag)) { $fields['o:tag'] = $mailer->tag; } // allows for tagging (needed for unsubscribe)

		// Build curl request.
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $apikey);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		
		// Additional curl request options.
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		# Success!
		if ($http_code == 200) {
			$data = json_decode($response);
		}
		else {
		}
	}
	
	public function unsubscribe($address, $tag = "*") {
		$config = Kohana::$config->load('mailer');
		
		$url = $config['driver_options']['unsub_url'];
		$apikey = $config['driver_options']['apikey'];
		
		$fields = array(
			'address' => $address,
			'tag' => $tag,
		);
		
		// Build curl request.
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $apikey);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		// Additional curl request options.
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		# Success!
		if ($http_code == 200) {
			$data = json_decode($response);
			Kohana::$log->add(Log::INFO, 'Successfully unsubscribed :email from :tag via Mailgun HTTP API.', array(':email' => $address, ':tag' => $tag));
		}
		else {
			Kohana::$log->add(Log::ERROR, 'Error unsubscribing via mailgun. Details: :details', array(':details' => print_r($response, TRUE)));
		}
	}
	
	public function resubscribe($address) {
		$config = Kohana::$config->load('mailer');
		
		$url = $config['driver_options']['unsub_url']."/$address";
		$apikey = $config['driver_options']['apikey'];
		
		// Build curl request.
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $apikey);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		// Additional curl request options.
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		# Success!
		if ($http_code == 200) {
			$data = json_decode($response);
			Kohana::$log->add(Log::INFO, 'Successfully resubscribed :email via Mailgun HTTP API at :url.', array(':email' => $address, ':url' => $url));
		}
		else {
			Kohana::$log->add(Log::ERROR, 'Error resubscribing via mailgun. Details: :details', array(':details' => print_r($response, TRUE)));
		}
	}
}
