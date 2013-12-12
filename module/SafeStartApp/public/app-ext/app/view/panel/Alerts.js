Ext.define('SafeStartExt.view.panel.Alerts', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelAlerts',

    requires: [
        'SafeStartExt.store.Alerts'
    ],
    layout: {
        type: 'hbox',
        align: 'stretch'
    },

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
                    itemSelector: 'div.sfa-alert-item',
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
                layout: {
                    type: 'vbox'
                },
                name: 'alert-details',
                padding: '10 20',
                hidden: true,
                items: [{
                    xtype: 'dataview',
                    itemTpl: new Ext.XTemplate(
                        '<div class="sfa-alert-info">',
                        '<div>Vehicle: {vehicle.title} (<b>{vehicle.plantId}</b>)</div>',
                        '<div>Fault: <b>{alertDescription}</b></div>',
                        '<div>Description: {description} </div>',
                        '<div>Added by: {user.firstName} {user.lastName} at {creationDate}</div>',
                        '<tpl for="history">',
                            '<div>{action} by: {username} at {date} </div>',
                        '</tpl>',
                        '</div>'
                    )
                }, {
                    xtype: 'container',
                    width: 500,
                    layout: {
                        type: 'hbox'
                    },
                    items: [{
                        xtype: 'combobox',
                        fieldLabel: 'Status',
                        width: 430,
                        labelWidth: 130,
                        name: 'status',
                        store: {
                            proxy: {
                                type: 'memory'
                            },
                            fields: ['key', 'value'],
                            data: [{
                                key: 'New',
                                value: 'new'
                            }, {
                                key: 'Closed',
                                value: 'closed'
                            }]
                        },
                        displayField: 'key',
                        valueField: 'value',
                        editable: false,
                        queryMode: 'local'
                    }, {
                        xtype: 'button',
                        width: 70,
                        text: 'Update'
                    }]
                }, {
                    xtype: 'textarea',
                    width: 500,
                    labelWidth: 130,
                    fieldLabel: 'Comment',
                    name: 'comment'
                }],
                bbar: {
                    xtype: 'container',
                    layout: {
                        type: 'hbox'
                    },
                    items: [{
                        xtype: 'button',
                        name: 'delete',
                        text: 'Delete',
                        ui: 'red',
                        scale: 'medium'
                    }, {
                        flex: 1
                    }, {
                        xtype: 'button',
                        name: 'save',
                        text: 'Save',
                        ui: 'blue',
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
    onSelect: function (selModel, record) {
        var panel = this.down('panel[name=alert-details]');
        var alertData = [{
            vehicle: {
                title: 'Title',
                plantId: 'PlantId'
            },
            alertDescription: 'Alert description',
            description: 'Description',
            user: {
                firstName: 'Firstname',
                lastName: 'Lastname'
            },
            creationDate: 'dd/mm/yyyy',
            history: [
                {action: 'Action', username: 'Firstname Lastname', date: 'dd/mm/yyyy'}
            ]
        }];
        panel.show();
        panel.down('dataview').update(alertData);
    },

    onDeselect: function (selModel, record) {
        var panel = this.down('panel[name=alert-details]');
        panel.hide();
    }
});