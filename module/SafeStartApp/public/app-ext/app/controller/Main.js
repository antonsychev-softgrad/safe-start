Ext.define('SafeStartExt.controller.Main', {
    extend: 'Ext.app.Controller',
    require: [ 'Ext.ux.Router' ],
    refs: [{
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'mainNavPanel'
    }, {
        selector: 'viewport > SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentAuth',
        ref: 'authPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentUsers',
        ref: 'usersPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentCompany',
        ref: 'companyPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentCompanies',
        ref: 'companiesPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentContact',
        ref: 'contactPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentSystemStatistic',
        ref: 'systemStatisticPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentSystemSettings',
        ref: 'systemSettingsPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentCompanySettings',
        ref: 'companySettingsPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentAlerts',
        ref: 'alertsPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtBottomNav': {
                redirectTo: this.redirectTo,
                changeTab: this.changeTab
            },
            'SafeStartExtMain': {
                mainMenuLoaded: this.updateMainMenu,
                notSupportedAction: this.notSupportedAction,
                changeCompanyAction: this.changeCompanyAction,
                changeTab: this.changeTab
            },
            'SafeStartExtAbstractAlerts': {
                increaseAlertsCounter: this.increaseAlertsCounter,
                decreaseAlertsCounter: this.decreaseAlertsCounter
            }
        });
        

        var me = this;
        Ext.ux.Router.on({
            routemissed: function(token, match, params) {
                switch(token) {
                    case 'company-settings':
                        me.notSupportedAction();
                        break;
                }
                me.showDefaultPage();
            }
        });
        this.startUpdateAlertsBadge();
    },


    updateMainMenu: function (menu) {
        var mainNavPanel = this.getMainNavPanel();
        this.getMainPanel().removeAll();
        mainNavPanel.applyButtons(menu);

        if (! Ext.History.getToken()) {
            this.getApplication().getDefaultPage();
        }
    },

    changeCompanyAction: function (company) {
        if (company) {
            SafeStartExt.companyRecord = company;
            this.getMainNavPanel().enableAll();
            this.updateAlertsBadge();
        }
    },

    notSupportedAction: function () {
        Ext.Msg.alert({
            msg: 'Not supported by Internet Explorer 9 and older versions. ' +
                'Please download one of modern browsers, like a Google Chrome, Safari or newest version of IE.',
            width: 300,
            buttons: Ext.Msg.OK        
        });
    },

    showDefaultPage: function () {
        this.redirectTo(this.getApplication().getDefaultPage());
    },

    showAuthPage: function () {
        var page = this.getAuthPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Auth');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Auth');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showContactPage: function () {
        var page = this.getContactPanel();
        if (!page) {
            page = Ext.create('SafeStartExt.view.component.Contact');
            this.getMainPanel().add(page);
        }
        this.getMainNavPanel().setActiveButton('Contact');
        this.getMainPanel().getLayout().setActiveItem(page);
        this.getMainNavPanel().enableAll();
    },

    showCompaniesPage: function () {
        var page = this.getCompaniesPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Companies');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Companies');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showCompanyPageById: function (params) {
        if (! params.id) {
            this.redirectTo(this.getApplication().getDefaultPage());
            return;
        }

        this.getMainPanel().fireEvent('setCompanyByIdAction', params.id);

        var page = this.getCompanyPanel();
        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Company');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Company');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showCompanyPage: function () {
        var page = this.getCompanyPanel();
        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Company');
            this.getMainPanel().add(page);
        }


        this.getMainPanel().fireEvent('changeCompanyAction', this.getApplication().getCompanyRecord());

        this.getMainNavPanel().setActiveButton('Company');
        this.getMainPanel().getLayout().setActiveItem(page);
        this.getMainNavPanel().enableAll();
    },

    showUsersPage: function () {
        var page = this.getUsersPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Users');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Users');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showUsersPageById: function (params) {
        if (! params.id) {
            this.redirectTo(this.getApplication().getDefaultPage());
            return;
        }

        this.getMainPanel().fireEvent('setCompanyByIdAction', params.id);

        var page = this.getUsersPanel();
        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Users');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Users');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showAlertsPage: function () {
        var page = this.getAlertsPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Alerts', {
                companyId: this.getApplication().getCompanyRecord().getId()
            });
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Alerts');
        this.getMainPanel().getLayout().setActiveItem(page);
        this.getMainNavPanel().enableAll();
    },

    showAlertsPageById: function (params) {
        if (! params.id) {
            this.redirectTo(this.getApplication().getDefaultPage());
            return;
        }

        this.getMainPanel().fireEvent('setCompanyByIdAction', params.id);

        var page = this.getAlertsPanel();
        if (! page) {
            page = Ext.create('SafeStartExt.view.component.Alerts', {
                companyId: params.id
            });
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('Alerts');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showSystemStatisticPage: function () {
        var page = this.getSystemStatisticPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.SystemStatistic');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('SystemStatistic');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showSystemSettingsPage: function () {
        var page = this.getSystemSettingsPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.SystemSettings');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('SystemSettings');
        this.getMainPanel().getLayout().setActiveItem(page);
    },

    showCompanySettingsPage: function () {
        var page = this.getCompanySettingsPanel();

        if (! page) {
            page = Ext.create('SafeStartExt.view.component.CompanySettings');
            this.getMainPanel().add(page);
        }

        this.getMainNavPanel().setActiveButton('CompanySettings');
        this.getMainPanel().getLayout().setActiveItem(page);
        this.getMainNavPanel().enableAll();
    },

    redirectTo: function(name) {
        Ext.History.add(name);
    },

    changeTab: function(name) {
        switch(name) {
            case 'Company':
                if (this.getApplication().isAllowed('showCompanyPageById')) {
                    this.redirectTo('company/' + SafeStartExt.companyRecord.getId());
                } else {
                    this.redirectTo('company');
                }
                break;
            case 'Companies':
                this.redirectTo('companies');
                break;
            case 'CompanySettings':
                this.redirectTo('company-settings');
                break;
            case 'SystemSettings':
                this.redirectTo('system-settings');
                break;
            case 'SystemStatistic':
                this.redirectTo('system-statistic');
                break;
            case 'Alerts':
                if (this.getApplication().isAllowed('showAlertsPageById')) {
                    this.redirectTo('alerts/' + SafeStartExt.companyRecord.getId());
                } else {
                    this.redirectTo('alerts');
                }
                break;
            case 'Users':
                if (this.getApplication().isAllowed('showUsersPageById')) {
                    this.redirectTo('users/' + SafeStartExt.companyRecord.getId());
                } else {
                    this.redirectTo('users');
                }
                break;
            default:
                this.redirectTo(name.toLowerCase());
                break;
        }
    },

    startUpdateAlertsBadge: function () {
        var me = this;
        this.stopUpdateAlertsBadge();
        this.updateAlertsIntervalId = setInterval(function () {
            me.updateAlertsBadge();
        }, 60000);
    },

    stopUpdateAlertsBadge: function () {
        if (this.hasOwnProperty('updateAlertsIntervalId')) {
            clearInterval(this.updateAlertsIntervalId); 
            delete this.updateAlertsIntervalId;
        }
    },

    updateAlertsBadge: function() {
        var me = this,
            companyRecord = SafeStartExt.companyRecord,
            mainNavPanel = this.getMainNavPanel();

        if (!companyRecord || !companyRecord.get || !companyRecord.get('id')) {
            return;
        }
        SafeStartExt.Ajax.request({
            url: 'company/' + companyRecord.get('id') + '/get-new-incoming?now=' + new Date().getTime(),
            success: function (result) {
                var badgeText = '';
                if (result.alerts) {
                    badgeText = result.alerts;
                }

                me.alertsCounter = parseInt(result.alerts, 10);
                mainNavPanel.setBadge('Alerts', badgeText);
            },
            silent: true
        });
    },

    increaseAlertsCounter: function () {
        console.log('inc');
        var mainNavPanel = this.getMainNavPanel();
        this.alertsCounter++;
        mainNavPanel.setBadge('Alerts', this.alertsCounter);
    },

    decreaseAlertsCounter: function () {
        console.log('dec');
        var mainNavPanel = this.getMainNavPanel();
        if (this.alertsCounter < 2) {
            this.alertsCounter = 0;
            mainNavPanel.setBadge('Alerts', '');
        }
        this.alertsCounter--;
        mainNavPanel.setBadge('Alerts', this.alertsCounter);
    }
});
