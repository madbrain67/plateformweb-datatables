<?php 

declare(strict_types=1);

namespace Plateformweb\Datatables;

class Datatable extends Debug
{
    private array $tableAttr = [
        'class' => 'display datatable-for-serverside',
        'style' => 'width:100%;',
    ];
    private array $cdnCss = [
        [
            'href' => 'https://cdn.datatables.net/v/dt/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.2.2/af-2.7.0/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.4/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css',
            'rel' => 'stylesheet', 
            'integrity' => 'sha384-gkBYgjzdSQYM2rbWH4EZLu4t7Yqq50pXXrTNOjlzlhAvOrXZwx2CKtQoidnnkLNW',
            'crossorigin' => 'anonymous'
        ]
    ];
    private array $cdnJs = [
        [
            'src' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
            'integrity' => 'sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7',
            'crossorigin' => 'anonymous'
        ],
        [
            'src' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
            'integrity' => 'sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n',
            'crossorigin' => 'anonymous'
        ],
        [
            'src' => 'https://cdn.datatables.net/v/dt/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.2.2/af-2.7.0/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.4/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js',
            'integrity' => 'sha384-wQiJZdWnSdDXh/VauQ+4njGu7wnS7FmqZnmEC9IzpJJO4dVvUZuX7caP/n+4LQDn',
            'crossorigin' => 'anonymous'
        ]
    ];
    private array $columns = [[
        'data' => 'column_0', 
        'name' => 'column_0',
        'label' => '',
        'orderable' => false, 
        'searchable' => false, 
        'selectable' => false, 
        'extraButton' => false,
        'label_attr' => [
            'class' => 'column_0', 
        ],
    ]];
    private string $targetLanguage = 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/';
    private string $language = 'fr-FR';
    private array $rows = [];
    private array $extraDatas = [];
    private array $buttons = [];
    private ?string $processingUrl;
    private string $dom = 'flrtip';
    private int $pageLength = 10;
    private array $lengthMenu = [[10, 25, 50, 100], [10, 25, 50, 100]];
    private bool $pageLengthAll = true;
    private string $pageLengthAllName = 'All';
    private bool $responsive = false;
    private bool $bInfo = true;
    private bool $paging = true;
    private bool $autoWidth = false;
    private array $selectabletype = [];
    private array $selectableColumns = [];
    private bool $selectable = false;
    private bool $processing = true;
    private bool $serverSide = true;
    private bool $scrollX = false;
    private bool $stateSave = false;
    private bool $fixedHeader = false;
    private bool $colReorder = false;
    private array $defaultOrder = [];
    private ?string $uniqid = null;
    private int $firstColumnVisible = 0;
    private array $modals = [];
    private string $selectableRows = 'multi'; // 'multi', 'single', or 'os'

    public function __construct(string $processingUrl)
    {
        $this->processingUrl = $processingUrl; 
    }

    private function generateAttributes(array $attributes): string
    {
      $result = '';
      foreach ($attributes as $key => $value) {
        if (!empty($value)) {
          $result .= $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" ';
        }
      }
  
      return trim($result);
    }

    private function encodeJson(array $datas): string
    {
        return json_encode($datas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function css(): string
    {
        return <<<CSS
            .dataTables_scrollHeadInner table {
                margin: 0 !important;
            }
            .dataTables_scrollFootInner table {
                margin: 0 !important;
            }
            .dataTables_scrollBody table thead {
                visibility: hidden;
            }
            table.datatable-for-serverside div.dt-processing {
                background-color: #f9f9f9;
                color: #5d5d5d !important;
                font-size: 16px;
                font-weight: bold;
                padding: 10px;
                border-radius: 10px;
                text-align: center;
                z-index: 1000;
                border: 1px solid #c3c3c3;
                height: 70px;
            }
            table.datatable-for-serverside div.dt-processing > div:last-child > div {
                width: 8px;
                height: 8px;
                border-radius: 25%;
                background: #e5261f;
            }
            table.datatable-for-serverside div.dt-info, 
            table.datatable-for-serverside div.dt-info .select-info {
                display: flex;
                flex-direction: column;
            }
            button.dt-button.disabled, 
            button.dt-button.disabled:hover, 
            button.dt-button:disabled, 
            button.dt-button:disabled:hover {
                background: repeating-linear-gradient(
                    45deg,
                    #ccc,
                    #ccc 10px,
                    #ddd 10px,
                    #ddd 20px
                ) !important;
                color: #888 !important;
                cursor: not-allowed !important;
                opacity: 0.7 !important;
                text-shadow: none !important;
            }
            table.datatable-for-serverside input.dt-select-checkbox {
                appearance: none;
                position: relative;
                display: inline-block;
                width: 23px;
                height: 23px;
                border: 2px solid #888888 !important;
                border-radius: 3px;
                vertical-align: middle;
                margin-top: 0;
                color: inherit;
                font-size: 20px;
                line-height: 0px;
                padding: 0 !important;
                cursor: pointer;
            }
            table.datatable-for-serverside input.dt-select-checkbox:checked:after, 
            table.datatable-for-serverside input.dt-select-checkbox:indeterminate:after {
                display: block;
                content: "✓";
                color: green;
            }
            table.datatable-for-serverside input.dt-select-checkbox:checked:after {
                margin-top: 10px;
                margin-left: 2px;
            }
            table.datatable-for-serverside input.dt-select-checkbox:indeterminate:after {
                position: absolute;
                top: 10px;
                left: 2px;
                height: 4px;
                width: 4px;
                background-color: #fff;
                border-radius: 2px;
            }
            table.datatable-for-serverside.dtr-column > tbody > tr > td.expand:before, 
            table.datatable-for-serverside.dtr-column > tbody > tr > td.control:before {
                display: none !important;
                box-sizing: border-box;
                content: "";
                border-top: 5px solid transparent;
                border-left: 10px solid rgba(0, 0, 0, .5);
                border-bottom: 5px solid transparent;
                border-right: 0 solid transparent;
                margin-right: 8px;
            }
            table.datatable-for-serverside.dtr-column.collapsed > tbody > tr > td.expand:before, 
            table.datatable-for-serverside.dtr-column.collapsed > tbody > tr > td.control:before {
                display: inline-block !important;
            }
            table.dataTable.stripe > tbody > tr:nth-child(odd).selected > *, 
            table.dataTable.display > tbody > tr:nth-child(odd).selected > *, 
            table.dataTable.stripe > tbody > tr.selected > *,
            table.dataTable.display > tbody > tr.selected > *,
            table.dataTable.row-border > tbody > tr.selected + tr.selected > td, 
            table.dataTable.display > tbody > tr.selected + tr.selected > td,  
            table.dataTable.stripe > tbody > tr.selected:hover > *,
            table.dataTable.display > tbody > tr.selected:hover > *  {
                --dt-row-selected: 0, 0, 0, 0.023;
                box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, 0.023);
                box-shadow: inset 0 0 0 9999px rgba(var(--dt-row-stripe), var(--dt-row-stripe-alpha));
                color: inherit;
                border-top: 1px solid rgba(0, 0, 0, 0.15);
            }
            .dt-container .datatable-for-serverside tbody td.expand, 
            .dt-container .datatable-for-serverside tbody td.control {
                text-align: left;
                cursor: pointer;
            }
            div.dt-processing {
                padding: 20px;
                background: #fcfcfc;
                border: 2px solid #707070;
                border-radius: 10px;
            }
            div.dt-processing > div:last-child > div {
                background: rgb(215 27 27);
            }
            div.dt-container span.select-info, 
            div.dt-container span.select-item {
                display: block;
                margin-left: 0;
            }
        CSS;
    }

    private function javascript(string $tableId): string
    {
        return <<<JS
            (function() {
                function DataTableManager(options, dynamicDefs) {
                    this.options = options;
                    this.dynamicDefs = dynamicDefs;
                    this.dataTableInstance = null;
                }
        
                DataTableManager.prototype.initialize = function() {
                    const tableElement = document.getElementById('{$tableId}');
                    if (!tableElement) {
                        console.error(`Table '#{$tableId}' not found.`);
                        return;
                    }
                    this.setupOptions();
                    this.initDataTable();
                    this.addEventListeners();
                };
        
                DataTableManager.prototype.setupOptions = function() {
                    const defaultOptions = {
                        processing: {$this->convertBooleanToJs($this->processing)},
                        serverSide: {$this->convertBooleanToJs($this->serverSide)},
                        ajax: {
                            url: '{$this->processingUrl}',
                            type: 'POST',
                            data: (d) => {
                                d.extra = {$this->encodeJson($this->extraDatas)};
                                if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                                    console.log('Data sent to the server:', d);
                                }
                            },
                            dataSrc: (json) => {
                                if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                                    console.log('Data received:', json.data);
                                }
                                return json.data || [];
                            },
                            error: (xhr, error, thrown) => {
                                console.error('AJAX error: ', xhr.status + xhr.statusText);
                                console.error('Error details:', error);
                                console.error('Exception:', thrown);
                            }
                        },
                        language: { url: '{$this->targetLanguage}{$this->language}.json' },
                        dom: '{$this->dom}',
                        order: {$this->getOrderJson()},
                        lengthMenu: {$this->getLengthMenuJson()},
                        pageLength: {$this->pageLength},
                        createdRow: (row, data, dataIndex) => {
                            // Adding attributes to rows
                            if (this.options.rows) {
                                Object.entries(this.options.rows).forEach(([key, value]) => {
                                    if (key != 'id') {
                                        row.setAttribute(key, value);
                                    }
                                });
                            }

                            if (data.class) {
                                // Add each class in data.class to the line
                                data.class.split(' ').forEach(function(className) {
                                    row.classList.add(className);
                                });
                            }

                            row.setAttribute('id', 'tr-' + dataIndex);
                        },
                        columnDefs: [
                            // Adding dynamic configurations
                            ...this.dynamicDefs, 
                            {
                                targets: '_all',
                                createdCell: (td, cellData, rowData, row, col) => {
                                    const attributes = this.options.columns[col]?.attr;
                                    if (attributes) {
                                        Object.entries(attributes).forEach(([key, value]) => {
                                            if (key != 'id') {
                                                td.setAttribute(key, value);
                                            }
                                        });
                                    }

                                    var normalizedId = 'td-' + this.options.columns[col].data + '-' + row;
                                    td.setAttribute('id', normalizedId.replace('_', '-').toLowerCase());
                                }
                            }, 
                        ],
                        bInfo: {$this->convertBooleanToJs($this->bInfo)},
                        paging: {$this->convertBooleanToJs($this->paging)},
                        autoWidth: {$this->convertBooleanToJs($this->autoWidth)}, 
                        stateSave: {$this->convertBooleanToJs($this->stateSave)},
                        fixedHeader: {$this->convertBooleanToJs($this->fixedHeader)},
                        colReorder: {$this->convertBooleanToJs($this->colReorder)},
                        initComplete: function () {
                            var api = this.api();
                            api.columns('.search').every(function () {
                                const column = this;
                                const footerCell = column.footer();

                                if (!footerCell) return; // Vérifier si la colonne a un footer

                                // Vider le contenu existant du footer
                                footerCell.innerHTML = '';

                                // Créer un élément <select>
                                const select = document.createElement("select");
                                select.classList.add("select2");

                                // Ajouter une option par défaut
                                const defaultOption = document.createElement("option");
                                defaultOption.value = "XXXfiltre";
                                defaultOption.textContent = "Filtre";
                                select.appendChild(defaultOption);

                                // Ajouter le <select> au footer
                                footerCell.appendChild(select);

                                // Ajouter un événement "change"
                                select.addEventListener("change", function () {
                                    const val = select.value ? '^' + select.value + '$' : ''; 
                                    column.search(val, true, false).draw();
                                });

                                // Remplir les options dynamiquement avec les données de la première page
                                callbackOptionsSelect(column, select);
                            });
                        },
                        drawCallback: function () {
                            var api = this.api();
                            api.columns('.search').every(function () {
                                const column = this;
                                let footerCell = column.footer();

                                if (!footerCell) return; // Vérifier si la colonne a un footer

                                let select = footerCell.querySelector("select.select2");

                                if (!select) {
                                    select = document.createElement("select");
                                    select.classList.add("select2");

                                    let defaultOption = document.createElement("option");
                                    defaultOption.value = "XXXfiltre";
                                    defaultOption.textContent = "Filtre";
                                    select.appendChild(defaultOption);

                                    footerCell.appendChild(select);
                                } else {
                                    // Supprime toutes les options sauf la première
                                    Array.from(select.options)
                                        .slice(1)
                                        .forEach(option => option.remove());
                                }

                                callbackOptionsSelect(column, select);
                            });

                            if ('{$this->convertBooleanToJs($this->responsive)}' === 'true') {
                                api.columns.adjust(); 
                                api.responsive.recalc(); 
                            }
                        }
                    };
        
                    this.options = { 
                        ...defaultOptions, 
                        ...this.options,
                    };
                    
                    if ('{$this->convertBooleanToJs($this->responsive)}' === 'true' && '{$this->convertBooleanToJs($this->debug)}' === 'true') {
                        console.log(this.options);
                    }
                };
        
                DataTableManager.prototype.initDataTable = function() {
                    this.dataTableInstance = new DataTable(document.getElementById('{$tableId}'), this.options);

                    this.dataTableInstance.on('page', () => {
                        const page = this.dataTableInstance.page();
                        if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                            console.log('Page changed:', page);
                        }
                    });

                    this.dataTableInstance.on('order', (e, settings) => {
                        const order = this.dataTableInstance.order();
                        if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                            console.log('Current order:', order);
                        }
                    });

                    if ('{$this->convertBooleanToJs($this->selectable)}' === 'true') {
                        this.dataTableInstance.on('select deselect', (e, dt, type, indexes) => {
                            var selectedRows = this.dataTableInstance.rows({
                                selected: true
                            }).count();

                            var buttonsConf = {$this->getButtonsConfig()};
                            if (buttonsConf.length > 0) {
                                buttonsConf.forEach((btn, index) => {
                                    if (btn.enabled === false) {
                                        this.dataTableInstance.button(index).enable(selectedRows > 0);
                                    }
                                });
                            }

                            if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                                console.log('buttonsConf:', buttonsConf);
                                console.log('Event:', e);
                                console.log('dt:', dt);
                                console.log('type:', type);
                                console.log('indexes:', indexes);
                            }
                        });
                    }
                };

                DataTableManager.prototype.addEventListeners = function() {
                    window.addEventListener('resize', () => {
                        if (this.dataTableInstance) {
                            this.dataTableInstance.responsive.recalc();
                        }
                    });
                };

                var stripHtml = function (html) {
                    var div = document.createElement('div');
                    div.innerHTML = html;
                    return div.textContent || div.innerText || '';
                }

                var callbackOptionsSelect = function(column, select) {
                    var tab = {};
                    column.data().each(function (d) {
                        var stripped = stripHtml(d).trim();
                        if (tab[stripped]) {
                            tab[stripped]++;
                        } else {
                            tab[stripped] = 1;
                        }
                    });

                    Object.keys(tab).forEach(function (val) {
                        if (val) {
                            select.append('<option value="' + val + '">' + val + ' (' + tab[val] + ')</option>');
                        }
                    });
                }
        
                if (window.dataTableManagerInstance) {
                    window.dataTableManagerInstance.dataTableInstance.destroy();
                    window.dataTableManagerInstance = null;
                }
        
                var dynamicDefs = [];
                const options = {
                    columns: {$this->getColumnsJson()}.map(column => {
                        if (typeof column.render === "string" && column.render.endsWith("()")) {
                            try {
                                column.render = eval(column.render);
                            } catch (e) {
                                console.warn('Failed to parse render function: ' + column.render, e);
                            }
                        }
                        return column;
                    }),
                    rows: {$this->encodeJson($this->rows)},
                };

                if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                    options.debug = true;
                }

                if ('{$this->convertBooleanToJs($this->selectable)}' === 'true') {
                    var select = null;
                    var selectabletype = {$this->encodeJson($this->selectabletype)};

                    if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                        console.log('selectabletype: ', selectabletype);
                    }

                    if (selectabletype.includes('row')) {
                        dynamicDefs.push(
                            {
                                targets: 0,  
                                className: 'selectable',
                                select: { 
                                    style: '{$this->selectableRows}', // 'multi', 'single', or 'os'
                                    selector: 'td', 
                                    blurable: false, 
                                    items: 'row'
                                },  
                            }, 
                            {
                                targets: '_all',
                                className: '',
                                select: false, 
                            }
                        );
                        selectRow = {
                            selector: 'td:first-child'
                        };

                        select = {...select, ...selectRow};
                    }

                    if (selectabletype.includes('column')) {
                        dynamicDefs.push(
                            {
                                targets: {$this->encodeJson($this->selectableColumns)},
                                className: 'selectable',
                                select: { 
                                    selector: 'td' 
                                }
                            }, 
                            {
                                targets: '_all',
                                select: false,
                            }
                        );

                        selectColumn = {
                            items: 'column'
                        };

                        select = {...select, ...selectColumn};
                    }

                    if (selectabletype.includes('cell')) {
                        selectCell = {
                            style: '{$this->selectableRows}', // 'multi', 'single', or 'os'
                            selector: 'td'
                        };

                        select = {...select, ...selectCell};
                    }
                    
                    if (select) {
                        options.select = select;
                    }

                    if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                        console.log('select:', select);
                        console.log('dynamicDefs select:', dynamicDefs);
                    }
                }

                if ('{$this->convertBooleanToJs($this->responsive)}' === 'true') {
                    options.responsive = {
                        details: {
                            renderer: function (api, rowIdx, columns) {
                                if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                                    console.log(api, rowIdx, columns);
                                }

                                let data = columns
                                    .map((col, i) => {
                                        return col.hidden
                                            ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + col.title + '</td><td style="word-break:break-all;">' + col.data + '</td></tr>'
                                            : '';
                                    })
                                    .join('')
                                ;
                
                                let table = document.createElement('table');
                                table.innerHTML = data;
                
                                return data ? table : false;
                            },
                            type: 'column', 
                            target: {$this->firstColumnVisible} 
                        }
                    };

                    {$this->getColumnsJson()}.map((column, index) => {
                        if (column.name !== "column_0" && index !== {$this->firstColumnVisible}) {
                            if (typeof column.responsivePriority !== "undefined" && parseInt(column.responsivePriority) > 0) {
                                dynamicDefs.push({
                                    targets: index, 
                                    responsivePriority: column.responsivePriority
                                });
                            } 
                            else {
                                dynamicDefs.push({
                                    targets: index, 
                                    responsivePriority: 999999999
                                });
                            }
                        }
                    });

                    dynamicDefs.push(
                        {
                            targets: 0, 
                            responsivePriority: -1
                        },
                        {
                            targets: {$this->firstColumnVisible},
                            className: 'control',
                        }
                    );

                    if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                        console.log('dynamicDefs: ', dynamicDefs);
                    }
                } 
                else {
                    options.autoWidth = false;
                }

                if ('{$this->convertBooleanToJs($this->scrollX)}' === 'true') {
                    options.scrollX = '{$this->convertBooleanToJs($this->scrollX)}';
                }

                var buttonsConfig = {$this->getButtonsConfig()};
                if (buttonsConfig.length > 0) {
                    buttonsConfig.forEach(function(button, index) {
                        if (button.action) {         
                            if (typeof button.action === "string") {
                                button.action = new Function('e', 'dt', 'node', 'config', button.action);
                            }               
                        }
                    });

                    options.dom = 'Bflrtip';
                    options.buttons = buttonsConfig;
                }
        
                window.dataTableManagerInstance = new DataTableManager(options, dynamicDefs);
                window.dataTableManagerInstance.initialize();
            })();
        JS;
    }

    private function jsonRegexPrefix(string $regex, string $json): string
    {
        return \preg_replace($regex, '"$1"', $json);
    }

    private function getColumns(): array
    {
        if ($this->selectable === true && in_array('row', $this->selectabletype)) {
            if ($this->columns[0]['data'] === 'column_0') {
                $this->columns[0]['render'] = 'DataTable.render.select()';
            }
        }

        // Deletes column 0 if there is no selection by row
        if ($this->selectable === false || !in_array('row', $this->selectabletype)) {
            if ($this->columns[0]['data'] === 'column_0') {
                unset($this->columns[0]);
                $this->columns = array_values($this->columns);
            }
        }

        // Retrieves the column numbers that have the selectable key set to true
        $this->selectableColumns = array_keys(array_map(
            fn($column) => $column['name'], 
            array_filter($this->columns, fn($column) => isset($column['selectable']) && $column['selectable'] === true)
        ));

        // Retrieves the number of the first visible column to display the responsive icon if enabled
        $this->firstColumnVisible = key(array_filter($this->columns, fn($column) => isset($column['visible']) && $column['visible'] === true));
        $this->extraDatas = array_merge($this->extraDatas, ['firstColumnNameVisible' => $this->columns[$this->firstColumnVisible]['name']]);

        return $this->columns;
    }

    private function getColumnsJson(): string 
    {
        // Converted to JSON while replacing ‘__FUNC__:’ to insert functions
        return $this->jsonRegexPrefix('/"__FUNC__:(.*?)"/', json_encode($this->getColumns(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function getOrderJson(): string 
    {
        // Filter columns where the key order exists
        $orderValues = array_filter($this->getColumns(), function ($column) {
            return isset($column['order']);
        });

        // Building the order table for our DataTables
        $orders = array_map(function ($key, $item) {
            return [$key, $item['order']];
        }, array_keys($orderValues), $orderValues);

        $this->defaultOrder = empty($orders) ? $this->defaultOrder : $orders;

        return json_encode($this->defaultOrder);
    }

    private function getLengthMenuJson(): string 
    {
        if ($this->pageLengthAll === true) {
            $this->lengthMenu[0][] = -1;
            $this->lengthMenu[1][] = $this->pageLengthAllName;
        }

        return json_encode($this->lengthMenu);
    }

    private function convertBooleanToJs(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    private function generateAttributesLabel(array $columns): string
    {
        $html = '';
        foreach ($columns as $column) {
            $attributes = array_key_exists('label_attr', $column) ? $this->generateAttributes($column['label_attr']) : '';
            $html .= <<<HTML
                <th {$attributes}>{$column['label']}</th>
            HTML;
        }
        
        return <<<HTML
            <tr>{$html}</tr>
        HTML;
    }

    private function generateCdn($cdnArray, $tag): string
    {
        $balise = '';
        foreach ($cdnArray as $element) {
            $balise .= "<$tag ";
            foreach ($element as $key => $value) {
                $balise .= "$key=\"$value\" ";
            }
            $balise .= ($tag === 'script') ? '></script>' : '>';
            $balise .= PHP_EOL;
        }
        return $balise;
    }
    
    private function generateCdnCss(): string
    {
        return $this->generateCdn($this->cdnCss, 'link');
    }
    
    private function generateCdnJs(): string
    {
        return $this->generateCdn($this->cdnJs, 'script');
    }

    public function setDom(string $dom): self
    {
        $this->dom = $dom; 

        return $this;
    }

    /**
     * Enables or disables datatable responsive
     */
    public function setResponsive(bool $responsive = true): self
    {
        $this->responsive = $responsive; 

        return $this;
    }

    /**
     * Enables or disables datatable pagination
     */
    public function setPagination(bool $paging): self
    {
        $this->paging = $paging; 

        return $this;
    }

    /**
     * Enables or disables datatable pagination information
     */
    public function setbInfo(bool $bInfo): self
    {
        $this->bInfo = $bInfo; 

        return $this;
    }

    /**
     * Enables or disables automatic column width to prevent columns from being cut off
     */
    public function setAutoWidth(bool $autoWidth = true): self
    {
        $this->autoWidth = $autoWidth; 

        return $this;
    }

    /**
     * Activates selectable rows, columns or cells 
     * [‘row’, ‘column’, ‘cell’]
     */
    public function setSelectable(array $selectabletype): self
    {
        $this->selectabletype = $selectabletype;
        $this->selectable = true; 

        return $this;
    }

    /**
     * 'multi', 'single', or 'os'
     */
    public function setSelectableRows(string $selectableRows): self
    {
        $this->selectableRows = $selectableRows; 

        return $this;
    }

    public function setExtraDatas(array $extraDatas): self
    {
        $this->extraDatas = $extraDatas;

        return $this;
    }

    public function setRows(array $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Customise the number of entries to be displayed per page
     */
    public function setLengthMenu(array $lengthMenu): self
    {
        $this->pageLength = $lengthMenu[0];
        $this->lengthMenu = [$lengthMenu, $lengthMenu];

        return $this;
    }

    /**
     * Customise the ‘name’ to display all entries
     */
    public function setPageLengthAllName(string $pageLengthAllName): self 
    {
        $this->pageLengthAllName = $pageLengthAllName;

        return $this;
    }

    /**
     * Customise the number of results to be displayed by default
     * 
     * The number must be in the table of numbers of entries to display per page
     * 
     * Default [5, 25, 50, 100]
     */
    public function setpageLength(int $pageLength): self
    {
        $this->pageLength = $pageLength;

        return $this;
    }

    /**
     * Enables or disables all results on a page
     */
    public function setPageLengthAll(bool $pageLengthAll): self
    {
        $this->pageLengthAll = $pageLengthAll;

        return $this;
    }

    /**
     * Activates or deactivates the current treatment display ...
     */
    public function setProcessing(bool $processing): self
    {
        $this->processing = $processing;

        return $this;
    }

    public function setServerSide(bool $serverSide): self
    {
        $this->serverSide = $serverSide;

        return $this;
    }

    public function setScrollX(bool $scrollX): self
    {
        $this->scrollX = $scrollX;

        return $this;
    }

    /**
     * Enables or disables recording of status in localstorage
     * 
     * @see https://datatables.net/reference/option/stateSave
     */
    public function setStateSave(bool $stateSave): self
    {
        $this->stateSave = $stateSave;

        return $this;
    }

    /**
     * Enables or disables FixedHeader. Locks the table header at the top of the table, 
     * ensuring that the user always knows what each column corresponds to.
     * 
     * @see https://datatables.net/extensions/responsive/examples/column-control/fixedHeader.html
     */
    public function setFixedHeader(bool $fixedHeader): self
    {
        $this->fixedHeader = $fixedHeader;

        return $this;
    }

    /**
     * Activates or deactivates ColReorder. Using Responsive with the ColReorder extension, 
     * allows you to rearrange columns by clicking and dragging column headers.
     * 
     * @see https://datatables.net/extensions/responsive/examples/column-control/colreorder.html
     */
    public function setColReorder(bool $colReorder): self
    {
        $this->colReorder = $colReorder;

        return $this;
    }

    /**
     * url or relative path of translation files 
     * by default https://cdn.datatables.net/plug-ins/2.2.2/i18n/
     */
    public function setTargetLanguage(string $targetLanguage): self
    {
        $this->targetLanguage = $targetLanguage;

        return $this;
    }

    /**
     * Allows you to change the interface language
     * The format must comply with an international standard called IETF BCP 47 
     * Ex: "fr-FR", "en-US", "de-DE"
     * 
     * @see https://datatables.net/reference/option/language.url
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Adds a column to the datatable
     */
    public function addColumn(string $column, array $options = []): self
    {
        $data = array_reverse(explode('.', $column))[0];

        $column = [
            'data'        => $data, // orderable
            'name'        => $column, // searchable
            'label'       => $options['label'] ?? $data, 
            'orderable'   => $options['orderable'] ?? false, 
            'searchable'  => $options['searchable'] ?? false,
            'selectable'  => $options['selectable'] ?? false,
            'visible'     => $options['visible'] ?? true,
            'export'      => $options['export'] ?? false,
            'extraButton' => $options['extraButton'] ?? false,
        ];

        if (array_key_exists('visible', $options) && $options['visible'] === false) {
            $column['orderable'] = false;
            $column['searchable'] = false;
            $column['visible'] = false;
        }

        if (array_key_exists('filter', $options)) {
            $column['filter'] = $options['filter'] ? 'true' : 'false';
        }

        if (array_key_exists('order', $options)) {
            $column['order'] = $options['order'];
        }

        if (array_key_exists('label_attr', $options)) {
            $column['label_attr'] = $options['label_attr'];
        }

        if (array_key_exists('attr', $options)) {
            $column['attr'] = $options['attr'];
        }

        if (array_key_exists('responsivePriority', $options) && is_int($options['responsivePriority'])) {
            $column['responsivePriority'] = $options['responsivePriority'];
            $column['visible'] = true;
        }

        $this->columns[] = $column;

        return $this;
    }

    private function columnsExport(): array
    {
        // Retrieves the number of export-typed columns
        $export = array_keys(array_map(
            fn($column) => $column['name'], 
            array_filter($this->columns, fn($column) => isset($column['export']) && $column['export'] === true)
        ));

        if (empty($export)) {
            $export = array_keys(array_map(
                fn($column) => $column['name'], 
                array_filter($this->columns, fn($column) => $column['data'] !== 'column_0')
            ));
        }

        return $export;
    }

    /**
     * Adds the DataTable Excel button
     * 
     * Note - this will not work in Safari.
     * 
     * @link https://datatables.net/extensions/buttons/
     */
    public function addExcelButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text' => 'Excel',
            'title' => null,
            'filename' => 'Export_Excel_'.date('Y_m_d_H_m_s'),
            'enabled' => true
        ], $options);

        $options['extend'] = 'excelHtml5';
        $options['exportOptions']['columns'] = $this->columnsExport();

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * Adds the DataTable PDF button
     * 
     * @link https://datatables.net/extensions/buttons/
     */
    public function addPdfButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text' => 'Pdf',
            'title' => null,
            'filename' => 'Export_Pdf_'.date('Y_m_d_H_m_s'),
            'enabled' => true
        ], $options);

        $options['extend'] = 'pdf';
        $options['exportOptions']['columns'] = $this->columnsExport();

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * Adds the DataTable Copy button
     * 
     * @link https://datatables.net/extensions/buttons/
     */
    public function addCopyButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text' => 'Copy',
            'title' => null,
            'enabled' => true
        ], $options);

        $options['extend'] = 'copy';

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * Adds the DataTable CSV button
     * 
     * @link https://datatables.net/extensions/buttons/
     */
    public function addCsvButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text' => 'CSV',
            'title' => null,
            'filename' => 'Export_CSV'.date('Y_m_d_H_m_s'),
            'enabled' => true
        ], $options);

        $options['extend'] = 'csv';
        $options['exportOptions']['columns'] = $this->columnsExport();

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * Adds the PRINT button from DataTable
     * 
     * @link https://datatables.net/extensions/buttons/
     */
    public function addPrintButton(array $options = []): self
    {
        // Structure du bouton Copy
        $options = array_replace_recursive([
            'text' => 'Print',
            'autoPrint' => true,
            'title' => '',
            'enabled' => true
        ], $options);

        $options['extend'] = 'print';
        $options['exportOptions']['columns'] = $this->columnsExport();

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * A set of Buttons to set the visibility of individual columns.
     *
     * @see https://datatables.net/reference/button/columnsVisibility
     */
    public function addColumnsVisibilityButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text' => 'Show/Hide columns',
        ], $options);

        $options['extend'] = 'colvis';
        $options['columns'] = ':not(.column_0)';

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * A button which triggers a drop down with another set of buttons.
     *
     * @see A button which triggers a drop down with another set of buttons.
     */
    public function addCollectionButton(array $options = []): self
    {
        $options = array_replace_recursive([
            'text'    => 'Export',
            'buttons' => ['csv', 'excel', 'pdf', 'print'],
        ], $options);

        $options['extend'] = 'collection';

        $this->buttons[] = $options;

        return $this;
    }

    /**
     * Adds a custom button to DataTable
     *
     * @param CustomButton $customButton
     */
    public function addCustomButton(CustomButton $customButton): self
    {
        $columns = array_merge(
            array_map(fn($column) => $column['data'], array_filter($this->columns, fn($column) => $column['selectable'] === true)),
            array_map(fn($column) => $column['data'], array_filter($this->columns, fn($column) => $column['extraButton'] === true))
        );

        if (!empty($columns)) {
            $columns = array_map(
                fn($value) => [
                    'datas' => [],
                    'key' => $value,
                ],
                $columns
            );
        }

        // Structure du bouton personnalisé
        $this->buttons[] = $customButton
            ->setColumns($columns)
            ->setExtraColumns($this->extraDatas)
            ->generate()
        ;

        if ($customButton->getModal()) {
            $this->modals[] = $customButton->getModal();
        }

        return $this;
    }

    /**
     * Generates json button configuration for DataTable
     */
    public function getButtonsConfig(): ?string
    {
        return !empty($this->buttons) ? json_encode($this->buttons) : json_encode([]);
    }

    public function getUniqId(): string
    {
        if (!$this->uniqid) {
            $this->uniqid = 'dynamic-datatable-';
            $this->uniqid .= $this->stateSave === false ? uniqid() : '';
        }

        return $this->uniqid;
    }

    public function setTableAttr(array $attr): self
    {
        if (isset($attr['class'])) {
            $this->tableAttr['class'] = implode(' ', array_merge(explode(' ', $this->tableAttr['class']), explode(' ', $attr['class'])));
            unset($attr['class']);
        }

        if (isset($attr['style'])) {
            $this->tableAttr['style'] = implode(';', array_merge(explode(';', $this->tableAttr['style']), explode(';', $attr['style'])));
            unset($attr['style']);
        }

        $this->tableAttr = array_merge($this->tableAttr, $attr);

        return $this;
    }

    public function setCdnCss(array $cdnCss): self
    {
        $this->cdnCss = $cdnCss;

        return $this;
    }

    public function setCdnJs(array $cdnJs): self
    {
        $this->cdnJs = $cdnJs;

        return $this;
    }

    private function getTableAttr(): string
    {
        return implode(" ", array_map(
            fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES)),
            array_keys($this->tableAttr),
            $this->tableAttr
        ));
    }

    /**
     * DataTables CDN
     * 
     * @see https://datatables.net/download/
     */
    public function __toString(): string
    {
        try {
            $uniqid = $this->getUniqId();
            $table_attr = $this->getTableAttr();
            $thead = $tfood = $this->generateAttributesLabel($this->getColumns());
            $html = <<<HTML
                {$this->generateCdnCss()}
                <style>
                    {$this->css()}
                </style>
                <table id="{$uniqid}" {$table_attr}>
                    <thead>
                        {$thead}
                    </thead>
                    <tfoot>
                        {$tfood}
                    </tfoot>
                </table>
                {$this->generateCdnJs()}
                <script defer>
                    {$this->javascript($uniqid)}
                </script>
            HTML;

            if (!empty($this->modals)) {
                foreach ($this->modals as $modal) {
                    $html .= <<<HTML
                        <div class="modal fade" id="{$modal}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body"></div>
                                </div>
                            </div>
                        </div>
                    HTML;
                }
            }
    
            return $html;
        } catch (\Throwable $e) {
            error_log('Error in __toString: ' . $e->getMessage());
            return '<!-- An error has occurred while generating the DataTable -->';
        }
    }
}