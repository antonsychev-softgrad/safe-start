Ext.define('SafeStartApp.view.pages.panel.VehicleInspections', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspectionsPanel',

    requires: [

    ],

    config: {
        name: 'vehicle-inspections',
        cls: 'sfa-vehicle-inspection',
        layout: {
            type: 'card'
        }
    },

    initialize: function () {
        this.callParent();
    }

});