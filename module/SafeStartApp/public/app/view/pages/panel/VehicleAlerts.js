Ext.define('SafeStartApp.view.pages.panel.VehicleAlerts', {
    extend: 'Ext.navigation.View',

    alias: 'widget.SafeStartVehicleAlertsPanel',

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    requires: [
        'SafeStartApp.store.Alerts',
        'SafeStartApp.view.pages.panel.VehicleAlert'
    ],

    config: {
        navigationBar: {
            ui: 'sencha',
            items: [
                { xtype: 'spacer' },
                {
                    xtype: 'searchfield',
                    placeHolder: 'Search...',
                    name: 'search-alert',
                    listeners: {
                        scope: this,
                        clearicontap: function (field) {
                            field.parent.parent.parent.down('list[name=vehicle-alerts]').getStore().clearFilter();
                        },
                        keyup: function (field) {
                            field.parent.parent.parent.filterStoreDataBySearchFiled(field.parent.parent.parent.down('list[name=vehicle-alerts]').getStore(), field, 'alert_title');
                        }
                    }
                },
                {
                    xtype: 'selectfield',
                    name: 'filter-alert-by-type',
                    placeHolder: 'Status',
                    valueField: 'rank',
                    displayField: 'title',
                    store: {
                        data: [
                            { rank: '', title: 'All'},
                            { rank: 'new', title: 'New'},
                            { rank: 'closed', title: 'Closed'}
                        ]
                    },
                    listeners: {
                        change: function (obj, newValue, oldValue, eOpts) {
                            obj.parent.parent.parent.filterStoreDataByFiled(obj.parent.parent.parent.down('list[name=vehicle-alerts]').getStore(), newValue, 'status');
                        }
                    }
                },
                {
                    xtype: 'button',
                    ui: 'action',
                    name: 'refresh-alerts',
                    iconCls: 'refresh',
                    handler: function () {
                        this.parent.parent.parent.down('list[name=vehicle-alerts]').getStore().loadData();
                    }
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
            title: 'Vehicle Alerts',
            emptyText: 'No new Alerts',
            itemTpl: [
                '<div class="headshot" style="background-image:url({thumbnail});"></div>',
                '{alert_description}',
                '<span>{user.firstName} {user.lastName} at {title}</span>'
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