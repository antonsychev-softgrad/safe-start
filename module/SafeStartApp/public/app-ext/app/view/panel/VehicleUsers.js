Ext.define('SafeStartExt.view.panel.VehicleUsers', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtPanelVehicleUsers',

    requires: [
        'SafeStartExt.store.VehicleUsers'
    ],

    padding: '10 20',
    name: 'vehicle-users',
    layout: {
        type: 'vbox'
    },

    initComponent: function () {
        Ext.apply(this, {
            buttonAlign: 'left',
            buttons: [{
                text: 'Save',
                ui: 'blue',
                scale: 'medium',
                scope: this,
                handler: function() {
                    this.fireEvent('saveVehicleUsers', this.getValues());
                }
            }],
            listeners: {
                afterrender: function () {
                    this.store.load();
                    this.buildList();
                }
            }
        });

        this.store = SafeStartExt.store.VehicleUsers.create({
            vehicleId: this.vehicle.get('id'),
            listeners: {
                load: function () {
                    this.buildList();
                },
                scope: this
            }
        });
        this.callParent();
    },

    buildList: function () {
        this.removeAll();

        this.store.each(function (user) {
            this.add({
                xtype: 'combobox',
                editable: false,
                name: 'assigned',
                fieldLabel: user.getFullName(),
                displayField: 'title',
                valueField: 'rank',
                queryMode: 'local',
                userId: user.get('id'),
                width: 400,
                labelWidth: 140,
                cls:'sfa-combobox',
                value: user.get('assigned'),
                store: {
                    fields: ['rank', 'title'],
                    data: [
                        { rank: 'no', title: 'No access'},
                        { rank: 'user', title: 'User'},
                        { rank: 'responsible', title: 'Responsible'}
                    ]
                }

            });
        }, this);
    },

    getValues: function () {
        var values = [];    
        Ext.each(this.query('combobox[name=assigned]'), function (selectfield) {
            values.push({
                userId: selectfield.userId,
                assigned: selectfield.getValue()
            });
        });
        return values;
    }

});