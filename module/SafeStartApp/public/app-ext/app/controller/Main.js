Ext.define('SafeStartExt.controller.Main', {
    extend: 'Ext.app.Controller',

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
                showPage: this.showPage
            },
            'SafeStartExtMain': {
                mainMenuLoaded: this.updateMainMenu,
                notSupportedAction: this.notSupportedAction,
                changeCompanyAction: this.changeCompanyAction
            }
        });   
    },

    updateMainMenu: function (menu) {
        var mainNavPanel = this.getMainNavPanel();
        this.getMainPanel().removeAll();
        mainNavPanel.applyButtons(menu);

        var getter;
        Ext.each(menu, function (name) {
            getter = 'get' + name + 'Panel';
            if (typeof this[getter] === 'function') {
                this.showPage(name);
                return false;
            }
        }, this);
    },

    changeCompanyAction: function (company) {
        if (company) {
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

    showPage: function (name) {
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
            this.getMainPanel().getLayout().setActiveItem(pagePanel);
            this.getMainNavPanel().setActiveButton(name);
        } else {
            this.notSupportedAction();
        }
    }

});
