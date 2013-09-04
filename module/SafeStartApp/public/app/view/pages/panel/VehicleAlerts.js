Ext.define('SafeStartApp.view.pages.panel.VehicleAlerts', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleAlertsPanel',

    requires: [

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
    }

});