<?php

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 
    'description' => ( "eZ Publish Script Executor\n\n" . "Allows execution of simple PHP scripts which uses eZ Publish functionality,\n" . "when the script is called all necessary initialization is done\n" . "\n" . "ezexec.php myscript.php" ) , 
    'use-session' => false , 
    'use-modules' => true , 
    'use-extensions' => true 
) );

$script->startup();

$options = $script->getOptions( "","[provider]", array() );

$script->initialize();

$provider = $options['arguments'][0];

$searchini = eZINI::instance( 'xrowsearch.ini' );

$filename = $searchini->variable( $provider, 'Namespace' );
// setup

$handler = new xrowSOLRHandler();
$manager = new ezcSearchEmbeddedManager();
$session = new ezcSearchSession( $handler, $manager );

$session->beginTransaction();

$handler->deleteByurl( "meta_installation_id_ms:xrowsearch-"."$filename" );

$session->commit();

echo "XML data has benn deleted !";

$script->shutdown();