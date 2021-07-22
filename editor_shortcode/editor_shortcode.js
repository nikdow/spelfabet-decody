function update(){
    const elem = document.forms['decody_editor'].elements['editor'];
    const data = {'action': 'parse_text', 'text': elem.value};
    jQuery.post( ajax_object.ajax_url, data, function( response ){
        let display = "";
        response.output.forEach( word => display += "<span class='" + (word.warn ? 'warn' : '') + "'>" + word.word + "</span> " );
        jQuery('#results').html(display);
    }, 'json');
}