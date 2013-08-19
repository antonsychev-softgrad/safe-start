Ext.define('SafeStartApp.controller.Company', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    init: function () {

    },

    config: {
        control: {

        },

        refs: {
            navMain: 'SafeStartCompanyPage > list[name=vehicles]',
            vehicleInfoPanel: 'SafeStartCompanyPage > panel[name=vehicle-info]',
            addVehicleButton: 'SafeStartCompanyToolbar > button[action=add-vehicle]'
        }
    }

});