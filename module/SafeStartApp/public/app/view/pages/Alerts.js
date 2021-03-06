Ext.define('SafeStartApp.view.pages.Alerts', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.view.pages.panel.VehicleAlerts'
    ],

    xtype: 'SafeStartAlertsPage',

    companyId: 0,

    config: {
        title: 'Alerts',
        id: 'SafeStartAlertsPageTab',
        iconCls: 'favorites',
        styleHtmlContent: true,
        layout: 'hbox',
        tab: {
            action: 'alerts'
        },
        items: [

        ],

        listeners: {
            scope: this,
            activate: function(page) {
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
            btnTitle: 'Outstanding Alerts'
        });

        this.add(this.getInfoPanel());

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) {
            this.disable();
            return;
        }

        SafeStartApp.app.getController('Main').updateAlertsBadge();
    },

    getInfoPanel: function() {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'company-alerts',
            layout: 'card',
            minWidth: 150,
            flex: 1,
            items: [
                {
                    xtype: 'SafeStartVehicleAlertsPanel'
                }
            ]
        };
    },

    loadData: function() {
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;
        // we need only new alerts
        this.companyId = SafeStartApp.companyModel.get('id');
        this.down('SafeStartMainToolbar').setBtnTitle(SafeStartApp.companyModel.get('title') + ': ' + 'Outstanding Alerts');

        this.down('SafeStartVehicleAlertsPanel').loadCompanyList(this.companyId, 'new');
    }

});