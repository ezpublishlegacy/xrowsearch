<?php

class xrowSearchServerFunctions extends ezjscServerFunctions
{
    public static function autocomplete()
    {
        $http = eZHTTPTool::instance();
        $searchResult = array();
        if ( $http->hasPostVariable( 'term' ) )
        {
            $searchStr = trim( $http->postVariable( 'term' ) );
            $result = array();
            $findINI = eZINI::instance( 'ezfind.ini' );
            $solrINI = eZINI::instance( 'solr.ini' );
            $contentINI = eZINI::instance( 'content.ini' );
            $siteINI = eZINI::instance();
            $xrowsearchINI = eZINI::instance( 'xrowsearch.ini' );
            $rootNode = $contentINI->variable( 'NodeSettings', 'RootNode' );
            $currentLanguage = $siteINI->variable( 'RegionalSettings', 'ContentObjectLocale' );
            $classIncludeArray = array();
            if($xrowsearchINI->hasVariable('AutocompleteSettings', 'ClassIncludeArray'))
            {
                foreach($xrowsearchINI->variable('AutocompleteSettings', 'ClassIncludeArray') as $classInclude)
                {
                    if(is_string($classInclude))
                    {
                        $classID = eZContentClass::classIDByIdentifier($classInclude);
                    }
                    elseif(is_numeric($classInclude))
                    {
                        $classID = (int)$classInclude;
                    }
                    if($classIncludeString != '')
                        $classIncludeString .= ' OR ';
                    $classIncludeString .= 'meta_contentclass_id_si:' . $classID;
                }
            }

            //$input = isset( $args[0] ) ? mb_strtolower( $args[0], 'UTF-8' ) : null;
            $limit = $findINI->variable( 'AutoCompleteSettings', 'Limit' );

            $facetField = $findINI->variable( 'AutoCompleteSettings', 'FacetField' );

            $params = array( 'q' => '*:*',
                             'json.nl' => 'arrarr',
                             'facet' => 'true',
                             'facet.field' => $facetField,
                             'facet.prefix' => $searchStr,
                             'facet.limit' => $limit,
                             'facet.mincount' => 1 );

            $fullSolrURI = $solrINI->variable( 'SolrBase', 'SearchServerURI' );
            // Autocomplete search should be done in current language and fallback languages
            $validLanguages = array_unique(
                array_merge(
                    $siteINI->variable( 'RegionalSettings', 'SiteLanguageList' ),
                    array( $currentLanguage )
                )
            );
            $params['fq'] = 'meta_path_si:' . $rootNode . ' AND meta_language_code_ms:(' . implode( ' OR ', $validLanguages ) . ')';
            if(isset($classIncludeString) && $classIncludeString != '')
            {
                $params['fq'] = $params['fq'] . ' AND ' . $classIncludeString;
            }

            $solrBase = new eZSolrBase( $fullSolrURI );
            $result = $solrBase->rawSolrRequest( '/select', $params, 'json');
            if(is_array($result['facet_counts']['facet_fields'][$facetField]) && count($result['facet_counts']['facet_fields'][$facetField]) > 0)
            {
                foreach($result['facet_counts']['facet_fields'][$facetField] as $fieldItem)
                {
                    $searchResult[] = $fieldItem[0];
                }
            }
        }
        return $searchResult;
    }
    
    public static function autocomplete_branchenbuch_what()
    {
        $http = eZHTTPTool::instance();
        $searchResult = array();
        $searchResult_c=array();
        $searchResult_e=array();
        if ( $http->hasPostVariable( 'term' ) )
        {
            $searchStr_c = trim( $http->postVariable( 'term' ) );
            $result_c = array();
            $searchText_c='attr_name_s:"'.$searchStr_c.'"^10';
            $params_item_c= array('query' => "ezf_df_text:(*".$searchStr_c."*)"." OR ".$searchText_c,
                                  'subtree_array' => array(580898,580899),
                                  'class_id' => array('frontpage'),
                                  'limit' => 15);
            $result_c = eZFunctionHandler::execute('ezfind', 'search', $params_item_c);
            
            $searchStr_e = trim( $http->postVariable( 'term' ) );
            $result_e = array();
            $searchText_e='attr_name_s:"'.$searchStr_e.'"^10';
            $params_item_e= array('query' => "ezf_df_text:(*".$searchStr_e."*)"." OR ".$searchText_e,
                                  'subtree_array' => array(580898,580899),
                                  'class_id' => array('localbusiness'),
                                  'filter' => array('or',array('attr_package_si:(1 2 3)')),
                                  'limit' => 15);
            $result_e = eZFunctionHandler::execute('ezfind', 'search', $params_item_e);

            if($result_c['SearchCount'] > 0)
            {
                foreach($result_c['SearchResult'] as $fieldItem)
                {
                    $searchResult_c[] = $fieldItem->ContentObject->Name;
                }
            }
            if($result_e['SearchCount'] > 0)
            {
                foreach($result_e['SearchResult'] as $fieldItem)
                {
                    $searchResult_e[] = $fieldItem->ContentObject->Name;
                }
            }
        }
        $searchResult=array_merge($searchResult_c,$searchResult_e);
        return $searchResult;
    }
    public static function autocomplete_branchenbuch_where()
    {
        $http = eZHTTPTool::instance();
        $searchResult = array();
        if ( $http->hasPostVariable( 'term' ) )
        {
            $searchStr = trim( $http->postVariable( 'term' ) );
            $result = array();
            $findINI = eZINI::instance( 'ezfind.ini' );
            $solrINI = eZINI::instance( 'solr.ini' );
            $contentINI = eZINI::instance( 'content.ini' );
            $siteINI = eZINI::instance();
            $xrowsearchINI = eZINI::instance( 'xrowsearch.ini' );
            $rootNode = $contentINI->variable( 'NodeSettings', 'RootNode' );
            $currentLanguage = $siteINI->variable( 'RegionalSettings', 'ContentObjectLocale' );
            $classIncludeArray = array();
            if($xrowsearchINI->hasVariable('AutocompleteSettings', 'ClassIncludeArray'))
            {
                foreach($xrowsearchINI->variable('AutocompleteSettings', 'ClassIncludeArray') as $classInclude)
                {
                    if(is_string($classInclude))
                    {
                        $classID = eZContentClass::classIDByIdentifier($classInclude);
                    }
                    elseif(is_numeric($classInclude))
                    {
                        $classID = (int)$classInclude;
                    }
                    if($classIncludeString != '')
                        $classIncludeString .= ' OR ';
                    $classIncludeString .= 'meta_contentclass_id_si:' . $classID;
                }
            }
    
            $limit = $findINI->variable( 'AutoCompleteSettings', 'Limit' );
    
            $facetField = 'subattr_location___cityfacet____ms';
    
            $params = array( 'q' => '*:*',
                    'json.nl' => 'arrarr',
                    'facet' => 'true',
                    'facet.field' => $facetField,
                    'facet.prefix' => $searchStr,
                    'facet.limit' => $limit,
                    'facet.mincount' => 1 );
    
            $fullSolrURI = $solrINI->variable( 'SolrBase', 'SearchServerURI' );
            // Autocomplete search should be done in current language and fallback languages
            $validLanguages = array_unique(
                    array_merge(
                            $siteINI->variable( 'RegionalSettings', 'SiteLanguageList' ),
                            array( $currentLanguage )
                    )
            );
            $params['fq'] = 'meta_path_si:' . $rootNode . ' AND meta_language_code_ms:(' . implode( ' OR ', $validLanguages ) . ')';
            if(isset($classIncludeString) && $classIncludeString != '')
            {
                $params['fq'] = $params['fq'] . ' AND ' . $classIncludeString;
            }
    
            $solrBase = new eZSolrBase( $fullSolrURI );
            $result = $solrBase->rawSolrRequest( '/select', $params, 'json');
            if(is_array($result['facet_counts']['facet_fields'][$facetField]) && count($result['facet_counts']['facet_fields'][$facetField]) > 0)
            {
                foreach($result['facet_counts']['facet_fields'][$facetField] as $fieldItem)
                {
                    $searchResult[] = $fieldItem[0];
                }
            }
        }
        return $searchResult;
    }
}