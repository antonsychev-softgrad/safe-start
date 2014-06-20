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
    autoScroll: true,
    buttonAlign: 'left',
    cls: 'sfa-vehicle-form',
    padding: 20,

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Model',
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
                fieldLabel: 'Plant ID',
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
                height: 32,
                labelWidth: 130,
                maxWidth: 400,
                labelSeparator: '',
                fieldLabel: 'Expiry Date',
                items: [{
                    xtype: 'datefield',
                    name: 'expiryDate',
                    format: SafeStartExt.dateFormat
                }],
                cls: 'sfa-datepicker',
            }, {
                xtype: 'fieldcontainer',
                height: 32,
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
                height: 110,
                fieldLabel: 'Next service due',
                maxWidth: 800,
                cls: 'sfa-field-group',
                labelCls: 'sfa-field-group-label',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                labelAlign: 'top',
                items: [{
                    xtype: 'container',
                    layout: 'hbox',
                    padding: '0 0 5 0',
                    items: [{
                        xtype: 'numberfield',
                        labelWidth: 130,
                        labelSeparator: '*',
                        fieldLabel: 'Hours',
                        name: 'serviceDueHours'
                    }, {
                        xtype: 'numberfield',
                        labelWidth: 180,
                        width: 300,
                        labelSeparator: '',
                        fieldLabel: 'Service Reminder Threshold',
                        name: 'serviceThresholdHours'
                    }, {
                        xtype: 'component',
                        html: 'Hours',
                        cls: 'x-form-item-label'
                    }]
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    items: [{
                        xtype: 'numberfield',
                        //hideTrigger: true,
                        labelWidth: 130,
                        labelSeparator: '*',
                        fieldLabel: 'Kilometers',
                        name: 'serviceDueKm'
                    }, {
                        xtype: 'numberfield',
                        labelWidth: 180,
                        width: 300,
                        labelSeparator: '',
                        fieldLabel: 'Service Reminder Threshold',
                        name: 'serviceThresholdKm'
                    }, {
                        xtype: 'component',
                        html: 'Kms',
                        cls: 'x-form-item-label'
                    }]
                }]
            }, {
                xtype: 'fieldcontainer',
                height: 110,
                fieldLabel: 'Current Odometer',
                cls: 'sfa-field-group',
                labelCls: 'sfa-field-group-label',
                padding: '1 0 0 0',
                labelAlign: 'top',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                maxWidth: 400,
                items: [{
                    xtype: 'numberfield',
                    //hideTrigger: true,
                    fieldLabel: 'Hours',
                    labelWidth: 130,
                    labelSeparator: '',
                    name: 'currentOdometerHours'
                }, {
                    xtype: 'numberfield',
                    //hideTrigger: true,
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
                }],
                cls: 'sfa-datepicker'
            }],
            bbar: [{
                xtype: 'container',
                defaults: {
                    xtype: 'button',
                    margin: '4 8'
                },
                items: [{
                    text: 'Delete',
                    ui: 'red',
                    name: 'delete',
                    scale: 'medium',
                    minWidth: 140,
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
                    minWidth: 140,
                    handler: function () {
                        if (me.isValid()) {
                            me.fireEvent('updateVehicleAction', me.getRecord(), me.getValues());
                        }
                    }
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
        this.down('field[name=expiryDate]').setValue(Ext.Date.format(new Date(record.get('expiryDate') * 1000), SafeStartExt.dateFormat));
    }
});
