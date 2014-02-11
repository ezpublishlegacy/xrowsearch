jQuery.extend({
    initOverlayBlocker : function(divID, callbackFunc){
        jQuery('body').append(jQuery('<div/>', {
            id: divID
        }));
        jQuery('#'+divID).css('display', 'block').click(function() {
            jQuery(this).css('display', 'none');
            if(typeof callbackFunc == 'function'){
                callbackFunc.call(this);
            }
        });
    }
});
jQuery.extend({
    loadAutocomplete : function(element, divautocomplete, height, callbackFunc){
        var position = element.position();
        var contentparent = jQuery('#'+divautocomplete+'-parent-content'),
            htmlcontent = contentparent.html(),
            content = '<div id="'+divautocomplete+'-content">'+htmlcontent+'</div>';
        jQuery('#'+divautocomplete).html(content).css({ display: "block", height: 0 }).animate({ height: height+'px' }, 600);
        if(typeof callbackFunc == 'function'){
            callbackFunc.call(this);
        }
    }
});