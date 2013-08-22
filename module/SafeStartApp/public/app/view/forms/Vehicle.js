Ext.define('SafeStartApp.view.forms.Vehicle', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartVehicleForm',
    config: {
        minHeight: 400,
        maxWidth: 600,
        scrollable: false,
        items: [
            {
                xtype: 'fieldset',
                title: 'Company Settings',
                items: [
                    {
                        xtype: 'hiddenfield',
                        name: 'id'
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'companyId'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Title',
                        required: true,
                        name: 'title'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Type',
                        required: true,
                        name: 'type'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Project Name',
                        name: 'projectName'
                    },
                    {
                        xtype: 'textfield',
                        label: 'Project Number',
                        name: 'projectNumber'
                    },
                    {
                        xtype: 'togglefield',
                        name: 'enabled',
                        label: 'Enabled',
                        listeners: {
                            change: function(field, slider, thumb, newValue, oldValue) {

                            }
                        }
                    }

                ]
            },
            {
                xtype: 'toolbar',
                docked: 'bottom',
                items: [
                    {
                        xtype: 'button',
                        name: 'delete-data',
                        text: 'Delete',
                        ui: 'decline',
                        iconCls: 'delete',
                        handler: function() {
                            this.up('SafeStartVehicleForm').fireEvent('delete-data', this.up('SafeStartVehicleForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Reset',
                        name: 'reset-data',
                        handler: function() {
                            this.up('SafeStartVehicleForm').fireEvent('reset-data', this.up('SafeStartVehicleForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function() {
                            this.up('SafeStartVehicleForm').fireEvent('save-data', this.up('SafeStartVehicleForm'));
                        }
                    }
                ]
            }
        ]
    }
});
