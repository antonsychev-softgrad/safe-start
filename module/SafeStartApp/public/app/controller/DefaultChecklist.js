Ext.define('SafeStartApp.controller.DefaultChecklist', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

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
            navMain: 'SafeStartSystemSettingsPage > tabpanel > panel[name=checklist] > nestedlist[name=checklist-tree]',
            infoPanel: 'SafeStartSystemSettingsPage > tabpanel > panel[name=checklist] > panel[name=field-info]',
            addButton: 'SafeStartSystemSettingsPage > tabpanel > panel[name=checklist] > nestedlist[name=checklist-tree] > toolbar > button[action=add-field]'
        }
    },

    selectedNodeId: 0,
    selectedRecord: 0,
    onSelectAction: function () {
        console.log('onSelectAction');
        if (this.selectedNodeId == arguments[4].get('id')) return;
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        this.selectedNodeId = arguments[4].get('id');
        this.showUpdateForm();

    },

    showUpdateForm: function() {
        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
    },

    addAction: function () {
        console.log('addAction');
        if (!this.currentForm) this._createForm();
        if (this.vehicleModel) this.vehicleModel.destroy();
        this.vehicleModel = Ext.create('SafeStartApp.model.ChecklistField');
        this.currentForm.setRecord(this.vehicleModel);
    },

    saveAction: function () {
        if (!this.vehicleModel) this.vehicleModel = Ext.create('SafeStartApp.model.ChecklistField');
        if (this.validateFormByModel(this.vehicleModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            formValues.companyId = SafeStartApp.companyModel.get('id');
            SafeStartApp.AJAX('vehicle/' + this.currentForm.getValues().id + '/update', formValues, function (result) {
                if (result.fieldId) {
                    self._reloadStore(result.fieldId);
                    self.currentForm.down('button[name=delete-data]').show();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this vehicle?", function(){
            SafeStartApp.AJAX('vehicle/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                self.getNavMain().getStore().loadData();
                self.currentForm.reset();
                self.currentForm.down('button[name=delete-data]').hide();
                self.getNavMain().goToNode(self.getNavMain().getStore().getRoot());
            });
        });
    },

    resetAction: function() {
        this.currentForm.reset();
    },

    _createForm: function () {
        if (!this.currentForm) {
            this.currentForm = Ext.create('SafeStartApp.view.forms.ChecklistField');
            this.getInfoPanel().add(this.currentForm);
            this.currentForm.addListener('save-data', this.saveAction, this);
            this.currentForm.addListener('delete-data', this.deleteAction, this);
        }
    },

    _reloadStore: function (fieldId) {
        this.getNavMain().getStore().loadData();
        this.getNavMain().getStore().addListener('data-load-success', function () {
            if (!fieldId) return;
            this.currentForm.setRecord(this.getNavMain().getStore().getById(fieldId));
        }, this);

    }



});