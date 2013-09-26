Ext.define('SafeStartApp.view.pages.SystemStatistic', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main'
    ],

    xtype: 'SafeStartSystemStatisticPage',

    config: {
        title: 'Statistic',
        iconCls: 'info',
        styleHtmlContent: true,
        layout: 'card',
        tab: {
          action: 'system-statistic'
        },
        items: [

        ],

        listeners: {
            scope: this,
            activate: function (page) {
                page.loadData();
            }
        }
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Main');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top'
        });

        this.add(this.getInfoPanel());

    },

    getInfoPanel: function () {
        var self = this;
        this.checkListTree = new SafeStartApp.view.components.UpdateChecklist({checkListStore: this.checklistDefaultStoreStore});
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            layout: 'card',
            name: 'statistic',
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    items: [
                        {
                            xtype: 'selectfield',
                            name: 'range',
                            label: 'Status',
                            cls: 'sfa-atatus',
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
                            label: 'From',
                            picker: {
                                yearFrom: new Date().getFullYear() - 10,
                                yearTo: new Date().getFullYear()
                            }
                        },
                        {
                            xtype: 'datepickerfield',
                            name: 'to',
                            label: 'To',
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
                                this.up('SafeStartSystemStatisticPage').updateDataView();
                            }
                        }
                    ]
                },
                {
                    id: 'SafeStartSystemStatisticContent',
                    tpl: [
                        '<div class="top">',
                        '<div class="name">Period from {period.from} to {period.to}</div>',
                        '<div class="name">Total amount of database inspections: {total.database_inspections} </div>',
                        '<div class="name">Total amount of email inspections: {total.email_inspections} </div>',
                        '</div>'
                    ].join('')
                }
            ],
            listeners: {

            }
        };
    },

    loadData: function () {

        var from = new Date();
        from.setYear(from.getYear() - 1);
        this.down('datepickerfield[name=from]').setValue(from);

        var date = new Date();
        date.setMonth(date.getMonth() + 1);
        this.down('datepickerfield[name=to]').setValue(date);

        this.updateDataView();
    },

    chartAdded: false,
    updateDataView: function () {
        var self = this;
        var post = {};
        if (this.down('datepickerfield[name=from]').getValue()) post.from = this.down('datepickerfield[name=from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=to]').getValue()) post.to = this.down('datepickerfield[name=to]').getValue().getTime() / 1000;
        post.range = this.down('selectfield[name=range]').getValue();

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datepickerfield[name=from]').getValue(), SafeStartApp.dateFormat),
            to: Ext.Date.format(this.down('datepickerfield[name=to]').getValue(), SafeStartApp.dateFormat)
        };

        SafeStartApp.AJAX('admin/getstatistic', post, function (result) {
            if (!self.chartAdded) {
                try{
                    self.addChart();
                    self.chartAdded = true;
                } catch (e) {
                    console.log(e);
                    return;
                }
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
        this.down('panel[name=statistic]').add({
            xtype: 'chart',
            id: 'SafeStartSystemStatisticChart',
            style: {
                marginTop: '100px'
            },
            animate: true,
            store: {
                fields: ['date', 'value1', 'value2'],
                data: [
                    {'date': 'y-m-d', 'value1': 0, 'value2': 0}
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
                    fields: ['value1', 'value2'],
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
                }
            ]
        });
    }

});