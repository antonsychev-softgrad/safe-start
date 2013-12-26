Ext.define('SafeStartApp.view.pages.panel.VehicleAlerts', {
    extend: 'Ext.navigation.View',

    alias: 'widget.SafeStartVehicleAlertsPanel',

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    requires: [
        'SafeStartApp.store.Alerts',
        'SafeStartApp.view.pages.panel.VehicleAlert'
    ],

    config: {
        cls:'sfa-container-padding',
        navigationBar: {cls:'sfa-alerts-topbar',
            ui: 'sencha',
            maxWidth: 600,
            items: [
                {
                    xtype: 'searchfield',
                    cls:'sfa-alerts-searchbar',
                    placeHolder: 'Search...',
                    name: 'search-alert',
                    flex: 1,
                    minWidth: 80,
                    listeners: {
                        clearicontap: function (field) {
                            this.up('SafeStartVehicleAlertsPanel').down('list[name=vehicle-alerts]').getStore().clearFilter();
                        },
                        keyup: function (field) {
                            var alertsPanel = this.up('SafeStartVehicleAlertsPanel');
                            alertsPanel.filterStoreDataBySearchFiled(alertsPanel.down('list[name=vehicle-alerts]').getStore(), field, 'alertDescription');
                        }
                    }
                },
                {
                    xtype: 'selectfield',
                    name: 'filter-alert-by-type',
                    placeHolder: 'Status',
                    valueField: 'rank',
                    displayField: 'title',
                    minWidth: 80,
                    flex: 1,
                    store: {
                        data: [
                            { rank: '', title: 'All'},
                            { rank: 'new', title: 'New'},
                            { rank: 'closed', title: 'Closed'}
                        ]
                    },
                    listeners: {
                        change: function (field, newValue, oldValue, eOpts) {
                            var alertsPanel = this.up('SafeStartVehicleAlertsPanel');
                            alertsPanel.filterStoreDataByFiled(alertsPanel.down('list[name=vehicle-alerts]').getStore(), newValue, 'status');
                        }
                    }
                },
                {
                    xtype: 'button',
                    ui: 'action',
                    name: 'refresh-alerts',
                    iconCls: 'refresh',
                    handler: function () {
                        this.up('SafeStartVehicleAlertsPanel').down('list[name=vehicle-alerts]').getStore().loadData();
                    }
                }, {
                    xtype: 'title',
                    title: 'Vehicle Alerts'
                }


            ]
        },
        listeners: {
            push: function (view, item) {
                this.hideFilters();
            },
            pop: function (view, item) {
                this.showFilters();
            }
        }


    },

    initialize: function () {
        this.callParent();
        this.alertsStore = Ext.create('SafeStartApp.store.Alerts');
        this.add(this.getListPanel());
    },

    getListPanel: function () {
        var self = this;
        return {
            xtype: 'list',
            name: 'vehicle-alerts',
            emptyText: 'No new Alerts',
            plugins: [{
                xclass: 'Ext.plugin.ListPaging',
                autoPaging: true,
                noMoreRecordsText: ''
            }],
            itemTpl: [
                '<div class="headshot x-hasbadge" style="position: relative; background-image:url({thumbnail});">',
                '<tpl if="refreshedTimes !== 0">',
                '<div style="position:absolute;" class="x-badge sfa-alert-badge">{refreshedTimes+1}</div>',
                '</tpl>',
                '</div>',
                '<div class="sfa-alert-description">{alertDescription}&nbsp</div>',
                '<span>{vehicle.title} <b>{vehicle.plantId}</b> added by {user.firstName} {user.lastName} at {creationDate}</span></div>'
            ].join(''),
            cls: 'sfa-alerts',
            store: this.alertsStore,
            listeners: {
                itemtap: function (list, index, node, record) {
                    self.onSelectAlertAction(list, index, node, record);
                }
            }
        };
    },

    loadList: function (vehicleId, status) {
        this.vehicleId = vehicleId;
        this.alertsStore.getProxy().setExtraParam('vehicleId', this.vehicleId);
        this.alertsStore.loadData();
    },

    loadCompanyList: function (companyId, status) {
        this.companyId = companyId;
        this.status = status;
        this.alertsStore.getProxy().setExtraParam('companyId', this.companyId);
        if (this.status) {
            this.alertsStore.getProxy().setExtraParam('status', this.status);
            this.down('selectfield[name=filter-alert-by-type]').hide();
        }
        this.alertsStore.loadData();
    },

    onSelectAlertAction: function (list, index, node, record) {
        if (this.alertView) this.alertView.destroy();
        this.alertView = Ext.create('SafeStartApp.view.pages.panel.VehicleAlert');
        this.alertView.setRecord(record);
        this.push(this.alertView);
    },

    hideFilters: function () {
        this.down('selectfield[name=filter-alert-by-type]').hide();
        this.down('searchfield[name=search-alert]').hide();
        this.down('button[name=refresh-alerts]').hide();
    },

    showFilters: function () {
        try {
            this.down('list[name=vehicle-alerts]').deselectAll();
            if (!this.status) this.down('selectfield[name=filter-alert-by-type]').show();
            this.down('searchfield[name=search-alert]').show();
            this.down('button[name=refresh-alerts]').show();
        } catch (e) {

        }
    }

});
