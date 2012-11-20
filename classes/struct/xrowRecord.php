<?php

class xrowRecord implements ezcBasePersistable, ezcSearchDefinitionProvider
{
    public $meta_key;
    public $meta_guid;
    public $meta_name;
    public $meta_main_url_alias;
    public $text;
    public $meta_installation_id;
    public $meta_language_code;
    public $meta_anon_access;
    public $meta_is_invisible;
    public $meta_class_identifier;
    public $meta_section_id;
    const VIRTUAL_CLASSIDENTIFIER = "external";
    function __construct( $installation_id = false, $language = 'ger-DE', $meta_guid = null, $meta_name = null, $meta_main_url_alias = null, $text = null, $keywords = null )
    {
        if ( $meta_guid === null )
        {
            $this->meta_guid = uniqid();
        }
        else
        {
            $this->meta_guid = $meta_guid;
        }
        $this->meta_name = $meta_name;
        $this->meta_main_url_alias = $meta_main_url_alias;
        $this->meta_key = $keywords;
        $this->text = $text;
        $this->meta_is_invisible = false;
        $this->meta_section_id = 1;
        $this->meta_language_code = $language;
        if ( $installation_id === null or $installation_id === false )
        {
            $this->meta_installation_id = eZSolr::installationID();
            $this->meta_anon_access = true;
        }
        else
        {
            $this->meta_installation_id = $installation_id;
            $this->meta_anon_access = true;
        }
        $this->meta_class_identifier = self::VIRTUAL_CLASSIDENTIFIER;
    }

    static public function getDefinition()
    {
        $n = new ezcSearchDocumentDefinition( 'xrowSearch' );
        
        $n->idProperty = 'meta_guid';
        $n->fields['meta_is_invisible'] = new ezcSearchDefinitionDocumentField( 'meta_is_invisible', ezcSearchDocumentDefinition::BOOLEAN, 7, true, false, true );
        $n->fields['meta_anon_access'] = new ezcSearchDefinitionDocumentField( 'meta_anon_access', ezcSearchDocumentDefinition::BOOLEAN, 7, true, false, true );
        $n->fields['meta_installation_id'] = new ezcSearchDefinitionDocumentField( 'meta_installation_id', xrowSOLRHandler::MS );
        $n->fields['meta_section_id'] = new ezcSearchDefinitionDocumentField( 'meta_section_id', xrowSOLRHandler::SI );
        $n->fields['meta_class_identifier'] = new ezcSearchDefinitionDocumentField( 'meta_class_identifier', xrowSOLRHandler::MS );
        $n->fields['meta_language_code'] = new ezcSearchDefinitionDocumentField( 'meta_language_code', xrowSOLRHandler::MS );
        $n->fields['meta_guid'] = new ezcSearchDefinitionDocumentField( 'meta_guid', xrowSOLRHandler::MS );
        $n->fields['meta_key'] = new ezcSearchDefinitionDocumentField( 'meta_key', xrowSOLRHandler::KEY );
        $n->fields['meta_name'] = new ezcSearchDefinitionDocumentField( 'meta_name', ezcSearchDocumentDefinition::TEXT, 2, true, false, true );
        $n->fields['meta_main_url_alias'] = new ezcSearchDefinitionDocumentField( 'meta_main_url_alias', xrowSOLRHandler::MS );
        $n->fields['text'] = new ezcSearchDefinitionDocumentField( 'text', ezcSearchDocumentDefinition::TEXT, 1, false, false, true );
        return $n;
    }

    function getState()
    {
        $state = array( 
        
            'meta_installation_id' => $this->meta_installation_id ,
            'meta_section_id' => $this->meta_section_id ,
            'meta_language_code' => $this->meta_language_code , 
            'meta_guid' => $this->meta_guid,
            'meta_class_identifier' => $this->meta_class_identifier ,
            'meta_is_invisible' => $this->meta_is_invisible ,
            'meta_anon_access' => $this->meta_anon_access ,
            'meta_key' => $this->meta_key , 
            'meta_name' => $this->meta_name , 
            'meta_main_url_alias' => $this->meta_main_url_alias , 
            'text' => $this->text 
        );
        return $state;
    }

    public function setState( array $state )
    {
        foreach ( $state as $key => $value )
        {
            $this->$key = $value;
        }
    }
}