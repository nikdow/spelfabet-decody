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
        let counts = {
            warn: 0,
            noLevel : 0,
            isHFW: 0,
        };
        let total = 0;
        response.output.forEach( word => {
            let classes = [];
            total++;
            if(target_level && word.level !== false && word.level > target_level) {
                classes.push('warn');
                counts.warn++;
            }
            if( word.level === false ) {
                classes.push('no-level');
                counts.noLevel++;
            }
            if( word.isHFW ) {
                classes.push('hfw');
                counts.isHFW++;
            }
            display += "<span class='" + classes.join(' ') + "' title='" + word.n + "'>" + word.word + "</span> ";
        });
        $('#decody_output').html(display);
        $('#no-level').html((counts.noLevel/total*100).toFixed(0) + '%');
        $('#warn').html((counts.warn/total*100).toFixed(0) + '%');
        $('#hfw').html((counts.isHFW/total*100).toFixed(0) + '%');
    }, 'json');
}