Ext.define('SafeStartExt.controller.Users', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'mainNavPanel'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtPanelUsersList',
        ref: 'companiesListView'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtContainerTopNav',
        ref: 'companiesTopNav'
    }, {
        selector: 'SafeStartExtComponentCompanies panel[name=user-info]',
        ref: 'companyInfoPanel'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtFormUser',
        ref: 'companyFormPanel'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtPanelUsersList': {
                changeUserAction: this.changeUserAction,
            }
        });
    },


});
