Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Company',
        'SafeStartApp.store.Vehicles',
        'SafeStartApp.model.Vehicle'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompanyPage',

    config: {
        title: 'Company',
        iconCls: 'more',
        styleHtmlContent: true,
        scrollable: false,
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

     //   this.alertsStore = Ext.create('SafeStartApp.store.AllAlerts');
      //  this.add(this.getAlertsList());

        this.disable();
    },

    getVehiclesList: function() {
        var self = this;
        return {
            xtype: 'nestedlist',
            id: 'companyVehicles',
            name: 'vehicles',
            minWidth: 150,
            maxWidth: 300,
            title: 'Vehicles',
            displayField: 'text',
            cls: 'sfa-left-container',
            detailCard: false,
            flex:1,
            getTitleTextTpl: function() {
                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
            },
            getItemTextTpl: function() {
                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
            },
            detailCard: new Ext.Panel(),
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
                               // scope: this,
                                clearicontap: function () {
                                    self.vehiclesStore.clearFilter();
                                },
                                keyup: function (field) {
                                    self.filterStoreDataBySearchFiled(self.vehiclesStore, field, 'text');
                                    //todo: fix searching
                                    //this.up('nestedlist[name=vehicles]').setData( this.up('nestedlist[name=vehicles]').getStore().getData());
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
                                this.up('nestedlist[name=vehicles]').getStore().loadData();
                            }
                        }
                    ]
                }
            ]
        };
    },

    getInfoPanel: function() {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'info-container',
            layout: 'card',
            minWidth: 150,
            flex: 2,
            scrollable: false,

            items: [
                {
                    xtype: 'panel',
                    name: 'vehicle-info',
                    scrollable: true,
                    layout: 'card'
                },
                {
                    xtype: 'panel',
                    name: 'vehicle-inspection',
                    html: "Daily Inspection",
                    cls: 'x-form-fieldset-title'
                },
                {
                    xtype: 'panel',
                    name: 'vehicle-manage',
                    scrollable: true,
                    layout: 'card'
                }
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
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get ||!SafeStartApp.companyModel.get('id')) return;
        this.vehiclesStore.getProxy().setExtraParam('companyId', SafeStartApp.companyModel.get('id') || 0);
        this.down('SafeStartCompanyToolbar').setTitle(SafeStartApp.companyModel.get('title'));
        this.vehiclesStore.loadData();
        if (this.vehiclesStore.getRoot()) this.down('nestedlist[name=vehicles]').goToNode(this.vehiclesStore.getRoot());
    }


});