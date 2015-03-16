Ext.define('SafeStartExt.view.form.VehicleCustomFields', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Checkbox',
        'Ext.form.field.Text',
        'Ext.form.FieldSet',
        'Ext.form.FieldContainer'
    ],
    xtype: 'SafeStartExtFormVehicleCustomFields',
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

        this.callParent();
    },

    loadFields: function (fields) {

//        if (! record.get('id')) {
//            this.down('button[name=delete]').disable();
//        } else {
//            this.down('button[name=delete]').enable();
//        }

        var items = [];

        for (var i in fields) {
            var field = fields[i];
            switch (fields[i].type) {
                case 'text':
                    items.push(this.createTextField(field));
                    break;
                case 'datePicker':
                    items.push(this.createDatePickerField(field));
                    break;
                case 'checkbox':
                    items.push(this.createCheckboxField(field));
                    break;
            }
        }
        this.add(items);
        //this.callParent(arguments);
        //this.down('field[name=expiryDate]').setValue(Ext.Date.format(new Date(record.get('expiryDate') * 1000), SafeStartExt.dateFormat));
    },


    createTextField: function (field) {
        return {
            xtype: 'textfield',
            width: 400,
            labelWidth: 130,
            name: field.id,
            cls: 'sfa-textfield-custom',
            fieldLabel: field.title,
            value: field.default_value
        };
    },

    createDatePickerField: function (field) {
        return {
            xtype: 'datefield',
            value: field.default_value,
            name: field.id,
            labelWidth: 130,
            width: 400,
            cls: 'sfa-datepicker-custom',
            fieldLabel: field.title
        };
    },

    createCheckboxField: function (field) {
        return {
            xtype: 'checkbox',
            name: field.id,
            cls: 'sfa-checkbox-custom',
            labelWidth: 130,
            maxWidth: 400,
            fieldLabel: field.title,
            checked: !! field.default_value,
            uncheckedValue: ''
        };
    }
});
