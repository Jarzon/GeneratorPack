window.addEventListener('load', function () {
    if(lines.children.length === 0) {
        addLine('id', 'number', '', 'private');
        addLine('status', 'number', '0', 'private');
        addLine('created', 'datetime', '', 'private');
        addLine('updated', 'datetime', '', 'private');
        addLine('user_id', 'number', '', 'private');
    }
});