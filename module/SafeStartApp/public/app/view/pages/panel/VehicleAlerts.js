Ext.define('SafeStartApp.view.pages.panel.VehicleAlerts', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleAlertsPanel',

    requires: [
        'SafeStartApp.store.ChecklistAlerts'
    ],

    config: {
        name: 'vehicle-alerts',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();
        this.alertsStore = Ext.create('SafeStartApp.store.ChecklistAlerts');
        this.add(this.getListPanel());
    },

    getListPanel: function() {
        return {
            xtype: 'list',
            name: 'vehicle-alerts',
            title: 'Vehicle Alerts',
            emptyText: 'No new Alerts',
            itemTpl: [
                '<div class="headshot" style="background-image:url({thumbnail});"></div>',
                '{comment}',
                '<span>{title}</span>'
            ].join(''),
            cls: 'x-contacts',
            store: this.alertsStore
        };
    },

    loadList: function(vehicleId) {
        this.vehicleId = vehicleId;
        this.alertsStore.getProxy().setExtraParam('vehicleId', this.vehicleId);
        this.alertsStore.loadData();
    }

});