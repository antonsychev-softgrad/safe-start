Ext.define('SafeStartExt.controller.Auth', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport',
        ref: 'viewport'
    }, {
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'bottomNavPanel'
    }, {
        selector: 'viewport > SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentAuth',
        ref: 'authPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentCompany',
        ref: 'companyPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentContact',
        ref: 'contactPanel'
    }],

    init: function () {
        this.callParent();
        this.control({
            'SafeStartExtComponentAuth': {
                loginAction: this.loginAction
            },
            'SafeStartExtContainerTopNav': {
                logoutAction: this.logoutAction
            }
        });
    },

    loginAction: function (data) {
        var viewport = this.getViewport();
        SafeStartExt.Ajax.request({
            url: 'user/login',
            data: data,
            success: function () {
                viewport.fireEvent('reloadMainMenu');
                viewport.down('SafeStartExtMain').on('mainMenuLoaded', function () {
                    Ext.History.setHash('');
                }, this, {single: true});
            }
        });
    },

    logoutAction: function() {
        var viewport = this.getViewport();
        SafeStartExt.Ajax.request({
            url: 'user/logout',
            success: function () {
                viewport.fireEvent('reloadMainMenu');
                viewport.down('SafeStartExtMain').on('mainMenuLoaded', function () {
                    Ext.History.setHash('auth');
                }, this, {single: true});
            }
        });
    }

});
