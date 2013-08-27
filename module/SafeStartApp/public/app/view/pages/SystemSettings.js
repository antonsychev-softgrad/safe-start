Ext.define('SafeStartApp.view.pages.SystemSettings', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.SystemSettings'
    ],

    xtype: 'SafeStartSystemSettingsPage',

    config: {
        title: 'Settings',
        iconCls: 'settings',
        styleHtmlContent: true,
        scrollable: false,
        layout: 'card',
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
        return {
            cls: 'sfa-info-container',
            xtype: 'tabpanel',
            layout: 'card',
            minWidth: 150,
            scrollable: false,

            items: [
                {
                    xtype: 'panel',
                    name: 'checklist',
                    title: 'Default Checklist',
                    layout: 'hbox',
                    scrollable: false,
                    items: [
                        {
                            xtype: 'nestedlist',
                            name: 'checklist-tree',
                            flex: 1,
                            title: 'Checklist',
                            displayField: 'text',
                            getTitleTextTpl: function () {
                                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
                            },
                            getItemTextTpl: function () {
                                return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
                            },
                            detailCard: new Ext.Panel(),
                            store: this.checklistDefaultStoreStore,
                            items: [
                                {
                                    xtype: 'toolbar',
                                    docked: 'top',

                                    items: [
                                        {
                                            xtype: 'button',
                                            name: 'add-field',
                                            action: 'add-field',
                                            ui: 'action',
                                            iconCls: 'add'
                                        }
                                    ]
                                }
                            ]

                        },
                        {
                            xtype: 'panel',
                            layout: 'card',
                            flex: 2,
                            minWidth: 150,
                            name: 'field-info',
                            scrollable: false
                        }
                    ]
                },
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