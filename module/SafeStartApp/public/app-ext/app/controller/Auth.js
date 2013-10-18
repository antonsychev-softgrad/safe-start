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
        this.getViewport().setLoading(true);
        Ext.Ajax.request({
            url: '/api/user/login',
            params: Ext.encode({data: data}),
            method: 'POST',
            success: function () {
                this.getViewport().fireEvent('reloadMainMenu');
                this.getViewport().setLoading(false);
            },
            failure: function () {
            },
            scope: this
        });
    },

    logoutAction: function() {
        this.getViewport().setLoading(true);
        Ext.Ajax.request({
            url: '/api/user/logout',
            method: 'GET',
            success: function () {
                this.getViewport().fireEvent('reloadMainMenu');
                this.getViewport().setLoading(false);
            },
            failure: function () {
            },
            scope: this
        });
    }

});
