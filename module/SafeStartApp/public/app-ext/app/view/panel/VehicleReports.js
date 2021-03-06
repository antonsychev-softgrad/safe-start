Ext.define('SafeStartExt.view.panel.VehicleReports', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelVehicleReports',

    requires: [
        'Ext.chart.axis.Numeric',
        'Ext.chart.axis.Time',
        'Ext.chart.series.Line'
    ],
    record: null,

    cls: 'sfa-vehicle-report',
    layout: {
        type: 'fit'
    },

    initComponent: function() {
        var prevDate = new Date();
        prevDate.setMonth(prevDate.getMonth() - 5);
        Ext.apply(this, {
            listeners: {
                afterrender: function () {
                    this.loadData(this.vehicle);
                },
                scope: this
            },
            items: {
                cls: 'sfa-system-settings',
                xtype: 'tabpanel',
                defaults: {
                    styleHtmlContent: true
                },
                items: [{
                    cls: 'sfa-statistic vehicles-reporting',
                    xtype: 'panel',
                    title: 'General',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    name: 'vehicle-report',
                    scrollable: true,
                    tbar: {
                        xtype: 'container',
                        docked: 'top',
                        layout: 'hbox',
                        defaults: {
                            margin: '5'
                        },
                        items: [{
                            xtype: 'datefield',
                            name: 'from',
                            label: 'From',
                            labelWidth: '',
                            dateFormat: SafeStartExt.dateFormat,
                            cls: 'sfa-datepicker',
                            value: new Date()
                        }, {
                            xtype: 'datefield',
                            name: 'to',
                            label: 'To',
                            labelWidth: '',
                            dateFormat: SafeStartExt.dateFormat,
                            cls: 'sfa-datepicker',
                            value: new Date()
                        }, {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'blue',
                            scale: 'medium',
                            action: 'refresh',
                            text: 'Refresh',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').updateDataView();
                            }
                        }, {
                            xtype: 'button',
                            ui: 'blue',
                            scale: 'medium',
                            name: 'print',
                            text: 'Print Report',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').printDataView();
                            }
                        }, {
                            xtype: 'button',
                            ui: 'blue',
                            scale: 'medium',
                            name: 'print-action-list',
                            text: 'Print Action List',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').printActionList();
                            }
                        }, {
                            xtype: 'button',
                            ui: 'blue',
                            scale: 'medium',
                            name: 'export-data',
                            text: 'Export',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').exportData();
                            }
                        }, {
                            xtype: 'button',
                            ui: 'blue',
                            scale: 'medium',
                            name: 'send-action-list',
                            text: 'Send Action List',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').sendActionList();
                            }
                        }]
                    },
                    items: [{
                        xtype: 'panel',
                        height: 200,
                        items: [{
                            //id: 'SafeStartVehicleReportContent1',
                            xtype: 'container',
                            name: 'vehicle-report-content-one',
                            docked: 'top',
                            tpl: [
                                '<div class="top">',
                                '<table style="max-width: 700px;" class="sfa-total-of-numbers"><tr>',
                                '<td>',
                                /*'<div class="name">Period from {period.from} to {period.to}</div>',*/
                                '<div class="name">Total <b>kms travelled</b>: <span style="color:#0F5B8D;">{statistic.kms}</span> </div>',
                                '<div class="name">Total <b>hours used</b>: <span style="color:#0F5B8D;">{statistic.hours} </span></div>',
                                '</td>',
                                '<td>',
                                '<div class="name">Total number of <b>completed inspections</b>: <span style="color:#0F5B8D;">{statistic.inspections} </span></div>',
                                '<div class="name">Total number of <b>completed Alerts</b>: <span style="color:#0F5B8D;">{statistic.completed_alerts}</span> </div>',
                                '<div class="name">Total number of <b>outstanding Alerts</b>: <span style="color:#0F5B8D;">{statistic.new_alerts} </span></div>',
                                '</td>',
                                '</tr></table>',
                                '</div>'
                            ].join('')
                        }]
                    }]
                }, {
                    cls: 'sfa-statistic',
                    xtype: 'panel',
                    flex: 1,
                    title: 'Inspections',
                    layout: {
                        type: 'fit'
                    },
                    name: 'vehicle-inspection-report',
                    tbar: {
                        xtype: 'container',
                        defaults: {
                            margin: 5
                        },
                        layout: 'hbox',
                        docked: 'top',
                        items: [{
                            xtype: 'combobox',
                            queryMode: 'local',
                            name: 'inspections-range',
                            label: 'Status',
                            labelWidth: '',
                            cls: 'sfa-status sfa-combobox',
                            valueField: 'rank',
                            displayField: 'title',
                            value: 'monthly',
                            store: {
                                proxy: 'memory',
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
                            name: 'inspections-from',
                            cls: 'sfa-datepicker',
                            value: new Date(new Date().setMonth(new Date().getMonth() - 6)),
                            label: 'From',
                            labelWidth: ''
                        }, {
                            xtype: 'datefield',
                            name: 'inspections-to',
                            cls: 'sfa-datepicker',
                            label: 'To',
                            labelWidth: '',
                            value: new Date()
                        }, {
                            xtype: 'button',
                            name: 'inspections-reload',
                            ui: 'blue',
                            scale: 'medium',
                            action: 'refresh',
                            iconCls: 'refresh',
                            text: 'Refresh',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').updateInspectionsDataView();
                            }
                        }]
                    },
                    items: [{
                        xtype: 'chart',
                        name: 'vehicle-inspections',
                        flex: 1,
                        animate: true,
                        store: {
                            fields: ['date', 'value1']
                        },
                        axes: [{
                            type: 'numeric',
                            position: 'left',
                            title: {
                                text: 'Number of inspections',
                                fontSize: 15
                            },
                            fields: ['value1'],
                            minimum: 0
                        }, {
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
                        }],
                        series: [{
                            type: 'bar',
                            column: true,
                            xField: 'date',
                            yField: ['value1'],
                            style: {
                                lineWidth: 2,
                                maxBarWidth: 30,
                                stroke: 'dodgerblue',
                                fill: '#94ae0a',
                                opacity: 0.6
                            }
                        }]
                    }]
                }, {
                    cls: 'sfa-statistic sfa-reporting-alerts',
                    xtype: 'panel',
                    title: 'Alerts',
                    autoScroll: true,
                    layout: {
                        type: 'fit'
                    },
                    name: 'vehicle-report',
                    tbar: {
                        xtype: 'container',
                        layout: 'hbox',
                        defaults: {
                            margin: 5
                        },
                        items: [{
                            xtype: 'datefield',
                            cls: 'sfa-datepicker',
                            name: 'alerts-from',
                            label: 'From',
                            labelWidth: '',
                            dateFormat: SafeStartExt.dateFormat,
                            value: prevDate
                        }, {
                            xtype: 'datefield',
                            name: 'alerts-to',
                            label: 'To',
                            cls: 'sfa-datepicker',
                            labelWidth: '',
                            dateFormat: SafeStartExt.dateFormat,
                            value: new Date()
                        }, {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'blue',
                            scale: 'medium',
                            action: 'refresh',
                            iconCls: 'refresh',
                            text: 'Refresh',
                            handler: function() {
                                this.up('SafeStartExtPanelVehicleReports').updateAlertsDataView();
                            }
                        }]
                    },
                    items: [{
                        xtype: 'container',
                        name: 'vehicle-alerts',
                        flex: 1,

                        tpl: [
                            '<tpl for=".">',
                            '<div class="sfa-statistic-item-{[xindex % 2 === 0 ? "even" : "odd"]}">{[xindex]}) {updateDate} <b>{alertDescription}</b>&nbsp;</div>',
                            '</tpl>'
                        ].join('')
                    }]
                }]
            }
        });
        this.callParent();
    },

    loadData: function() {
        var date = new Date();
        date.setMonth(date.getMonth() - 1);
        this.down('datefield[name=from]').setValue(date);
        date.setMonth(date.getMonth() - 5);
        this.down('datefield[name=inspections-from]').setValue(date);
        this.updateDataView();
        this.updateInspectionsDataView();
        this.updateAlertsDataView();
    },

    updateDataView: function() {
        var self = this;
        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datefield[name=from]').getValue(), SafeStartExt.dateFormat),
            to: Ext.Date.format(this.down('datefield[name=to]').getValue(), SafeStartExt.dateFormat)
        };
        var post = {};
        if (this.down('datefield[name=from]').getValue()) {
            post.from = this.down('datefield[name=from]').getValue().getTime() / 1000;
        }
        if (this.down('datefield[name=to]').getValue()) {
            post.to = this.down('datefield[name=to]').getValue().getTime() / 1000;
        }
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + self.vehicle.get('id') + '/statistic',
            data: post,
            success: function(result) {
                if (!self.chartAdded) {
                    try {
                        self.addChart();
                        self.chartAdded = true;
                    } catch (e) {
                        return;
                    }
                }
                var chart = self.down('chart[name=vehicle-report-chart]');
                var axis = chart.axes.items[1];
                axis.setConfig({
                    dateFormat: SafeStartExt.dateFormat,
                    fromDate: new Date(),
                    toDate: self.down('datefield[name=to]').getValue()
                });
                if (result.statistic) {
                    data.statistic = result.statistic;
                    self.down('container[name=vehicle-report-content-one]').update(data);
                    if (result.statistic.chart && result.statistic.chart.length) {
                        chart.show();
                        // chart.axes.items[0].setDateFormat(SafeStartExt.dateFormat);
                        // chart.axes.items[0].setFromDate(self.down('datefield[name=from]').getValue());
                        // chart.axes.items[0].setToDate(self.down('datefield[name=to]').getValue());
                        var store = chart.getStore();
                        store.removeAll();
                        store.add(result.statistic.chart);
                        store.sync();
                    } else {
                        chart.hide();
                    }
                }

            }
        });
    },

    updateInspectionsDataView: function() {
        var self = this;
        var post = {};
        if (this.down('datefield[name=inspections-from]').getValue()) {
            post.from = this.down('datefield[name=inspections-from]').getValue().getTime() / 1000;
        }
        if (this.down('datefield[name=inspections-to]').getValue()) {
            post.to = this.down('datefield[name=inspections-to]').getValue().getTime() / 1000;
        }
        post.range = this.down('combobox[name=inspections-range]').getValue();
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/inspections-statistic',
            data: post,
            success: function(result) {
                self.down('chart[name=vehicle-inspections]').show();
                var store = self.down('chart[name=vehicle-inspections]').getStore();
                store.removeAll();
                store.add(result.chart);
                store.sync();
            }
        });
    },

    updateAlertsDataView: function() {
        var post = {};
        var self = this;
        var dateFrom;
        var dateTo;

        if (this.down('datefield[name=alerts-from]').getValue()) {
            dateFrom = this.down('datefield[name=alerts-from]').getValue();
            if (dateFrom instanceof Date) {
                dateFrom.setHours(0);
                dateFrom.setMinutes(0);
                dateFrom.setSeconds(0);
                post.from = parseInt(dateFrom.getTime() / 1000, 10);
            }
        }
        if (this.down('datefield[name=alerts-to]').getValue()) {
            dateTo = this.down('datefield[name=alerts-to]').getValue();
            if (dateTo instanceof Date) {
                dateTo.setHours(23);
                dateTo.setMinutes(59);
                dateTo.setSeconds(59);
                post.to = parseInt(dateTo.getTime() / 1000, 10);
            }
        }

        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/alerts-statistic',
            data: post,
            success: function(result) {
                var data = [];
                Ext.each(result.alerts, function(alert) {
                    data.push({
                        alertDescription: alert.alert_description,
                        updateDate: Ext.Date.format(new Date(alert.update_date * 1000), SafeStartExt.dateFormat)
                    });
                });
                self.down('container[name=vehicle-alerts]').update(data);
            }
        });

    },

    printDataView: function() {
        var from = Math.round(this.down('datefield[name=from]').getValue().getTime() / 1000);
        var to = Math.round(this.down('datefield[name=to]').getValue().getTime() / 1000);
        var url = '/api/vehicle/' + this.vehicle.get('id') + '/print-statistic/' +
            from + '/' + to + '/vehicle_report_' + this.vehicle.get('id') + '.pdf';

        window.open(url, '_blank');
    },

    addChart: function() {
        this.down('panel[name=vehicle-report]').add({
            xtype: 'chart',
            //id: 'SafeStartVehicleReportChart',
            name: 'vehicle-report-chart',
            flex: 1,
            animate: true,
            store: {
                fields: ['date', 'value'],
                data: [{
                    'date': 'y-m-d',
                    'value': 0
                }]
            },
            axes: [{
                type: 'numeric',
                position: 'left',
                title: {
                    text: 'kms/hours',
                    fontSize: 15
                },
                fields: 'value',
                minimum: 0
            }, {
                type: 'time',
                dateFormat: SafeStartExt.dateFormat,
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
            }],
            series: [{
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
            }]
        });
    },


    printActionList: function() {
        window.open('/api/vehicle/' + this.vehicle.get('id') + '/print-action-list/vehicle_action_list_' + this.vehicle.get('id') + '.pdf', '_blank');
    },

    exportData: function() {
        var from = Math.round(this.down('datefield[name=from]').getValue().getTime() / 1000);
        var to = Math.round(this.down('datefield[name=to]').getValue().getTime() / 1000);
        var url = '/api/vehicle/' + this.vehicle.get('id') + '/export/' +
            from + '/' + to;

        window.open(url, '_blank');
    },

    sendActionList: function() {
        var self = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + this.vehicle.get('id') + '/send-action-list',
            success: function(response, opts) {
                if (! (Ext.isArray(response.responses) && response.responses.length)) {
                    Ext.Msg.alert('Status', 'List of action could not be sent because respondents not found');
                    return;
                }

                var messageBox = Ext.create('Ext.window.Window', {
                    title: 'Action List has been sent to:',
                    padding: 10,
                    maxHeight: self.getHeight() * 0.8,
                    autoScroll: true,
                    width: 300,
                    items: [{
                        xtype: 'dataview',
                        width: '100%',
                        store: {
                            proxy: 'memory',
                            fields: ['email']
                        },
                        itemSelector: 'div.sfa-previous-alert-item',
                        tpl: [
                            '<tpl for=".">',
                            '<div class="sfa-previous-alert-item" style="font-size: 16px;"> {email} </div>',
                            '</tpl>'
                        ].join(''),
                        data: response.responses
                    }],
                    bbar: {
                        xtype: 'container',
                        padding: '10 0 0',
                        layout: {
                            type: 'hbox',
                            pack: 'center'
                        },
                        items: [{
                            xtype: 'button',
                            text: 'OK',
                            scale: 'medium',
                            ui: 'blue',
                            handler: function () {
                                this.up('window').close();
                            }
                        }]
                    }
                });
                self.add(messageBox);
                messageBox.show();
            },
            failure: function(response, opts) { }
        });
    }

});
