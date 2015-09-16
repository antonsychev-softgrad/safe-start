Ext.define('SafeStartApp.view.forms.VehicleCustomFields', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartVehicleCustomFieldsForm',
    config: {
        cls: 'sfa-form-no-title',
        items: [
            {
                xtype: 'textfield',
                label: 'Make',
                required: true,
                name: 'title'
            },
            {
                xtype: 'textfield',
                label: 'Plant ID',
                required: true,
                name: 'plantId'
            },

            {
                xtype: 'togglefield',
                name: 'enabled',
                label: 'Enabled',
                listeners: {
                    change: function (field, slider, thumb, newValue, oldValue) {

                    }
                }
            },
            {
                xtype: 'fieldset',
                title: 'Next service due:',
                items: [
                    {
                        xtype: 'numberfield',
                        maxValue: 1000000,
                        minValue: 0,
                        name: 'serviceDueHours',
                        required: true,
                        label: 'Hours'
                    },
                    {
                        xtype: 'numberfield',
                        maxValue: 1000000,
                        minValue: 1000,
                        name: 'serviceDueKm',
                        required: true,
                        label: 'Kilometres'
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Current odometer:',
                items: [
                    {
                        xtype: 'textfield',
                        name: 'currentOdometerHours',
                        label: 'Hours'
                    },
                    {
                        xtype: 'textfield',
                        name: 'currentOdometerKms',
                        label: 'Kilometres'
                    }
                ]
            },
            {
                xtype: 'textfield',
                disabled: true,
                name: 'nextServiceDay',
                label: 'Estimated Date of Next Service'
            }

//            {
//                xtype: 'toolbar',
//                docked: 'bottom',
//                maxWidth: '',
//                items: [
//                    {
//                        xtype: 'button',
//                        name: 'delete-data',
//                        text: 'Delete',
//                        ui: 'decline',
//                        hidden: true,
//                        iconCls: 'delete',
//                        handler: function () {
//                            this.up('SafeStartVehicleForm').fireEvent('delete-data', this.up('SafeStartVehicleForm'));
//                        }
//                    },
//                    { xtype: 'spacer' },
//                    {
//                        xtype: 'button',
//                        text: 'Reset',
//                        name: 'reset-data',
//                        handler: function () {
//                            this.up('SafeStartVehicleForm').fireEvent('reset-data', this.up('SafeStartVehicleForm'));
//                        }
//                    },
//                    {
//                        xtype: 'button',
//                        text: 'Save',
//                        name: 'save-data',
//                        ui: 'confirm',
//                        handler: function () {
//                            this.up('SafeStartVehicleForm').fireEvent('save-data', this.up('SafeStartVehicleForm'));
//                        }
//                    }
//                ]
//            }
        ]
    },
    initialize: function(){
        this.callParent();
        console.log('HAH');
    }
});
