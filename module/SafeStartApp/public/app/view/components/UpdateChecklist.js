Ext.define('SafeStartApp.view.components.UpdateChecklist', {
    extend: 'Ext.Panel',
    requires: [
        'SafeStartApp.model.ChecklistField',
        'SafeStartApp.view.forms.ChecklistField'
    ],
    mixins: [
        'Ext.mixin.Observable',
        'SafeStartApp.controller.mixins.Form'
    ],
    alias: 'widget.SafeStartUpdateChecklist',
    checklistStore: null,

    config: {
        name: 'checklist',
        title: 'Default Checklist',
        layout: {
            type: 'hbox',
            align: 'stretch'
        }
    },

    initialize: function () {
        if (this.config.checklistStore) {
            this.checklistStore = this.config.checklistStore;
        } else {
            this.checklistStore = Ext.create('SafeStartApp.store.VehicleChecklist');
        }

        this.callParent();

        this.add(this.createNestedList(this.getChecklistStore()));
        var form = this.add(this.createForm()).down('SafeStartChecklistFieldForm');
        form.on('save-data', this.saveAction, this);
        form.on('delete-data', this.deleteAction, this);
    },

    setVehicleId: function (vehicleId) {
        if (this.vehicleId != vehicleId) {
            this.getChecklistStore().getProxy().setExtraParam('vehicleId', vehicleId);
            this.vehicleId = vehicleId;
            this.getChecklistStore().loadData();
        }
    },

    createNestedList: function (store) {
        return {
            xtype: 'nestedlist',
            name: 'checklist-tree',
            flex: 1,
            masked: {
                xtype: 'loadmask',
                message: 'Loading...'
            },
            title: 'Checklist',
            displayField: 'text',
            useTitleAsBackText: false,
            getTitleTextTpl: function () {
                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
            },
            getItemTextTpl: function () {
                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
            },
            detailCard: false,
            store: store,
            listeners: {
                itemtap:  function(nestedlist, list, index, target, record) {
                    this.up('SafeStartUpdateChecklist').onSelectAction(record);
                },
                back:  function(nestedlist, node) {
                    this.up('SafeStartUpdateChecklist').onSelectAction(node.parentNode);
                }
            },
            items: [
            {
                xtype: 'toolbar',
                docked: 'top',
                cls:'sfa-add-inside',
                items: [
                {
                    xtype: 'button',
                    name: 'add-field',
                    action: 'add-field',
                    text: 'Add Inspection Field',
                    ui: 'action',
                    iconCls: 'add',
                    handler: function() {
                        this.up('SafeStartUpdateChecklist').addAction();
                    }
                }
                ]
            }
            ]
        };
    },

    createForm: function () {
        return {
            xtype: 'container',
            flex: 2,
            layout: 'fit',
            items: [{
                xtype: 'SafeStartChecklistFieldForm',
                flex: 1
            }]
        };
    },

    getForm: function () {
        return this.down('SafeStartChecklistFieldForm');
    },

    getChecklistStore: function () {
        return this.checklistStore;
    },

    getTreeList: function () {
        return this.down('nestedlist[name=checklist-tree]');
    },

    getInfoPanel: function() {
        return this.down('panel[name=field-info]');
    },

    getNavMain: function() {
        return this.down('nestedlist[name=checklist-tree]');
    },

    getAddButton: function() {

    },

    selectedNodeId: 0,

    onSelectAction: function (record) {
        this.currentRecord  = record;
        if (record.isRoot()) {
            this.getForm().resetRecord();
            this.selectedNodeId = 0;
        } else {
            this.getForm().setRecord(record);
            this.selectedNodeId = record.get('id');
        }
    },

    addAction: function () {
        var form = this.getForm();
        var record = form.getRecord();
        form.down('button[name=delete-data]').hide();
        record = Ext.create('SafeStartApp.model.ChecklistField', {
            parentId: this.selectedNodeId,
            vehicleId: this.vehicleId,
            is_root: this.selectedNodeId === 0,
            type: this.selectedNodeId === 0 ? 'root' : 'text'
        });
        form.setRecord(record);
    },

    saveAction: function (form) {
        // calculate order
        var record = form.getRecord();
        if (this.validateFormByModel(record, form)) {
            var self = this;
            var formValues = form.getValues();
            
            if (record.get('is_root')) {
                formValues.type = 'root';
            }
            SafeStartApp.AJAX(this._getUpdateUrl(), formValues, function (result) {
                if (result.fieldId) {
                    self._reloadStore(result.fieldId);
                    form.down('button[name=delete-data]').show();
                }
            });
        }
    },

    deleteAction: function () {
        var self = this;
        var parentId = this.getForm().getRecord().parentNode.get('id');
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this field from checklist?", function (btn) {
            if (btn != 'yes') {
                return;
            } 
            SafeStartApp.AJAX(self._getDeleteUrl(), {}, function (result) {
                self._reloadStore(parentId);
            });
        });
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
        this.fieldId = fieldId;
        this.getNavMain().getStore().addListener('load', function () {
            var node = this.getNavMain().getStore().getNodeById(this.fieldId);
            if (! node) {
                this.getNavMain().goToNode(this.getNavMain().getStore().getRoot());
                this.getForm().resetRecord();
                this.selectedNodeId = 0;
                return;
            }
            this.getForm().setRecord(node);
            if (node.isLeaf()) {
                this.getNavMain().goToLeaf(node);
            }
            else {
                this.getNavMain().goToNode(node);
            }
            if (Ext.isNumeric(node.get('id'))) {
                this.selectedNodeId = node.get('id');
            } else {
                this.selectedNodeId = 0;
            }
        }, this, {single: true});
        this.getNavMain().getStore().loadData();
    },

    _getDeleteUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/update';
    }

});

