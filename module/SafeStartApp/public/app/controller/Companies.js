Ext.define('SafeStartApp.controller.Companies', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [
        'SafeStartApp.model.CompanySubscription'
    ],

    init: function () {

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


    onSelectAction: function (element, index, target, record, e, eOpts) {
        if (!this.currentCompanyForm) this._createForm();
        this.currentCompanyForm.setRecord(record);
        if (!record.get('restricted')) this.currentCompanyForm.down('fieldset').down('fieldset').disable();
        if (record.get('expiry_date')) this.currentCompanyForm.down('datepickerfield').setValue(new Date(record.get('expiry_date') * 1000));
        this.currentCompanyForm.down('button[name=delete-data]').show();
        this.currentCompanyForm.down('button[name=send-credentials]').show();
        this.currentCompanyForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        if (!this.currentCompanyForm) this._createForm();
        if (this.companyModel) {
            //todo: check if form bot empty
            this.companyModel.destroy();
        }
        this.companyModel = Ext.create('SafeStartApp.model.Company');
        this.currentCompanyForm.setRecord(this.companyModel);
        this.currentCompanyForm.down('fieldset').down('fieldset').disable();
        this.currentCompanyForm.down('button[name=delete-data]').hide();
        this.currentCompanyForm.down('button[name=send-credentials]').hide();
        this.currentCompanyForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.companyModel) this.companyModel = Ext.create('SafeStartApp.model.Company');
        if (this.validateFormByModel(this.companyModel, this.currentCompanyForm)) {
            if (!this.companySubscriptionModel) this.companySubscriptionModel = Ext.create('SafeStartApp.model.CompanySubscription');
            if (this.currentCompanyForm.getValues().restricted) {
                if (this.validateFormByModel(this.companySubscriptionModel, this.currentCompanyForm)) {
                    this._saveData();
                }
            } else {
                this._saveData();
            }
        }
    },

    sendCredentialsAction: function () {
        SafeStartApp.AJAX('admin/company/' + this.currentCompanyForm.getValues().id + '/send-credentials', {}, function (result) {

        });
    },

    deleteAction: function () {
        //todo: show confirm dialog
        var self = this;
        SafeStartApp.AJAX('admin/company/' + this.currentCompanyForm.getValues().id + '/delete', {}, function (result) {
            self.getNavMain().getStore().loadData();
            self.currentCompanyForm.reset();
            self.currentCompanyForm.down('button[name=delete-data]').hide();
            self.currentCompanyForm.down('button[name=send-credentials]').hide();
            self.currentCompanyForm.down('button[name=reset-data]').show();
        });
    },

    resetAction: function() {
        this.currentCompanyForm.reset();
    },

    _createForm: function () {
        if (!this.currentCompanyForm) {
            this.currentCompanyForm = Ext.create('SafeStartApp.view.forms.CompanySettings');
            this.getCompanyInfoPanel().removeAll(true);
            this.getCompanyInfoPanel().setHtml('');
            this.getCompanyInfoPanel().add(this.currentCompanyForm);
            this.currentCompanyForm.addListener('save-data', this.saveAction, this);
            this.currentCompanyForm.addListener('send-credentials', this.sendCredentialsAction, this);
            this.currentCompanyForm.addListener('reset-data', this.resetAction, this);
            this.currentCompanyForm.addListener('delete-data', this.deleteAction, this);
        }
    },

    _saveData: function () {
        var self = this;
        var formValues = this.currentCompanyForm.getValues();
        if (this.currentCompanyForm.down('datepickerfield').getValue()) formValues.expiry_date = (this.currentCompanyForm.down('datepickerfield').getValue().getTime() / 1000);
        else formValues.expiry_date = null;
        SafeStartApp.AJAX('admin/company/' + this.currentCompanyForm.getValues().id + '/update', formValues, function (result) {
            if (result.companyId) {
                self._reloadStore(result.companyId);
                self.currentCompanyForm.down('button[name=delete-data]').show();
                self.currentCompanyForm.down('button[name=send-credentials]').show();
                self.currentCompanyForm.down('button[name=reset-data]').hide();
            }
        });
    },

    _reloadStore: function (companyId) {
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            this.currentCompanyForm.setRecord(this.getNavMain().getStore().getById(companyId));
        }, this);

    }

});