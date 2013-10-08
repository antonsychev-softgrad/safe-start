Ext.define('SafeStartApp.controller.Company', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    init: function () {
        this.startUpdateAlertsBadge();
        Ext.Viewport.on('userLogin', function () {
            this.startUpdateAlertsBadge();
        }, this);
        Ext.Viewport.on('userLogout', function () {
            this.stopUpdateAlertsBadge();
        }, this);
    },

    config: {
        control: {

        },

        refs: {
            pages: 'SafeStartMainView',
            navMain: 'SafeStartCompanyPage > list[name=vehicles]',
            alertsPage: 'SafeStartAlertsPage',
            vehicleInfoPanel: 'SafeStartCompanyPage > panel[name=vehicle-info]',
            addVehicleButton: 'SafeStartCompanyToolbar > button[action=add-vehicle]'
        }
    },

    startUpdateAlertsBadge: function () {
        var me = this;
        this.stopUpdateAlertsBadge();
        this.updateAlertsIntervalId = setInterval(function () {
            me.updateAlertsBadge();
        }, 4000);
    },

    stopUpdateAlertsBadge: function () {
        if (this.hasOwnProperty('updateAlertsIntervalId')) {
            clearInterval(this.updateAlertsIntervalId); 
            delete this.updateAlertsIntervalId;
        }
    },

    updateAlertsBadge: function() {
        var self = this;
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;
        SafeStartApp.AJAX('company/' + SafeStartApp.companyModel.get('id') + '/get-new-incoming?now=' + new Date().getTime(), {}, function (result) {
            if (SafeStartApp.userModel.get('role') == 'superAdmin') {
                self.getPages().getTabBar().getComponent(2).setBadgeText(result.alerts);
            } else {
                self.getPages().getTabBar().getComponent(1).setBadgeText(result.alerts);
            }
        }, function() {}, true);
    }

});