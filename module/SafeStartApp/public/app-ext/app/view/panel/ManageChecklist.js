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
            items: [{
                xtype: 'treepanel',
                store: store,
                rootVisible: false,
                displayField: 'title',
                flex: 1,
                // margin: '0 10 0 0',
                minWidth: 350,
                height: '100%',
                valueField: 'id',
                listeners: {
                    beforeselect: this.onSelect,
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
                    defaults: {
                        labelWidth: 180,
                        anchor: '100%'
                    }
                },
                items: [{
                    xtype: 'container',
                    name: 'blank'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldRoot'
                }, {
                    xtype: 'SafeStartExtFormInspectionfieldGroup'
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

    onSelect: function (view, record) {
        var activeForm = this.getFormsPanel().getLayout().getActiveItem(),
            type = record.get('type');

        record = record || new SafeStartExt.model.InspectionField({});

        if (activeForm !== this.getForm(type)) {
            activeForm = this.switchForm(type);
        }

        if (activeForm) {
            activeForm.loadRecord(record);
            return;
        }

        return false;
    },

    switchForm: function (type) {
        var newForm = this.getForm(type);
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