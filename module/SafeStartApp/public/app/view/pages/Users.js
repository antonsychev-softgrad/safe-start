Ext.define('SafeStartApp.view.pages.Users', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Users',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Users'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartUsersPage',
    companyId: 0,
    config: {
        title: 'Users',
        iconCls: 'user',
        styleHtmlContent: true,
        layout: 'hbox',
        tab: {
            action: 'users'
        },
        items: [

        ],

        listeners: {
            scope: this,
            activate: function(page) {
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

        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) this.disable();
    },

    getUsersList: function() {
        var self = this;
        return {
            xtype: 'list',
            name: 'users',
            itemTpl: '<div class="contact">{firstName} {lastName}</div>',
            minWidth: 150,
            maxWidth: 300,
            showAnimation: {
                type: 'pop'
            },
            hideAnimation: {
                type: 'pop',
                out: 'true'
            },
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
                                    self.filterStoreDataBySearchFiled(self.usersStore, field, 'firstName');
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
                            }
                        }
                    ]
                },
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    items: [
                        {
                            iconCls: 'add',
                            ui: 'action',
                            text: 'Add User',
                            action: 'add-user'
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
            flex: 2,
            minWidth: 150
        };
    },

    loadData: function() {
        if (!SafeStartApp.companyModel || !SafeStartApp.companyModel.get || !SafeStartApp.companyModel.get('id')) return;
        if (SafeStartApp.companyModel.get('id') == this.companyId) return;
        this.companyId = SafeStartApp.companyModel.get('id');
        this.down('SafeStartUsersToolbar').add(
            {
                ui: 'action',
                text: SafeStartApp.companyModel.get('title')+': '+'Users'
            }
        );
        this.usersStore.getProxy().setExtraParam('companyId', this.companyId);
        this.usersStore.loadData();
    }
});