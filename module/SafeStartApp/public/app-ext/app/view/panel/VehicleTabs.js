Ext.define('SafeStartExt.view.panel.VehicleTabs', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.panel.VehicleInfo'
    ],
    xtype: 'SafeStartExtPanelVehicleTabs',
    layout: {
        type: 'card'
    },
    border: 0,

    tbar: {
        hidden: true
    },

    initComponent: function () {
        Ext.apply(this, {
            items: []
        });
        this.callParent();
    },
    ui: 'light',

    applyTabs: function (pagesStore) {
        this.down('toolbar').removeAll();
        this.removeAll();

        pagesStore.each(function (page) {
            switch (page.get('action')) {
                case 'info': 
                    this.initPage('SafeStartExtPanelVehicleInfo', page.get('text'));
                    break;
                case 'fill-checklist':
                    this.initPage('SafeStartExtPanelChecklist', page.get('text'));
                    break;
                case 'alerts':
                    this.initPage('SafeStartExtPanelAlerts', page.get('text'));
                    break;
                case 'inspections':
                    this.initPage('SafeStartExtPanelInspections', page.get('text'));
                    break;
                case 'report':
                    this.initPage('SafeStartExtPanelReport', page.get('text'));
                    break;
                case 'update-checklist':
                    this.initPage('SafeStartExtPanelUpdateChecklist', page.get('text'));
                    break;
                case 'users':
                    this.initPage('SafeStartExtPanelUsers', page.get('text'));
                    break;
            }
        }, this);
    },

    initPage: function (alias, title) {
        var button;

        if (Ext.ClassManager.getNameByAlias('widget.' + alias) === '') {
            button = Ext.widget('button', {
                text: title,
                handler: function () {
                    this.up('SafeStartExtMain').fireEvent('notSupportedAction');
                },
                scope: this
            });
        } else {
            button = Ext.widget('button', {
                text: title,
                handler: function () {
                    this.activatePageByAlias(alias);
                },
                scope: this
            });
        }

        this.down('toolbar').show();
        this.down('toolbar').add(button);
    },

    activatePageByAlias: function (alias) {
        var panel = this.down(alias);
        if (! panel) {
            panel = Ext.widget(alias);
            this.add(panel);
        }
        this.getLayout().setActiveItem(panel);
    }

});
