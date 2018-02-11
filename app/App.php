<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/7/18
 * Time: 8:56 AM
 */

namespace Epguides;

use DI\ContainerBuilder;
use DI\Bridge\Slim\App as DiBridge;

class App extends DiBridge {

	protected function configureContainer(ContainerBuilder $builder)
	{
	  $builder->addDefinitions([
	  	'settings.displayErrorDetails' => true,
	  ]);

	  $builder->addDefinitions(__DIR__.DS.'container.php');


	}

}