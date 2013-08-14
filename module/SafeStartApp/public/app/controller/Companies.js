Ext.define('SafeStartApp.controller.Companies', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    init: function(){

    },

    config: {
        control: {
            navMain: {
                itemsingletap: 'onCompanySelectAction'
            }
        },

        refs: {
            main: 'SafeStartCompaniesPage',
            navMain: 'SafeStartCompaniesPage > list[name=companies]',
            companyInfo: 'SafeStartCompaniesPage > panel[name=company-info]'
        }
    },


    onCompanySelectAction: function(element, index, target, record, e, eOpts) {
        var record = element.getStore().getAt(index);
        if (!this.currentCompanyForm) {
            this.currentCompanyForm = Ext.create('SafeStartApp.view.forms.CompanySettings');
            this.getCompanyInfo().removeAll(true);
            this.getCompanyInfo().add(this.currentCompanyForm);
        }
        this.currentCompanyForm.setRecord(record);
    }

});