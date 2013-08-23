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
                leafitemtap: 'onSelectAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompanyPage > nestedlist[name=vehicles]',
            infoPanel: 'SafeStartCompanyPage > panel[name=info-container]',
            addButton: 'SafeStartCompanyToolbar > button[action=add-vehicle]'
        }
    },

    selectedNodeId: 0,
    selectedRecord: 0,
    onSelectAction: function () {
        if (this.selectedNodeId == arguments[4].get('id')) return;
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        this.selectedNodeId = arguments[4].get('id');
        switch(arguments[4].get('action')) {
            case 'info':
                this.getInfoPanel().setActiveItem(0);
                this.showUpdateForm();
                break;
        }
    },

    showUpdateForm: function() {
        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
        this.currentForm.down('button[name=reset-data]').hide();
    },

    addAction: function () {
        this.getInfoPanel().setActiveItem(0);
        this.selectedNodeId = 0;
        if (!this.currentForm) this._createForm();
        if (this.vehicleModel) this.vehicleModel.destroy();
        this.vehicleModel = Ext.create('SafeStartApp.model.Vehicle');
        this.currentForm.setRecord(this.vehicleModel);
        this.currentForm.down('button[name=delete-data]').hide();
        this.currentForm.down('button[name=reset-data]').show();
    },

    saveAction: function () {
        if (!this.vehicleModel) this.vehicleModel = Ext.create('SafeStartApp.model.Vehicle');
        if (this.validateFormByModel(this.vehicleModel, this.currentForm)) {
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
            this.getInfoPanel().getActiveItem().add(this.currentForm);
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