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
            }
        });
        

        var me = this;
        Ext.ux.Router.on({
            routemissed: function(token, match, params) {
                switch(token) {
                    case 'SystemStatistic':
                    case 'SystemSettings':
                    case 'Alerts':
                        me.notSupportedAction();
                        break;
                }
            }
        });
    },

    updateMainMenu: function (menu) {
        var mainNavPanel = this.getMainNavPanel();
        this.getMainPanel().removeAll();
        mainNavPanel.applyButtons(menu);

        var currentHash = Ext.History.getHash();
        if (!currentHash) {
            var getter;
            Ext.each(menu, function (name) {
                getter = 'get' + name + 'Panel';
                if (typeof this[getter] === 'function') {
                    this.redirectTo(name);
                    return false;
                }
            }, this);
        }
    },

    changeCompanyAction: function (company) {
        if (company) {
            SafeStartExt.companyRecord = company;
            this.getMainNavPanel().enableAll();
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

    showPage: function (obj, name) {
        var pagePanel = null,
            getter = 'get' + name + 'Panel',
            alias;

        if (typeof this[getter] == 'function') {
            pagePanel = this[getter]();
            if (! pagePanel) {
                alias = 'SafeStartExt.view.component.' + name;
                pagePanel = Ext.create(alias);
                this.getMainPanel().add(pagePanel);
            }
            this.getMainNavPanel().setActiveButton(name);
            this.getMainPanel().getLayout().setActiveItem(pagePanel);
        } else {
            this.notSupportedAction();
        }
    },

    redirectTo: function(name) {
        Ext.History.add(name);
    },

    changeTab: function(name) {
        switch(name) {
            case 'Company':
                this.redirectTo('Company/' + SafeStartExt.companyRecord.getId());
                break;
            default:
                this.redirectTo(name);
        }
    },

    showCompanyById: function(params) {
        var store = Ext.create('SafeStartExt.view.panel.CompaniesList').getListStore(),
            company,
            me = this;

        this.showPage({}, 'Company');

        store.on('load', function() {
            company = this.getById(parseInt(params.id));
            if (company) {
                me.getMainPanel().fireEvent('setCompanyAction', company);
                me.getMainNavPanel().enableAll();
            }
        });
        store.load();
    },

    showCompany: function() {
        var company = this.getApplication().companyRecord;

        if (company.getId()) {
            this.showPage({}, 'Company');
            this.getMainPanel().fireEvent('setCompanyAction', company);
            this.getMainNavPanel().enableAll();
        }
    }

});
