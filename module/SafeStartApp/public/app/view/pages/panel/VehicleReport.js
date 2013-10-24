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
        name: 'vehicle-report',
        cls: 'sfa-vehicle-inspection sfa-vehicle-report',
        scrollable: true,
        minHeight: 300,
        layout: {
            type: 'vbox'
        },
        items: [
            {
                xtype: 'toolbar',
                cls:'sfa-top-toolbar',
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
                        labelWidth : '',
                        dateFormat: SafeStartApp.dateFormat, 
                        cls:'sfa-label-to',
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
                        text: 'Print',
                        ui: 'confirm',
                        handler: function () {
                            this.up('SafeStartVehicleReportPanel').printDataView();
                        }
                    }
                ]
            },
            {
                xtype: 'panel', 
                items: [{
                    id: 'SafeStartVehicleReportContent',
                    docked: 'top',
                    tpl: [
                        '<div class="top">',
                        '<div class="name">Period from {period.from} to {period.to}</div>',
                        '<div class="name">Amount of travelled kms: {statistic.kms} </div>',
                        '<div class="name">Sum of used hours: {statistic.hours} </div>',
                        '<div class="name">Total number of completed inspections: {statistic.inspections} </div>',
                        '<div class="name">Total number of completed Alerts: {statistic.completed_alerts} </div>',
                        '<div class="name">Total number of outstanding Alerts: {statistic.new_alerts} </div>',
                        '</div>'
                    ].join('')
                }]
            }
        ]

    },

    loadData: function (record) {
        this.record = record;
        var date = new Date();
        date.setMonth(date.getMonth() - 1);
        this.down('datepickerfield[name=from]').setValue(date);
        this.updateDataView();
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
                    try{
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

    printDataView: function() {
        var from = Math.round(this.down('datepickerfield[name=from]').getValue().getTime() / 1000);
        var to =  Math.round(this.down('datepickerfield[name=to]').getValue().getTime() / 1000);
        window.open('/api/vehicle/' + this.record.get('id') + '/print-statistic/' + from + '/' + to, '_blank');
    },

    addChart: function () {
        this.add(
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
    }
});