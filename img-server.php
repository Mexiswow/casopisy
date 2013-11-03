<?php

// absolute filesystem path to this web root
define('WWW_DIR', dirname(__FILE__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/libs');

// Load Nette Framework or autoloader generated by Composer
require LIBS_DIR . '/autoload.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(APP_DIR . '/temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(APP_DIR . '/config.neon');
if(file_exists(APP_DIR . '/config-server.neon'))
	$configurator->addConfig(APP_DIR . '/config-server.neon');
$container = $configurator->createContainer();

//error_reporting(E_ALL & ~E_NOTICE);

//------------------ do tricks: ------------
$img = basename($container->httpRequest->url->path);

preg_match('~^(\d+)-(\d+)-([0-9a-z]+).png$~', $img, $m);
$allowed = (\Casopisy\Obsah::getFilesSecretHash($m[1], $m[2], "") == $m[3]) OR $container->user->isInRole('admin');

//check permission
if (file_exists("data/img/$m[1]-$m[2].png") AND $allowed) {
	$file = "data/img/$m[1]-$m[2].png";
}
else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$file = $container->parameters['staticDir'] . '/images/chyba404.png';
}

header("Content-Type: image/png");
readfile($file);


/* HELPER
$files = \Nette\Utils\Finder::findFiles("*.png")->from('data/img/');
foreach ($files as $file => $info) {
	preg_match('~^(\d+)-(\d+)~', $info->getFilename(), $m);
	rename($file, "data/img/$m[0].png");
}
*/
