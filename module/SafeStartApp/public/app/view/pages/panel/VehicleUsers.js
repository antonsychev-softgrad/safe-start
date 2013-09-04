Ext.define('SafeStartApp.view.pages.panel.VehicleUsers', {
    extend: 'Ext.Panel',
    mixins: ['Ext.mixin.Observable'],
    alias: 'widget.SafeStartVehicleUsersPanel',

    requires: [

    ],

    config: {
        name: 'vehicle-users',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'vbox'
        }
    },

    initialize: function () {
        this.callParent();
    },

    users: [],
    vehicleId: 0,

    buildList: function (users, vehicleId) {
        this.users = users;
        this.vehicleId = vehicleId;
        Ext.each(this.query('formpanel'), function (form) {
            this.remove(form);
        }, this);

        this.add({
            xtype: 'formpanel',
            items: [
                {
                    xtype: 'titlebar',
                    docked: 'top',
                    title: 'Vehicle Users'
                }
            ]
        });

        Ext.each(this.users, function (user) {
            this.add(
                {
                    xtype: 'formpanel',
                    margin: '2 20 2 20',
                    items: [
                        {
                            xtype: 'hiddenfield',
                            name: 'userId',
                            value: user.id
                        },
                        {
                            xtype: 'selectfield',
                            name: 'assigned',
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
                        }
                    ]

                }
            );
        }, this);

        if (this.users.length) {
            this.add({
                xtype: 'formpanel',
                items: [
                    {
                        xtype: 'toolbar',
                        items: [
                            { xtype: 'spacer' },
                            {
                                text: 'Save',
                                action: 'save-data',
                                ui: 'confirm',
                                handler: function () {
                                    var panel = this.up('SafeStartVehicleUsersPanel');
                                    SafeStartApp.app.getController('CompanyVehicles').updateUsersAction(panel.getValue(), panel.vehicleId, panel);
                                }
                            }
                        ]
                    }
                ]
            });
        } else {
            this.add({
                xtype: 'formpanel',
                margin: '0 0 0 0',
                html: 'No date for display'
            });
        }

    },

    getValue: function () {
        this.value = [];
        Ext.each(this.query('formpanel'), function (form) {
            if (form.getValues().userId) this.value.push(form.getValues());
        }, this);
        return this.value;
    }

});