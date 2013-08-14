Ext.define('SafeStartApp.controller.Companies', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [
        'SafeStartApp.model.CompanySubscription'
    ],

    init: function(){

    },

    config: {
        control: {
            navMain: {
                itemtap: 'onSelectAction'
            },
            addCompanyButton: {
                tap: 'addAction'
            }
        },

        refs: {
            main: 'SafeStartCompaniesPage',
            navMain: 'SafeStartCompaniesPage > list[name=companies]',
            companyInfoPanel: 'SafeStartCompaniesPage > panel[name=company-info]',
            addCompanyButton: 'SafeStartMainToolbar > button[action=add-company]'
        }
    },


    onSelectAction: function(element, index, target, record, e, eOpts) {
        if (!this.currentCompanyForm) this._createForm();
        this.currentCompanyForm.setRecord(record);
        if (!record.get('restricted')) this.currentCompanyForm.down('fieldset').down('fieldset').disable();
        if (record.get('expiry_date')) this.currentCompanyForm.down('datepickerfield').setValue(new Date(record.get('expiry_date') * 1000));
    },

    addAction: function() {
        if (!this.currentCompanyForm) this._createForm();
        if (this.companyModel) {
            //todo: check if form bot empty
            this.companyModel.destroy();
        }
        this.companyModel = Ext.create('SafeStartApp.model.Company');
        this.currentCompanyForm.setRecord(this.companyModel);
        this.currentCompanyForm.down('fieldset').down('fieldset').disable();
    },

    saveAction: function() {
        var self = this;
        if (!this.companyModel) this.companyModel = Ext.create('SafeStartApp.model.Company');
        if (this.validateFormByModel(this.companyModel, this.currentCompanyForm)) {
            if (!this.companySubscriptionModel) this.companySubscriptionModel = Ext.create('SafeStartApp.model.CompanySubscription');
            if (this.currentCompanyForm.getValues().restricted) {
                if (this.validateFormByModel(this.companySubscriptionModel, this.currentCompanyForm)) {
                    var formValues = this.currentCompanyForm.getValues();
                    formValues.expiry_date = (this.currentCompanyForm.down('datepickerfield').getValue().getTime() / 1000);
                    SafeStartApp.AJAX('admin/'+this.currentCompanyForm.getValues().id+'/company/update', formValues, function (result) {

                    });
                }
            }
        }
    },

    _createForm: function() {
        if (!this.currentCompanyForm) {
            this.currentCompanyForm = Ext.create('SafeStartApp.view.forms.CompanySettings');
            this.getCompanyInfoPanel().removeAll(true);
            this.getCompanyInfoPanel().setHtml('');
            this.getCompanyInfoPanel().add(this.currentCompanyForm);
            this.currentCompanyForm.addListener('save-data', this.saveAction, this);
        }
    }

});