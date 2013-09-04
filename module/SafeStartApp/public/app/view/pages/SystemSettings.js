Ext.define('SafeStartApp.view.pages.SystemSettings', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.SystemSettings',
        'SafeStartApp.view.components.UpdateChecklist',
        'SafeStartApp.store.ChecklistDefault'
    ],

    xtype: 'SafeStartSystemSettingsPage',

    config: {
        title: 'Settings',
        iconCls: 'settings',
        styleHtmlContent: true,
        layout: 'card',
        items: [

        ],

        listeners: {
            scope: this,
            activate: function (page) {
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

        this.checklistDefaultStoreStore = new  SafeStartApp.store.ChecklistDefault();
        this.add(this.getInfoPanel());
    },


    getInfoPanel: function () {
        var self = this;
        this.checkListTree = new SafeStartApp.view.components.UpdateChecklist({checkListStore: this.checklistDefaultStoreStore});
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
                    layout: 'card'
                }
            ],
            listeners: {

            }
        };
    },

    loadData: function () {
        var self = this;
        self.checkListTree.getTreeList().getStore().loadData();
        self.checkListTree.getTreeList().getStore().addListener('data-load-success', function () {
            var node = this.checkListTree.getTreeList().getStore().getNodeById(this.checkListTree.getTreeList().selectedNodeId);
            if (!node) node = this.checklistDefaultStoreStore.getRoot();
            if (node.isLeaf()) this.checkListTree.getTreeList().goToLeaf(node);
            else this.checkListTree.getTreeList().goToNode(node);
        }, self);
    }

});