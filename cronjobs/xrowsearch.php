<?php

global $cli, $isQuiet;

$plugins = xrowExternalSearchProvider::getExternalProviders();

$cli = $GLOBALS['cli'];

foreach ( $plugins as $plug )
{
    if ( ! $plug instanceof ExternalSearchXMLPlugin )
    {
        throw new Exception( "Provider Plugin not found." );
    }
    
    try
    {
        $records = $plug->load();
    }
    catch ( Exception $e )
    {
        $cli->output( $e->getMessage() . "\n" );
        continue;
    }
    // setup
    $handler = new xrowSOLRHandler();
    $manager = new ezcSearchEmbeddedManager();
    $session = new ezcSearchSession( $handler, $manager );
    
    $plug->delete( $handler );
    
    $session->beginTransaction();
    
    if ( ! $isQuiet )
    {
        $amount = count( $records );
        $cli->output( "Adding $amount items to the index." );
        $output = new ezcConsoleOutput();
        $bar = new ezcConsoleProgressbar( $output, $amount );
    }
    foreach ( $records as $key => $elem )
    {
        if ( ( $key % 499 ) === 0 and $key !== 0 )
        {
            
            $session->commit();
            
            sleep( 3 );
            
            $session->beginTransaction();
        
        }
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

}