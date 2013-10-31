Ext.define('SafeStartExt.controller.Companies', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'mainNavPanel'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtPanelCompaniesList',
        ref: 'companiesListView'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtContainerTopNav',
        ref: 'companiesTopNav'
    }, {
        selector: 'SafeStartExtComponentCompanies panel[name=company-info]',
        ref: 'companyInfoPanel'
    }, {
        selector: 'SafeStartExtComponentCompanies SafeStartExtFormCompany',
        ref: 'companyFormPanel'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtPanelCompaniesList': {
                changeCompanyAction: this.changeCompanyAction,
                addCompanyAction: this.addCompanyAction
            },
            'SafeStartExtFormCompany': {
                updateCompanyAction: this.updateCompany,
                deleteCompanyAction: this.deleteCompany,
                manageCompanyAction: this.manageCompany,
                sendPasswordAction: this.sendPassword
            },
        });
    },

    addCompanyAction: function() {
        this._showForm();
        this._hideFormButtons();
        this.getCompanyFormPanel().getForm().reset(true);
        this.getCompanyFormPanel().getForm().setValues({ id: 0 });
    },

    changeCompanyAction: function (company) {
        this.getMainPanel().fireEvent('changeCompanyAction', company);
        this.setCompanyInfo(company);
    },

    setCompanyInfo: function (company) {
        this._showForm();
        this._showFormButtons();
        this.getCompanyFormPanel().getForm().loadRecord(company);

        if (company.get('restricted')) {
            this.getCompanyFormPanel().down('[name=subscription]').enable();
        } else {
            this.getCompanyFormPanel().down('[name=subscription]').disable();
        }
    },

    _hideFormButtons: function() {
        this.getCompanyFormPanel().down('button[name=delete-data]').hide();
        this.getCompanyFormPanel().down('button[name=manage-data]').hide();
        this.getCompanyFormPanel().down('button[name=send-password]').hide();

    },

    _showFormButtons: function() {
        this.getCompanyFormPanel().down('button[name=delete-data]').show();
        this.getCompanyFormPanel().down('button[name=manage-data]').show();
        this.getCompanyFormPanel().down('button[name=send-password]').show();
    },

    _showForm: function() {
        if (!this.getCompanyFormPanel()) {
            this._createCompanyForm();
        } else {
            this.getCompanyInfoPanel().show();
        }
    },

    updateCompany: function(company, values) {
        this._prepareFields(company, values);

        var me = this;
        SafeStartExt.Ajax.request({
            url: 'admin/company/' + values.id + '/update',
            data: values,
            success: function (res) {
                if (res.done) {
                    me._onCompanyUpdated(company);
                }
            }
        });
    },

    deleteCompany: function(company) {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'admin/company/' + company.getId() + '/delete',
            success: function (res) {
                if (res.done) {
                    me.getCompaniesListView().getListStore().load();
                    me.getCompanyInfoPanel().hide();
                }
            }
        });
    },

    manageCompany: function(company) {
        this.getMainPanel().fireEvent('changeTab', 'Company');
    },

    sendPassword: function(company) {
        SafeStartExt.Ajax.request({
            url: 'admin/company/' + company.getId() + '/send-credentials',
            success: function (res) {

            }
        });
    },

    _onCompanyUpdated: function(company) {
        var store = this.getCompaniesListView().getListStore(),
            form =  this.getCompanyFormPanel().getForm();
        
        if (company) {
            store.on('load', function() {
                form.loadRecord(store.getById(company.getId()));
            }, null, {single: true});
        } else {
            form.reset(true);
        }

        store.load();
    },

    _prepareFields: function(company, values) {
        if (!values.id) {
            values.id = 0;
        }
        if (values.expiry_date) {
            values.expiry_date = new Date(values.expiry_date).getTime() / 1000;
        }
        
        values.restricted = (values.restricted === 'on') ? 1 : 0;

        if (!values.restricted && company) {
            values.max_users = company.get('max_users');
            values.max_vehicles = company.get('max_vehicles');
            values.expiry_date = company.get('expiry_date');
        } else if (!values.restricted && !company) {
            values.max_users = null;
            values.max_vehicles = null;
            values.expiry_date = null;
        }
    },

    _createCompanyForm: function() {
        this.getCompanyInfoPanel().add(Ext.create('SafeStartExt.view.form.Company'));
    }

});
