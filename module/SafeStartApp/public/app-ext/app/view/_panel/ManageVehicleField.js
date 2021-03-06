Ext.define('SafeStartExt.view._panel.ManageVehicleField', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.store.VehicleField',
        'SafeStartExt.view.form.vehiclefield.Root',
//        'SafeStartExt.view.form.vehiclefield.Group',
//        'SafeStartExt.view.form.vehiclefield.Radio',
        'SafeStartExt.view.form.vehiclefield.Checkbox',
        'SafeStartExt.view.form.vehiclefield.Text',
//        'SafeStartExt.view.form.vehiclefield.Label',
        'SafeStartExt.view.form.vehiclefield.DatePicker',
        'Ext.tree.Panel'
    ],
    autoScroll: true,
    cls:'sfa-manage-checklists',
    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    initTree: function () {
        this.insert(0, {
            xtype: 'treepanel',
            store: this.treeStore,
            folderSort: true,
            useArrows: true,
            rootVisible: false,
            allowDeselect: true,
            displayField: 'title',
            flex: 1,
            minWidth: 350,
            height: '100%',
            valueField: 'id',
            sorters: [{
                property: 'sortOrder'
            }],
            listeners: {
                beforeselect: this.onBeforeSelect,
                beforedeselect: this.onBeforeDeselect,
                containerclick: this.onRootSelect,
                scope: this
            }
        });
    },

    initTreeStore: function () {
        this.treeStore = SafeStartExt.store.VehicleField.create({
            vehicleId: this.vehicle.get('id'),
            listeners: {
                beforeremove: this.onBeforeRemove,
                scope: this
            }
        });
    },

    initComponent: function() {
        this.initTreeStore();
        Ext.apply(this, {
            listeners: {
                afterrender: this.initTree
            },
            tbar: {
                xtype: 'container',
                layout: 'hbox',
                defaults: {
                    margin: 5
                },
                items: [{
                    xtype: 'button',
                    ui: 'blue',
                    scale: 'medium',
                    text: 'Refresh',
                    handler: this.onRefresh,
                    scope: this
                }, {
                    xtype: 'tbfill'
                }, {
                    xtype: 'button',
                    ui: 'blue',
                    scale: 'medium',
                    text: 'Add vehicle field',
                    handler: this.onAddField,
                    name: 'add-field',
                    align: 'right',
                    scope: this
                }]
            },
            items: [{
                xtype: 'panel',
                name: 'forms-panel',
                flex: 1,
                minWidth: 350,
                maxWidth: 600,
                layout: 'card',
                defaults: {
                    maxWidth: 500,
                    padding: 10,
                    trackResetOnLoad: true,
                    defaults: {
                        labelWidth: 180,
                        anchor: '100%'
                    },
                    buttonAlign: 'left',
                    buttons: [{
                        text: 'Delete',
                        ui: 'red',
                        scale: 'medium',
                        name: 'delete-field',
                        handler: function (btn) {
                            this.onDeleteField(btn.up('form'));
                        },
                        scope: this
                    }, {
                        text: 'Save',
                        ui: 'blue',
                        scale: 'medium',
                        name: 'save-field',
                        handler: function (btn) {
                            this.onSaveField(btn.up('form'));
                        },
                        scope: this
                    }]
                },
                items: [{
                    xtype: 'container',
                    name: 'blank'
                },
                    {
                    xtype: 'SafeStartExtFormVehiclefieldRoot'
                },
// {
//                    xtype: 'SafeStartExtFormVehiclefieldGroup',
//                    listeners: {
//                        onChangeType: this.onChangeType,
//                        scope: this
//                    }
//                },
                    {
                    xtype: 'SafeStartExtFormVehiclefieldText',
                    listeners: {
                        onChangeType: this.onChangeType,
                        scope: this
                    }
                },
//                    {
//                    xtype: 'SafeStartExtFormVehiclefieldLabel',
//                    listeners: {
//                        onChangeType: this.onChangeType,
//                        scope: this
//                    }
//                },
                    {
                    xtype: 'SafeStartExtFormVehiclefieldCheckbox',
                    listeners: {
                        onChangeType: this.onChangeType,
                        scope: this
                    }
                },
//                    {
//                    xtype: 'SafeStartExtFormVehiclefieldRadio',
//                    listeners: {
//                        onChangeType: this.onChangeType,
//                        scope: this
//                    }
//                },
                    {
                    xtype: 'SafeStartExtFormVehiclefieldDatePicker',
                    listeners: {
                        onChangeType: this.onChangeType,
                        scope: this
                    }
                }]
            }]
        });
        this.callParent();
    },

    onChangeType: function (form) {
        var record = form.getRecord();
        if (form.isDirty()) {
            record.setDirty(true);
            form.updateRecord(record);
            record.endEdit();
        }
        this.onBeforeSelect(null, record);
    },

    onBeforeRemove: function (node, child) {
    },

    onBeforeSelect: function (view, record) {
        var activeForm = this.getActiveForm(),
            type = record.get('type');

        record = record || new SafeStartExt.model.VehicleField({});

        if (activeForm !== this.getForm(type)) {
            activeForm = this.switchForm(type);
        }

        this.activeRecord = record;

        if (record.get('id') === 0) {
            this.down('button[name=add-field]').disable();
        } else {
            this.down('button[name=add-field]').enable();
        }

        if (activeForm) {
            activeForm.loadRecord(record);
            return;
        }

        return false;
    },

    onBeforeDeselect: function () {
        var form = this.getActiveForm(),
            record;

        if (! form) {
            return;
        }

        record = form.getRecord();
        if (record && form.isDirty()) {
            record.setDirty(true);
            form.updateRecord(record);
            record.endEdit();
        }
        if (record.get('id') === 0 && (! record.get('title') && ! record.get('description'))) {
            record.destroy();
        }
        this.onDeselect();
    },

    onDeselect: function () {
        var formsPanel = this.getFormsPanel();
        formsPanel.getLayout().setActiveItem(formsPanel.down('component[name=blank]'));
    },

    onRootSelect: function () {
        this.down('treepanel').getSelectionModel().deselect(this.activeRecord);
        this.activeRecord = null;
        this.onDeselect();
    },

    switchForm: function (type) {
        var newForm = this.getForm(type);
        window.test = newForm;
        if (newForm) {
            this.getFormsPanel().getLayout().setActiveItem(newForm);
            return newForm;
        }
        this.getFormsPanel().getLayout().setActiveItem(0);
        return null;
    },

    getForm: function (type) {
        return this.getFormsPanel().down('component[name=' + type + ']');
    },

    getActiveForm: function () {
        var activeView = this.getFormsPanel().getLayout().getActiveItem();
        if (activeView.xtypesMap.form) {
            return activeView;
        }
        return null;
    },

    onSaveField: function (form) {
        if (! (typeof form.validate === 'function' && ! form.validate())) {
            form.updateRecord();
            this.fireEvent('saveField', form);
        }
    },

    onDeleteField: function (form) {
        this.fireEvent('deleteField', form);
    },

    onAddField: function () {
        var parentField = this.activeRecord,
            vehicleId = this.vehicle && this.vehicle.get('id') || 0,
            field;

        field = SafeStartExt.model.VehicleField.create({
            vehicleId: vehicleId
        });

        if (! parentField) {
            parentField = this.down('treepanel').getStore().getRootNode();
            field.set('type', 'root');
        } else {
            field.set('parentId', parentField.get('id'));
        }
        if (parentField.parentNode) {
            field.set('iconCls', 'sfa-icon-leaf');
        }

        parentField.appendChild(field);
        parentField.expand();

        this.down('treepanel').getSelectionModel().select([field]);
    },

    onRefresh: function () {
        var button = this;
        button.down('button').disable();
        var store = this.down('treepanel').getStore();
        this.setLoading(true, true);

        store.on('load', function () {
            this.setLoading(false);
            if (this.activeRecord) {
                this.down('treepanel').getSelectionModel().deselect(this.activeRecord);
            }
        }, this, {single: true});

        this.down('treepanel').getStore().load();
        setTimeout(function() {button.down('button').enable();}, 2000);

    },

    getFormsPanel: function () {
        return this.down('panel[name=forms-panel]');
    },

    _getDeleteUrl: function() {
        return 'vehiclefield/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'vehiclefield/' + this.getForm().getValues().id + '/update';
    }
});
