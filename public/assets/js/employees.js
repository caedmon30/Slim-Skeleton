$(document).ready(
    function () {

        $(() => {
            const SERVICE_URL = 'http://localhost:8080/api/status';

            var empSource = new DevExpress.data.CustomStore({
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

            const dataGrid = $('#dataGridEmployees').dxDataGrid({
                dataSource: empSource,
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
                columns: [
                    {dataField: 'empStatusName', caption: 'Employee Type', validationRules: [{type: 'required'}],
                },
                ],
            }).dxDataGrid('instance');


        });

    }
);
