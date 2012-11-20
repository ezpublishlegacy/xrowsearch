<?php
class xrowExternalSearchProvider
{

    final static function getExternalProviders()
    {
        $plugins = array();
        $searchini = eZINI::instance( 'xrowsearch.ini' );
        $list = $searchini->variable( 'Settings', 'DataProviderList' );
        foreach ( $list as $item )
        {  
            $pluginname = $searchini->variable( $item, 'Plugin' );
           
            $params = array( 
                'ExternalURL' => $searchini->variable( $item, 'ExternalURL' ),
                'Namespace' => $item
            );
            
           $plugins[] = new $pluginname( $params ); 
        }
        return $plugins;
    }

    final static function getExternalProvider( $name )
    {
        $searchini = eZINI::instance( 'xrowsearch.ini' );
        $list = $searchini->variable( 'Settings', 'DataProviderList' );
        if ( in_array( $name, $list ) )
        {
            $pluginname = $searchini->variable( $name, 'Plugin' );
            $params = array( 
                'ExternalURL' => $searchini->variable( $name, 'ExternalURL' ),
                'Namespace' =>$name
            );
            return new $pluginname( $params );
        }
    }
}