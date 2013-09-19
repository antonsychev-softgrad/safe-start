Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Company',
        'SafeStartApp.view.pages.nestedlist.Vehicles',
        'SafeStartApp.view.components.UpdateVehicleChecklist',
        'SafeStartApp.view.pages.panel.VehicleInspection',
        'SafeStartApp.view.pages.panel.VehicleAlerts',
        'SafeStartApp.view.pages.panel.VehicleUsers',
        'SafeStartApp.view.pages.panel.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails',
        'SafeStartApp.store.MenuVehicles',
        'SafeStartApp.model.MenuVehicle'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompanyPage',
    companyId: 0,
    config: {
        title: 'Company',
        iconCls: 'more',
        styleHtmlContent: true,
        layout: 'hbox',
        cls: 'page-company',
        items: [

        ],

        listeners: {
            scope: this,
            activate: function (page) {
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

        this.vehiclesStore = new SafeStartApp.store.MenuVehicles();
        this.add(this.getVehiclesList());

        this.add(this.getInfoPanel());

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) this.disable();
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
                    xtype: 'SafeStartVehicleInspectionsPanel'
                },
                {
                    xtype: 'SafeStartVehicleAlertsPanel'
                },
                {
                    xtype: 'panel',
                    name: 'vehicle-manage',
                    layout: 'card'
                },
                {
                    xtype: 'SafeStartVehicleUsersPanel'
                },
                {
                    xtype: 'SafeStartVehicleInspectionDetails'
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
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;
        if (SafeStartApp.companyModel.get('id') == this.companyId) return;
        this.companyId = SafeStartApp.companyModel.get('id');
        this.vehiclesStore.getProxy().setExtraParam('companyId', this.companyId);
        this.down('SafeStartCompanyToolbar').setTitle(SafeStartApp.companyModel.get('title'));
        this.down('nestedlist[name=vehicles]').goToNode(this.vehiclesStore.getRoot());
        this.vehiclesStore.loadData();
    }

});