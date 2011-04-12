<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Denmark');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'da_DK.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 */
if (getenv('KOHANA_ENV') !== FALSE)
{
	Kohana::$environment = getenv('KOHANA_ENV');
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => 'http://localhost:8888/spjdr/flow/',
	'index_file' => '',
	'profiling' => ! IN_PRODUCTION,
	'caching' => IN_PRODUCTION
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	 'auth'       => MODPATH.'auth',		// Basic authentication
	 'cache'      => MODPATH.'cache',		// Caching with multiple backends
	 'fust'		  => MODPATH.'fust',		
	// 'codebench'  => MODPATH.'codebench',	// Benchmarking tool
	 'database'   => MODPATH.'database',	// Database access
	 'image'      => MODPATH.'image',		// Image manipulation
	 'orm'        => MODPATH.'orm',			// Object Relationship Mapping
	// 'oauth'      => MODPATH.'oauth',		// OAuth authentication
	 'pagination' => MODPATH.'pagination',	// Paging of results
	 'email'		  => MODPATH.'banks-kohana-email',
	// 'unittest'   => MODPATH.'unittest',	// Unit testing
	// 'userguide'  => MODPATH.'userguide',	// User guide and API documentation
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('noaccess','noaccess(.<format>)')
	->defaults(array(
		'controller'=>'pages',
		'action'=>'view',
		'page'=>'noaccess',
		'format'=>'html'
	));

Route::set('pages','pages/<page>(/<action>(.<format>))')
	->defaults(array(
		'controller'=>'pages',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('flow-new','new(.<format>)')
	->defaults(array(
		'controller'=>'flows',
		'action'=>'new',
		'format'=>'html'
	));
	
Route::set('join','join/<secret>')
	->defaults(array(
		'controller'=>'join',
		'action'=>'accept'
	));
	
Route::set('account-actions','<action>(.<format>)',
	array(
		'action'=>'(login|logout|register)'
	))
	->defaults(array(
		'controller'=>'account',
		'format'=>'html'
	));

Route::set('my-account','@<username>(/<action>)(.<format>)',
	array('username'=>'(.*?)')
	)
	->defaults(array(
		'controller'	=>'account',
		'action'		=>'view',
		'format'=>'html'
	)); 

Route::set('flow-actions','<flow>(/<action>)(.<format>)',array('action'=>'edit|delete|view'))
	->defaults(array(
		'controller'=>'flows',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('flow-items','<flow>/<controller>(/<action>)(.<format>)',array('controller'=>'tags|invitations|users','action'=>'index|new'))
	->defaults(array(
		'controller'=>'tags',
		'action'=>'index',
		'format'=>'html'
	));

Route::set('flow-item','<flow>/<controller>/<item>(/<action>)(.<format>)',array('controller'=>'tags|invitations'))
	->defaults(array(
		'controller'=>'tags',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('flow-user','<flow>/@<username>(/<action>)(.<format>)')
	->defaults(array(
		'controller'=>'users',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('stream-new','<flow>/new(.<format>)')
	->defaults(array(
		'controller'=>'streams',
		'action'=>'new',
		'format'=>'html'
	));
	
Route::set('stream-index','<flow>/streams(.<format>)')
	->defaults(array(
		'controller'=>'streams',
		'action'=>'index',
		'format'=>'html'
	));

Route::set('event-new','<flow>/<stream>/new(.<format>)')
	->defaults(array(
		'controller'=>'events',
		'action'=>'new',
		'format'=>'html'
	));

Route::set('stream','<flow>/<stream>(/<action>)(.<format>)',array('action'=>'edit|delete|view'))
	->defaults(array(
		'controller'=>'streams',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('stream-appoint','<flow>/<stream>/<editee>/@<username>(.<format>)',array('editee'=>'withdraw|appoint'))
	->defaults(array(
		'controller'=>'users',
		'action'=>'view',
		'format'=>'html'
	));

Route::set('event','<flow>/<stream>/<event>(/<action>)',array('action'=>'edit|delete|view|lock'))
	->defaults(array(
		'controller'=>'events',
		'action'=>'view',
		'format'=>'html'
	));

/*
Route::set('default','<flow>(/<controller>(/<action>))',
	array('controller'=>'flows|tags|invitations')
	array('action'=>'new')
	->defaults(array(
		'controller'=>'flows',
		'action'=>'view'
	));
*/

Route::set('home', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'home',
		'action'     => 'index',
	));

$request = Request::instance();

try
{
	if ( ! defined('SUPPRESS_REQUEST'))
	{	
   	 	// Execute the main request
    	$request->execute();
	}
}
catch (Exception $e)
{
        // Be sure to log the error
        Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));
 
        // If there was an error, send a 404 response and display an error
        $request->status   = 404;
        $request->response = View::factory('errors/404');
}

if ( $request->send_headers()->response )
{
	echo $request->response;
}
