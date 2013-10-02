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
            navMain: 'SafeStartUsersPage > list[name=users]',
            mainToolbar: 'SafeStartUsersPage > SafeStartMainToolbar',
            infoPanel: 'SafeStartUsersPage > panel[name=user-info]',
            addButton: 'SafeStartUsersPage  button[action=add-user]'
        }
    },


    onSelectAction: function (element, index, target, record, e, eOpts) {
        var button = null;
        if (Ext.os.deviceType !== 'Desktop') {
            button = this.getMainToolbar().down('button[action=toggle-menu]');
            if (button) {
                button.getHandler().call(button, button);
            }
        }

        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(record);
        this.currentForm.down('button[name=delete-data]').show();
        this.currentForm.down('button[name=send-credentials]').show();
        this.currentForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        if (!this.currentForm) this._createForm();
        if (this.userModel) {
            //todo: check if form not empty
            this.userModel.destroy();
        }
        this.userModel = Ext.create('SafeStartApp.model.User');
        this.currentForm.setRecord(this.userModel);
        this.currentForm.down('button[name=delete-data]').hide();
        this.currentForm.down('button[name=send-credentials]').hide();
        this.currentForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.userModel) this.userModel = Ext.create('SafeStartApp.model.User');
        if (this.validateFormByModel(this.userModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            formValues.companyId = SafeStartApp.companyModel.get('id');
            SafeStartApp.AJAX('user/' + this.currentForm.getValues().id + '/update', formValues, function (result) {
                if (result.userId) {
                    self._reloadStore(result.userId);
                    self.currentForm.down('button[name=delete-data]').show();
                    self.currentForm.down('button[name=send-credentials]').show();
                    self.currentForm.down('button[name=reset-data]').hide();
                }
            });
        }
    },

    sendCredentialsAction: function () {
        SafeStartApp.AJAX('user/' + this.currentForm.getValues().id + '/send-credentials', {}, function (result) {

        });
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this user account?", function(){
            SafeStartApp.AJAX('user/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                self.getNavMain().getStore().loadData();
                self.currentForm.reset();
                self.currentForm.down('button[name=delete-data]').hide();
                self.currentForm.down('button[name=send-credentials]').hide();
                self.currentForm.down('button[name=reset-data]').show();
            });
        });
    },

    resetAction: function() {
        this.currentForm.reset();
    },

    _createForm: function () {
        if (!this.currentForm) {
            this.currentForm = Ext.create('SafeStartApp.view.forms.CompanyUser');
            this.getInfoPanel().removeAll(true);
            this.getInfoPanel().setHtml('');
            this.getInfoPanel().add(this.currentForm);
            this.currentForm.addListener('save-data', this.saveAction, this);
            this.currentForm.addListener('send-credentials', this.sendCredentialsAction, this);
            this.currentForm.addListener('reset-data', this.resetAction, this);
            this.currentForm.addListener('delete-data', this.deleteAction, this);
        }
    },

    _reloadStore: function (userId) {
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            if (!userId) return;
            this.currentForm.setRecord(this.getNavMain().getStore().getById(userId));
        }, this, {single: true});

    }



});