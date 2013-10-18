Ext.define('SafeStartApp.view.pages.panel.InspectionBreakdownsReport', {
    extend: 'Ext.Panel',
    xtype: 'SafeStartInspectionBreakdownsReportPanel',
    alias: 'widget.SafeStartInspectionBreakdownsReportPanel',

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
        title: 'Inspection Breakdowns',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        name: 'breakdown',
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
        this.add(
            [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    cls: 'sfa-top-toolbar',
                    items: [
                        {
                            xtype: 'datepickerfield',
                            name: 'from',
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
                                this.up('SafeStartInspectionBreakdownsReportPanel').updateDataView();
                            }
                        }
                    ]
                }
            ]
        );
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

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datepickerfield[name=from]').getValue(), SafeStartApp.dateFormat),
            to: Ext.Date.format(this.down('datepickerfield[name=to]').getValue(), SafeStartApp.dateFormat)
        };

        SafeStartApp.AJAX('admin/getInspectionBreakdownsStatistic', post, function (result) {
            if (!self.chartAdded) {
                self.addChart();
                self.chartAdded = true;
            }
            if (result.statistic) {
                if (result.statistic.chart) {
                    self.down('#SafeStartInspectionBreakdownsStatisticChart').show();
                    self.down('#SafeStartInspectionBreakdownsStatisticChart').getStore().setData(result.statistic.chart);
                    self.down('#SafeStartInspectionBreakdownsStatisticChart').getStore().sync();
                }
            }
        });
    },

    addChart: function () {
        this.add({
            xtype: 'chart',
            id: 'SafeStartInspectionBreakdownsStatisticChart',
            minHeight: 300,
            flex: 1,
            animate: true,
            store: {
                fields: ['key', 'count', 'additional']
            },
            axes: [
                {
                    type: 'numeric',
                    position: 'left',
                    title: {
                        text: 'Quantity',
                        fontSize: 15
                    },
                    fields: ['count'],
                    minimum: 0
                },
                {
                    type: 'category',
                    position: 'bottom',
                    fields: 'key',
                    title: {
                        text: 'CheckLists',
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
                    xField: 'key',
                    yField: ['count'],
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
                }
            ]
        });
    }

});