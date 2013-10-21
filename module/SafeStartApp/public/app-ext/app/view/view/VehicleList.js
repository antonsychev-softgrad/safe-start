Ext.define('SafeStartExt.view.view.VehicleList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View'
    ],
    xtype: 'SafeStartExtViewVehicleList',


    layout: 'card',
    width: '100%',
    border: 0,

    tbar: [{
        text: 'Back'
    }, {
        xtype: 'text',
        fontSize: 22,
        style: {
            fontSize: '24px'
        },
        text: 'Ford REGNUMBER'
    }],

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                width: '100%',
                itemTpl: new Ext.XTemplate(
                    '<div class=sfa-vehicle-item>',
                    '{title}',
                    '</div>'
                ),
                listeners: {
                    itemtap: this.onVehicleTap
                },
                data: [{
                    title: 'Ford REGNUMBER'
                }, {
                    title: 'Mitsubishi REGNUMBER'
                }, {
                    title: 'REGNUMBER'
                }, {
                    title: 'REGNUMBER'
                }, {
                    title: 'REGNUMBER'
                }]
            }]
        });
        this.callParent();
    },

    onVehicleTap: function () {
        //update toolbar
        //show sub menu
    }
});
