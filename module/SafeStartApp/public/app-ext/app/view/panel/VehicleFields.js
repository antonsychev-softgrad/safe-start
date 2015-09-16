Ext.define('SafeStartExt.view.panel.VehicleFields', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.form.Vehicle',
        'SafeStartExt.view.form.VehicleCustomFields'
    ],
    xtype: 'SafeStartExtPanelVehicleFields',
    border: 0,
    cls: 'sfa-vehicles-tabpanel sfa-info-container',
    layout: 'vbox',
    autoScroll: true,

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtFormVehicle'
            },{
                xtype: 'SafeStartExtFormVehicleCustomFields'
            }],
            bbar: [{
                xtype: 'container',
                defaults: {
                    xtype: 'button',
                    margin: '4 8'
                },
                items: [{
                    text: 'Delete',
                    ui: 'red',
                    name: 'delete',
                    scale: 'medium',
                    minWidth: 140,
                    handler: function () {
                        Ext.Msg.confirm({
                            title: 'Confirmation',
                            msg: 'Are you sure want to delete this vehicle?',
                            buttons: Ext.Msg.YESNO,
                            fn: function (btn) {
                                if (btn !== 'yes') {
                                    return;
                                }
                                me.fireEvent('deleteVehicleAction', me.getVehicleForm().getRecord());
                            }
                        });
                    }
                }, {
                    text: 'Save',
                    ui: 'blue',
                    scale: 'medium',
                    minWidth: 140,
                    handler: function () {
                        if (me.getVehicleForm().isValid()) {
                            me.fireEvent(
                                'updateVehicleAction',
                                me.getVehicleForm().getRecord(),
                                me.getVehicleForm().getValues(),
                                me.getCustomFieldsForm().getValues()
                            );
                        }
                    }
                }]
            }]
        });

        this.callParent();
    },

    getVehicleForm: function () {
        return this.down('SafeStartExtFormVehicle');
    },

    getCustomFieldsForm: function () {
        return this.down('SafeStartExtFormVehicleCustomFields');
    },

    setVehicle: function (record) {
        if (! record.get('id')) {
            this.down('fieldcontainer[name=usage-value]').show();
            this.down('button[name=delete]').disable();
        } else {
            //if(! record.get('useKms')) {
            //    this.down('container[name=service-due-kms]').disable();
            //} else {
            //    this.down('container[name=service-due-kms]').enable();
            //}
            //if(! record.get('useHours')) {
            //    this.down('container[name=service-due-hours]').disable();
            //} else {
            //    this.down('container[name=service-due-hours]').enable();
            //}
            this.down('button[name=delete]').enable();
        }
        this.getVehicleForm().loadRecord(record);
    }

//    loadRecord: function (record) {
//        console.log(record)

//        this.callParent(arguments);
//        this.down('field[name=expiryDate]').setValue(Ext.Date.format(new Date(record.get('expiryDate') * 1000), SafeStartExt.dateFormat));
//    }

});
