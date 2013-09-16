Ext.define('SafeStartApp.view.pages.Alerts', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.store.Alerts'
    ],

    xtype: 'SafeStartAlertsPage',

    companyId: 0,

    config: {
        title: 'Alerts',
        iconCls: 'favorites',
        styleHtmlContent: true,
        layout: 'hbox',
        badgeText: '0',
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

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Main');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top'
        });

        this.add(this.getInfoPanel());

        self.updateAlertsBadge();
        setInterval(function(){ self.updateAlertsBadge(); }, 10000);

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) this.disable();
    },

    getInfoPanel: function() {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'company-alerts',
            layout: 'card',
            minWidth: 150,
            flex: 1,
            scrollable: true
        };
    },

    loadData: function() {
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;
        if (SafeStartApp.companyModel.get('id') == this.companyId) return;
        this.companyId = SafeStartApp.companyModel.get('id');
    },

    updateAlertsBadge: function() {
        this.setBadgeText('5');
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;

    }

});