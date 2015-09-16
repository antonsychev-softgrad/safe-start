Ext.define('SafeStartExt.view.form.vehiclefield.Root', {
    extend: 'Ext.form.Panel',
    xtype: 'SafeStartExtFormVehiclefieldRoot',
    requires: [],
    name: 'root',

    initComponent: function() {
        Ext.apply(this, {
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            }, {
                xtype: 'hiddenfield',
                name: 'parentId'
            }, {
                xtype: 'hiddenfield',
                name: 'vehicleId'
            }, {
                xtype: 'hiddenfield',
                name: 'type',
                value: 'root'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Field Title',
                required: true,
                name: 'title'
            }]
        });
        this.callParent();
    },

    loadRecord: function (record) {
        if (record.get('id') === 0) {
            this.down('button[name=delete-field]').disable();
        } else {
            this.down('button[name=delete-field]').enable();
        }
        this.callParent(arguments);
    },

    validate: function () {
        if (Ext.each(this.query('field[required]'), function (field) {
            if (Ext.util.Format.trim('' + field.getValue()).length === 0) {
                Ext.Msg.alert({
                    msg: 'Field ' + field.fieldLabel + ' is required',
                    buttons: Ext.Msg.OK
                });
                return false;
            }
        }) !== true) { // compare with return value of Ext.each
            return false;
        }
        return true;
    }

});