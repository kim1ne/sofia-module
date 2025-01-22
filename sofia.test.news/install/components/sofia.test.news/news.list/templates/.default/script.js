BX.ready(function () {
    let trRows = document.querySelectorAll('#sofia tr');
    let menu = document.querySelector('.menu_sofia')

    trRows.forEach(tr => {
        tr.addEventListener('click', (e) => {
            let id = tr.dataset.idRow;

            if (!id) {
                return;
            }

            if (menu) {
                clearButtonMenu();

                let buttons = menuButton(id);

                buttons.forEach(btn => {
                    BX.append(btn, menu)
                })
            }
        })
    })
});

function menuButton(id) {
    return [
        BX.create('span', {
            text: 'Удалить',
            events: {
                click: () => {
                    deleteRow(id)
                }
            }
        }),
        BX.create('span', {
            text: 'Редактировать',
            events: {
                click: () => {
                    editRow(id)
                }
            }
        }),
    ]
}

function create() {
    let id = 1;
    let popup = BX.PopupWindowManager.create('create-row-' + id, null, {
        darkMode: true,
        content: getCreateForm(id),
        width: 300,
        height: 300,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Создать',
                events: {
                    click: () => {
                        let form = getForm(id)

                        if (form) {
                            let inputs = form.querySelectorAll('input');

                            let data = {};

                            inputs.forEach(input => {
                                data[input.name] = input.value
                            });

                            BX.ajax.runComponentAction('sofia.test.news:news.list', 'create', {
                                mode: 'class',
                                data: data
                            }).then((response) => {
                                window.location.reload();
                            }).catch((response) => {
                                console.log(response)
                            })
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                events: {
                    click: () => {
                        popup.close();
                    }
                }
            }),
        ]
    });

    popup.show();
}

function editRow(id) {
    let row = getRow(id);

    if (!row) {
        return;
    }

    let popup = BX.PopupWindowManager.create('edit-row-' + id, null, {
        darkMode: true,
        content: getEditForm(row),
        width: 300,
        height: 300,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Сохранить',
                events: {
                    click: () => {
                        let form = getForm(id);

                        if (form) {
                            let inputs = form.querySelectorAll('input');
                            let data = {};

                            data['ID'] = id;

                            inputs.forEach(input => {
                                data[input.name] = input.value
                            });

                            console.log(data)

                            BX.ajax.runComponentAction('sofia.test.news:news.list', 'edit', {
                                mode: 'class',
                                data: data
                            }).then((response) => {
                                let data = response.data;

                                let tds = row.querySelectorAll('td');

                                tds.forEach(td => {
                                    let field = td.dataset.field;
                                    let value = data[field];
                                    td.textContent = value;
                                });

                                popup.destroy();
                                clearButtonMenu();

                            }).catch((response) => {
                                console.log(response)
                            })
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                events: {
                    click: () => {
                        popup.destroy();
                        clearButtonMenu()
                    }
                }
            }),
        ]
    });

    popup.show()
}

function getEditForm(row) {
    if (!row) {
        return;
    }

    let tds = row.querySelectorAll('td');

    let children = [];

    tds.forEach(td => {
        let field = td.dataset.field;

        children.push(BX.create('input', {
            attrs: {
                type: 'text',
                placeholder: field,
                name: field,
                value: td.textContent
            }
        }));
    });

    let id = row.dataset.idRow;

    return BX.create('form', {
        children: children,
        props: {
            className: 'form-' + id
        }
    })
}

function getCreateForm(id) {
    return BX.create('form', {
        props: {
            className: 'form-' + id
        },
        children: [
            BX.create('input', {
                attrs: {
                    type: 'text',
                    placeholder: 'TITLE',
                    name: 'TITLE',
                }
            }),
            BX.create('input', {
                attrs: {
                    type: 'text',
                    placeholder: 'DESCRIPTION',
                    name: 'DESCRIPTION',
                }
            }),
            BX.create('input', {
                attrs: {
                    type: 'text',
                    placeholder: 'AUTHOR_ID',
                    name: 'AUTHOR_ID',
                }
            }),
        ]
    })
}

function getForm(id) {
    return document.querySelector('.form-' + id)
}

function getRow(id) {
    return document.querySelector('[data-id-row="' + id + '"]');
}

function deleteRow(id) {
    BX.ajax.runComponentAction('sofia.test.news:news.list', 'delete', {
        mode: 'class',
        data: {
            id: id
        }
    }).then((response) => {
        let row = getRow(id)

        if (row) {
            row.remove();
            clearButtonMenu();
            window.location.reload();
        }
    }).catch((response) => {
        console.log(response)
    })
}

function clearButtonMenu() {
    document.querySelectorAll('.menu_sofia span').forEach(btn => {
        btn.remove();
    });
}
