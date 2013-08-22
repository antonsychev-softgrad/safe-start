Ext.define('SafeStartApp.controller.Vehicles', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

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
            navMain: 'SafeStartCompanyPage > nestedlist[name=vehicles]',
            infoPanel: 'SafeStartCompanyPage > panel[name=vehicle-info]',
            addButton: 'SafeStartCompanyPage > button[action=add-vehicle]'
        }
    },


    onSelectAction: function (list, index, node, record) {
        console.log(node.get('action'));
       /* if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(record);
        this.currentForm.down('button[name=delete-data]').show();
        this.currentForm.down('button[name=send-credentials]').show();
        this.currentForm.down('button[name=reset-data]').hide();*/
    },

    addAction: function () {
        if (!this.currentForm) this._createForm();
        if (this.userModel) {
            //todo: check if form bot empty
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
                    self.currentForm.down('button[name=reset-data]').hide();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this vehicle?", function(){
            SafeStartApp.AJAX('user/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                self.getNavMain().getStore().loadData();
                self.currentForm.reset();
                self.currentForm.down('button[name=delete-data]').hide();
                self.currentForm.down('button[name=reset-data]').show();
            });
        });
    },

    resetAction: function() {
        this.currentForm.reset();
    },

    _createForm: function () {
        if (!this.currentForm) {
            this.currentForm = Ext.create('SafeStartApp.view.forms.Vehicle');
            this.getInfoPanel().removeAll(true);
            this.getInfoPanel().setHtml('');
            this.getInfoPanel().add(this.currentForm);
            this.currentForm.addListener('save-data', this.saveAction, this);
            this.currentForm.addListener('reset-data', this.resetAction, this);
            this.currentForm.addListener('delete-data', this.deleteAction, this);
        }
    },

    _reloadStore: function (userId) {
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            if (!userId) return;
            this.currentForm.setRecord(this.getNavMain().getStore().getById(userId));
        }, this);

    }



});