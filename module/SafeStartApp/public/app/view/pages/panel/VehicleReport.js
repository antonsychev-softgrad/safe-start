Ext.define('SafeStartApp.view.pages.panel.VehicleReport', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleReportPanel',

    requires: [

    ],

    record: null,

    config: {
        name: 'vehicle-report',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'card'
        },
        items: [
            {
                xtype: 'toolbar',
                docked: 'top',
                items: [
                    {
                        xtype: 'datepickerfield',
                        name: 'from',
                        label: 'From',
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

                        }
                    }
                ]
            },
            {
                id: 'SafeStartVehicleReportContent',
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
            }
        ]

    },

    loadData: function (record) {
        this.record = record;
        var from = new Date();
        from.setMonth(from.getMonth() - 1);
        this.down('datepickerfield[name=from]').setValue(from);
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
                data.statistic = result.statistic;
                self.down('#SafeStartVehicleReportContent').setData(data);
            }
        });
    }

});