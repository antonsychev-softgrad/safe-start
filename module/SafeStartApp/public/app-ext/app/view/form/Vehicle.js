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
    // layout: {
    //     type: 'vbox'
    // },
    padding: 5,

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            buttons: [{
                text: 'Delete',
                ui: 'red',
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
                xtype: 'box',
                flex: 1
            }, {
                text: 'Save',
                ui: 'green',
                scale: 'medium',
                handler: function () {
                    if (me.isValid()) {
                        me.fireEvent('updateVehicleAction', me.getRecord(), me.getValues());
                    }
                }
            }],
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Title',
                allowBlank: false,
                name: 'title'
            }, {                
                xtype: 'fieldcontainer',
                fieldLabel: 'Next Service Day',
                items: [{
                    xtype: 'datefield',
                    disabled: true,
                    name: 'nextServiceDay'
                }]
            }, {
                xtype: 'textfield',
                fieldLabel: 'Type',
                name: 'type'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Plan ID',
                allowBlank: false,
                name: 'plantId'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Project Name',
                name: 'projectName'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Project Number',
                name: 'projectNumber'
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Enabled',
                items: [{
                    xtype: 'checkboxfield',
                    name: 'enabled',
                    inputValue: true
                }]
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Until next service due',
                labelAlign: 'top',
                items: [{
                    xtype: 'numberfield',
                    fieldLabel: 'Hours',
                    name: 'serviceDueHours'
                }, {
                    xtype: 'numberfield',
                    fieldLabel: 'Kilometers',
                    name: 'serviceDueKm'
                }]
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Current Odometer',
                labelAlign: 'top',
                items: [{
                    xtype: 'numberfield',
                    fieldLabel: 'Hours',
                    name: 'currentOdometerHours'
                }, {
                    xtype: 'numberfield',
                    fieldLabel: 'Kilometers',
                    name: 'currentOdometerKms'
                }]
            }]
        });
        this.callParent();
    }
});
