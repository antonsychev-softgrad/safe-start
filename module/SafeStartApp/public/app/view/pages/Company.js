Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Company',
        'SafeStartApp.view.pages.nestedlist.Vehicles',
        'SafeStartApp.view.pages.panel.VehicleInspection',
        'SafeStartApp.store.Vehicles',
        'SafeStartApp.model.Vehicle'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompanyPage',

    config: {
        title: 'Company',
        iconCls: 'more',
        styleHtmlContent: true,
        layout: 'hbox',

        items: [

        ],

        listeners: {
            scope: this,
            show: function (page) {
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

        this.vehiclesStore = new SafeStartApp.store.Vehicles();
        this.add(this.getVehiclesList());

        this.add(this.getInfoPanel());

        this.disable();
    },

    getVehiclesList: function () {
        return {
            xtype: 'SafeStartNestedListVehicles',
            store: this.vehiclesStore
        };
    },

    getInfoPanel: function () {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'info-container',
            layout: 'card',
            minWidth: 150,
            flex: 2,
            items: [
                {
                    xtype: 'panel',
                    name: 'vehicle-info',
                    layout: 'card'
                },
                {
                    xtype: 'SafeStartVehicleInspection'
                },
                {
                    xtype: 'panel',
                    name: 'vehicle-manage',
                    layout: 'card'
                }
            ]
        };
    },

    getAlertsList: function () {
        return {
            xtype: 'list',
            name: 'alerts',
            itemTpl: '<div class="contact">{title}</div>',
            minWidth: 150,
            maxWidth: 300,
            cls: 'sfa-right-container',
            flex: 3,
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

    loadData: function () {
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get('id')) return;
        this.vehiclesStore.getProxy().setExtraParam('companyId', SafeStartApp.companyModel.get('id') || 0);
        this.down('SafeStartCompanyToolbar').setTitle(SafeStartApp.companyModel.get('title'));
        this.vehiclesStore.loadData();
        if (this.vehiclesStore.getRoot()) this.down('nestedlist[name=vehicles]').goToNode(this.vehiclesStore.getRoot());
    }


});