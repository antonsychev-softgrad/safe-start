Ext.define('SafeStartApp.view.field.ReadOnly', {
    extend: 'Ext.field.Field',
    xtype: 'readonlyfield',

    config:{
        cls: 'x-form-field-readonly'
    },

    setValue: function (value) {
        var formatFn = this.config.formatFn;
        if (formatFn && typeof formatFn === 'function') {
            value = formatFn(value);
        }
        this.callParent([value]);
    },


    updateValue: function (newValue) {
        this.setHtml(newValue);
    }

});
