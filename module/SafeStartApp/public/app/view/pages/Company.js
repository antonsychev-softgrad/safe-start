Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Company',
        'SafeStartApp.model.Vehicle',
        'SafeStartApp.store.Vehicles'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompanyPage',

    config: {
        title: 'Company',
        iconCls: 'more',
        styleHtmlContent: true,
        scrollable: true,
        layout: 'hbox',

        items: [

        ],

        listeners: {
            scope: this,
            show: function(page) {
                page.loadData();
            }
        }
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Company');
        this.add({
            xtype: 'SafeStartCompanyToolbar',
            docked: 'top'
        });

        this.vehiclesStore = Ext.create('SafeStartApp.store.Vehicles');
        this.add(this.getVehiclesList());

        this.add(this.getInfoPanel());

        this.alertsStore = Ext.create('SafeStartApp.store.AllAlerts');
        this.add(this.getAlertsList());

        this.disable();
    },

    getVehiclesList: function() {
        return {
            xtype: 'list',
            name: 'vehicles',
            itemTpl: '<div class="contact">{title}</div>',
            minWidth: 150,
            maxWidth: 300,
            cls: 'sfa-left-container',
            flex:1,
            store: this.vehiclesStore,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    items: [
                        {
                            xtype: 'searchfield',
                            placeHolder: 'Search...',
                            listeners: {
                                scope: this,
                                clearicontap: function () {
                                    self.vehiclesStore.clearFilter();
                                },
                                keyup: function (field) {
                                    return self.filterStoreDataBySearchFiled(self.vehiclesStore, field, 'title');
                                }
                            }
                        },
                        { xtype: 'spacer' },
                        {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            iconCls: 'refresh',
                            cls:'sfa-search-reload',
                            handler: function() {
                                this.up('list[name=vehicles]').getStore().loadData();
                            }
                        }
                    ]
                }
            ]
        };
    },

    getInfoPanel: function() {
        return {
            cls: 'card',
            xtype: 'panel',
            name: 'vehicle-info',
            layout: 'card',
            minWidth: 150,
            flex: 2,
            scrollable: true,
            items: [

            ]
        };
    },

    getAlertsList: function() {
        return {
            xtype: 'list',
            name: 'alerts',
            itemTpl: '<div class="contact">{title}</div>',
            minWidth: 150,
            maxWidth: 300,
            cls: 'sfa-right-container',
            flex:3,
            store: this.alertsStore,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    title: 'Alerts',
                    items: [

                    ]
                }
            ]
        }
    },

    loadData: function() {
        this.vehiclesStore.getProxy().setExtraParam('companyId', SafeStartApp.companyModel.get('id') || 0);
        this.down('SafeStartCompanyToolbar').setTitle(SafeStartApp.companyModel.get('title'));
        this.vehiclesStore.loadData();
    }


});