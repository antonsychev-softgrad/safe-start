Ext.define('SafeStartExt.view.panel.Alerts', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelAlerts',

    requires: [
        'SafeStartExt.store.Alerts'
    ],

    initComponent: function () {
        var store = SafeStartExt.store.Alerts.create({vehicleId: this.vehicle.get('id')});
        Ext.apply(this, {
            items: [{
                xtype: 'panel',
                title: 'Alerts',
                ui: 'light-left',
                flex: 1,
                border: 0,
                maxWidth: 250,
                cls: 'sfa-previous-inspections-left-coll',
                overflowY: 'auto',
                items: [{
                    xtype: 'dataview',
                    itemSelector: 'div.sfa-vehicle-item',
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                        '<div class="sfa-alert-item">',
                            '<div class="sfa-alert-icon" style="background-image: url({thumbnail});">',
                            '</div>',
                            '<div class="sfa-alert-info">',
                                '<div class="sfa-alert-title">{alertDescription}</div>',
                                '<div class="sfa-alert-vehicle">{[values[\'SafeStartExt.model.Vehicle\'].title]}<b>{[values[\'SafeStartExt.model.Vehicle\'].plantId]}</b></div>',
                                '<div class="sfa-alert-user">added by {[values[\'SafeStartExt.model.User\'].firstName]} {[values[\'SafeStartExt.model.User\'].lastName]} at {creationDate}</div>',
                            '</div>',
                        '</div>',
                        '</tpl>'
                    ),
                    store: store,
                    listeners: {
                        select: this.onSelect,
                        deselect: this.onDeselect, 
                        scope: this
                    }
                }]
            }, {
                xtype: 'panel',
                hidden: true,
                items: [{
                    xtype: 'dataview',
                    itemSelector: 'div',
                    tpl: new Ext.XTemplate('')
                }, {
                    xtype: 'combobox',
                    name: 'status',
                    store: {
                        proxy: {
                            type: 'memory'
                        },
                        fields: ['key', 'value'],
                        data: [{
                            key: 'New',
                            value: 'new'
                        }]
                    },
                    displayField: 'key',
                    valueField: 'value',
                    editable: false,
                    queryMode: 'local'
                }, {
                    xtype: 'textarea',
                    name: 'comment'
                }],
                tbar: {
                    xtype: 'container',
                    items: [{
                        xtype: 'button',
                        name: 'save',
                        text: 'Save',
                        ui: 'blue',
                        scale: 'medium'
                    }, {
                        xtype: 'button',
                        name: 'delete',
                        text: 'Delete',
                        ui: 'red',
                        scale: 'medium'
                    }]
                },
                flex: 2
            }],
            listeners: {
                afterrender: function () {
                    store.load();
                }
            }
        });

        this.callParent();
    },
    onSelect: function () {

    },
    onDeselect: function () {

    }
});