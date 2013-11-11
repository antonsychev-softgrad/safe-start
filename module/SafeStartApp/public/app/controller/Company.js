Ext.define('SafeStartApp.controller.Company', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    config: {
        control: {
            'SafeStartCompanySettingsForm': {
                'save-data': 'updateCompanySettings'
            }
        },

        refs: {
            pages: 'SafeStartMainView',
            navMain: 'SafeStartCompanyPage > list[name=vehicles]',
            alertsPage: 'SafeStartAlertsPage',
            vehicleInfoPanel: 'SafeStartCompanyPage > panel[name=vehicle-info]',
            addVehicleButton: 'SafeStartCompanyToolbar > button[action=add-vehicle]'
        }
    },


    updateCompanySettings: function (form) {
        // var me = this;
        var formValues = form.getValues();
        // var data = {
        //     address: formValues.address,
        //     phone: formValues.phone,
        //     description: formValues.description,
        //     logo: formValues.logo
        // };

        SafeStartApp.AJAX('company/' + SafeStartApp.userModel.get('companyId') + '/update', formValues);
    }

});