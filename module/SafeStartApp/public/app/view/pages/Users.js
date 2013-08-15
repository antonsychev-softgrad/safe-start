Ext.define('SafeStartApp.view.pages.Users', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Users',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Users'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartUsersPage',

    config: {
        title: 'Users',
        iconCls: 'user',

        styleHtmlContent: true,
        scrollable: true,

        layout: {
            type: 'card',
            animation: {
                type: 'slide',
                direction: 'left',
                duration: 250
            }
        },

        items: [
            {
                cls: 'card',
                xtype: 'panel',
                name: 'company-info',
                scrollable: true,
                html: '<div><h2></h2></div>'
            }
        ]
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Users');
        this.add({
            xtype: 'SafeStartUsersToolbar',
            docked: 'top'
        });

        this.usersStore = Ext.create('SafeStartApp.store.Users');

        this.add({
            xtype: 'list',
            name: 'users',
            itemTpl: '<div class="contact">{title}</div>',
            docked: 'left',
            width: 300,
            store: this.usersStore,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',

                    items: [
                        { xtype: 'spacer' },
                        {
                            xtype: 'searchfield',
                            placeHolder: 'Search...',
                            listeners: {
                                scope: this,
                                clearicontap: function () {
                                    self.usersStore.clearFilter();
                                },
                                keyup: function (field) {
                                   return self.filterStoreDataBySearchFiled(self.usersStore, field, 'title');
                                }
                            }
                        },
                        { xtype: 'spacer' },
                        {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            iconCls: 'refresh',
                            handler: function() {
                                this.up('list[name=users]').getStore().loadData();
                            }
                        }
                    ]
                }
            ]
        });

        this.disable();
    }
});