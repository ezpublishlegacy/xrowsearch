<?php

abstract class ExternalSearchXMLPlugin
{

    function __construct( $params )
    {
        if ( $params )
        {
            $this->params = $params;
        }
        if ( $this->params['Namespace'] )
        {
            $this->namespace = 'xrowsearch-'. $this->params['Namespace'];
        }
        
        if ( $this->params['Namespace']==='schlueterschexml' )
        {
        	$this->params['ExternalURL'] = $this->params['ExternalURL'] .'_'. date('d.m.Y').'.xml';
        }
    }
    /*
     * @return Records Returns Object Records
     */
    
    abstract protected function structureXml( SimpleXMLElement $xml);
    
    private $xml;
    private $params;
    public $namespace;

    public function load()
    {  
        $a=get_object_vars($this);
        $fault_url = $a['params']['ExternalURL'];
     
        $file_in = file_get_contents( $this->params['ExternalURL'] );
        if ( $file_in === false && function_exists( 'curl_init' ) )
        {  
            $ch = curl_init();
            $timeout = 5;
            curl_setopt( $ch, CURLOPT_URL, $this->params['ExternalURL'] );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
            $file_in = curl_exec( $ch );
            curl_close( $ch );
            
            throw new Exception( "Failed to read XML file from " . $fault_url );
        }
        else
        {
            
            $xml = simplexml_load_string( $file_in, 'SimpleXMLElement', LIBXML_NOCDATA );
            
            if ( $xml === false )
            {
                throw new Exception( "Failed to read XML file from " . $fault_url );
            }
            return $this->structureXML( $xml );
        }
    }
    
    public function delete()
    {
        $handler = new xrowSOLRHandler();
        $manager = new ezcSearchEmbeddedManager();
        $session = new ezcSearchSession( $handler, $manager );
        
        $session->beginTransaction();
        
        $handler->deleteByurl( "meta_installation_id_ms:xrowsearch-" . $this->params['Namespace']);
        
        $session->commit();
        
        sleep( 3 );
    }
}