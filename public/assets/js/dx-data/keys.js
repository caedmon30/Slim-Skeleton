window.jsPDF = window.jspdf.jsPDF;
$(document).ready(
    function () {

        $(() => {

            const SERVICE_URL = '/api/keys';
            const keySource = new DevExpress.data.CustomStore({
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
            const positions = [
                {
                    ID: 1,
                    Name: 'Full Time Staff',
            },
                {
                    ID: 2,
                    Name: 'Part Time Staff',
            },

                {
                    ID: 3,
                    Name: 'Faculty',
            },
                {
                    ID: 4,
                    Name: 'Student (Non-Employee)',
            },
                {
                    ID: 5,
                    Name: 'Student (Employee)',
            },
                {
                    ID: 6,
                    Name: 'Other',
            },
                {
                    ID: 7,
                    Name: 'Post-doc',
            },
            ];
            const dataGrid = $('#dataGridKeys').dxDataGrid({
                dataSource: keySource,
                repaintChangesOnly: true,
                showBorders: true,
                showColumnLines: false,
                showRowLines: true,
                rowAlternationEnabled: true,
                filterRow: { visible: true },
                columnMinWidth: 50,
                columnAutoWidth: true,
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
                    newRowPosition: 'viewportTop',
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
                }, {dataField: 'campusUid', visible: false, allowHeaderFiltering: false, caption: 'Campus UID', allowFiltering: false,validationRules: [{type: 'required'}],},
                    {
                        dataField: 'empStatus', visible: false, allowHeaderFiltering: false, allowFiltering: false, caption: 'Employee Type',
                        editorType: 'dxSelectBox',
                        editorOptions: {
                            dataSource: new DevExpress.data.ArrayStore({
                                data: positions,
                                key: 'ID',
                            }),
                        displayExpr: 'Name',
                        valueExpr: 'ID',
                        },
                        validationRules: [{type: 'required'},],
                },
                    {dataField: 'keyNumber', caption: 'Key Number', cssClass: "cell-left", allowHeaderFiltering: false, allowFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'keyCore', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'hookNumber', caption: 'Hook #', cssClass: "cell-left",  allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'roomNumber', caption: 'Room #', cssClass: "cell-left", allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'wingBldg', visible: false, caption: 'Location', allowHeaderFiltering: false, validationRules: [{type: 'required'}],},
                    {dataField: 'dateCheckedIn', caption: 'Check In', allowHeaderFiltering: false, dataType: 'date'},
                    {dataField: 'dateCheckedOut', caption: 'Check Out', allowHeaderFiltering: false, dataType: 'date',},
                    {dataField: 'addNotes', visible: false, allowFiltering: false, allowHeaderFiltering: false, caption: 'Notes',},
                ],
            }).dxDataGrid('instance');


        });

    }
);
