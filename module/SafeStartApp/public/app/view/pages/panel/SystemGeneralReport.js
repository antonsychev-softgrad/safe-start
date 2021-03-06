Ext.define('SafeStartApp.view.pages.panel.SystemGeneralReport', {
    extend: 'Ext.Panel',
    xtype: 'SafeStartSystemGeneralReportPanel',
    alias: 'widget.SafeStartSystemGeneralReportPanel',

    requires: [
        'Ext.chart.axis.Numeric',
        'Ext.chart.axis.Category',
        'Ext.chart.series.Line',
        'SafeStartApp.store.Companies'
    ],

    record: null,

    config: {
        cls: 'sfa-statistic',
        xtype: 'panel',
        title: 'General',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        name: 'statistic',
        scrollable: true,
        minHeight: 300
    },

    initialize: function () {
        var self = this;
        this.callParent();
        this.setContentPanel();
    },

    setContentPanel: function () {
        var self = this;
        var companiesStore = Ext.create(SafeStartApp.store.Companies);

        this.add(
            [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    cls: 'sfa-top-toolbar',
                    items: [
                        {
                            xtype: 'selectfield',
                            name: 'company',
                            label: 'Status',
                            labelWidth: '',
                            cls: 'sfa-status',
                            valueField: 'id',
                            displayField: 'title',
                            value: 0,
                            store: companiesStore
                        },
                        {
                            xtype: 'selectfield',
                            name: 'range',
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
                            name: 'from',
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
                            name: 'to',
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
                            name: 'reload',
                            ui: 'action',
                            action: 'refresh',
                            iconCls: 'refresh',
                            handler: function () {
                                this.up('SafeStartSystemGeneralReportPanel').updateDataView();
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    items: [
                        {
                            id: 'SafeStartSystemStatisticContent',
                            docked: 'top',
                            tpl: [
                                '<div class="top">',
                                '<table style="max-width: 700px;"><tr>',
                               /* '<tr><td><div class="name"><b>Period from {period.from} to {period.to}</b></div></td></tr>',*/
                                '<td>',
                                    '<div class="name">Total amount of database <b>inspections</b>: <span style="color:#0F5B8D;">{total.database_inspections}</span></div>',
                                    '<div class="name">Total amount of database <b>alerts</b>: <span style="color:#0F5B8D;">{total.database_alerts}</span> </div>',
                                    '<div class="name">Total amount of <b>email inspections</b>: <span style="color:#0F5B8D;">{total.email_inspections}</span> </div>',
                                '</td>',
                                '<td>',
                                    '<div class="name">Total amount of database <b>users</b>: <span style="color:#0F5B8D;">{total.database_users}</span>  </div>',
                                    '<div class="name">Total amount of database <b>subscription managers</b>: <span style="color:#0F5B8D;">{total.database_responsible_users}</span> </div>',
                                    '<div class="name">Total amount of database <b>vehicles</b>: <span style="color:#0F5B8D;">{total.database_vehicles}</span> </div>',
                                '</td>',
                                '</tr></table>',
                                '</div>'
                            ].join('')
                        }
                    ]
                }
            ]
        );

        companiesStore.loadData();
        companiesStore.addListener('data-load-success', function () {
            companiesStore.add({id: 0, title: 'All companies'});
            self.down('selectfield[name=company]').setValue(0);
        });
    },

    loadData: function () {
        var now = new Date();
        this.down('datepickerfield[name=to]').setValue(now);

        var from = new Date();
        from.setYear(from.getFullYear() - 1);
        this.down('datepickerfield[name=from]').setValue(from);

        this.updateDataView();
    },

    chartAdded: false,
    updateDataView: function () {
        var self = this;
        var post = {};
        if (this.down('datepickerfield[name=from]').getValue()) post.from = this.down('datepickerfield[name=from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=to]').getValue()) post.to = this.down('datepickerfield[name=to]').getValue().getTime() / 1000;
        post.range = this.down('selectfield[name=range]').getValue();
        if (this.down('selectfield[name=company]').getValue()) {
            post.company = this.down('selectfield[name=company]').getValue();
        } else {
            post.company = 0;
        }

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datepickerfield[name=from]').getValue(), SafeStartApp.dateFormat),
            to: Ext.Date.format(this.down('datepickerfield[name=to]').getValue(), SafeStartApp.dateFormat)
        };

        SafeStartApp.AJAX('admin/getstatistic', post, function (result) {
            if (!self.chartAdded) {
                self.addChart();
                self.chartAdded = true;
            }
            if (result.statistic) {
                if (result.statistic.total) {
                    data.total = result.statistic.total;
                    self.down('#SafeStartSystemStatisticContent').setData(data);
                }
                if (result.statistic.chart && result.statistic.chart.length) {
                    self.down('#SafeStartSystemStatisticChart').show();
                    self.down('#SafeStartSystemStatisticChart').getStore().setData(result.statistic.chart);
                    self.down('#SafeStartSystemStatisticChart').getStore().sync();
                }
            }
        });
    },

    addChart: function () {
        this.add({
            xtype: 'chart',
            id: 'SafeStartSystemStatisticChart',
            minHeight: 300,
            width: 500,
            height: 500,
            flex: 1,
            animate: true,
            store: {
                fields: ['date', 'value1', 'value2', 'value3'],
                data: [
                    {'date': 'y-m-d', 'value1': 0, 'value2': 0, 'value3': 0}
                ]
            },
            legend: {
                position: 'bottom'
            },
            axes: [
                {
                    type: 'numeric',
                    position: 'left',
                    title: {
                        text: 'Quantity',
                        fontSize: 15
                    },
                    fields: ['value1', 'value2', 'value3'],
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
                    type: 'line',
                    xField: 'date',
                    yField: 'value1',
                    labelField: 'value1',
                    title: 'DateBase Inspections',
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
                },
                {
                    type: 'line',
                    xField: 'date',
                    yField: 'value2',
                    labelField: 'value2',
                    title: 'Email Inspections',
                    style: {
                        stroke: "#94ae0a",
                        miterLimit: 3,
                        lineCap: 'miter',
                        lineWidth: 2
                    },
                    marker: {
                        type: 'circle',
                        fill: "#94ae0a",
                        radius: 10
                    }
                },
                {
                    type: 'line',
                    xField: 'date',
                    yField: 'value3',
                    labelField: 'value3',
                    title: 'DateBase Alerts',
                    style: {
                        stroke: "#A80000",
                        miterLimit: 3,
                        lineCap: 'miter',
                        lineWidth: 2
                    },
                    marker: {
                        type: 'circle',
                        fill: "#A80000",
                        radius: 10
                    }
                }
            ]
        });
    }

});