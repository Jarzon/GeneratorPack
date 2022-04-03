let lines = document.querySelector('#lines');

function addLine(name = null, type = null, defaultValue = null, isPublic = null) {
    let baseForm = document.querySelector('#baseForm').cloneNode(true);
    let typeSelect = baseForm.querySelector('#type');
    let value = baseForm.querySelector('#default');

    typeSelect.addEventListener('change', function () {
        let type = this.value;

        if (type === 'number') {
            value.setAttribute('type', 'number');
            value.value = '0';
        }
        else if (type === 'datetime' || type === 'date') {
            value.setAttribute('type', 'text');
            value.value = 'CURRENT_TIMESTAMP';
        } else {
            value.setAttribute('type', 'text');
            value.value = '';
        }
    });

    baseForm.id = '';

    let remove = document.createElement('td');
    remove.innerHTML = '<img alt="Remove" src="/img/delete.svg">';
    remove.onclick = function() {
        lines.removeChild(baseForm);
    };

    if(name !== null) {
        baseForm.querySelector('#name').value = name;
    }

    if(type !== null) {
        baseForm.querySelector('#type').value = type;
    }

    if(defaultValue !== null) {
        baseForm.querySelector('#default').value = defaultValue;
    }

    if(isPublic !== null) {
        baseForm.querySelector('#public').value = isPublic;
    }

    baseForm.appendChild(remove);

    lines.appendChild(baseForm);
}

window.addEventListener('load', function () {
    document.querySelector('#add').addEventListener('click', function (e) {
        addLine();
    });

    document.querySelector('#pack_name').addEventListener('change', function (e) {
        document.querySelector('#entity_name').setAttribute('value', document.querySelector('#pack_name').value);
    });

    document.querySelector('#entity_name').addEventListener('click', function (e) {
        document.querySelector('#entity_name').setAttribute('value', '');
    });

    addLine('id', 'number', '', 'private');
    addLine('user_id', 'number', '', 'private');
    addLine('status', 'number', '0', 'private');
});
