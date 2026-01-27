window.addEventListener('load', function () {
    if(lines.children.length === 0) {
        addLine('id', 'number', 0, 0, '', 'private', 1);
        addLine('status', 'number', 0, 0,'0', 'private', 1);
        addLine('created', 'datetime', 0, 0,'CURRENT_TIMESTAMP', 'private', 1);
        addLine('updated', 'datetime', 0, 0,'CURRENT_TIMESTAMP', 'private', 1);
        addLine('user_id', 'number', 0, 0,'', 'private', 1);
    }
});