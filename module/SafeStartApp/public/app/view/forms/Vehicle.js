Ext.define('SafeStartApp.view.forms.Vehicle', {
    extend: 'Ext.form.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartVehicleForm',
    config: {
        cls: 'sfa-form-no-title',
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
                label: 'Make',
                required: true,
                name: 'title'
            },
            {
                xtype: 'textfield',
                label: 'Model',
                name: 'type'
            },
            {
                xtype: 'textfield',
                label: 'Plant ID',
                required: true,
                name: 'plantId'
            },
        /*   {
                xtype: 'textfield',
                label: 'Registration',
                required: true,
                name: 'registration'
            },*/
            {
                xtype: 'textfield',
                label: 'Project Name',
                name: 'projectName'
            },
           /* {
                xtype: 'datepickerfield',
                name: 'warrantyStartDate',
                required: true,
                label: 'Warranty Start Date',
                value: new Date(),
                cls: 'sfa-datepicker',
                picker: {
                    yearFrom: new Date().getFullYear() - 10,
                    yearTo: new Date().getFullYear() + 1
                }
            },*/
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
            },
           /* {
                xtype: 'fieldset',
                title: 'Until next inspection due:',
                items: [
                    {
                        xtype: 'spinnerfield',
                        maxValue: 1000000,
                        minValue: 0,
                        stepValue: 24,
                        name: 'inspectionDueHours',
                        required: true,
                        label: 'Hours'
                    },
                    {
                        xtype: 'spinnerfield',
                        maxValue: 1000000,
                        minValue: 0,
                        stepValue: 500,
                        name: 'inspectionDueKms',
                        required: true,
                        label: 'Kilometres'
                    }
                ]
            },*/
            {
                xtype: 'toolbar',
                docked: 'bottom',
                maxWidth: '',
                items: [
                    {
                        xtype: 'button',
                        name: 'delete-data',
                        text: 'Delete',
                        ui: 'decline',
                        hidden: true,
                        iconCls: 'delete',
                        handler: function () {
                            this.up('SafeStartVehicleForm').fireEvent('delete-data', this.up('SafeStartVehicleForm'));
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Reset',
                        name: 'reset-data',
                        handler: function () {
                            this.up('SafeStartVehicleForm').fireEvent('reset-data', this.up('SafeStartVehicleForm'));
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function () {
                            this.up('SafeStartVehicleForm').fireEvent('save-data', this.up('SafeStartVehicleForm'));
                        }
                    }
                ]
            }
        ]
    }
});
