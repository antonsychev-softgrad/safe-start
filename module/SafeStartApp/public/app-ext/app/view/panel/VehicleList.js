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
    minWidth: 250,
    border: 0,

    initComponent: function () {
        var store = SafeStartExt.store.MenuVehicles.create({});

        Ext.apply(this, {
            tbar: {
                xtype: 'toolbar',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: [{
                    text: 'Add vehicle',
                    cls:'sfa-add-button',
                    handler: function () {
                        this.up('SafeStartExtPanelVehicleList').fireEvent('addVehicleAction');
                    }
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        flex: 1,
                        cls:'search',
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
                        xtype: 'button',
                        iconCls: 'sfa-icon-refresh',
                        scale: 'medium',
                        handler: function () {
                            this.up('toolbar').down('textfield').setValue('');
                            store.load();
                        }
                    }]
                }]
            },

            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class=sfa-vehicle-item>',
                    '{text}',
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
        return this.getList().getStore();
    },

    getList: function () {
        return this.down('dataview');
    }

});
