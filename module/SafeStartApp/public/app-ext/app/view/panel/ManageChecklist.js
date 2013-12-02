Ext.define('SafeStartExt.view.panel.ManageChecklist', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.store.ChecklistTree',
        'SafeStartExt.view.form.InspectionFieldRoot',
        'Ext.tree.Panel'
    ],
    xtype: 'SafeStartExtPanelManageChecklist',
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
                // autoScroll: false,
                rootVisible: false,
                displayField: 'title',
                width: 300,
                height: '100%',
                valueField: 'id',
                listeners: {
                    beforeselect: Ext.Function.bind(this.onSelect, this)
                }
            }, {
                xtype: 'panel',
                name: 'forms-panel',
                flex: 1,
                layout: 'card',
                items: [{
                    xtype: 'container',
                    name: 'blank'
                }, {
                    xtype: 'SafeStartExtFormInspectionFieldRoot'
                }, {
                    xtype: 'form',
                    name: 'radio',
                    html: 'form for radio'
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