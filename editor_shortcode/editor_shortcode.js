function update(){
    alert("update clicked!");
    const elem = document.forms['decode_editor'].elements['editor'];
    jQuery('#results').innerHTML = "<span class='warn'>Warning</span><span>No style</span>"  +  elem.value
}