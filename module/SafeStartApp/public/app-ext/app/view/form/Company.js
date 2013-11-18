Ext.define('SafeStartExt.view.form.Company', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Text',
        'Ext.form.field.Hidden',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormCompany',
    cls:'sfa-company-settings',
    border: 0,
    ui: 'transparent',
    buttonAlign: 'left',
    fieldDefaults: {
        msgTarget: 'side'
    },
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    // autoScroll: true,

    initComponent: function() {
        var me = this;
        Ext.apply(this, {
            buttons: [{
                text: 'Delete',
                name: 'delete-data',
                ui: 'red',
                scale: 'medium',
                handler: function() {
                    Ext.Msg.confirm({
                        title: 'Confirmation',
                        msg: 'Are you sure want to delete this company?',
                        buttons: Ext.Msg.YESNO,
                        fn: function(btn) {
                            if (btn !== 'yes') {
                                return;
                            }
                            me.fireEvent('deleteCompanyAction', me.getRecord());
                        }
                    });
                }
            }, {
                text: 'Manage',
                ui: 'blue',
                name: 'manage-data',
                scale: 'medium',
                handler: function() {
                    me.fireEvent('manageCompanyAction', me.getRecord());
                }
            }, {
                text: 'Save',
                ui: 'blue',
                name: 'save-data',
                formBind: true,
                scale: 'medium',
                handler: function() {
                    // if (me.isValid()) {
                        me.fireEvent('updateCompanyAction', me.getRecord(), me.getValues());
                    // }
                }
            }, {
                text: 'Send Password to Company Owner',
                ui: 'blue',
                name: 'send-password',
                cls:'sfa-last',
                scale: 'medium',
                handler: function() {
                    // if (me.isValid()) {
                        me.fireEvent('sendPasswordAction', me.getRecord());
                    // }
                }
            }],
            items: [{
                xtype: 'fieldcontainer',
                fieldLabel: 'Company Settings',
                maxWidth: 400,
                cls: 'sfa-field-group',
                labelCls: 'sfa-field-group-label',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                //cls:'sfa-company-settings',
                labelAlign: 'top',
                items: [{
                    xtype: 'hiddenfield',
                    name: 'id'
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Company Name',
                    labelWidth: 130,
                    labelSeparator: '*',
                    allowBlank: false,
                    name: 'title'
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Responsible Name',
                    allowBlank: false,
                    labelWidth: 130,
                    labelSeparator: '*',
                    name: 'firstName'
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Responsible Email',
                    vtype: 'email',
                    labelWidth: 130,
                    labelSeparator: '*',
                    allowBlank: false,
                    name: 'email'
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Company Address',
                    labelWidth: 130,
                    labelSeparator: '',
                    name: 'address'
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Company Phone',
                    labelWidth: 130,
                    labelSeparator: '',
                    name: 'phone'
                }, {
                    xtype: 'textareafield',
                    fieldLabel: 'Company Info',
                    labelWidth: 130,
                    height: 80,
                    labelSeparator: '',
                    name: 'description'
                }, {
                    xtype: 'checkboxfield',
                    name: 'restricted',
                    fieldLabel: 'Limited Access',
                    labelWidth: 130,
                    labelSeparator: '',
                    cls: 'sfa-limited-access',
                    handler: function() {
                        if (this.checked) {
                            this.up('form').down('[name=subscription]').enable();
                        } else {
                            this.up('form').down('[name=subscription]').disable();
                        }
                    }
                }]
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Subscription',
                name: 'subscription',
                maxWidth: 400,
                padding: '1 0 0 0',
                cls: 'sfa-field-group',
                labelCls: 'sfa-field-group-label',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                labelAlign: 'top',
                items: [{
                    xtype: 'numberfield',
                    hideTrigger: true,
                    maxValue: 1000,
                    labelWidth: 130,
                    minValue: 1,
                    stepValue: 1,
                    value: 1,
                    name: 'max_users',
                    required: true,
                    fieldLabel: 'Number of users'
                }, {
                    xtype: 'numberfield',
                    hideTrigger: true,
                    maxValue: 1000,
                    labelWidth: 130,
                    minValue: 1,
                    value: 1,
                    stepValue: 1,
                    name: 'max_vehicles',
                    required: true,
                    fieldLabel: 'Number of vehicles'
                }, {
                    xtype: 'datefield',
                    name: 'expiry_date',
                    required: true,
                    labelWidth: 130,
                    altFormats: 'U',
                    format: SafeStartExt.dateFormat,
                    fieldLabel: 'Expiry Date',
                    value: new Date(),
                    cls: 'sfa-datepicker'
                }]
            }]
        });
        this.callParent();
    }
});
