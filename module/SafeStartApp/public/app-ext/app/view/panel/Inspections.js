Ext.define('SafeStartExt.view.panel.Inspections', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.toolbar.Paging',
        'SafeStartExt.store.Inspections',
        'SafeStartExt.view.panel.InspectionInfo'
    ],
    xtype: 'SafeStartExtPanelInspections',

    // layout: 'fit',
    border: 0,
    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    initComponent: function () {
        var store = SafeStartExt.store.Inspections.create({
            vehicleId: this.vehicle.get('id')
        });
        Ext.apply(this, {
            items: [{
                xtype: 'panel',
                flex: 1,
                border: 0,
                maxWidth: 250,
                tbar: {
                    xtype: 'pagingtoolbar',
                    pageSize: 5,
                    store: store
                },
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
                    store: store
                }]
            }, {
                xtype: 'SafeStartExtPanelInspectionInfo',
                vehicle: this.vehicle,
                flex: 2
            }]
        });
        this.callParent();
    },

    getListStore: function () {
        return this.down('dataview').getStore();
    }
});
