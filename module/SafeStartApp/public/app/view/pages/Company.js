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

        layout: {
            type: 'card',
            animation: {
                type: 'slide',
                direction: 'left',
                duration: 250
            }
        },

        items: [
            {
                cls: 'card',
                xtype: 'panel',
                name: 'company-info',
                scrollable: true,
                html: '<div><h2></h2></div>'
            }
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
            xtype: 'SafeStartMainToolbar',
            docked: 'top'
        });

        this.vehiclesStore = Ext.create('SafeStartApp.store.Vehicles');

        this.add({
            xtype: 'list',
            name: 'vehicles',
            itemTpl: '<div class="contact">{title}</div>',
            docked: 'left',
            width: 300,
            store: this.vehiclesStore,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',

                    items: [
                        { xtype: 'spacer' },
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
                            handler: function() {
                                this.up('list[name=vehicles]').getStore().loadData();
                            }
                        }
                    ]
                }
            ]
        });

        this.disable();
    },

    loadData: function() {
        this.vehiclesStore.getProxy().setExtraParam('companyId', SafeStartApp.companyModel.get('id') || 0);
        this.vehiclesStore.loadData();
    }


});