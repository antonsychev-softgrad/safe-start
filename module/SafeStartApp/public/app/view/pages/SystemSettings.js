Ext.define('SafeStartApp.view.pages.SystemSettings', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.SystemSettings',
        'SafeStartApp.view.components.UpdateChecklist'
    ],

    xtype: 'SafeStartSystemSettingsPage',

    config: {
        title: 'Settings',
        iconCls: 'settings',
        styleHtmlContent: true,
        scrollable: false,
        layout: 'card',
        checkListStore: null,
        items: [

        ],

        listeners: {
            scope: this,
            show: function (page) {
                page.loadData();
            }
        }
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.SystemSettings');
        this.add({
            xtype: 'SafeStartSystemSettingsToolbar',
            docked: 'top'
        });

        this.checklistDefaultStoreStore = Ext.create('SafeStartApp.store.ChecklistDefault');
        this.add(this.getInfoPanel());
    },


    getInfoPanel: function () {
        this.checkListTree = Ext.create('SafeStartApp.view.components.UpdateChecklist', {
            checkListStore: this.checklistDefaultStoreStore //todo: why does not work?
        });
        this.checkListTree.checkListStore = this.checklistDefaultStoreStore;
        return {
            cls: 'sfa-info-container sfa-system-settings',
            xtype: 'tabpanel',
            layout: 'card',
            minWidth: 150,
            scrollable: false,

            items: [
                this.checkListTree,
                {
                    xtype: 'panel',
                    title: 'System',
                    name: 'system',
                    html: "System",
                    scrollable: true,
                    layout: 'card'
                }
            ]
        };
    },

    loadData: function () {
     //   this.checklistDefaultStoreStore.loadData();
        if (this.checklistDefaultStoreStore.getRoot()) this.down('nestedlist[name=checklist-tree]').goToNode(this.checklistDefaultStoreStore.getRoot());
    }

});