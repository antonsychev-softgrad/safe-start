Ext.define('SafeStartApp.view.pages.SystemSettings', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.view.pages.panel.UpdateChecklist',
        'SafeStartApp.store.ChecklistDefault'
    ],

    xtype: 'SafeStartSystemSettingsPage',

    config: {
        title: 'Settings',
        iconCls: 'settings',
        styleHtmlContent: true,
        layout: 'card',
        tab: {
            action: 'system-settings'            
        },
        items: [

        ],

        listeners: {
            afterrender: function (page) {
                page.loadData();
            }
        }
    },

    initialize: function () {
        this.callParent();

        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top'
        });

        this.checklistDefaultStoreStore = new  SafeStartApp.store.ChecklistDefault();
        this.add(this.getInfoPanel());
    },


    getInfoPanel: function () {
        return {
            cls: 'sfa-info-container sfa-system-settings',
            xtype: 'tabpanel',
            layout: 'card',
            minWidth: 150,
            scrollable: false,
            items: [
                {
                    xtype: 'SafeStartUpdateChecklistPanel',
                    checklistStore: this.checklistDefaultStoreStore
                }/*,
                {
                    xtype: 'panel',
                    title: 'System',
                    name: 'system',
                    html: "System",
                    layout: 'card'
                }*/
            ],
            listeners: {

            }
        };
    },

    getChecklistTree: function () {
        return this.down('SafeStartUpdateChecklistPanel');
    },

    loadData: function () {
        this.getChecklistTree().getChecklistStore().loadData();
    }

});