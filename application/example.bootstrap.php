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
	'base_url'   => 'http://spjdr.dk/flow',
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
	 'auth'       => MODPATH.'auth',       // Basic authentication
	 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
	 'image'      => MODPATH.'image',      // Image manipulation
	 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'oauth'      => MODPATH.'oauth',      // OAuth authentication
	 'pagination' => MODPATH.'pagination', // Paging of results
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('create','create')
	->defaults(array(
		'controller'=>'flows',
		'action'=>'create'
	));
	
Route::set('index','index(@<username>)')
	->defaults(array(
		'controller'=>'flows',
		'action'=>'index'
	));
	
Route::set('account','<action>',
	array(
		'action'=>'(login|logout|register)'
	))
	->defaults(array(
		'controller'=>'account'
	));

Route::set('my_account','@<username>(/<action>)',
	array('username'=>'(.*?)')
	)
	->defaults(array(
		'controller'	=>'account',
		'action'		=>'view'
	)); 

Route::set('edit','<uri>/<action>',array('action'=>'edit|delete'))
	->defaults(array(
		'controller'=>'flows',
		'action'=>'edit'
	));
	
Route::set('tags','<uri>/tags(/<id>/<action>)')
	->defaults(array(
		'controller'=>'tags',
		'action'=>'index'
	));

Route::set('default','<uri>(/<controller>(/<action>))')
	->defaults(array(
		'controller'=>'flows',
		'action'=>'view'
	));

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
