$(document).ready(
    function () {

        $(() => {
            const SERVICE_URL = '/api/users';

            var userSource = new DevExpress.data.CustomStore({
                key: 'id',

                load: (loadOptions) => {
                    return $.getJSON(SERVICE_URL);
                },

                byKey: (key) => {
                    return $.getJSON(SERVICE_URL + "/" + encodeURIComponent(key));
                },

                insert: (values) => {
                    return $.ajax({
                        url: SERVICE_URL,
                        method: "POST",
                        data: values,
                    });
                },

                update: (key, values) => {
                    return $.ajax({
                        url: SERVICE_URL + "/" + encodeURIComponent(key),
                        method: "PUT",
                        data: values,
                    });
                },

                remove: (key) => {
                    return $.ajax({
                        url: SERVICE_URL + "/" + encodeURIComponent(key),
                        method: "DELETE",
                    });
                },
            });

            const dataGrid = $('#dataGridUsers').dxDataGrid({
                dataSource: userSource,
                repaintChangesOnly: true,
                showBorders: true,
                showColumnLines: false,
                showRowLines: true,
                rowAlternationEnabled: true,
                export: {
                    enabled: true,
                    allowExportSelectedData: true
                },
                editing: {
                    refreshMode: 'repaint',
                    mode: 'form',
                    allowAdding: true,
                    allowUpdating: true,
                    allowDeleting: true,
                    useIcons: true,
                },
                onBeforeSend(method, ajaxOptions) {
                    ajaxOptions.xhrFields = {withCredentials: true};
                },
                searchPanel: {
                    visible: true,
                    width: 240,
                    placeholder: 'Search...',
                },
                headerFilter: {
                    visible: true,
                },
                selection: {
                    mode: 'multiple',
                },
                onExporting(e) {
                    const workbook = new ExcelJS.Workbook();
                    const worksheet = workbook.addWorksheet('Users');
                    DevExpress.excelExporter.exportDataGrid({
                        component: e.component,
                        worksheet,
                        autoFilterEnabled: true,
                    }).then(() => {
                        workbook.xlsx.writeBuffer().then((buffer) => {
                            saveAs(new Blob([buffer], {type: 'application/octet-stream'}), 'Users.xlsx');
                        });
                    });
                    e.cancel = true;
                },
                columns: [
                    {dataField: 'firstName', validationRules: [{type: 'required'}],}, {
                        dataField: 'lastName',
                        validationRules: [{type: 'required'}],
                }, {dataField: 'username', caption:'Directory ID', validationRules: [{type: 'required'}],}, {
                    dataField: 'emailAddress',
                    validationRules: [{type: 'required'}, {type: 'email', message: 'Email is not valid'},
                    ],
                },
                ],
            }).dxDataGrid('instance');


        });

    }
);
