Ext.define('SafeStartExt.view.panel.VehicleList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.MenuVehicles'
    ],
    xtype: 'SafeStartExtPanelVehicleList',
    cls:'sfa-left-coll',
    layout: 'fit',
    ui: 'light-left',
    border: 0,
    overflowY: 'auto',

    initComponent: function () {
        var store = SafeStartExt.store.MenuVehicles.create({});
        var searchFields = ['plantId', 'title', 'type', 'customFields'];
        var ignoreRoles = ['companyUser', 'guest'];
        var hidden = ignoreRoles.indexOf(SafeStartExt.getApplication().getUserRecord().get('role')) + 1;

        Ext.apply(this, {
            tbar: {
                xtype: 'toolbar',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: [{
                    text: 'Export vehicle data',
                    name: 'export-data',
                    cls:'sfa-export-button',
                    ui: 'blue',
                    hidden: !!hidden,
                    handler: function () {
                        this.up('SafeStartExtPanelVehicleList').fireEvent('exportCompanyAction');
                    }
                }, {
                    text: 'Add vehicle',
                    cls:'sfa-add-button',
                    handler: function () {
                        this.up('SafeStartExtPanelVehicleList').fireEvent('addVehicleAction');
                    }
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        flex: 1,
                        cls:'search',
                        margin: '0 5 0 5',
                        height: 22,
                        listeners: {
                            change: function (textfield, value) {
                                store.clearFilter();
                                if (value) {
                                    var filters = [
                                        new Ext.util.Filter({
                                            filterFn: function (item) {
                                                var regex = new RegExp(value, 'ig');
                                                var match = false;
                                                Ext.Object.each(item.data, function (property, value) {
                                                    if(searchFields.indexOf(property) > -1) {
                                                        if(property === 'customFields' && Array.isArray(value)) {
                                                            Ext.Array.each(value, function(o){
                                                                match = match || regex.test(String(o.default_value));
                                                            });
                                                        } else {
                                                            match = match || regex.test(String(value));
                                                        }
                                                    }
                                                });
                                                return match;
                                            }
                                        })
                                    ];
                                    store.filter(filters);
                                }
                            }
                        }
                    }, {
                        xtype: 'button',
                        iconCls: 'sfa-icon-refresh',
                        scale: 'medium',
                        handler: function () {
                            this.up('toolbar').down('textfield').setValue('');
                            store.load();
                        }
                    }]
                }]
            },

            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class="sfa-vehicle-item sfa-inspection-icon {[this.getState(values.lastInspectionDay)]}">',
                    '{text}',
                    '</div>',
                    '</tpl>',
                    {
                        getState: function(timestamp) {
                            var current = new Date(timestamp * 1000);
                            var today = new Date();
                                today.setHours(0, 0, 0, 0);
                            var week = new Date();
                                week.setDate(today.getDate() - 7);
                            var cls = "sfa-inspection-";
                            if(current > today)
                                cls += 'success';
                            else if(today >= current && current > week)
                                cls += 'warning';
                            else
                                cls += 'error';
                            return cls;
                        }
                    }
                ),
                store: store,
                listeners: {
                    itemclick: this.onVehicleClick,
                    scope: this
                }
            }]
        });
        this.callParent();
    },

    onVehicleClick: function (dataview, record) {
        this.fireEvent('changeVehicleAction', record);
    },

    getListStore: function () {
        return this.getList().getStore();
    },

    getList: function () {
        return this.down('dataview');
    }

});
