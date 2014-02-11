function initOverlayBlocker(divID, callbackFunc) {
    jQuery('body').append(jQuery('<div/>', {
        id: divID
    }));
    jQuery('#'+id).css('display', 'block').click(function() {
        jQuery(this).css('display', 'none');
        if(typeof callback != 'undefined')
            eval(callbackFunc+'();');
        
    });
};
function loadAutocomplete(element) {
    var position = element.position();
    var contentparent = jQuery('#'+divautocomplete+'-parent-content'),
        htmlcontent = contentparent.html(),
        content = '<div id="'+divautocomplete+'-content">'+htmlcontent+'</div>';
    jQuery('#'+divautocomplete).html(content).css({ display: "block", height: 0 }).animate({ height: height+'px' }, 600);
};