Ext.define('SafeStartApp.controller.Users', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [
        'SafeStartApp.view.forms.CompanyUser'
    ],

    init: function () {

    },

    config: {
        control: {
            navMain: {
                itemtap: 'onSelectAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartUsersPage list[name=users]',
            mainToolbar: 'SafeStartUsersPage SafeStartMainToolbar',
            infoPanel: 'SafeStartUsersPage panel[name=user-info]',
            addButton: 'SafeStartUsersPage button[action=add-user]',
            companyUserForm: 'SafeStartUsersPage SafeStartCompanyUserForm'
        }
    },


    onSelectAction: function (element, index, target, record, e, eOpts) {
        var button = null;
        var form = this.getCompanyUserForm();

        if (!form) {
            form = this._createForm();
        }
        form.setRecord(record);
        form.down('button[name=delete-data]').show();
        form.down('button[name=send-credentials]').show();
        form.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        var form = this.getCompanyUserForm();

        if (!form) {
            form = this._createForm();
        }
        if (this.userModel) {
            //todo: check if form not empty
            this.userModel.destroy();
        }
        this.userModel = Ext.create('SafeStartApp.model.User');
        form.setRecord(this.userModel);
        form.down('button[name=delete-data]').hide();
        form.down('button[name=send-credentials]').hide();
        form.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        var form = this.getCompanyUserForm();

        if (!this.userModel) this.userModel = Ext.create('SafeStartApp.model.User');
        if (this.validateFormByModel(this.userModel, form)) {
            var self = this;
            var formValues = form.getValues();
            formValues.companyId = SafeStartApp.companyModel.get('id');
            SafeStartApp.AJAX('user/' + form.getValues().id + '/update', formValues, function (result) {
                if (result.userId) {
                    self._reloadStore(result.userId);
                    form.down('button[name=delete-data]').show();
                    form.down('button[name=send-credentials]').show();
                    form.down('button[name=reset-data]').hide();
                }
            });
        }
    },

    sendCredentialsAction: function () {
        var form = this.getCompanyUserForm();
        SafeStartApp.AJAX('user/' + form.getValues().id + '/send-credentials', {}, function (result) {

        });
    },

    deleteAction: function () {
        var form = this.getCompanyUserForm(),
            self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this user account?", function(btn) {
            if (btn == 'yes') {
                SafeStartApp.AJAX('user/' + form.getValues().id + '/delete', {}, function (result) {
                    self.getNavMain().getStore().loadData();
                    form.reset();
                    form.down('button[name=delete-data]').hide();
                    form.down('button[name=send-credentials]').hide();
                    form.down('button[name=reset-data]').show();
                });
            }
        });
    },

    resetAction: function() {
        var form = this.getCompanyUserForm();
        if (form) {
            form.reset();
        }
    },

    _createForm: function () {
        var form = this.getCompanyUserForm(),
            infoPanel = this.getInfoPanel();
        if (!form) {
            form = Ext.create('SafeStartApp.view.forms.CompanyUser');
            infoPanel.removeAll(true);
            infoPanel.add(form);
            form.addListener('save-data', this.saveAction, this);
            form.addListener('send-credentials', this.sendCredentialsAction, this);
            form.addListener('reset-data', this.resetAction, this);
            form.addListener('delete-data', this.deleteAction, this);
        }
        return form;
    },

    _reloadStore: function (userId) {
        var form = this.getCompanyUserForm();
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            if (!userId) return;
            form.setRecord(this.getNavMain().getStore().getById(userId));
        }, this, {single: true});

    }



});