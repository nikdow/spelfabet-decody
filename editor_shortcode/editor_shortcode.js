function update(){
    const elem = document.forms['decody_editor'].elements['editor'];
    jQuery('#results').innerHTML = "<span class='warn'>Warning</span><span>No style</span>"  +  elem.value
}