Ext.define('SafeStartApp.view.pages.panel.VehicleUsers', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleUsersPanel',

    requires: [

    ],

    config: {
        name: 'vehicle-users',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();
    }

});