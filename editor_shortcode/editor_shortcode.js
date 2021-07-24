function update(){
    const $ = jQuery;
    const target_level = $("#target_level").val();
    const data = {
        'action': 'parse_text',
        'text':$("#editor").val(),
        'schema':$("#schema option:selected").val(),
        'target_level': target_level
    };
    const required = {'text':'Enter or paste in some text', 'schema':'select a teaching method (schema)'};
    for( const key of Object.keys( required ) ){
        if( ! data[key] ) {
            alert(required[key]);
            return;
        }
    }
    $.post( ajax_object.ajax_url, data, function( response ){
        let display = "";
        response.output.forEach( word => display += "<span class='" + ( target_level && word.level > target_level ? 'warn' : '') + "'>" + word.word + "</span> " );
        $('#decody_results').html(display);
    }, 'json');
}