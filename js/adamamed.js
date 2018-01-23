
function exportStats() {
    var url = window.location.href +"&export_stats=1";
    window.location = url; 
}

function exportAsDoc(tag) {
    var url = window.location.href +"&"+tag.trim();
    window.location = url; 
}

function confirmDeleteSubmit(email) {
    return confirm("האם למחוק את ההזמנה של "+email);
}

function confirmUpdateSubmit(email) {
    return confirm("האם לעדכן את ההזמנה של "+email);
}