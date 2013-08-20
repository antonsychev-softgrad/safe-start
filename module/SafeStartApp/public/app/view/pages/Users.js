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
        scrollable: false,
        layout: 'hbox',

        items: [

        ],

        listeners: {
            scope: this,
            show: function(page) {
                page.loadData();
            }
        }
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

        this.add(this.getUsersList());

        this.add(this.getInfoPanel());

        this.disable();
    },

    getUsersList: function() {
        return {
            xtype: 'list',
            name: 'users',
            itemTpl: '<div class="contact">{firstName} {lastName}</div>',
            minWidth: 150,
            maxWidth: 300,
            flex: 1,
            cls: 'sfa-left-container',
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
                                    return self.filterStoreDataBySearchFiled(self.usersStore, field, 'firstName');
                                }
                            }
                        },
                        { xtype: 'spacer' },
                        {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            iconCls: 'refresh',
                            cls:'sfa-search-reload',
                            handler: function() {
                                this.up('list[name=users]').getStore().loadData();
                                console.log(this.up('list[name=users]').getStore());
                            }
                        }
                    ]
                }
            ]
        };
    },

    getInfoPanel: function() {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            name: 'user-info',
            layout: 'card',
            minWidth: 150,
            flex: 2,
            scrollable: true
        };
    },

    loadData: function() {
        this.down('SafeStartUsersToolbar').setTitle(SafeStartApp.companyModel.get('title')+': '+'users');
        this.usersStore.getProxy().setExtraParam('companyId', SafeStartApp.companyModel.get('id') || 0);
        this.usersStore.loadData();
    }
});