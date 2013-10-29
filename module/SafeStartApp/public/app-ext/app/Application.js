Ext.ns('SafeStartExt');

SafeStartExt.dateFormat = SafeStartExt.dateFormat || 'Y/m/d';
SafeStartExt.timeFormat = SafeStartExt.timeFormat || 'H:i';

Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext.ux': 'lib/ux'
    }
});

Ext.define('SafeStartExt.Application', {
    name: 'SafeStartExt',

    extend: 'Ext.app.Application',

    appFolder: '/app-ext/app',

    requires: [
        'Ext.ux.Router',
        'SafeStartExt.view.Viewport',
        'SafeStartExt.model.User',
        'SafeStartExt.Ajax'
    ],

    controllers: [
        'Main',
        'Auth',
        'Companies',
        'Company',
        'Contact'
    ],

    routes: {
        'Auth': 'main#showPage',
        'Contact': 'main#showPage',
        'Companies': 'main#showPage',
        'Company': 'main#showPage'
    },

    userRecord: null,
    companyRecord: null,
    mainMenuLoaded: false,

    loadMainMenu: function () {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'web-panel/getMainMenu',
            success: function (result) {
                var mainView = me.getViewport().down('SafeStartExtMain');
                me.setUserData(result.userInfo);
                mainView.fireEvent('mainMenuLoaded', result.mainMenu || {});
                if (!me.mainMenuLoaded) {
                    me.mainMenuLoaded = true;
                    Ext.ux.Router.parse(Ext.History.getHash());
                }

                if (me.getUserRecord().get('role') === 'companyUser') {
                    mainView.fireEvent('changeCompanyAction', me.getUserRecord().getCompany());
                }
            }
        });
    },

    setUserData: function (data) {
        data = data || {};

        this.userRecord = SafeStartExt.model.User.create(data);
        if (data.company) {
            this.companyRecord = SafeStartExt.model.Company.create(data.company);
        } else {
            this.companyRecord = SafeStartExt.model.Company.create({});
        }
        this.userRecord.setCompany(this.companyRecord);
    },

    getUserRecord: function () {
        return this.userRecord;
    },

    getCompanyRecord: function () {
        return this.companyRecord;
    },

    launch: function () {
        var loadingEl = Ext.get('appLoadingIndicator');
        if (loadingEl) {
            loadingEl.remove();
        }

        var me = this;
        Ext.ux.Router.on({
            beforedispatch: function(token, match, params) {
                return me.mainMenuLoaded;
            },
        });
        
        this.viewport = SafeStartExt.view.Viewport.create({}); 
        this.viewport.on('reloadMainMenu', this.loadMainMenu, this);
        this.loadMainMenu();
    },

    getViewport: function () {
        return this.viewport;
    }
	
	// showRequestFailureInfoMsg: function (result, failureCalBack) {
 //        var func = Ext.emptyFn();
 //        if (failureCalBack && typeof failureCalBack == 'function') func = failureCalBack;
 //        var errorMessage = '';
 //        if (result.data && result.data.errorMessage) errorMessage = result.data.errorMessage;
 //        this.showFailureInfoMsg(errorMessage, func);
 //    }
});
