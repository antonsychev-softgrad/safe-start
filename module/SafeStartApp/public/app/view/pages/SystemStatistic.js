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
            cls: 'sfa-info-container sfa-system-settings',
            xtype: 'panel',
            layout: 'card',
            scrollable: false,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    items: [
                        {
                            xtype: 'selectfield',
                            name: 'range',
                            label: 'Status',
                            cls:'sfa-atatus',
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
                    xtype: 'chart',
                    id: 'SafeStartSystemStatisticChart',
                    animate: true,
                    store: {
                        fields: ['date', 'value'],
                        data: [

                        ]
                    },
                    axes: [
                        {
                            type: 'numeric',
                            position: 'left',
                            title: {
                                text: 'count',
                                fontSize: 15
                            },
                            fields: 'value',
                            grid: {
                                odd: {
                                    fill: '#e8e8e8'
                                }
                            },
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

                    ]
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
        this.updateDataView();
    },


    updateDataView: function () {
        var self = this;
        var post = {};
        if (this.down('datepickerfield[name=from]').getValue()) post.from = this.down('datepickerfield[name=from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=to]').getValue()) post.to = this.down('datepickerfield[name=to]').getValue().getTime() / 1000;
        post.range = this.down('selectfield[name=range]').getValue();
        SafeStartApp.AJAX('admin/getstatistic', post, function (result) {
            if (result.statistic) {
                if (result.statistic.chart && result.statistic.chart.length) {
                    self.down('#SafeStartSystemStatisticChart').show();
                    self.down('#SafeStartSystemStatisticChart').getStore().setData(result.statistic.chart);
                    self.down('#SafeStartSystemStatisticChart').getStore().sync();
                }
            }
        });
    }

});