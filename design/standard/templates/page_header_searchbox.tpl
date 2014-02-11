{def $limit = 6
     $classArray = ezini('AutocompleteSettings', 'ClassIncludeArray', 'xrowsearch.ini')
     $height = 0
     $oneLI = 41
     $fvalide_nodes = array()}

{if ezini_hasvariable('AutocompleteSettings', 'OnFocusNodes', 'xrowsearch.ini')}
    {foreach ezini('AutocompleteSettings', 'OnFocusNodes', 'xrowsearch.ini') as $onFocusItem}
        {def $title = ezini(concat('OnFocusNode_', $onFocusItem), 'Title', 'xrowsearch.ini')
             $fnode_id = ezini(concat('OnFocusNode_', $onFocusItem), 'NodeID', 'xrowsearch.ini')
             $link_to_main = cond(ezini(concat('OnFocusNode_', $onFocusItem), 'LinkToMainNode', 'xrowsearch.ini')|eq('true'), true(), false())
             $ignore_visibility = cond(ezini(concat('OnFocusNode_', $onFocusItem), 'ShowHiddenNodes', 'xrowsearch.ini')|eq('true'), true(), false())
             $sourceNode = fetch('content', 'node', hash('node_id', $fnode_id))
             $children = fetch('content', 'list', hash('parent_node_id', $fnode_id,
                                                       'limit', $limit,
                                                       'class_filter_type', 'include',
                                                       'class_filter_array', $classArray,
                                                       'ignore_visibility', $ignore_visibility,
                                                       'sort_by', $sourceNode.sort_array))
             $fvalide_node = hash('title', $title, 'link_to_main', $link_to_main, 'children', $children)}
         {set $fvalide_nodes = $fvalide_nodes|append($fvalide_node)}
         {undef $title $fnode_id $link_to_main $ignore_visibility $sourceNode $children $fvalide_node}
    {/foreach}

    {if $fvalide_nodes|count|gt(0)}
    <div id="header-autocomplete-parent-content" style="display: none">
    {foreach $fvalide_nodes as $valide_node_item}
        {set $height = $height|sum( $oneLI )}
        <ul class="header-search-ul">
            <li class="header-search-li-headline">{$valide_node_item.title|i18n('extension/hannover')}</li>
            {foreach $valide_node_item.children as $childrenNode}
            {set $height = $height|sum( $oneLI )}
            <li class="header-search-li-item"><a href={cond($valide_node_item.link_to_main, $childrenNode.object.main_node|ezurlclean, $childrenNode|ezurlclean)}>{$childrenNode.name|wash()}</a></li>
            {/foreach}
        </ul>
    {/foreach}
    </div>
    {/if}
{/if}
<div id="searchbox">
    <form action={"/content/search"|ezurl} class="search_wifoe">
        <div class="box-content">
        {if $pagedata.is_edit}
        <input id="header-searchtext" name="SearchText" type="text" value="" size="12" disabled="disabled" />
        <button id="header-searchbutton" type="submit" class="button-lupe-wifoe" disabled="disabled"><span class="search-lupe"></span></button>
        {else}
        <div id="ezautocomplete">
            <input id="header-searchtext" name="SearchText" type="text" value="" size="12" autocomplete="off" />
            <button id="header-searchbutton" type="submit" class="button-lupe-wifoe"><span class="search-lupe"></span></button>
            <div id="header-autocomplete"></div>
            <div id="header-autocomplete-rs"></div>
        </div>
        {if eq( $ui_context, 'browse' )}
         <input name="Mode" type="hidden" value="browse" />
        {/if}
        {/if}
        </div>
    </form>
</div>

{if $pagedata.is_edit|not()}

{ezscript_require( array('ezjsc::jquery', 'xrowsearch.js') )}
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">
{literal}
var element = jQuery('#header-searchtext'),
    divautocomplete = 'header-autocomplete',
    divautocompleteRS = divautocomplete+'-rs',
    overlayblocker = 'overlay-blocker',
    minLength = {/literal}{ezini( 'AutoCompleteSettings', 'MinQueryLength', 'ezfind.ini' )}{literal},
    height = {/literal}{$height}{literal};

jQuery(function() {
    element.focus(function() {
        jQuery.initOverlayBlocker(overlayblocker, function(){
            jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
        });
        var countChars = element.val().length;
        if(countChars == 0 && jQuery('#'+divautocomplete).height() == 0)
            jQuery.loadAutocomplete(element, divautocomplete, height);
    });
    element.keyup(function() {
        jQuery.initOverlayBlocker(overlayblocker, function(){
            jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
        });
        var countChars = $(this).val().length;
        if(countChars >= minLength) {
            jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
            jQuery.ez('xrowsearch::autocomplete', {'term':element.val()} , function(data) {
                window.console.log(data);
                element.autocomplete({
                    source: data.content,
                    appendTo: '#'+divautocompleteRS,
                    select: function( event, ui ) {
                        window.console.log('drin');
                    }
                });
            });
        }
        else if(countChars == 0 && jQuery('#'+divautocomplete).height() == 0) {
            jQuery.loadAutocomplete(element, divautocomplete, height);
        }
    });
});
{/literal}
</script>
{/if}