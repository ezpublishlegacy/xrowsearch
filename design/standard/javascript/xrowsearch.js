jQuery(document).ready(function() {
    if(jQuery('#header-searchtext').length > 0) {
        var headelement = jQuery('#header-searchtext'),
            autocompleteEnabled = headelement.data('autocompleteenabled');
        if(autocompleteEnabled == true) {
            var divautocomplete = headelement.data('divautocomplete'),
                overlayblocker = headelement.data('overlayblocker'),
                loadDefaultContentCounter = headelement.data('loaddefaultcontentcounter'),
                height = headelement.data('height');
            jQuery.autocompleteOnFocus(headelement, divautocomplete, overlayblocker, loadDefaultContentCounter, height);
            jQuery.autocompleteOnKeyup(headelement, divautocomplete, overlayblocker, loadDefaultContentCounter, height, 'xrowsearch::autocomplete');
        }
    }
    if(jQuery('#Search').length > 0) {
        jQuery.initAutocomplete(jQuery('#Search'), 'xrowsearch::autocomplete');
    }
});

jQuery.extend({
    initOverlayBlocker : function(divID, callbackFunc){
        if(jQuery('#'+divID).length == 0) {
            jQuery('body').append(jQuery('<div/>', {
                id: divID
            }));
        }
        jQuery('#'+divID).css('display', 'block').click(function() {
            jQuery(this).css('display', 'none');
            if(typeof callbackFunc == 'function'){
                callbackFunc.call(this);
            }
        });
    },
    loadAutocomplete : function(element, divautocomplete, height, callbackFunc){
        var position = element.position();
        var contentparent = jQuery('#'+divautocomplete+'-parent-content'),
            htmlcontent = contentparent.html(),
            content = '<div id="'+divautocomplete+'-content">'+htmlcontent+'</div>';
        jQuery('#'+divautocomplete).html(content).css({ display: "block", height: 0 }).animate({ height: height+'px' }, 600);
        if(typeof callbackFunc == 'function'){
            callbackFunc.call(this);
        }
    },
    autocompleteOnFocus : function(element, divautocomplete, overlayblocker, loadDefaultContentCounter, height){
        element.focus(function() {
            jQuery.initOverlayBlocker(overlayblocker, function(){
                jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
            });
            var countChars = element.val().length;
            if(countChars == loadDefaultContentCounter && jQuery('#'+divautocomplete).height() == 0) {
                jQuery.loadAutocomplete(element, divautocomplete, height);
            }
        });
    },
    autocompleteOnKeyup : function(element, divautocomplete, overlayblocker, loadDefaultContentCounter, height, functionName){
        element.keyup(function() {
            jQuery.initOverlayBlocker(overlayblocker, function(){
                jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
            });
            var countChars = $(this).val().length;
            if(countChars > loadDefaultContentCounter) {
                jQuery('#'+divautocomplete).html('').animate({ height: '0px' }, 600);
            }
            if(countChars > loadDefaultContentCounter) {
                jQuery.initAutocomplete(element, functionName);
            }
            else if(countChars == loadDefaultContentCounter && jQuery('#'+divautocomplete).height() == 0) {
                jQuery.loadAutocomplete(element, divautocomplete, height);
            }
        });
    },
    initAutocomplete : function(element, functionName, afterSelectActionFunction) {
        if(typeof element.data('appendto') != 'undefined') {
            var autoAppendto = element.data('appendto'),
                autoMinLength = 2;
            if(typeof element.data('minlength') != 'undefined')
                autoMinLength = element.data('minlength');
            element.autocomplete({
                source: function(request , response){
                    jQuery.ez(functionName, {'term':request.term}, function(data) {
                        response(jQuery.map(data.content, function(item) {
                            return {
                                label: item,
                                value: item
                            }
                        }));
                    });
                },
                minLength: autoMinLength,
                appendTo: autoAppendto,
                select: function(event, ui) {
                    if(typeof element.data('location') != 'undefined')
                    {
                        if(afterSelectActionFunction == 'branchenbuch_submit_function')
                        {
                            if( $(this).attr('id') =="Search_What")
                            {
                                window.location.href = element.data('location')+encodeURIComponent(ui.item.value)+'&Where='+$('#Search_Where').val()+'&SubTreeArray='+$('#hidden_node_id').val();
                            }else if( $(this).attr('id') =="Search_Where")
                            {
                                window.location.href = element.data('location')+encodeURIComponent(ui.item.value)+'&What='+$('#Search_What').val()+'&SubTreeArray='+$('#hidden_node_id').val();
                            }
                        }
                        else
                        {
                            window.location.href = element.data('location')+encodeURIComponent(ui.item.value);
                        }
                    }
                    else
                        return false;
                }
            });
        }
    }
});