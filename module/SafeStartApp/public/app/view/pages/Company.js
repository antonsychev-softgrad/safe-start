Ext.define('SafeStartApp.view.pages.Company', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Company',
        'SafeStartApp.view.pages.nestedlist.Vehicles',
        'SafeStartApp.view.pages.panel.VehicleInspection',
        'SafeStartApp.view.pages.panel.VehicleAlerts',
        'SafeStartApp.view.pages.panel.VehicleReport',
        'SafeStartApp.view.pages.panel.VehicleUsers',
        'SafeStartApp.view.pages.panel.UpdateVehicleChecklist',
        'SafeStartApp.view.pages.panel.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails',
        'SafeStartApp.view.pages.panel.Vehicles',
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

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Company');
        this.add({
            xtype: 'SafeStartCompanyToolbar',
            docked: 'top'
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
                height: 20,
                action: 'add-vehicle'
            });
        }
        this.add({
            xtype: 'panel',
            layout: 'card',
            name: 'left-container',
            flex: 1,
            maxWidth: 300,
            animation: 'fade',
            listeners: {
                initialize: function (panel) {
                    Ext.apply(this, {
                        _menuShown: true,
                        toggleMenu: function() {
                            if (this._menuShown) {
                                this.hideMenu();
                            } else {
                                this.showMenu();
                            }
                        },
                        getInfoContainer: function () {
                            return this.up('SafeStartCompanyPage').down('panel[name=info-container]');
                        },
                        showMenu: function () {
                            this._menuShown = true;
                            this.setWidth();
                            this.setFlex(1);
                            this.setActiveItem(0);
                        },
                        hideMenu: function () {
                            this._menuShown = false;
                            this.setWidth(50);
                            this.setFlex();
                            this.setActiveItem(1);
                        }
                    });
                }
            },
            items: [{
                xtype: 'panel',
                layout: 'fit',
                items: [{
                    xtype: 'toolbar',
                    docked: 'top',
                    items: additionalButton.concat({
                        xtype: 'spacer',
                        flex: 1
                    }, {
                        iconCls: 'arrow_left',
                        height: 20,
                        iconMask: true,
                        handler: function (btn) {
                            var panel = this.up('panel[name=left-container]');
                            panel.toggleMenu();
                        }
                    })
                }, {
                    xtype: 'SafeStartNestedListVehicles',
                    vehiclesStore: this.vehiclesStore,
                    store: this.listVehiclesStore
                }]
            }, {
                xtype: 'panel',
                items: [{
                    xtype: 'toolbar',
                    items: [{
                        iconCls: 'arrow_right',
                        height: 20,
                        iconMask: true,
                        handler: function () {
                            var panel = this.up('panel[name=left-container]');
                            panel.toggleMenu();
                        }
                    }]
                }]
            }]
        });
        this.down('panel[name=left-container]').setActiveItem(0);

        this.add(this.getInfoPanel());

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) this.disable();
    },

    getInfoPanel: function () {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'info-container',
            scrollable: null,
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
                    xtype: 'SafeStartVehicleAlertsPanel'
                },
                {
                    xtype: 'SafeStartVehicleInspectionsPanel'
                },
                {
                    xtype: 'SafeStartVehicleReportPanel'
                },
                {
                    xtype: 'SafeStartUpdateVehicleChecklistPanel'
                },
                {
                    xtype: 'SafeStartVehicleUsersPanel'
                },
                {
                    xtype: 'SafeStartVehicleInspectionDetails'
                },
                {
                    xtype: 'SafeStartVehiclesPanel'
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
        if (this.down('#SafeStartCompanyToolbarTitle')) {
            this.down('#SafeStartCompanyToolbarTitle').setText( SafeStartApp.companyModel.get('title') );
        } else {
            this.down('SafeStartCompanyToolbar').add({
                ui: 'action',
                id: 'SafeStartCompanyToolbarTitle',
                text: SafeStartApp.companyModel.get('title')
            });
        }
        this.vehiclesStore.loadData();
    }

});