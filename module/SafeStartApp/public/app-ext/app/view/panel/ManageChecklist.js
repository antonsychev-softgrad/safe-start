Ext.define('SafeStartExt.view.panel.ManageChecklist', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.store.ChecklistTree',
        'SafeStartExt.view.form.inspectionfield.Root',
        'SafeStartExt.view.form.inspectionfield.Group',
        'SafeStartExt.view.form.inspectionfield.Radio',
        'SafeStartExt.view.form.inspectionfield.Checkbox',
        'SafeStartExt.view.form.inspectionfield.Text',
        'SafeStartExt.view.form.inspectionfield.Label',
        'SafeStartExt.view.form.inspectionfield.DatePicker',
        'Ext.tree.Panel'
    ],
    xtype: 'SafeStartExtPanelManageChecklist',
    autoScroll: true,
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    listeners: {
        afterrender: function () {
            this.down('treepanel').getStore().load();
        }
    },

    initComponent: function() {
        var store = SafeStartExt.store.ChecklistTree.create({
            vehicleId: this.vehicle.get('id')
        });
        Ext.apply(this, {
            tbar: [{
                text: 'Refresh',
                handler: this.onRefresh,
                scope: this
            }, {
                xtype: 'tbfill'
            }, {
                text: 'Add inspection field',
                handler: this.onAddField,
                align: 'right',
                scope: this
            }],
            items: [{
                xtype: 'treepanel',
                store: store,
                useArrows: true,
                rootVisible: true,
                displayField: 'title',
                flex: 1,
                // margin: '0 10 0 0',
                minWidth: 350,
                height: '100%',
                valueField: 'id',
                listeners: {
                    beforeselect: this.onBeforeSelect,
                    beforedeselect: this.onBeforeDeselect,
                    scope: this
                }
            }, {
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
                    buttons: [{
                        text: 'Save',
                        handler: function (btn) {
                            this.onSaveField(btn.up('form'));
                        },
                        scope: this
                    }, {
                        text: 'Delete',
                        handler: function (btn) {
                            this.onDeleteField(btn.up('form'));
                        },
                        scope: this
                    }]
                },
                items: [{
                    xtype: 'container',
                    name: 'blank'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldRoot',
                    listeners: {
                        afterrender: function () {
                            this.down('field[name=type]').on('change', function () {
                                this.up('form').getRecord(); 
                            });
                        }
                    }
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldGroup',
                    listeners: {
                        onChangeType: this.onChangeType,
                        scope: this
                    }
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldText'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldLabel'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldCheckbox'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldRadio'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldDatePicker'
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

    onBeforeSelect: function (view, record) {
        var activeForm = this.getActiveForm(),
            type = record.get('type');

        record = record || new SafeStartExt.model.InspectionField({});

        if (activeForm !== this.getForm(type)) {
            activeForm = this.switchForm(type);
        }

        this.activeRecord = record;

        if (activeForm) {
            activeForm.loadRecord(record);
            // activeForm.getForm().reset();
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
    },

    onDeselect: function () {
        var formsPanel = this.getFormsPanel();
        formsPanel.getLayout().setActiveItem(formsPanel.down('component[name=blank]'));
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
        form.updateRecord();
        this.fireEvent('saveField', form);
    },

    onDeleteField: function (form) {
        console.log(form.getRecord());
    },

    onAddField: function () {
        var parentField = this.activeRecord,
            activeForm,
            field = SafeStartExt.model.InspectionField.create({
                vehicleId: this.vehicle.get('id')
            });

        if (typeof parentField === 'object' && parentField.get('id')) {
            parentField.appendChild(field);
            field.set('parentId', parentField.get('id'));
            activeForm = this.switchForm(field.get('type')); 
            activeForm.loadRecord(field);
        }
    },

    onRefresh: function () {
        this.onDeselect();
        this.down('treepanel').getStore().load();
    },

    getFormsPanel: function () {
        return this.down('panel[name=forms-panel]');
    },

    _getDeleteUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/delete';
    },

    _getUpdateUrl: function() {
        return 'checklist/' + this.getForm().getValues().id + '/update';
    }
});