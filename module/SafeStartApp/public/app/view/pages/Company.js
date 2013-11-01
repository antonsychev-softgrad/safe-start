Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.view.pages.nestedlist.Vehicles',
        'SafeStartApp.view.pages.panel.VehicleInspection',
        'SafeStartApp.view.pages.panel.VehicleAlerts',
        'SafeStartApp.view.pages.panel.VehicleReport',
        'SafeStartApp.view.pages.panel.VehicleUsers',
        'SafeStartApp.view.pages.panel.UpdateVehicleChecklist',
        'SafeStartApp.view.pages.panel.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails',
        'SafeStartApp.view.pages.panel.Vehicles',
        'SafeStartApp.view.pages.panel.LeftContainer',
        'SafeStartApp.view.forms.Vehicle',
        'SafeStartApp.store.MenuVehicles',
        'SafeStartApp.model.MenuVehicle'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompanyPage',
    companyId: 0,
    config: {
        title: 'Vehicles',
        iconCls: 'more',
        styleHtmlContent: true,
        layout: 'hbox',
        cls: 'page-company',
        tab: {
            action: 'company'
        },
        items: [

        ],

        listeners: {
            activate: function (page) {
                page.down('panel[cls=sfa-info-container]').setActiveItem(Number.POSITIVE_INFINITY);
                page.loadData();
            }
        }
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top',
            titleBtn: 'Vehicles'
        });

        this.vehiclesStore = new SafeStartApp.store.MenuVehicles({
            recursive: true
        });
        this.listVehiclesStore = new SafeStartApp.store.MenuVehicles({
            proxy: {
                type: 'memory',
                rootProperty: 'data'
            }
        });

        var additionalButton = [];

        if (SafeStartApp.userModel.get('role') != 'companyUser') {
            additionalButton.push({
                cls: 'sfa-add-button',
                iconCls: 'add',
                ui: 'action',
                text: 'Add Vehicle',
                action: 'add-vehicle'
            });
        }
        this.add({
            xtype: 'SafeStartLeftContainer',
            flex: 1,
            items: [{
                xtype: 'panel',
                cls: 'sfa-left-container',
                margin: '0',
                layout: 'fit',
                items: [{
                    xtype: 'toolbar',
                    cls: 'sfa-menu-toggle',
                    docked: 'top',
                    items: additionalButton.concat({
                        xtype: 'spacer',
                        flex: 1
                    }, {
                        iconCls: 'arrow_left',
                        //height: 22,
                        iconMask: true,
                        handler: function (btn) {
                            var panel = this.up('SafeStartLeftContainer');
                            panel.toggleMenu();
                        }
                    })
                }, {
                    xtype: 'SafeStartNestedListVehicles',
                    vehiclesStore: this.vehiclesStore,
                    margin: '0 0 0 0',
                    store: this.listVehiclesStore
                }]
            }, {
                xtype: 'panel',
                cls: 'sfa-left-container',
                margin: '0',
                items: [{
                    xtype: 'toolbar',
                    items: [{
                        iconCls: 'arrow_right',
                        //height: 22,
                        iconMask: true,
                        handler: function () {
                            var panel = this.up('SafeStartLeftContainer');
                            panel.toggleMenu();
                        }
                    }]
                }]
            }]
        });

        this.add(this.getInfoPanel());

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) this.disable();
    },

    getInfoPanel: function () {
        return {
            xtype: 'panel',
            cls: 'sfa-info-container',
            name: 'info-container',
            scrollable: null,
            layout: 'card',
            flex: 2,
            items: [{
                xtype: 'SafeStartVehicleForm'
            }, {
                xtype: 'SafeStartVehicleInspection'
            }, {
                xtype: 'SafeStartVehicleAlertsPanel'
            }, {
                xtype: 'SafeStartVehicleInspectionsPanel'
            }, {
                xtype: 'SafeStartVehicleReportPanel'
            }, {
                xtype: 'SafeStartUpdateVehicleChecklistPanel'
            }, {
                xtype: 'SafeStartVehicleUsersPanel'
            }, {
                xtype: 'SafeStartVehicleInspectionDetails'
            }, {
                xtype: 'SafeStartVehiclesPanel'
            }]
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
        this.down('SafeStartMainToolbar').setBtnTitle(SafeStartApp.companyModel.get('title'));
        this.vehiclesStore.loadData();
    }

});