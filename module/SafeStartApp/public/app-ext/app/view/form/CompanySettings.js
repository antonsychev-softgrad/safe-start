Ext.define('SafeStartExt.view.form.CompanySettings', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Text',
        'Ext.form.field.TextArea'
    ],
    xtype: 'SafeStartExtFormCompanySettings',
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
        Ext.apply(this, {
            bbar: {
                xtype: 'container',
                padding: 10,
                layout: {
                    type: 'hbox',
                    pack: 'end'
                },
                maxWidth: 400,
                items: [{
                    xtype: 'button',
                    text: 'Save',
                    scale: 'medium',
                    ui: 'blue',
                    handler: function () {
                        var me = this;
                        SafeStartExt.Ajax.request({
                            url: 'company/' + me.getRecord().getId() + '/update',
                            data: me.getValues(),
                            success: function () {
                                me.updateRecord(me.getRecord());
                            }
                        });
                    },
                    scope: this
                }]
            },
            listeners: {
                afterrender: function () {
                    this.loadRecord(SafeStartExt.getApplication().getCompanyRecord());
                },
                scope: this
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'logo'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Company Name',
                name: 'title'
            }, {
                xtype: 'form',
                height: 90,
                layout: {
                    type: 'hbox'
                },
                items: [{
                    xtype: 'container',
                    height: 90,
                    flex: 1,
                    name: 'image-container'
                }, {
                    xtype: 'filefield',
                    width: 100,
                    padding: '40 0 0 0',
                    buttonOnly: true,
                    buttonConfig: {
                        scale: 'small'
                    },
                    buttonText: 'Upload Image',
                    ui: 'default',
                    msgTarget: 'side',
                    name: 'image',
                    listeners: {
                        change: function () {
                            var form = this.up('form');
                            if (form.isValid()) {
                                form.submit({
                                    url: '/api/upload-images',
                                    waitMsg: 'Uploading your photos...',
                                    success: function (fp, o) {
                                        //TODO: fix success/failure
                                    },
                                    failure: function (fp, o) {
                                        var data = Ext.decode(o.response.responseText);
                                        var hash = (data && data.data && data.data.hash) || '';
                                        form.down('container[name=image-container]').removeAll();
                                        form.up('SafeStartExtFormCompanySettings').down('hiddenfield[name=logo]').setValue(hash);
                                        if (hash) {
                                            form.down('container[name=image-container]').add({
                                                xtype: 'image', 
                                                height: 70,
                                                margin: 10,
                                                src: '/api/image/' + hash + '/'
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    }
                }]
            }, {
                xtype: 'textfield',
                fieldLabel: 'Company Address',
                name: 'address'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Company Phone',
                name: 'phone'
            }, {
                xtype: 'textareafield',
                fieldLabel: 'Company Info',
                name: 'description'
            }, {
                xtype: 'fieldset',
                name: 'subscription',
                title: 'Subscription:',
                hidden: false,
                disabled: true,
                width:200,
                cls: 'subscription-fieldset',
                items: [{
                    xtype: 'textfield',
                    name: 'max_users',
                    label: 'Number of users'
                }, {
                    xtype: 'textfield',
                    name: 'max_vehicles',
                    label: 'Number of vehicles'
                }, {
                    xtype: 'textfield',
                    name: 'expiry_date_formatted',
                    label: 'Expiry Date',
                    getValue: function() {
                        return Ext.Date.format(new Date(this.getRawValue() * 1000), SafeStartExt.dateFormat);
                    }
                }]
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        if (record.get('enabled')) {
            this.down('fieldset[name=subscription]').show();
            this.down('textfield[name=expiry_date_formatted]').setValue(
                Ext.Date.format(new Date(record.get('expiry_date') * 1000), SafeStartExt.dateFormat)
            );
        } else {
            this.down('fieldset[name=subscription]').hide();
        }

        if (record.get('logo')) {
            var container = this.down('container[name=image-container]');
            container.removeAll();
            container.add({
                xtype: 'image', 
                height: 70,
                margin: 10,
                src: '/api/image/' + record.get('logo') + '/'
            });
        }

        this.callParent(arguments);
    }
});


