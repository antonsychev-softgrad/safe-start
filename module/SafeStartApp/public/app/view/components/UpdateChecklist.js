Ext.define('SafeStartApp.view.components.UpdateChecklist', {
    extend: 'Ext.Panel',

    xtype: 'SafeStartUpdateChecklistComponent',

    config: {
        xtype: 'panel',
        name: 'checklist',
        title: 'Default Checklist',
        layout: 'hbox',
        scrollable: true,
        items: [

        ],
        listeners: {
            scope: this,
            show: function (page) {
                page.loadData();
            }
        }
    },

    loadData: function() {
        var self = this;
        this.add(
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
                detailCard: new Ext.Panel(),
                store: self.checkListStore,
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
            }
        );

        this.add(
            {
                xtype: 'panel',
                layout: 'card',
                flex: 2,
                minWidth: 150,
                name: 'field-info',
                scrollable: false
            }
        );
    },

    getTreeList: function() {
        return this.down('nestedlist[name=checklist-tree]');
    }
});