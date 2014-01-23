Ext.define('SafeStartExt.view.panel.SystemGeneralReport', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelSystemGeneralReport',

    requires: [
        'Ext.chart.axis.Numeric',
        'Ext.chart.axis.Category',
        'Ext.chart.series.Line',
        'SafeStartExt.store.Companies'
    ],

    record: null,

    cls: 'sfa-statistic-test',
    title: 'General',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    name: 'statistic',
    
    listeners: {
        afterrender: function () {
            this.loadData();
        }
    },

    initComponent: function () {
        this.callParent();
        this.setContentPanel();
    },

    setContentPanel: function () {
        var self = this;
        var prevYear = new Date();
        prevYear.setFullYear(prevYear.getFullYear() - 1);
        var companiesStore = Ext.create(SafeStartExt.store.Companies);
        companiesStore.on('load', function () {
            this.insert(0, {
                id: 0,
                title: 'All Companies'
            });
        });


        this.add([{
            xtype: 'container',
            dock: 'top',
            layout: 'hbox',
            defaults: {
                margin: 5
            },
            items: [{
                xtype: 'combobox',
                queryMode: 'local',
                name: 'company',
                fieldLabel: 'Status',
                labelWidth: 49,
                cls: 'sfa-status-test sfa-combobox',
                valueField: 'id',
                displayField: 'title',
                value: 0,
                store: companiesStore
            }, {
                xtype: 'combobox',
                queryMode: 'local',
                name: 'range',
                fieldlabel: 'Status',
                cls: 'sfa-status-second-test sfa-combobox',
                valueField: 'rank',
                displayField: 'title',
                value: 'monthly',
                store: {
                    proxy: {
                        type: 'memory'
                    },
                    fields: ['rank', 'title'],
                    data: [{
                        rank: 'monthly',
                        title: 'Monthly'
                    }, {
                        rank: 'weekly',
                        title: 'Weekly'
                    }]
                }
            }, {
                xtype: 'datefield',
                name: 'from',
                cls: 'sfa-datepicker sfa-from-test',
                fieldLabel: 'From',
                labelWidth: 40,
                value: prevYear
            }, {
                xtype: 'datefield',
                name: 'to',
                labelWidth: 20,
                cls: 'sfa-datepicker',
                fieldLabel: 'To',
                value: new Date()
            }, {
                xtype: 'button',
                name: 'reload',
                ui: 'blue',
                scale: 'medium',
                action: 'refresh',
                text: 'Refresh',
                handler: function() {
                    this.up('SafeStartExtPanelSystemGeneralReport').updateDataView();
                }
            }]
        }, {
            xtype: 'panel',
            height: 100,
            items: [{
                id: 'SafeStartSystemStatisticContent',
                docked: 'top',
                cls: 'sfa-total-test',
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
            }]
        }]);

        companiesStore.load();
        companiesStore.addListener('data-load-success', function() {
            companiesStore.add({
                id: 0,
                title: 'All companies'
            });
            self.down('combobox[name=company]').setValue(0);
        });
    },

    loadData: function() {
        var now = new Date();
        this.down('datefield[name=to]').setValue(now);

        var from = new Date();
        from.setYear(from.getFullYear() - 1);
        this.down('datefield[name=from]').setValue(from);

        this.updateDataView();
    },

    chartAdded: false,
    updateDataView: function() {
        var self = this;
        var post = {};
        if (this.down('datefield[name=from]').getValue()) {
            post.from = this.down('datefield[name=from]').getValue().getTime() / 1000;
        }
        if (this.down('datefield[name=to]').getValue()) {
            post.to = this.down('datefield[name=to]').getValue().getTime() / 1000;
        }
        post.range = this.down('combobox[name=range]').getValue();
        if (this.down('combobox[name=company]').getValue()) {
            post.company = this.down('combobox[name=company]').getValue();
        } else {
            post.company = 0;
        }

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datefield[name=from]').getValue(), SafeStartExt.dateFormat),
            to: Ext.Date.format(this.down('datefield[name=to]').getValue(), SafeStartExt.dateFormat)
        };

        SafeStartExt.Ajax.request({
            url: 'admin/getstatistic', 
            data: post, 
            success: function(result) {
                if (!self.chartAdded) {
                    self.addChart();
                    self.chartAdded = true;
                }
                if (result.statistic) {
                    if (result.statistic.total) {
                        data.total = result.statistic.total;
                        self.down('#SafeStartSystemStatisticContent').update(data);
                    }
                    if (result.statistic.chart && result.statistic.chart.length) {
                        self.down('#SafeStartSystemStatisticChart').show();
                        var store = self.down('#SafeStartSystemStatisticChart').getStore();
                        store.removeAll();
                        var charts = {},
                            resCharts = [];
                        Ext.each(result.statistic.chart, function (chart) {
                            var key = chart.date;
                            if (charts.hasOwnProperty(key)) {
                                charts[key].value1 += parseInt(chart.value1, 10);
                                charts[key].value2 += parseInt(chart.value2, 10);
                                charts[key].value3 += parseInt(chart.value3, 10);
                            } else {
                                charts[key] = {};
                                charts[key].value1 = parseInt(chart.value1, 10);
                                charts[key].value2 = parseInt(chart.value2, 10);
                                charts[key].value3 = parseInt(chart.value3, 10);
                                charts[key].date = chart.date;
                            }
                        });

                        Ext.iterate(charts, function (key, value) {
                            resCharts.push(value);
                        });
                        store.add(resCharts);
                        store.sync();
                    }
                }
            }
        });
    },

    addChart: function() {
        this.add({
            xtype: 'chart',
            id: 'SafeStartSystemStatisticChart',
            wdith: 500,
            height: 500,
            flex: 1,
            animate: true,
            store: {
                proxy: {
                    type: 'memory'
                },
                fields: ['date', 'value1', 'value2', 'value3'],
                data: [{
                    'date': 'y-m-d',
                    'value1': 0,
                    'value2': 0,
                    'value3': 0
                }]
            },
            legend: {
                position: 'bottom'
            },
            axes: [{
                type: 'numeric',
                position: 'left',
                title: {
                    text: 'Quantity',
                    fontSize: 15
                },
                fields: ['value1', 'value2', 'value3'],
                minimum: 0
            }, {
                type: 'category',
                position: 'bottom',
                fields: ['date'],
                title: {
                    text: 'Date',
                    fontSize: 15
                },
                label: {
                    rotate: {
                        degrees: -30
                    }
                }
            }],
            series: [{
                type: 'line',
                axis: 'left',
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
            }, {
                type: 'line',
                axis: 'left',
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
            }, {
                type: 'line',
                axis: 'left',
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
            }]
        });
    }

});
