$(document).ready(
    function () {

        $(() => {
            const SERVICE_URL = 'http://localhost:8080/api/keys';

            var keySource = new DevExpress.data.CustomStore({
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

            const dataGrid = $('#dataGridKeys').dxDataGrid({
                dataSource: keySource,
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
                    refreshMode: 'reload',
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
                }, {dataField: 'campusUid', caption: 'Campus UID', validationRules: [{type: 'required'}],},
                    {
                    dataField: 'empStatus',caption: 'Employee Type',
                    validationRules: [{type: 'required'},],
                },
                    {dataField: 'keyNumber', validationRules: [{type: 'required'}],},
                    {dataField: 'keyCore', validationRules: [{type: 'required'}],},
                    {dataField: 'hookNumber', caption: 'Hook #', validationRules: [{type: 'required'}],},
                    {dataField: 'roomNumber', caption: 'Room #', validationRules: [{type: 'required'}],},
                    {dataField: 'wingBldg', caption: 'Location', validationRules: [{type: 'required'}],},
                    {dataField: 'dateCheckedIn', caption: 'In', validationRules: [{type: 'date'}],},
                    {dataField: 'dateCheckedOut', caption: 'Out', validationRules: [{type: 'date'}],},
                    {dataField: 'addNotes', caption: 'Notes', validationRules: [{type: 'required'}],},
                ],
            }).dxDataGrid('instance');


        });

    }
);
