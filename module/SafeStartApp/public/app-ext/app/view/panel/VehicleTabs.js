Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.container.Container',
    requires: [
        'SafeStartExt.view.form.Vehicle',
        'SafeStartExt.view.panel.VehicleInspection'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    border: 0,
    cls: 'sfa-vehicles-tabpanel sfa-info-container',
    layout: 'fit',

    initComponent: function () {
        var activeTab;
        Ext.apply(this, {
            items: [{
                xtype: 'tabpanel',
                items: this.getTabs()
            }]
        });


        this.callParent();

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
        activeTab.params = this.params;

        this.down('tabpanel').setActiveTab(activeTab);
    },

    changeAction: function (action, params) {
        var activeTab = this.down('component[action=' + action + ']');
        activeTab.params = params;
        if (activeTab) {
            this.down('tabpanel').setActiveTab(activeTab);
            return activeTab;
        }
    },

    getTabs: function () {
        var tabs = [];

        this.vehicle.pages().each(function (page) {
            switch (page.get('action')) {
                case 'info': 
                    tabs.push({
                        xtype: 'SafeStartExtFormVehicle', 
                        action: 'info',
                        title: page.get('text') 
                    });
                    break;
                case 'inspections':
                    tabs.push({
                        xtype: 'SafeStartExtPanelInspections',
                        title: page.get('text'),
                        action: 'inspections',
                        vehicle: this.vehicle
                    });
                    break;
                case 'fill-checklist':
                   tabs.push({
                       xtype: 'SafeStartExtPanelVehicleInspection',
                       title: page.get('text'),
                       action: 'fill-checklist',
                       vehicle: this.vehicle
                   });
                   break;
                case 'alerts':
                case 'users':
                case 'report':
                case 'update-checklist':
                    tabs.push({
                        xtype: 'container',
                        title: page.get('text'),
                        listeners: {
                            beforeactivate: function () {
                                this.up('SafeStartExtMain').fireEvent('notSupportedAction');
                                return false;
                            }
                        }
                    });
                    break;
            }
        }, this);
        return tabs;
    }

});
