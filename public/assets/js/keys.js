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
                showColumnLines: true,
                showRowLines: true,
                rowAlternationEnabled: true,
                filterRow: { visible: true },
                scrolling: {
                    rowRenderingMode: 'virtual',
                },
                columnAutoWidth: false,
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
                columnChooser: {
                    enabled: true
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
                    allowSearch: true,
                },
                selection: {
                    mode: 'multiple',
                },
                pager: {
                    showPageSizeSelector: true,
                    allowedPageSizes: [10, 20, 50],
                    showInfo: true,
                    showNavigationButtons: true
                },loadPanel: {
                    height: 100,
                    width: 250,
                    indicatorSrc: "https://js.devexpress.com/Content/data/loadingIcons/rolling.svg"
                },
                onExporting(e) {
                    const workbook = new ExcelJS.Workbook();
                    const worksheet = workbook.addWorksheet('Keys');
                    DevExpress.excelExporter.exportDataGrid({
                        component: e.component,
                        worksheet,
                        autoFilterEnabled: true,
                    }).then(() => {
                        workbook.xlsx.writeBuffer().then((buffer) => {
                            saveAs(new Blob([buffer], {type: 'application/octet-stream'}), 'Keys.xlsx');
                        });
                    });
                    e.cancel = true;
                },
                columns: [
                    {dataField: 'firstName', allowHeaderFiltering: false, validationRules: [{type: 'required'}],}, {
                        dataField: 'lastName',allowHeaderFiltering: false,
                        validationRules: [{type: 'required'}],
                }, {dataField: 'campusUid', allowHeaderFiltering: false, caption: 'Campus UID', allowFiltering: false,validationRules: [{type: 'required'}],},
                    {
                    dataField: 'empStatus',caption: 'Status',
                    validationRules: [{type: 'required'},],
                },
                    {dataField: 'keyNumber', caption: 'Key #',allowHeaderFiltering: false, allowFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'keyCore', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'hookNumber', caption: 'Hook #', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'roomNumber', caption: 'Room #', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'wingBldg', caption: 'Location', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'dateCheckedIn', caption: 'In', allowHeaderFiltering: false, dataType: 'date'},
                    {dataField: 'dateCheckedOut', caption: 'Out', allowHeaderFiltering: false, dataType: 'date',},
                    {dataField: 'addNotes', allowFiltering: false, allowHeaderFiltering: false, caption: 'Notes',},
                ],
            }).dxDataGrid('instance');


        });

    }
);
