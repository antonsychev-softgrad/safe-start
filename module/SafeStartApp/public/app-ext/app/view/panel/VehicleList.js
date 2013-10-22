Ext.define('SafeStartExt.view.panel.VehicleList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.MenuVehicles'
    ],
    xtype: 'SafeStartExtPanelVehicleList',

    layout: 'fit',
    border: 0,
    title: 'Vehicles',
    tbar: [{
        xtype: 'textfield',
        placeHolder: 'Search...'
    }, {
        text: 'refresh'
    }],

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class=sfa-vehicle-item>',
                    '{title}',
                    '</div>',
                    '</tpl>'
                ),
                store: SafeStartExt.store.MenuVehicles.create({}),
                listeners: {
                    itemclick: this.onVehicleClick,
                    scope: this
                }
            }]
        });
        this.callParent();
    },

    onVehicleClick: function (dataview, record) {
        this.fireEvent('changeVehicleAction', record);
    },

    getListStore: function () {
        return this.down('dataview').getStore();
    }

});
