(function () {
    'use strict';
    if (window.__funditReportExportsLoaded) return;
    window.__funditReportExportsLoaded = true;
    var path = window.location.pathname.toLowerCase().replace(/\/+$/, '');
    var isReportPage = /\/reports\//.test(path) || /\/fixed_deposits\/(report|deposit_statement)/.test(path) || /\/loan\/(loan_report|loan_report_projection|exportexceview|appraisal_report)/.test(path);
    if (!isReportPage) return;
    function reportTables() {
        return Array.prototype.filter.call(document.querySelectorAll('.main-content table'), function (table) { return table.rows && table.rows.length > 0; });
    }
    function requestServerExport(format) {
        var form = document.querySelector('.main-content form');
        var action = form && form.action ? form.action : window.location.pathname;
        var parameters = new URLSearchParams(window.location.search);
        if (form) {
            new FormData(form).forEach(function (value, key) { parameters.set(key, value); });
        }
        parameters.set('search', format);
        window.location.href = action + (action.indexOf('?') === -1 ? '?' : '&') + parameters.toString();
    }
    window.funditRequestReportExport = requestServerExport;
    function moveParSummaryToTop() {
        if (path.indexOf('/reports/par_report') === -1) return;
        var table = document.getElementById('resulta');
        var footer = table ? table.querySelector('tfoot') : null;
        if (!table || !footer || document.getElementById('par-summary-top')) return;

        var headers = Array.prototype.map.call(table.querySelectorAll('thead th'), function (cell) {
            return cell.textContent.trim();
        });
        var metrics = [];
        Array.prototype.forEach.call(footer.querySelectorAll('tr'), function (row, rowIndex) {
            var cells = Array.prototype.map.call(row.cells, function (cell) { return cell.textContent.trim(); });
            var populated = cells.map(function (value, index) { return {value: value, index: index}; })
                .filter(function (item) { return item.value && item.value !== '-'; });
            if (!populated.length) return;

            if (rowIndex === 0 && populated[0].value.toUpperCase() === 'TOTAL') {
                populated.slice(1).forEach(function (item) {
                    if (headers[item.index]) metrics.push({label: headers[item.index], value: item.value});
                });
                return;
            }

            if (populated.length > 1) {
                metrics.push({label: populated[0].value, value: populated[1].value});
            }
        });

        var wrapper = document.createElement('section');
        wrapper.id = 'par-summary-top';
               wrapper.style.cssText = 'margin:0 0 24px;padding:22px;border:1px solid #e5eaf2;border-radius:16px;background:linear-gradient(145deg,#fff,#f7f9fc);box-shadow:0 8px 24px rgba(26,54,93,.08)';

        var heading = document.createElement('div');
        heading.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px';
        heading.innerHTML = '<div><h3 style="margin:0;color:#173b67;font-size:20px;font-weight:700">Portfolio at Risk Summary</h3><p style="margin:4px 0 0;color:#6b778c;font-size:13px">Outstanding portfolio and arrears ageing overview</p></div><span style="padding:7px 12px;border-radius:999px;background:#e8f1ff;color:#245ca6;font-size:12px;font-weight:700">PAR</span>';

        var grid = document.createElement('div');
        grid.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px';
        metrics.forEach(function (metric) {
            var card = document.createElement('div');
            card.style.cssText = 'position:relative;min-height:94px;padding:16px;border:1px solid #e6ebf2;border-radius:12px;background:#fff;box-shadow:0 3px 10px rgba(26,54,93,.05)';
            var isRisk = /at risk|367|181|121|91|61|31/i.test(metric.label);
            card.innerHTML = '<span style="position:absolute;left:0;top:14px;bottom:14px;width:4px;border-radius:0 4px 4px 0;background:' + (isRisk ? '#e65353' : '#2f73c8') + '"></span>' +
                '<div style="margin-left:5px;color:#738095;font-size:11px;font-weight:700;letter-spacing:.35px;text-transform:uppercase;line-height:1.35">' + metric.label + '</div>' +
                '<div style="margin:10px 0 0 5px;color:#152b46;font-size:18px;font-weight:800;line-height:1.2">' + metric.value + '</div>';
            grid.appendChild(card);
        });

        wrapper.appendChild(heading);
        wrapper.appendChild(grid);
        table.parentNode.insertBefore(wrapper, table);
        footer.remove();
    }
    function installPagination() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;
        reportTables().forEach(function (table) {
            var body = table.tBodies && table.tBodies[0];
            if (!body || table.closest('#par-summary-top')) return;
            if (jQuery.fn.DataTable.isDataTable(table)) {
                jQuery(table).DataTable().page.len(10).draw(false);
                return;
            }
            var dataTable = jQuery(table).DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ordering: true,
                searching: true,
                paging: true,
                info: true,
                retrieve: true
            });
            dataTable.page.len(10).draw(false);
        });
    }
    function installControls() {
        if (!reportTables().length) return;
        var host = document.querySelector('.main-content form, .main-content .page-header, .main-content .card-body');
        if (!host) return;
        var toolbar = document.createElement('span');
        toolbar.className = 'fundit-report-export-tools no-export';
        toolbar.style.cssText = 'display:inline-flex;gap:8px;margin:8px 0 8px 8px;vertical-align:middle';
        if (!document.querySelector('button[name="search"][value="pdf"],a[href*="pdf"],.buttons-pdf')) {
            var pdf = document.createElement('button'); pdf.type = 'button'; pdf.className = 'btn btn-danger btn-sm';
            pdf.innerHTML = '<i class="fa fa-file-pdf"></i> PDF'; pdf.addEventListener('click', function () { requestServerExport('pdf'); }); toolbar.appendChild(pdf);
        }
        if (!document.querySelector('button[name="search"][value="excel"],a[href*="excel"],.buttons-excel,#exportTableCSV,#exportTableClientSummary')) {
            var excel = document.createElement('button'); excel.type = 'button'; excel.className = 'btn btn-success btn-sm';
            excel.innerHTML = '<i class="fa fa-file-excel"></i> Excel'; excel.addEventListener('click', function () { requestServerExport('excel'); }); toolbar.appendChild(excel);
        }
        if (toolbar.children.length) host.appendChild(toolbar);
        moveParSummaryToTop();
        setTimeout(installPagination, 250);

    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', installControls); else installControls();
}());