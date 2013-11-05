Ext.define('SafeStartApp.view.forms.CompanySettings', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    requires: ['SafeStartApp.view.field.ReadOnly'],
    xtype: 'SafeStartCompanySettingsForm',
    config: {
        minHeight: 400,
        cls: 'comp-settings',
        items: [{
            xtype: 'fieldset',
            title: 'Company Settings',
            cls: 'sfa-company-settings',
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            }, {
                xtype: 'hiddenfield',
                name: 'logo'
            }, {
                xtype: 'textfield',
                label: 'Company Name',
                name: 'title'
            }, {
                xtype: 'container',
                layout: {
                    type: 'hbox',
                    align: 'middle'
                },
                items: [{
                    xtype: 'container',
                    flex: 1,
                    height: 90,
                    margin: '0 0 10 0',
                    items: [{
                        xtype: 'image',
                        height: 70,
                        width: 70
                    }]
                }, {
                    xtype: 'container',
                    flex: 2.3,
                    padding: '30 0 0 0',
                    items: [{
                        xtype: 'imageupload',
                        autoUpload: true,
                        url: 'api/upload-images',
                        height: 40,
                        padding: 0,
                        name: 'image',
                        maxWidth: 160,
                        states: {
                            browse: {
                                text: 'Change company logo',
                                ui: 'action',
                                cls: 'sfa-last'
                            },
                            uploading: {
                                loading: false
                            }
                        },
                        listeners: {
                            success: function(btn, data) {
                                var form = btn.up('SafeStartCompanySettingsForm');
                                form.down('image').setSrc('api/image/' + data.hash + '/70x70');
                                form.down('field[name=logo]').setValue(data.hash);
                            }
                        }
                    }]
                }]
            }, {
                xtype: 'textfield',
                label: 'Company Address',
                name: 'address'
            }, {
                xtype: 'textfield',
                label: 'Company Phone',
                name: 'phone'
            }, {
                xtype: 'textareafield',
                label: 'Company Info',
                name: 'description'
            }, {
                xtype: 'fieldset',
                title: 'Subscription:',
                hidden: true,
                disabled: true,
                cls: 'subscription-fieldset',
                items: [{
                    xtype: 'readonlyfield',
                    name: 'max_users',
                    label: 'Number of users'
                }, {
                    xtype: 'readonlyfield',
                    name: 'max_vehicles',
                    label: 'Number of vehicles'
                }, {
                    xtype: 'readonlyfield',
                    name: 'expiry_date',
                    label: 'Expiry Date',
                    formatFn: function(date) {
                        return Ext.Date.format(new Date(date * 1000), SafeStartApp.dateFormat);
                    }
                }]
            }]
        }, {
            xtype: 'toolbar',
            docked: 'bottom',
            items: [{
                xtype: 'spacer'
            }, {
                xtype: 'button',
                text: 'Save',
                name: 'save-data',
                ui: 'confirm',
                handler: function() {
                    this.up('SafeStartCompanySettingsForm').fireEvent('save-data', this.up('SafeStartCompanySettingsForm'));
                }
            }]
        }]
    },

    setRecord: function (record) {
        if (! record) {
            return;
        }

        if (record.get('enabled')) {
            this.down('fieldset[cls~=subscription-fieldset]').show();
        } else {
            this.down('fieldset[cls~=subscription-fieldset]').hide();
        }

        if (record.get('logo')) {
            this.down('image').setSrc('api/image/' + record.get('logo') + '/70x70');
        }
        this.callParent([record]);
    }
});