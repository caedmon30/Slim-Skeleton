window.jsPDF = window.jspdf.jsPDF;
$(document).ready(
    function () {

        $(() => {

            const SERVICE_URL = '/api/requests';

            const requestsource = new DevExpress.data.CustomStore({
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

            const dataGrid = $('#dataGridRequests').dxDataGrid({
                dataSource: requestsource,
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
                    const worksheet = workbook.addWorksheet('requests');
                    DevExpress.excelExporter.exportDataGrid({
                        component: e.component,
                        worksheet,
                        autoFilterEnabled: true,
                    }).then(() => {
                        workbook.xlsx.writeBuffer().then((buffer) => {
                            saveAs(new Blob([buffer], {type: 'application/octet-stream'}), 'requests.xlsx');
                        });
                    });
                    e.cancel = true;
                },
                columns: [
                    {dataField: 'first_name', allowHeaderFiltering: false,},
                    {dataField: 'last_name', allowHeaderFiltering:false,},
                    {dataField: 'email',},
                    {dataField: 'uid', caption: 'Directory ID', cssClass: "cell-left", allowHeaderFiltering: true, allowFiltering: true,},
                    {dataField: 'pi_supervisor',caption: 'PI/Supervisor',},
                    {dataField: 'pi_email',caption: 'PI/Supervisor',},
                    {dataField: 'request_reason', cssClass: "cell-left",},
                    {dataField: 'status',dataType: "enum",},
                    {dataField: 'date_submitted', allowHeaderFiltering: false, dataType: "datetime",},
                    { cellTemplate: function (container, options) {
                            $('<a>' + '<i class="fa-light fa-pen"></i>' + '</a>')
                                .attr('href', 'request-create/' + options.data.id)
                                .attr('target', '_blank')
                                .attr("class", 'text-red-800')
                                .appendTo(container);
                        },    tooltip: {
                            enabled: true,
                            text: 'Edit request',
                        },},
                ],
            }).dxDataGrid('instance');


        });

    }
);
