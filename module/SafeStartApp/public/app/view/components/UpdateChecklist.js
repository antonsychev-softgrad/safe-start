Ext.define('SafeStartApp.view.components.UpdateChecklist', {
    extend: 'Ext.Panel',
    mixins: ['Ext.mixin.Observable'],
    xtype: 'SafeStartUpdateChecklistComponent',

    checkListStore: null,

    config: {
        xtype: 'panel',
        name: 'checklist',
        title: 'Default Checklist',
        layout: 'hbox',
        items: [
            {
                xtype: 'nestedlist',
                name: 'checklist-tree',
                flex: 1,
                title: 'Checklist',
                displayField: 'text',
                useTitleAsBackText: false,
                getTitleTextTpl: function () {
                    return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
                },
                getItemTextTpl: function () {
                    return '{' + this.getDisplayField() + '}<tpl if="leaf !== true">  </tpl>';
                },
                detailCard: false,
                store: this.checkListStore,
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
                name: 'field-info'
            }
        ]
    },

    constructor: function (config) {
        this.callParent(arguments);
        Ext.apply(this, config);
        this.getTreeList().setStore(this.getChecklistStore());
    },

    getChecklistStore: function () {
        return this.checkListStore;
    },

    getTreeList: function () {
        return this.down('nestedlist[name=checklist-tree]');
    }

});