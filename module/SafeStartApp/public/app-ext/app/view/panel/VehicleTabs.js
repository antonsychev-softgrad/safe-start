Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.tab.Panel',
    requires: [
        'SafeStartExt.view.panel.VehicleInfo'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    border: 0,
    cls: 'sfa-vehicles-tabpanel',

    initComponent: function () {
        var tabs = this.getTabs();
        Ext.apply(this, {
            items: tabs
        });

        this.callParent();

        this.setActiveTab(this.items.first());
    },

    getTabs: function (pagesStore) {
        var tabs = [];
        this.pagesStore.each(function (page) {
            switch (page.get('action')) {
                case 'info': 
                    tabs.push(this.initPage('SafeStartExtPanelVehicleInfo', page.get('text')));
                    break;
                case 'fill-checklist':
                    tabs.push(this.initPage('SafeStartExtPanelChecklist', page.get('text')));
                    break;
                case 'alerts':
                    tabs.push(this.initPage('SafeStartExtPanelAlerts', page.get('text')));
                    break;
                case 'inspections':
                    tabs.push(this.initPage('SafeStartExtPanelInspections', page.get('text')));
                    break;
                case 'report':
                    tabs.push(this.initPage('SafeStartExtPanelReport', page.get('text')));
                    break;
                case 'update-checklist':
                    tabs.push(this.initPage('SafeStartExtPanelUpdateChecklist', page.get('text')));
                    break;
                case 'users':
                    tabs.push(this.initPage('SafeStartExtPanelUsers', page.get('text')));
                    break;
            }
        }, this);
        return tabs;
    },

    initPage: function (alias, title) {
        if (Ext.ClassManager.getNameByAlias('widget.' + alias) !== '') {
            return {xtype: alias, title: title};
        }
        return {
            xtype: 'panel',
            title: title,
            listeners: {
                beforeactivate: function () {
                    this.up('SafeStartExtMain').fireEvent('notSupportedAction');
                    return false;
                }
            }
        };
    }

});
