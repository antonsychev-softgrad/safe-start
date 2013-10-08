Ext.define('SafeStartApp.view.pages.panel.VehicleUsers', {
    extend: 'Ext.form.Panel',
    alias: 'widget.SafeStartVehicleUsersPanel',

    requires: [

    ],

    config: {
        name: 'vehicle-users',
        scrollable: true,
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'vbox'
        },
        items: [{
            xtype: 'toolbar',
            margin: 10,
            docked: 'top',
            title: 'Vehicle Users'
        }, {
            xtype: 'toolbar',
            docked: 'bottom',
            items: [{
                xtype: 'spacer'
            }, {
                text: 'Save',
                action: 'save-data',
                ui: 'confirm',
                handler: function() {
                    var panel = this.up('SafeStartVehicleUsersPanel');
                    panel.fireEvent('updateUsers', panel,  panel.getUsersData(), panel.vehicleId);
                }
            }]
        }, {
            xtype: 'titlebar',
            docked: 'top',
            cls: 'sfa-vehicle-users-empty-data',
            title: 'No available users'
        }]
    },

    initialize: function () {
    },

    users: [],
    vehicleId: 0,

    buildList: function (users, vehicleId) {
        Ext.each(this.query('selectfield'), function (field) {
            this.remove(field);
        }, this);
        this.vehicleId = vehicleId;

        Ext.each(users, function (user) {
            this.add({
                xtype: 'selectfield',
                name: 'assigned',
                userId: user.id,
                label: user.firstName + ' ' + user.lastName,
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
                        { rank: 'no', title: 'No access'},
                        { rank: 'user', title: 'User'},
                        { rank: 'responsible', title: 'Responsible'}
                    ]
                },
                value: user.assigned
            });
        }, this);

        if (users && users.length) {
            this.down('button[action=save-data]').show();
            this.down('titlebar').hide();
        } else {
            this.down('button[action=save-data]').hide();
            this.down('titlebar').show();
        }
    },

    getUsersData: function () {
        var values = [];
        Ext.each(this.query('selectfield'), function (field) {
            values.push({
                userId: field.config.userId,
                assigned: field.getValue()
            });
        });
        return values;
    }

});