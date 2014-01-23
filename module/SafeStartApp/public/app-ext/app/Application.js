Ext.ns('SafeStartExt');

SafeStartExt.dateFormat = SafeStartExt.dateFormat || 'd/m/Y';
SafeStartExt.timeFormat = SafeStartExt.timeFormat || 'H:i';

Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext.ux': '/app-ext/lib/ux'
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
        'Contact',
        'Users',
        'SystemSettings'
    ],

    enableRouter: false,

    routes: {
        '/': 'main#showDefaultPage',
        'auth': 'main#showAuthPage',
        'companies': 'main#showCompaniesPage',
        'company': 'main#showCompanyPage',
        'company/:id': 'main#showCompanyPageById',
        'contact': 'main#showContactPage',
        'users': 'main#showUsersPage',
        'alerts': 'main#showAlertsPage',
        'system-statistic': 'main#showSystemStatisticPage',
        'system-settings': 'main#showSystemSettingsPage',
        'company-settings': 'main#showCompanySettingsPage',
        'users/:id': 'main#showUsersPageById',
        'alerts/:id': 'main#showAlertsPageById',
        'reset/:id': 'main#resetPassword'
    },

    acl: {
        'guest': [
            'showAuthPage',
            'showContactPage',
            'resetPassword'
        ],
        'companyUser': [
            'showCompanyPage',
            'showAlertsPage',
            'showContactPage',
            'resetPassword'
        ],
        'companyManager': [
            'showCompanyPage',
            'showAlertsPage',
            'showUsersPage',
            'showCompanySettingsPage',
            'showContactPage',
            'resetPassword'
        ],
        'companyAdmin': [
            'showCompanyPage',
            'showAlertsPage',
            'showUsersPage',
            'showCompanySettingsPage',
            'showContactPage',
            'resetPassword'
        ],
        'superAdmin': [
            'showCompaniesPage',
            'showCompanyPageById',
            'showAlertsPageById',
            'showUsersPage',
            'showUsersPageById',
            'showAlertsPage',
            'showAlertsPageById',
            'showSystemSettingsPage',
            'showSystemStatisticPage',
            'resetPassword'
        ]
    },

    isAllowed: function (action) {
        var role = this.getUserRecord().get('role');
        return Ext.isArray(this.acl[role]) && Ext.Array.contains(this.acl[role], action);
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
                if (!me.mainMenuLoaded) {
                    me.mainMenuLoaded = true;
                }
                mainView.fireEvent('mainMenuLoaded', result.mainMenu || {});

                if (me.getUserRecord().get('role') === 'companyUser') {
                    mainView.fireEvent('changeCompanyAction', me.getUserRecord().getCompany());
                }
            }
        });
    },

    getDefaultPage: function () {
        switch (this.getUserRecord().get('role')) {
            case 'guest':
                return 'auth';
            case 'companyUser':
            case 'companyManager':
            case 'companyAdmin':
                return 'company';
            default:
                return 'companies';
        }
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

    onBeforeDispatch: function (token, match, params) {
        return this.isAllowed(match.action);
    },

    launch: function () {
        Ext.form.field.Base.prototype.validateOnBlur = false;
        
        var loadingEl = Ext.get('appLoadingIndicator');
        if (loadingEl) {
            loadingEl.remove();
        }

        Ext.ux.Router.on({
            beforedispatch: this.onBeforeDispatch,
            scope: this
        });

        this.viewport = SafeStartExt.view.Viewport.create({}); 
        this.viewport.down('SafeStartExtMain').addListener('mainMenuLoaded', function () {
            Ext.ux.Router.init(this);
        }, this, {single: true, order: 'after', delay: 1});

        this.viewport.addListener('reloadMainMenu', this.loadMainMenu, this);
        this.loadMainMenu();
    },

    getViewport: function () {
        return this.viewport;
    }
	
});
