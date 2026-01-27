window.addEventListener('load', function () {
    if(lines.children.length === 0) {
        addLine('id', 'number', '', 'private', 1);
        addLine('status', 'number', '0', 'private', 1);
        addLine('created', 'datetime', 'CURRENT_TIMESTAMP', 'private', 1);
        addLine('updated', 'datetime', 'CURRENT_TIMESTAMP', 'private', 1);
        addLine('user_id', 'number', '', 'private', 1);
    }
});