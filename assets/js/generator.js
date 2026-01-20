let lines = document.querySelector('#lines');

function generateRow(name = null, type = null, defaultValue = null, isPublic = null) {
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

    let actions = document.createElement('td');
    let moveup = document.createElement('div');
    moveup.innerHTML = '<img alt="MoveUp" src="/img/arrow_up.svg">';
    moveup.onclick = function() {
        baseForm.parentElement.insertBefore(baseForm, baseForm.previousSibling);
    };

    let movedown = document.createElement('div');
    movedown.innerHTML = '<img alt="MoveDown" src="/img/arrow_down.svg">';
    movedown.onclick = function() {
        baseForm.parentElement.insertBefore(baseForm.nextSibling, baseForm);
    };

    let remove = document.createElement('div');
    remove.innerHTML = '<img alt="Remove" src="/img/delete.svg">';
    remove.onclick = function() {
        lines.removeChild(baseForm);
    };

    actions.append(moveup);
    actions.append(movedown);
    actions.append(remove);

    baseForm.appendChild(actions);

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

    return baseForm;
}

function addLine(name = null, type = null, defaultValue = null, isPublic = null) {
    let row = generateRow(name, type, defaultValue, isPublic);

    lines.appendChild(row);
}

function addNewLine() {
    let row = generateRow();

    if(lines.children.length >= 1) {
        lines.children[1].after(row);
    } else {
        lines.appendChild(row);
    }
}

window.addEventListener('load', function () {
    document.querySelector('#add').addEventListener('click', function (e) {
        addNewLine();
    });

    document.querySelector('#pack_name').addEventListener('change', function (e) {
        document.querySelector('#entity_name').setAttribute('value', document.querySelector('#pack_name').value);
    });

    document.querySelector('#entity_name').addEventListener('click', function (e) {
        document.querySelector('#entity_name').setAttribute('value', '');
    });
});
