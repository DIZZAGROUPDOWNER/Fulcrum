<?php

/**
 * Whoops Displayer
 *
 * @package     Fulcrum\Support\Exceptions
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */
namespace Fulcrum\Support\Exceptions;

use Whoops;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class Whoops_Displayer implements Exception_Displayer_Contract {
	/**
	 * The Whoops run instance.
	 *
	 * @var \Whoops\Run
	 */
	protected $whoops;

	protected $error_page;

	public function __construct( Run $whoops ) {
		$this->whoops = $whoops;

		$this->register();
	}

	public function register() {
		$error_page = new PrettyPageHandler;

		$error_page->setEditor( 'sublime' );

		$this->whoops->pushHandler( $error_page );

		$this->whoops->register();
	}
}
