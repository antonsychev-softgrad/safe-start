Ext.define('SafeStartApp.view.pages.panel.VehicleReport', {
    extend: 'Ext.Panel',
    xtype: 'SafeStartVehicleReportPanel',
    alias: 'widget.SafeStartVehicleReportPanel',

    requires: [
        'Ext.chart.axis.Numeric',
        'Ext.chart.axis.Time',
        'Ext.chart.series.Line'
    ],

    record: null,

    config: {
        cls: 'sfa-vehicle-report',
        minHeight: 300,
        layout: {
            type: 'card'
        },
        items: {
            cls: 'sfa-system-settings',
            xtype: 'tabpanel',
            defaults: {
                styleHtmlContent: true
            },
            minHeight: 300,
            items: [
                {
                    cls: 'sfa-statistic vehicles-reporting',
                    xtype: 'panel',
                    title: 'General',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    name: 'vehicle-report',
                    scrollable: true,
                    minHeight: 300,
                    items: [
                        {
                            xtype: 'toolbar',
                            cls: 'sfa-top-toolbar',
                            docked: 'top',
                            items: [
                                {
                                    xtype: 'datepickerfield',
                                    name: 'from',
                                    label: 'From',
                                    labelWidth: '',
                                    dateFormat: SafeStartApp.dateFormat,
                                    picker: {
                                        yearFrom: new Date().getFullYear() - 10,
                                        yearTo: new Date().getFullYear()
                                    },
                                    value: new Date()
                                },
                                {
                                    xtype: 'datepickerfield',
                                    name: 'to',
                                    label: 'To',
                                    labelWidth: '',
                                    dateFormat: SafeStartApp.dateFormat,
                                    cls: 'sfa-label-to',
                                    picker: {
                                        yearFrom: new Date().getFullYear() - 10,
                                        yearTo: new Date().getFullYear()
                                    },
                                    value: new Date()
                                },
                                {
                                    xtype: 'button',
                                    name: 'reload',
                                    ui: 'action',
                                    action: 'refresh',
                                    iconCls: 'refresh',
                                    handler: function () {
                                        this.up('SafeStartVehicleReportPanel').updateDataView();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    name: 'print',
                                    text: 'Print Report',
                                    ui: 'confirm',
                                    handler: function () {
                                        this.up('SafeStartVehicleReportPanel').printDataView();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    name: 'print-action-list',
                                    text: 'Print Action List',
                                    ui: 'confirm',
                                    handler: function () {
                                        this.up('SafeStartVehicleReportPanel').printActionList();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    name: 'send-action-list',
                                    text: 'Send Action List',
                                    ui: 'confirm',
                                    handler: function () {
                                        this.up('SafeStartVehicleReportPanel').sendActionList();
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            items: [
                                {
                                    id: 'SafeStartVehicleReportContent',
                                    docked: 'top',
                                    tpl: [
                                        '<div class="top">',
                                        '<table style="max-width: 700px;"><tr>',
                                        '<td>',
                                        /*'<div class="name">Period from {period.from} to {period.to}</div>',*/
                                        '<div class="name">Amount of <b>travelled kms</b>: <span style="color:#0F5B8D;">{statistic.kms}</span> </div>',
                                        '<div class="name">Sum of <b>used hours</b>: <span style="color:#0F5B8D;">{statistic.hours} </span></div>',
                                        '</td>',
                                        '<td>',
                                        '<div class="name">Total number of <b>completed inspections</b>: <span style="color:#0F5B8D;">{statistic.inspections} </span></div>',
                                        '<div class="name">Total number of <b>completed Alerts</b>: <span style="color:#0F5B8D;">{statistic.completed_alerts}</span> </div>',
                                        '<div class="name">Total number of <b>outstanding Alerts</b>: <span style="color:#0F5B8D;">{statistic.new_alerts} </span></div>',
                                        '</td>',
                                        '</tr></table>',
                                        '</div>'
                                    ].join('')
                                }
                            ]
                        }

                    ]
                },
                {
                    cls: 'sfa-statistic',
                    xtype: 'panel',
                    title: 'Inspections',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    name: 'vehicle-inspection-report',
                    scrollable: true,
                    minHeight: 300,
                    items: [
                        {
                            xtype: 'toolbar',
                            docked: 'top',
                            cls: 'sfa-top-toolbar',
                            items: [
                                {
                                    xtype: 'selectfield',
                                    name: 'inspections-range',
                                    label: 'Status',
                                    labelWidth: '',
                                    cls: 'sfa-status',
                                    valueField: 'rank',
                                    displayField: 'title',
                                    value: 'monthly',
                                    store: {
                                        data: [
                                            { rank: 'monthly', title: 'Monthly'},
                                            { rank: 'weekly', title: 'Weekly'}
                                        ]
                                    }
                                },
                                {
                                    xtype: 'datepickerfield',
                                    name: 'inspections-from',
                                    cls: 'sfa-data',
                                    label: 'From',
                                    labelWidth: '',
                                    picker: {
                                        yearFrom: new Date().getFullYear() - 10,
                                        yearTo: new Date().getFullYear()
                                    }
                                },
                                {
                                    xtype: 'datepickerfield',
                                    name: 'inspections-to',
                                    cls: 'sfa-data',
                                    label: 'To',
                                    labelWidth: '',
                                    picker: {
                                        yearFrom: new Date().getFullYear() - 10,
                                        yearTo: new Date().getFullYear()
                                    },
                                    value: new Date()
                                },
                                {
                                    xtype: 'button',
                                    name: 'inspections-reload',
                                    ui: 'action',
                                    action: 'refresh',
                                    iconCls: 'refresh',
                                    handler: function () {
                                        this.up('SafeStartVehicleReportPanel').updateInspectionsDataView();
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'chart',
                            name: 'vehicle-inspections',
                            minHeight: 300,
                            flex: 1,
                            animate: true,
                            store: {
                                fields: ['date', 'value1']
                            },
                            axes: [
                                {
                                    type: 'numeric',
                                    position: 'left',
                                    title: {
                                        text: 'Number of inspections',
                                        fontSize: 15
                                    },
                                    fields: ['value1'],
                                    minimum: 0
                                },
                                {
                                    type: 'category',
                                    position: 'bottom',
                                    fields: 'date',
                                    title: {
                                        text: 'Date',
                                        fontSize: 15
                                    },
                                    label: {
                                        rotate: {
                                            degrees: -30
                                        }
                                    }
                                }
                            ],
                            series: [
                                {
                                    type: 'bar',
                                    xField: 'date',
                                    yField: ['value1'],
                                    style: {
                                        lineWidth: 2,
                                        maxBarWidth: 30,
                                        stroke: 'dodgerblue',
                                        fill: '#94ae0a',
                                        opacity: 0.6
                                    }
                                }
                            ]
                        }
                     ]
                }
            ]
        }
    },

    loadData: function (record) {
        this.record = record;
        var date = new Date();
        date.setMonth(date.getMonth() - 1);
        this.down('datepickerfield[name=from]').setValue(date);
        date.setMonth(date.getMonth() - 5);
        this.down('datepickerfield[name=inspections-from]').setValue(date);
        this.updateDataView();
        this.updateInspectionsDataView();
    },

    updateDataView: function () {
        var self = this;
        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datepickerfield[name=from]').getValue(), SafeStartApp.dateFormat),
            to: Ext.Date.format(this.down('datepickerfield[name=to]').getValue(), SafeStartApp.dateFormat)
        };
        var post = {};
        if (this.down('datepickerfield[name=from]').getValue()) post.from = this.down('datepickerfield[name=from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=to]').getValue()) post.to = this.down('datepickerfield[name=to]').getValue().getTime() / 1000;
        SafeStartApp.AJAX('vehicle/' + this.record.get('id') + '/statistic', post, function (result) {
            if (result.statistic) {
                if (!self.chartAdded) {
                    try {
                        self.addChart();
                        self.chartAdded = true;
                    } catch (e) {
                        console.log(e);
                        return;
                    }
                }
                data.statistic = result.statistic;
                self.down('#SafeStartVehicleReportContent').setData(data);
                if (result.statistic.chart && result.statistic.chart.length) {
                    self.down('#SafeStartVehicleReportChart').show();
                    self.down('#SafeStartVehicleReportChart').getAxes()[1].setDateFormat(SafeStartApp.dateFormat);
                    self.down('#SafeStartVehicleReportChart').getAxes()[1].setFromDate(self.down('datepickerfield[name=from]').getValue());
                    self.down('#SafeStartVehicleReportChart').getAxes()[1].setToDate(self.down('datepickerfield[name=to]').getValue());
                    self.down('#SafeStartVehicleReportChart').getStore().setData(result.statistic.chart);
                    self.down('#SafeStartVehicleReportChart').getStore().sync();
                }
            }
        });
    },

    updateInspectionsDataView: function() {
        var self = this;
        var post = {};
        if (this.down('datepickerfield[name=inspections-from]').getValue()) post.from = this.down('datepickerfield[name=inspections-from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=inspections-to]').getValue()) post.to = this.down('datepickerfield[name=inspections-to]').getValue().getTime() / 1000;
        post.range = this.down('selectfield[name=inspections-range]').getValue();
        SafeStartApp.AJAX('vehicle/' + this.record.get('id') + '/inspections-statistic', post, function (result) {
             self.down('chart[name=vehicle-inspections]').show();
             self.down('chart[name=vehicle-inspections]').getStore().setData(result.chart);
             self.down('chart[name=vehicle-inspections]').getStore().sync();
        });

    },

    printDataView: function () {
        var from = Math.round(this.down('datepickerfield[name=from]').getValue().getTime() / 1000);
        var to = Math.round(this.down('datepickerfield[name=to]').getValue().getTime() / 1000);
        window.open('/api/vehicle/' + this.record.get('id') + '/print-statistic/' + from + '/' + to, '_blank');
    },

    addChart: function () {
        this.down('panel[name=vehicle-report]').add(
            {
                xtype: 'chart',
                id: 'SafeStartVehicleReportChart',
                minHeight: 300,
                flex: 1,
                animate: true,
                store: {
                    fields: ['date', 'value'],
                    data: [
                        {'date': 'y-m-d', 'value': 0}
                    ]
                },
                axes: [
                    {
                        type: 'numeric',
                        position: 'left',
                        title: {
                            text: 'kms/hours',
                            fontSize: 15
                        },
                        fields: 'value',
                        minimum: 0
                    },
                    {
                        type: 'time',
                        position: 'bottom',
                        fields: 'date',
                        title: {
                            text: 'Date of inspection',
                            fontSize: 15
                        },
                        label: {
                            rotate: {
                                degrees: -30
                            }
                        }
                    }
                ],
                series: [
                    {
                        type: 'line',
                        xField: 'date',
                        yField: 'value',
                        labelField: 'value',
                        title: 'Inspections',
                        style: {
                            stroke: "#115fa6",
                            miterLimit: 3,
                            lineCap: 'miter',
                            lineWidth: 2
                        },
                        marker: {
                            type: 'circle',
                            fill: "#115fa6",
                            radius: 10
                        }
                    }
                ]
            }
        );
    },


    printActionList: function () {
        window.open('/api/vehicle/' + this.record.get('id') + '/print-action-list', '_blank');
    },

    sendActionList: function () {
        SafeStartApp.AJAX('vehicle/' + this.record.get('id') + '/send-action-list', {}, function (result) {});
    }

});