let lines = document.querySelector('#lines');
let draggedRow;
let draggedTextInputRow = false;

function generateRow(status = null, name = null, type = null, min = null, max = null, defaultValue = null, isPublic = null) {
    let baseRow = document.querySelector('#baseForm').cloneNode(true);
    let dragCell = baseRow.querySelector('.drag');
    let typeSelect = baseRow.querySelector('.type');
    let valueInput = baseRow.querySelector('.default');
    let minInput = baseRow.querySelector('.min');
    let maxInput = baseRow.querySelector('.max');
    let statusInput = baseRow.querySelector('.status');
    let dragIcon = document.createElement('img');
    dragIcon.src = '/img/verification.svg';

    baseRow.addEventListener('mousedown', function (e) {
        console.log(e)
        if(e.target && e.target.nodeName === 'INPUT') {
            baseRow.draggable = false;
            draggedTextInputRow = baseRow;
        }
    });

    baseRow.addEventListener('dragstart', function (e) {
        if(!baseRow.draggable) return;
        if(!dragCell.matches(':hover')) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        baseRow.style = 'background: #000;';
        draggedRow = baseRow;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setDragImage(dragIcon, -10, -10);
        // Store the ID of the dragged row, for example
        e.dataTransfer.setData('text/plain', e.target.id);
    })

    baseRow.addEventListener('dragover', function (e) {
        if(draggedTextInputRow) {
            draggedTextInputRow.draggable = true;
            draggedTextInputRow = false;
        } else {
            e.preventDefault();
            if (draggedRow && baseRow.tagName === 'TR' && baseRow.parentNode !== draggedRow) {
                baseRow.parentNode.insertBefore(draggedRow, baseRow.rowIndex < draggedRow.rowIndex? baseRow : baseRow.nextSibling);
            }
        }
    })

    baseRow.addEventListener('dragend', function (e) {
        if(!draggedRow) return;
        draggedRow.style = '';
    })

    baseRow.addEventListener('change', function () {
        if(statusInput.value !== '1') statusInput.value = '2';
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

    baseRow.id = '';

    let actions = document.createElement('td');
    actions.className = 'actions';

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

    actions.append(remove);

    baseRow.appendChild(actions);

    if(name !== null) {
        baseRow.querySelector('.name').value = name;
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
        baseRow.querySelector('.public').value = isPublic;
    }

    if(status !== null) {
        statusInput.value = status;
    }

    return baseRow;
}

function addLine(name = null, type = null, min = null, max = null, defaultValue = null, isPublic = null, status = null) {
    let row = generateRow(status, name, type, min, max, defaultValue, isPublic);

    lines.appendChild(row);
}

let lineCounter = 0;

function addNewLine() {
    let row = generateRow(1, null, null, null, null, null, 'public');

    if(lines.children.length < 1 || !isNew) {
        console.log(lineCounter)
        lines.appendChild(row);
    } else {
        lines.children[lineCounter].after(row);
        lineCounter++;
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
