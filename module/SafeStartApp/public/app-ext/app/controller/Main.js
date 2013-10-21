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
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentContact',
        ref: 'contactPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtBottomNav': {
                showPage: this.showPage
            },
            'viewport': {
                mainMenuLoaded: this.updateMainMenu
            }
        });   
    },

    updateMainMenu: function (menu) {
        var mainNavPanel = this.getMainNavPanel();
        this.getMainPanel().removeAll();
        Ext.each(mainNavPanel.query('button'), function (button, index) {
            if (Ext.Array.contains(menu, button.menuItem)) {
                button.show();
            } else {
                button.hide();
            }
        });
        var firstBtn = mainNavPanel.down('button{isHidden() == false}');
        this.showPage(firstBtn.menuItem);
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
        }
    }

});
