Ext.define('SafeStartExt.view.form.Vehicle', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Text',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormVehicle',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    fieldDefaults: {
        msgTarget: 'side'
    },
    padding: 30,
    scrollable: true,
    buttonAlign: 'left',

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            buttons: [{
                text: 'Delete',
                ui: 'red',
                name: 'delete',
                scale: 'medium',
                handler: function () {
                    Ext.Msg.confirm({
                        title: 'Confirmation',
                        msg: 'Are you sure want to delete this vehicle?',
                        buttons: Ext.Msg.YESNO,
                        fn: function (btn) {
                            if (btn !== 'yes') {
                                return;
                            }
                            me.fireEvent('deleteVehicleAction', me.getRecord());
                        }
                    });
                }
            }, {
                text: 'Save',
                ui: 'blue',
                scale: 'medium',
                handler: function () {
                    if (me.isValid()) {
                        me.fireEvent('updateVehicleAction', me.getRecord(), me.getValues());
                    }
                }
            }],
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Model',
                // width: '100%',
                maxWidth: 400,
                labelWidth: 130,
                labelSeparator: '*',
                allowBlank: false,
                name: 'title'
            }, {                
                xtype: 'textfield',
                labelWidth: 130,
                maxWidth: 400,
                fieldLabel: 'Make',
                labelSeparator: '',
                name: 'type'
            }, {
                xtype: 'textfield',
                labelWidth: 130,
                maxWidth: 400,
                fieldLabel: 'Plan ID',
                labelSeparator: '*',
                allowBlank: false,
                name: 'plantId'
            }, {
                xtype: 'textfield',
                labelWidth: 130,
                maxWidth: 400,
                fieldLabel: 'Project Name',
                labelSeparator: '',
                name: 'projectName'
            }, {
                xtype: 'textfield',
                labelWidth: 130,
                maxWidth: 400,
                fieldLabel: 'Project Number',
                labelSeparator: '',
                name: 'projectNumber'
            }, {
                xtype: 'fieldcontainer',
                labelWidth: 130,
                maxWidth: 400,
                labelSeparator: '',
                fieldLabel: 'Enabled',
                items: [{
                    xtype: 'checkboxfield',
                    name: 'enabled',
                    inputValue: true
                }]
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Until next service due',
                maxWidth: 400,
                cls: 'sfa-field-group',
                padding: '1 0 0 0',
                labelCls: 'sfa-field-group-label',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                labelAlign: 'top',
                items: [{
                    xtype: 'numberfield',
                    hideTrigger: true,
                    labelWidth: 130,
                    labelSeparator: '*',
                    fieldLabel: 'Hours',
                    name: 'serviceDueHours'
                }, {
                    xtype: 'numberfield',
                    hideTrigger: true,
                    labelWidth: 130,
                    labelSeparator: '*',
                    fieldLabel: 'Kilometers',
                    name: 'serviceDueKm'
                }]
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Current Odometer',
                cls: 'sfa-field-group',
                labelCls: 'sfa-field-group-label',
                labelAlign: 'top',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                maxWidth: 400,
                items: [{
                    xtype: 'numberfield',
                    hideTrigger: true,
                    fieldLabel: 'Hours',
                    labelWidth: 130,
                    labelSeparator: '',
                    name: 'currentOdometerHours'
                }, {
                    xtype: 'numberfield',
                    hideTrigger: true,
                    labelWidth: 130,
                    fieldLabel: 'Kilometers',
                    labelSeparator: '',
                    name: 'currentOdometerKms'
                }]
            }, {
                xtype: 'fieldcontainer',
                labelWidth: 130,
                maxWidth: 400,
                labelSeparator: '',
                fieldLabel: 'Next Service Day',
                items: [{
                    xtype: 'datefield',
                    disabled: true,
                    name: 'nextServiceDay'
                }]
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        if (! record.get('id')) {
            this.down('button[name=delete]').disable();
        } else {
            this.down('button[name=delete]').enable();
        }
        this.callParent(arguments);
    }
});
