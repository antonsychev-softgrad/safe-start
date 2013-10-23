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

    needUpdate: true,

    init: function () {
        this.control({
            'SafeStartExtPanelCompaniesList': {
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtComponentCompanies': {
                activate: this.refreshPage,
                afterrender: this.refreshPage
            }
        });
    },

    changeCompanyAction: function (company) {
        this.getMainPanel().fireEvent('changeCompanyAction', company);
        this.setCompanyInfo(company);
    },

    setCompanyInfo: function (company) {
        this.getCompanyInfoPanel().setCompanyInfo(company);
    },

    refreshPage: function () {
        if (this.needUpdate) {
            this.needUpdate = false;
            this.getCompaniesListView().getListStore().load();
        }
    }
});
