<?php

require 'autoload.php';

global $cli, $isQuiet;
$cli = eZCLI::instance();
$script = eZScript::instance( array( 
    'description' => ( "eZ Publish Script Executor\n\n" . "Allows execution of simple PHP scripts which uses eZ Publish functionality,\n" . "when the script is called all necessary initialization is done\n" . "\n" . "ezexec.php myscript.php" ) , 
    'use-session' => false , 
    'use-modules' => true , 
    'use-extensions' => true 
) );

$script->startup();

$options = $script->getOptions( "", "[provider]", array() );

$script->initialize();

$provider = $options['arguments'][0];

if ( ! $provider )
{
    throw new Exception( "Parameter Provider not given." );
}

$plug = xrowExternalSearchProvider::getExternalProvider( $provider );

if ( ! $plug instanceof ExternalSearchXMLPlugin )
{
    throw new Exception( "Provider Plugin not found." );
}
$records = $plug->load();
// setup
$handler = new xrowSOLRHandler();
$manager = new ezcSearchEmbeddedManager();
$session = new ezcSearchSession( $handler, $manager );

$session->beginTransaction();

if ( ! $isQuiet )
{
    $amount = count( $records );
    $cli->output( "Adding $amount items to the index from $records->params['ExternalURL']." );
    $output = new ezcConsoleOutput();
    $bar = new ezcConsoleProgressbar( $output, $amount );
}
foreach ( $records as $elem )
{
    $session->index( $elem );
    if ( isset( $bar ) )
    {
        $bar->advance();
    }
}
if ( ! $isQuiet )
{
    $bar->finish();
    $cli->output( "\n" );
}

$session->commit();

sleep( 3 );

$cli->output( "external xml file has already been uploaded!" );

$script->shutdown();