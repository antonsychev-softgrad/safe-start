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
    autoScroll: true,
    minWidth: 512,

    initComponent: function() {
        var me = this;
        Ext.apply(this, {
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
                            if (this.up('form').down('[name=unlim_users]').getValue()) {
                                this.up('form').down('[name=max_users]').disable();
                            } else {
                                this.up('form').down('[name=max_users]').enable();
                            }
                            this.up('form').down('[name=max_vehicles]').enable();
                        } else {
                            this.up('form').down('[name=max_users]').disable();
                            this.up('form').down('[name=max_vehicles]').disable();
                        }
                    },
                    listeners: {
                        change: function (el) {
                            this.isValid();
                        },
                        scope: this
                    }
                },{
                    xtype: 'checkboxfield',
                    name: 'unlim_expiry_date',
                    fieldLabel: 'Unlimited Expiry Date',
                    labelWidth: 130,
                    labelSeparator: '',
                    cls: 'sfa-limited-access',
                    handler: function() {
                        if (this.checked) {
                            this.up('form').down('[name=expiry_date]').disable();
                        } else {
                            this.up('form').down('[name=expiry_date]').enable();
                        }
                    },
                    listeners: {
                        change: function (el) {
                            this.isValid();
                        },
                        scope: this
                    }
                },{
                    xtype: 'checkboxfield',
                    name: 'unlim_users',
                    fieldLabel: 'Unlimited Users',
                    labelWidth: 130,
                    labelSeparator: '',
                    cls: 'sfa-limited-access',
                    handler: function() {
                        if (this.checked) {
                            this.up('form').down('[name=max_users]').disable();
                        } else {
                            if (this.up('form').down('[name=restricted]').getValue()) {
                                this.up('form').down('[name=max_users]').enable();
                            } else {
                                this.up('form').down('[name=max_users]').disable();
                            }
                        }
                    },
                    listeners: {
                        change: function (el) {
                            this.isValid();
                        },
                        scope: this
                    }
                }]

            }, {
                xtype: 'fieldcontainer',
                fieldLabel: 'Subscription',
                name: 'subscription',
                maxWidth: 422,
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
                    //hideTrigger: true,
                    maxValue: 1000,
                    labelWidth: 130,
                    minValue: 1,
                    stepValue: 1,
                    value: 1,
                    name: 'max_users',
                    required: true,
                    fieldLabel: 'Number of users',
                    listeners: {
                        change: function (el) {
                            this.isValid();
                        },
                        scope: this
                    }
                }, {
                    xtype: 'numberfield',
                    //hideTrigger: true,
                    maxValue: 1000,
                    labelWidth: 130,
                    minValue: 1,
                    value: 1,
                    stepValue: 1,
                    name: 'max_vehicles',
                    required: true,
                    fieldLabel: 'Number of vehicles',
                    listeners: {
                        change: function (el) {
                            this.isValid();
                        },
                        scope: this
                    }
                }, {
                    xtype: 'datefield',
                    name: 'expiry_date',
                    required: true,
                    labelWidth: 130,
                    altFormats: 'U',
                    format: SafeStartExt.dateFormat,
                    fieldLabel: 'Subscription Expiry',
                    value: new Date(),
                    cls: 'sfa-datepicker sfa-expiry-date'
                }]
            }],
            bbar: [{
                xtype: 'container',
                defaults: {
                    margin: '4 8'
                },
                items: [{
                    xtype: 'button',
                    text: 'Delete',
                    name: 'delete-data',
                    ui: 'red',
                    scale: 'medium',
                    minWidth: 140,
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
                    xtype: 'button',
                    text: 'Manage',
                    ui: 'blue',
                    name: 'manage-data',
                    scale: 'medium',
                    minWidth: 140,
                    handler: function() {
                        me.fireEvent('manageCompanyAction', me.getRecord());
                    }
                }, {
                    xtype: 'button',
                    text: 'Save',
                    ui: 'blue',
                    name: 'save-data',
                    formBind: true,
                    scale: 'medium',
                    minWidth: 140,
                    handler: function() {
                        if (this.isValid()) {
                            var values = me.getValues();
                            var date = this.down('datefield').getValue();
                            if(date === null) {
                                date = new Date();
                            }
                            if(date instanceof Date) {
                                values.expiry_date = date.getTime();
                            }
                            // me.getRecord().set('expiry_date', values.expiry_date);
                            this.fireEvent('updateCompanyAction', me.getRecord(), values);
                        }
                    },
                    scope: this
                }, {
                    xtype: 'button',
                    text: 'Send Password to Company Owner',
                    ui: 'transparent',
                    minWidth: 240,
                    name: 'send-password',
                    cls:'sfa-last',
                    scale: 'medium',
                    handler: function() {
                        me.fireEvent('sendPasswordAction', me.getRecord());
                    }
                }]
            }]
        });
        this.callParent();
    }
});
