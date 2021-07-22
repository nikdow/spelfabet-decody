function update(){
    alert("update clicked!");
    const form = document.forms['decody_editor'];
    const textarea = form.elements.editor;
    textarea.value = "<span style='color: red'>Red span</span><span>No style</span>";
}