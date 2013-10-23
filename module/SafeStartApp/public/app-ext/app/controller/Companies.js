Ext.define('SafeStartExt.controller.Companies', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'SafeStartExtComponentCompanies SafeStartExtPanelCompaniesList',
        ref: 'companiesListView'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtContainerTopNav',
        ref: 'companiesTopNav'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtPanelCompanyInfo',
        ref: 'companyInfoPanel'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtPanelCompaniesList': {
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtMain': {

            }
        });
    },

    changeCompanyAction: function (company) {
        this.getMainPanel().fireEvent('changeCompanyAction', company);
        this.setCompanyInfo(company);
    },

    setCompanyInfo: function (company) {
        this.getCompanyInfoPanel().setCompanyInfo(company);
    }

});
