
function exportStats() {
    var url = window.location.href +"&export_stats=1";
    window.location = url; 
}

function exportAsDoc(tag) {
    var url = window.location.href +"&"+tag.trim();
    window.location = url; 
}
