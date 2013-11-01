Ext.define('SafeStartApp.controller.Main', {
    extend: 'Ext.app.Controller',

    config: {
        routes: {
            'auth': 'showAuthAction',
            'companies': 'showCompainesAction',
            'company/:companyId': 'showCompanyAction',
            'alerts/:companyId': 'showAlertsAction',
            'users/:companyId': 'showUsersAction',
            'system-settings': 'showSystemSettingsAction',
            'system-statistic': 'showSystemStatisticAction',
            'company-settings': 'showCompanySettingsAction',
            'contact': 'showContactAction'
        },

        control: {
            SafeStartMainView: {
                changeTab: 'changeTab'
            }
        },

        refs: {
            mainView: 'SafeStartMainView',
            companiesPage: 'SafeStartCompaniesPage',
            companyPage: 'SafeStartCompanyPage',
            alertsPage: 'SafeStartAlertsPage',
            usersPage: 'SafeStartUsersPage',
            systemSettingsPage: 'SafeStartSystemSettingsPage',
            systemStatisticPage: 'SafeStartSystemStatisticPage',
            vehiclesPage: 'SafeStartVehiclesPage',
            contactPage: 'SafeStartContactPage',
            authPage: 'SafeStartAuthPage',
            companySettingsPage: 'SafeStartCompanySettingsPage'
        }
    },

    changeTab: function (action) {
        switch (action) {
            case 'companies':
            case 'vehicles':
            case 'auth':
            case 'system-settings':
            case 'system-statistic':
            case 'company-settings':
            case 'contact':
                this.redirectTo(action);
                break;
            case 'alerts':
            case 'company':
            case 'users':
                if (SafeStartApp.companyModel) {
                    this.redirectTo(action + '/' + SafeStartApp.companyModel.get('id'));
                }
                break;
        }
    },

    showCompainesAction: function () {
        if (this.getMainView()) {
            this.activateTab(this.getCompaniesPage());
        }
    },

    showCompanyAction: function (companyId) {
        var me = this;
        if (SafeStartApp.mainMenuLoaded) {
            this.activateTabByCompany(this.getCompanyPage(), companyId);
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTabByCompany(me.getCompanyPage(), companyId, true);
                },
                single: true
            });
        }
    },

    enableCompanyTabs: function () {
        if (this.getCompanyPage()) {
            this.getCompanyPage().enable();
        }
        if (this.getUsersPage()) {
            this.getUsersPage().enable();
        }
        if (this.getAlertsPage()) {
            this.getAlertsPage().enable();
        }
    },

    activateTabByCompany: function (panel, companyId, silent) {
        var me = this;
        var companyModel = SafeStartApp.companyModel;
        if (! companyModel.get('id')) {
            var companiesStore = this.getCompaniesPage().companiesStore;

            if (companiesStore.getCount()) {
                companyModel = companiesStore.findRecord('id', companyId);
                if (companyModel) {
                    SafeStartApp.companyModel = companyModel;
                    this.enableCompanyTabs();
                    this.activateTab(panel, silent);
                }
            } else {
                companiesStore.on({
                    load: function () {
                        companyModel = companiesStore.findRecord('id', companyId);
                        if (companyModel) {
                            SafeStartApp.companyModel = companyModel;
                            me.enableCompanyTabs();
                            me.activateTab(panel, silent);
                        }
                    }
                });
            }
            return;
        }
        this.activateTab(panel, silent);
    },

    activateTab: function (panel, silent) {
        if (! panel) {
            return;
        }
        if (silent) {
            panel.setShowAnimation({});
        }
        this.getMainView().setActiveItem(panel);
    },

    showAlertsAction: function (companyId) {
        var me = this;
        if (SafeStartApp.mainMenuLoaded) {
            this.activateTabByCompany(this.getAlertsPage(), companyId);
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTabByCompany(me.getAlertsPage(), companyId, true);
                },
                single: true
            });
        }
    },

    showUsersAction: function (companyId) {
        var me = this;
        if (SafeStartApp.mainMenuLoaded) {
            this.activateTabByCompany(this.getUsersPage(), companyId);
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTabByCompany(me.getUsersPage(), companyId, true);
                },
                single: true
            });
        }
    },

    showSystemSettingsAction: function () {
        var me = this;
        if (this.getMainView()) {
            this.activateTab(this.getSystemSettingsPage());
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTab(me.getSystemSettingsPage(), true);
                },
                single: true
            });
        }
    },

    showCompanySettingsAction: function () {
        var me = this;
        if (this.getMainView()) {
            this.activateTab(this.getCompanySettingsPage());
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTab(me.getCompanySettingsPage(), true);
                },
                single: true
            });
        }
    },

    showSystemStatisticAction: function () {
        var me = this;
        if (this.getMainView()) {
            this.activateTab(this.getSystemStatisticPage());
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTab(me.getSystemStatisticPage(), true);
                },
                single: true
            });
        }
    },

    showContactAction: function () {
        var me = this;
        if (this.getMainView()) {
            this.activateTab(this.getContactPage());
        } else {
            Ext.Viewport.on({
                mainMenuLoaded: function () {
                    me.activateTab(me.getContactPage(), true);
                },
                single: true
            });
        }
    },

    showAuthAction: function () {
        if (this.getMainView()) {
            this.activateTab(this.getAuthPage());
        }
    }

});
