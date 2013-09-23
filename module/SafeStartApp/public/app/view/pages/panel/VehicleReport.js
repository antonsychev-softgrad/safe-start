Ext.define('SafeStartApp.view.pages.panel.VehicleReport', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleReportPanel',

    requires: [

    ],

    config: {
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
                        value: new Date().setMonth((new Date().getMonth())-1)
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
            }
        ]

    },


    loadData: function () {

    }


});