Ext.define('SafeStartExt.view.panel.VehicleList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.MenuVehicles'
    ],
    xtype: 'SafeStartExtPanelVehicleList',
    cls:'sfa-left-coll',
    layout: 'fit',
    ui: 'light-left',
    border: 0,
    title: 'Vehicles',

    initComponent: function () {
        var store = SafeStartExt.store.MenuVehicles.create({});

        Ext.apply(this, {
            tbar: [{
                xtype: 'textfield',
                cls:'search',
                flex: 1,
                margin: '0 5 0 5',
                height: 22,
                listeners: {
                    change: function (textfield, value) {
                        store.clearFilter();
                        if (value) {
                            store.filter('title', value);
                        }
                    }
                }
            }, {
                iconCls: 'sfa-icon-refresh',
                scale: 'medium',
                handler: function () {
                    this.up('toolbar').down('textfield').setValue('');
                    store.load();
                }
            }],
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
                store: store,
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
