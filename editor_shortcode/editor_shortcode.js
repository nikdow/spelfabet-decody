function update(){
    const elem = document.forms['decody_editor'].elements['editor'];
    const data = {'action': 'parse_text', 'text': elem.value};
    jQuery.post( ajax_object.ajax_url, data, function( response ){
        jQuery('#results').html(response);
    })
}