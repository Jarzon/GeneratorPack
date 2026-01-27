let lines = document.querySelector('#lines');

function generateRow(status = null, name = null, type = null, min = null, max = null, defaultValue = null, isPublic = null) {
    let baseForm = document.querySelector('#baseForm').cloneNode(true);
    let typeSelect = baseForm.querySelector('.type');
    let valueInput = baseForm.querySelector('.default');
    let minInput = baseForm.querySelector('.min');
    let maxInput = baseForm.querySelector('.max');
    let statusInput = baseForm.querySelector('.status');

    baseForm.addEventListener('change', function () {
        statusInput.value = '2';
    });

    typeSelect.addEventListener('change', function () {
        let type = this.value;

        if (type === 'number') {
            valueInput.setAttribute('type', 'number');
            valueInput.value = '0';
        }
        else if (type === 'datetime' || type === 'date') {
            valueInput.setAttribute('type', 'text');
            valueInput.value = 'CURRENT_TIMESTAMP';
        } else {
            valueInput.setAttribute('type', 'text');
            valueInput.value = '';
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
        if(statusInput.value === '-1') {
            remove.parentElement.parentElement.className = '';
            statusInput.value = '2';
        }
        else {
            remove.parentElement.parentElement.className = 'deleted';
            statusInput.value = '-1';
        }
    };

    actions.append(moveup);
    actions.append(movedown);
    actions.append(remove);

    baseForm.appendChild(actions);

    if(name !== null) {
        baseForm.querySelector('.name').value = name;
    }

    if(type !== null) {
        typeSelect.value = type;
    }

    if(min !== null) {
        minInput.value = min;
    }

    if(max !== null) {
        maxInput.value = max;
    }

    if(defaultValue !== null) {
        valueInput.value = defaultValue;
    }

    if(isPublic !== null) {
        baseForm.querySelector('.public').value = isPublic;
    }

    if(status !== null) {
        statusInput.value = status;
    }

    return baseForm;
}

function addLine(name = null, type = null, min = null, max = null, defaultValue = null, isPublic = null, status = null) {
    let row = generateRow(status, name, type, min, max, defaultValue, isPublic);

    lines.appendChild(row);
}

let lineCounter = 0;

function addNewLine() {
    let row = generateRow(1);

    if(lines.children.length >= 1) {
        lines.children[lineCounter].after(row);
        lineCounter++;
    } else {
        lines.appendChild(row);
    }
}

window.addEventListener('load', function () {
    document.querySelector('#add').addEventListener('click', function (e) {
        addNewLine();
    });

    let packName = document.querySelector('#pack_name');
    if(packName) {
        packName.addEventListener('change', function (e) {
            document.querySelector('#entity_name').setAttribute('value', document.querySelector('#pack_name').value);
        });
    }

    let entityName = document.querySelector('#entity_name');
    if(entityName) {
        entityName.addEventListener('click', function (e) {
            entityName.setAttribute('value', '');
        });
    }
});
