<?php

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

$provider = $options['arguments'][1];

if ( ! $provider )
{
    echo "Please enter a keyword ('update' or 'delete')!\n";
}
else
{
    if ( $provider == "update" )
    
    {
        $handler = new xrowSOLRHandler();
        $manager = new ezcSearchEmbeddedManager();
        $session = new ezcSearchSession( $handler, $manager );
        
        $session->beginTransaction();
        
        $handler->deleteByurl( "*:*" );
        
        $session->commit();
        
        sleep( 3 );
        
        global $cli, $isQuiet;
        
        $plugins = xrowExternalSearchProvider::getExternalProviders();
        
        $cli = $GLOBALS['cli'];
        
        foreach ( $plugins as $plug )
        {
            
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
    }
    elseif ( $provider == "delete" )
    {
        
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
        
        $provider = $options['arguments'][2];
        
        if ( ! $provider )
        {
            echo "Please give the name you want to delete !\n";
        }
        else
        {
            $searchini = eZINI::instance( 'xrowsearch.ini' );
            
            $filename = $searchini->variable( $provider, 'Namespace' );
            echo $filename;
            // setup
            
            $handler = new xrowSOLRHandler();
            $manager = new ezcSearchEmbeddedManager();
            $session = new ezcSearchSession( $handler, $manager );
            
            $session->beginTransaction();
            
            $handler->deleteByurl( "meta_installation_id_ms:xrowsearch-" . "$filename" );
            
            $session->commit();
            
            echo "XML data has benn deleted !";
            
            $script->shutdown();
        }
    
    }
    else
    {
        echo "Please enter a keyword ('upload' or 'delete')!\n";
    }
}