Ext.define('SafeStartExt.view.form.inspectionfield.Root', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormInspectionfieldRoot',
    requires: [],
    name: 'root',

    initComponent: function() {
        Ext.apply(this, {
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            }, {
                xtype: 'hiddenfield',
                name: 'parentId'
            }, {
                xtype: 'hiddenfield',
                name: 'vehicleId'
            }, {
                xtype: 'hiddenfield',
                name: 'type',
                value: 'root'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Checklist Title',
                required: true,
                name: 'title'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Title To Display In Report',
                required: true,
                name: 'description'
            }, {
                xtype: 'checkbox',
                name: 'additional',
                fieldLabel: 'Additional'
            }, {
                xtype: 'numberfield',
                maxValue: 1000,
                minValue: 0,
                stepValue: 1,
                name: 'sortOrder',
                required: true,
                fieldLabel: 'Position'
            }, {
                xtype: 'checkbox',
                name: 'enabled',
                fieldLabel: 'Enabled'
            }]
        });
        this.callParent();
    }

});