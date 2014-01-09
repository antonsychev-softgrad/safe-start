Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.container.Container',
    requires: [
        'SafeStartExt.view.form.Vehicle',
        'SafeStartExt.view.panel.Inspections',
        'SafeStartExt.view.panel.VehicleAlerts',
        'SafeStartExt.view.panel.Inspection',
        'SafeStartExt.view.panel.ManageChecklist',
        'SafeStartExt.view.panel.VehicleUsers',
        'SafeStartExt.view.panel.VehicleReports'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    border: 0,
    cls: 'sfa-vehicles-tabpanel sfa-info-container',
    layout: 'fit',

    initComponent: function () {
        var activeTab;
        var tabs = this.getTabs();
        Ext.apply(this, {
            items: [{
                xtype: 'tabpanel',
                items: tabs 
            }]
        });

        this.callParent();
        console.log(tabs);

        this.confirm = this.add(Ext.window.MessageBox.create({
            onConfirm: function () {},
            buttons: [{
                text: 'Confirm',
                handler: function (btn) {
                    btn.up('messagebox').onConfirm();
                }
            }, {
                text: 'Cancel',
                handler: function (btn) {
                    btn.up('messagebox').hide();
                }
            }],
            display: function (cfg) {
                cfg = cfg || {};
                if (typeof cfg.onConfirm == 'function') {
                    this.onConfirm = cfg.onConfirm;
                }
                this.show(cfg);
            }
        }));
        if (this.action) {
            activeTab = this.down('component[action=' + this.action + ']');
        }

        if (! activeTab) {
            activeTab = this.down('tabpanel').items.first();
        }
        activeTab.configData = this.params;

        this.down('tabpanel').setActiveTab(activeTab);
    },

    changeAction: function (action, configData) {
        var activeTab = this.down('component[action=' + action + ']');
        activeTab.configData = configData;
        if (activeTab) {
            this.down('tabpanel').setActiveTab(activeTab);
            return activeTab;
        }
    },

    getTabs: function () {
        var tabs = [];

        this.vehicle.pages().each(function (page) {
            var title = page.get('text');
            var tab = {
                action: page.get('action'),
                title: page.get('text'),
                pageConfig: page,
                vehicle: this.vehicle
            };
            switch (tab.action) {
                case 'info': 
                    tab.xtype = 'SafeStartExtFormVehicle';
                    break;
                case 'inspections':
                    tab.xtype = 'SafeStartExtPanelInspections';
                    tab.title = title + '<br> <small>(30 Days Since Last)</small>';
                    if (page.get('badge')) {
                        tab.title = title + '<br> <small>(' + page.get('badge') + ')</small>';
                    } 
                    break;
                case 'fill-checklist':
                    tab.xtype = 'SafeStartExtPanelInspection';
                    break;
                case 'update-checklist':
                    tab.xtype = 'SafeStartExtPanelManageChecklist';
                    break;
                case 'users':
                    tab.xtype = 'SafeStartExtPanelVehicleUsers';
                    break;
                case 'alerts':
                    tab.xtype = 'SafeStartExtPanelVehicleAlerts';
                    tab.title = title + ' (' + page.get('counter') + ')';
                    tab.enableCounterBadge = true;
                    break;
                case 'report':
                    tab.xtype = 'SafeStartExtPanelVehicleReports';
                    break;
            }
            tabs.push(tab);
        }, this);
        return tabs;
    }

});
