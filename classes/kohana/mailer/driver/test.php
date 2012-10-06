<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Mailer_Driver_Test implements Kohana_Mailer_Driver {
	
	public function deliver(Mailer $mailer, $options = array()) {
		Kohana::$log->add(Log::INFO, 'Sending email via test email driver. Email details: :mailer', array(':mailer' => print_r($mailer, TRUE)));
	}
	
	public function unsubscribe($address, $options = array()) {
		Kohana::$log->add(Log::INFO, 'Unsubscribed email :address via test email driver.', array(':address' => $address));
	}
	
	public function resubscribe($address, $options = array()) {
		Kohana::$log->add(Log::INFO, 'Resubscribed email :address via test email driver.', array(':address' => $address));
	}
	
	
	
}