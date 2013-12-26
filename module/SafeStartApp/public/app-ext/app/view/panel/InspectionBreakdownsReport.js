Ext.define('SafeStartExt.view.panel.InspectionBreakdownsReport', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelInspectionBreakdownsReport',
    requires: [
        'Ext.chart.axis.Numeric',
        'Ext.chart.axis.Category',
        'Ext.chart.series.Bar'
    ],

    record: null,

    cls: 'sfa-statistic',
    title: 'Inspection Breakdowns',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    name: 'breakdown',
    autoScroll: true,
    minHeight: 300,

    initComponent: function() {
        this.callParent();
        this.setContentPanel();
    },

    setContentPanel: function() {
        this.add([{
            xtype: 'toolbar',
            docked: 'top',
            cls: 'sfa-top-toolbar',
            items: [{
                xtype: 'datefield',
                name: 'from',
                label: 'From',
                labelWidth: ''
                // picker: {
                //     yearFrom: new Date().getFullYear() - 10,
                //     yearTo: new Date().getFullYear()
                // }
            }, {
                xtype: 'datefield',
                name: 'to',
                label: 'To',
                labelWidth: '',
                // picker: {
                //     yearFrom: new Date().getFullYear() - 10,
                //     yearTo: new Date().getFullYear()
                // },
                value: new Date()
            }, {
                xtype: 'button',
                name: 'reload',
                ui: 'action',
                action: 'refresh',
                iconCls: 'refresh',
                text: 'reload',
                handler: function() {
                    this.up('SafeStartExtPanelInspectionBreakdownsReport').updateDataView();
                }
            }]
        }]);
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

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datefield[name=from]').getValue(), SafeStartExt.dateFormat),
            to: Ext.Date.format(this.down('datefield[name=to]').getValue(), SafeStartExt.dateFormat)
        };

        SafeStartExt.Ajax.request({
            url: 'admin/getInspectionBreakdownsStatistic',
            data: post,
            success: function (result) {
                if (!self.chartAdded) {
                    self.addChart();
                    self.chartAdded = true;
                }
                if (result.statistic) {
                    if (result.statistic.chart) {
                        self.down('#SafeStartInspectionBreakdownsStatisticChart').show();
                        var store = self.down('#SafeStartInspectionBreakdownsStatisticChart').getStore();
                        store.removeAll();
                        store.add(result.statistic.chart);
                        store.sync();
                    }
                }
            }
        });
    },

    addChart: function() {
        this.add({
            xtype: 'chart',
            id: 'SafeStartInspectionBreakdownsStatisticChart',
            minHeight: 300,
            flex: 1,
            animate: true,
            store: {
                fields: ['key', 'count', 'additional']
            },
            axes: [{
                type: 'numeric',
                position: 'left',
                title: {
                    text: 'Quantity',
                    fontSize: 15
                },
                fields: ['count'],
                minimum: 0
            }, {
                type: 'category',
                position: 'bottom',
                fields: ['key'],
                title: {
                    text: 'CheckLists',
                    fontSize: 15
                },
                label: {
                    rotate: {
                        degrees: -30
                    }
                }
            }],
            series: [{
                type: 'bar',
                columnt: true,
                yField: ['key'],
                xField: ['count'],
                label: {
                    field: 'key',
                    display: 'insideEnd'
                },
                style: {
                    lineWidth: 2,
                    maxBarWidth: 30,
                    stroke: 'dodgerblue',
                    fill: 'palegreen',
                    opacity: 0.6
                },
                renderer: function(sprite, config, rendererData, index) {

                    var store = rendererData.store,
                        storeItems = store.getData().items,
                        record = storeItems[index],
                        last = storeItems.length - 1,
                        surface = sprite.getParent(),
                        changes = {},
                        lineSprites, firstColumnConfig, firstData, lastData, growth, string;
                    if (!record) {
                        return;
                    }

                    if (record.get('additional') && record.get('count') > 0) {
                        changes.fill = '#94ae0a';
                        /*   lineSprites = surface.myLineSprites;
                            if (!lineSprites) {
                                lineSprites = surface.myLineSprites = [];
                                lineSprites[0] = surface.add({type:'text'});
                            }

                            lineSprites[0].setAttributes({
                                text: 'Additional',
                                x: config.x - 8,
                                y: config.y - 40,
                                fill: '#000',
                                fontSize: 14,
                                zIndex: 10000,
                                opacity: 0.6,
                                scalingY: -1,
                                textAlign: "center",
                                rotate: -90
                            });*/
                    } else {
                        changes.fill = "#115fa6";
                    }

                    return changes;
                }
            }]
        });
    }

});