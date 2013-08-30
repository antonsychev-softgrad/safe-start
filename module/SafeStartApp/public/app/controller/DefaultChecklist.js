Ext.define('SafeStartApp.controller.DefaultChecklist', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],
    requires: [
        'SafeStartApp.model.ChecklistField',
        'SafeStartApp.view.forms.ChecklistField'
    ],
    config: {
        control: {
            navMain: {
                itemtap: 'onSelectAction',
                back: 'onSelectChangeAction'
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

    onSelectChangeAction: function (obj, node, selections, eOpts) {
        try{
            this.selectedRecord =  this.getNavMain().getStore().getById(node.get('parentId'));
            if (!this.selectedRecord)  {
                this.selectedRecord = Ext.create('SafeStartApp.model.ChecklistField');
                this.selectedNodeId = 0;
            }
            else this.selectedNodeId = parseInt(this.selectedRecord.get('id'));
            this.getNavMain().selectedNodeId = this.selectedNodeId;
            if (!this.currentForm) this._createForm();
            this.currentForm.setRecord(this.selectedRecord);
            this.currentForm.fireEvent('change', this.currentForm, this.selectedRecord);
            this.currentForm.down('button[name=delete-data]').show();
        } catch(e) {

        }
    },

    onSelectAction: function () {
        try{
            this.selectedRecord = arguments[4];
            this.selectedNodeId = parseInt(this.selectedRecord.get('id'));
            this.getNavMain().selectedNodeId = this.selectedNodeId;
            if (!this.currentForm) this._createForm();
            this.currentForm.setRecord(this.selectedRecord);
            this.currentForm.fireEvent('change', this.currentForm, this.selectedRecord);
            this.currentForm.down('button[name=delete-data]').show();
        } catch(e) {

        }
    },

    addAction: function () {
        if (!this.currentForm) this._createForm();
        this.currentForm.down('button[name=delete-data]').hide();
        if (this.checklistFieldModel) this.checklistFieldModel.destroy();
        this.checklistFieldModel = Ext.create('SafeStartApp.model.ChecklistField');
        this.checklistFieldModel.set('parentId', this.selectedNodeId);
        if(this.getNavMain().getStore().getProxy().getExtraParams()['vehicleId']) this.checklistFieldModel.set('vehicleId', parseInt(this.getNavMain().getStore().getProxy().getExtraParams()['vehicleId']));
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
            SafeStartApp.AJAX(this._getUpdateUrl(), formValues, function (result) {
                if (result.fieldId) {
                    self._reloadStore(result.fieldId);
                    self.currentForm.down('button[name=delete-data]').show();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this field from checklist?", function () {
            SafeStartApp.AJAX(self._getDeleteUrl(), {}, function (result) {
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

    reloadEventSet: false,
    _reloadStore: function (fieldId) {
        this.fieldId = fieldId;
        this.getNavMain().getStore().loadData();
        if (this.reloadEventSet) return;
        this.reloadEventSet = true;
        this.getNavMain().getStore().addListener('data-load-success', function () {
           // var record = this.getNavMain().getStore().getById(this.fieldId);
         //   this.currentForm.setRecord(record);
            var node = this.getNavMain().getStore().getNodeById(this.fieldId);
            if (node.isLeaf()) this.getNavMain().goToLeaf(node);
            else this.getNavMain().goToNode(node);
        }, this);

    },

    _getDeleteUrl: function() {
        return 'admin/checklist/' + this.currentForm.getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'admin/checklist/' + this.currentForm.getValues().id + '/update';
    }


});