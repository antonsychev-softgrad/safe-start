Ext.define('SafeStartApp.controller.DefaultChecklist', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    config: {
        control: {
            navMain: {
                leafitemtap: 'onSelectAction',
                selectionchange: 'onSelectChangeAction'
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

    onSelectChangeAction: function (obj, list, selections, eOpts) {
        if (!this.getNavMain().getActiveItem().getStore) return;
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        if (!parseInt(this.selectedRecord.get('id')))  this.selectedNodeId = 0;
        else  this.selectedNodeId = parseInt(this.selectedRecord.get('id'));
        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.fireEvent('change', this.currentForm, this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
    },

    onSelectAction: function () {
        this.selectedRecord = arguments[4];
        this.selectedNodeId = parseInt(this.selectedRecord.get('id'));
        if (!this.currentForm) this._createForm();
        this.currentForm.setRecord(this.selectedRecord);
        this.currentForm.fireEvent('change', this.currentForm, this.selectedRecord);
        this.currentForm.down('button[name=delete-data]').show();
    },

    addAction: function () {
        if (!this.currentForm) this._createForm();
        this.currentForm.down('button[name=delete-data]').hide();
        if (this.checklistFieldModel) this.checklistFieldModel.destroy();
        this.checklistFieldModel = Ext.create('SafeStartApp.model.ChecklistField');
        this.checklistFieldModel.set('parentId', this.selectedNodeId);
        if (this.selectedNodeId == 0) {
            this.checklistFieldModel.set('type', 'root');
            this.currentForm.showCreateRootCategory();
        } else {
            this.checklistFieldModel.set('type', 'text');
            this.currentForm.showCreateFieldCategory();
        }
        this.currentForm.setRecord(this.checklistFieldModel);
        this.currentForm.fireEvent('change', this.currentForm, this.checklistFieldModel);
    },

    saveAction: function () {
        if (!this.checklistFieldModel) this.checklistFieldModel = Ext.create('SafeStartApp.model.ChecklistField');
        if (this.validateFormByModel(this.checklistFieldModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            SafeStartApp.AJAX('admin/checklist/' + this.currentForm.getValues().id + '/update', formValues, function (result) {
                if (result.fieldId) {
                    self._reloadStore(result.fieldId);
                    self.currentForm.down('button[name=delete-data]').show();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this vehicle?", function () {
            SafeStartApp.AJAX('admin/checklist/' + self.currentForm.getValues().id + '/delete', {}, function (result) {
                var parentId = self.selectedRecord.get('parentId');
                self.getNavMain().getStore().loadData();
                self.getNavMain().getStore().addListener('data-load-success', function () {
                    self.currentForm.reset();
                    self.currentForm.down('button[name=delete-data]').hide();
                    try {
                        self.getNavMain().goToNode(self.getNavMain().getStore().getNodeById(parentId));
                    } catch (e) {
                        self.getNavMain().goToNode(self.getNavMain().getStore().getRoot());
                    }
                });
            });
        });
    },

    resetAction: function () {
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
            if (!this.selectedNodeId) {
                this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
            } else {
                try {
                    this.getNavMain().goToNode(this.getNavMain().getStore().getNodeById(fieldId));
                } catch (e) {
                    this.getNavMain().goToLeaf(this.getNavMain().getStore().getNodeById(fieldId));
                }
            }
            this.currentForm.setRecord(this.getNavMain().getStore().getById(fieldId));
        }, this);

    },

    _getDeleteUrl: function() {
        return 'admin/checklist/' + self.currentForm.getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'admin/checklist/' + self.currentForm.getValues().id + '/update';
    }


});