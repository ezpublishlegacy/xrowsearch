{def $limit = 6
     $classArray = ezini('AutocompleteSettings', 'ClassIncludeArray', 'xrowsearch.ini')
     $height = 0
     $oneLI = 41
     $fvalide_nodes = array()}

{if ezini_hasvariable('AutocompleteSettings', 'OnFocusNodes', 'xrowsearch.ini')}
    {foreach ezini('AutocompleteSettings', 'OnFocusNodes', 'xrowsearch.ini') as $onFocusItem}
        {def $fnode_id = ezini(concat('OnFocusNode_', $onFocusItem), 'NodeID', 'xrowsearch.ini')
             $ignore_visibility = cond(ezini(concat('OnFocusNode_', $onFocusItem), 'ShowHiddenNodes', 'xrowsearch.ini')|eq('true'), true(), false())
             $sourceNode = fetch('content', 'node', hash('node_id', $fnode_id))}
        {if $sourceNode}
            {def $children = fetch('content', 'list', hash('parent_node_id', $fnode_id,
                                                           'limit', $limit,
                                                           'class_filter_type', 'include',
                                                           'class_filter_array', $classArray,
                                                           'ignore_visibility', $ignore_visibility,
                                                           'sort_by', $sourceNode.sort_array))}
            {if $children|count|gt(0)}
                {def $fvalide_node = hash('title', $sourceNode.name|wash(), 'children', $children)}
                {set $fvalide_nodes = $fvalide_nodes|append($fvalide_node)}
                {undef $fvalide_node}
            {/if}
            {undef $children}
        {/if}
        {undef $fnode_id $ignore_visibility $sourceNode}
    {/foreach}

    {if $fvalide_nodes|count|gt(0)}
    <div id="header-autocomplete-parent-content" style="display: none">
    {foreach $fvalide_nodes as $valide_node_item}
        {set $height = $height|sum( $oneLI )}
        <ul class="header-search-ul">
            <li class="header-search-li-headline">{$valide_node_item.title}</li>
            {foreach $valide_node_item.children as $childrenNode}
            {set $height = $height|sum( $oneLI )}
            <li class="header-search-li-item"><a href={cond($childrenNode.is_main|not(), $childrenNode.object.main_node|ezurlclean, $childrenNode|ezurlclean)}>{$childrenNode.name|wash()}</a></li>
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
        <div id="header-ezautocomplete">
            <input id="header-searchtext" name="SearchText" type="text" value="" size="12" autocomplete="off" data-divautocomplete="header-autocomplete"
                                                                                                              data-overlayblocker="overlay-blocker"
                                                                                                              data-loaddefaultcontentcounter="0"
                                                                                                              data-height="{$height}"
                                                                                                              data-appendto="#header-autocomplete-rs"
                                                                                                              data-minlength="{ezini( 'AutoCompleteSettings', 'MinQueryLength', 'ezfind.ini' )}"
                                                                                                              data-location="{'content/search?SearchText='|ezurl('no', 'full')}"
                                                                                                              data-autocompleteenabled="{cond($pagedata.is_edit|not(), true, false)}" />
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