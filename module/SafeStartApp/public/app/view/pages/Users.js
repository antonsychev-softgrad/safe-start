Ext.define('SafeStartApp.view.pages.Users', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Users',
        'SafeStartApp.view.pages.panel.LeftContainer'
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

    initialize: function() {
        var self = this;
        this.callParent();

        this.add({
            xtype: 'SafeStartMainToolbar',
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
            xtype: 'SafeStartLeftContainer',
            flex: 1,
            items: [{
                xtype: 'container',
                layout: 'fit',
                items: [{
                    xtype: 'list',
                    name: 'users',
                    itemTpl: '<div class="contact">{firstName} {lastName}</div>',
                    showAnimation: {
                        type: 'pop'
                    },
                    hideAnimation: {
                        type: 'pop',
                        out: 'true'
                    },
                    margin: '0',
                    cls: 'sfa-left-container',
                    store: this.usersStore,
                    items: [{
                        xtype: 'toolbar',
                        docked: 'top',
                        items: [{
                            iconCls: 'add',
                            cls: 'sfa-add-button',
                            ui: 'action',
                            text: 'Add User',
                            action: 'add-user'
                        }, {
                            xtype: 'spacer',
                            flex: 1
                        }, {
                            iconCls: 'arrow_left',
                            //height: 22,
                            cls: 'sfa-collapse',
                            iconMask: true,
                            handler: function (btn) {
                                var panel = this.up('SafeStartLeftContainer');
                                panel.toggleMenu();
                            }
                        }]
                    }, {
                        xtype: 'toolbar',
                        docked: 'top',

                        items: [{
                            xtype: 'searchfield',
                            placeHolder: 'Search...',
                            flex: 1,
                            listeners: {
                                scope: this,
                                clearicontap: function() {
                                    self.usersStore.clearFilter();
                                },
                                keyup: function(field) {
                                    self.filterStoreDataBySearchFiled(self.usersStore, field, 'firstName');
                                }
                            }
                        }, {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            iconCls: 'refresh',
                            cls: 'sfa-search-reload',
                            handler: function() {
                                this.up('list[name=users]').getStore().loadData();
                            }
                        }]
                    }]
                }]
            }, {
                xtype: 'panel',
                cls: 'sfa-left-container',
                margin: '0',
                items: [{
                    xtype: 'toolbar',
                    items: [{
                        iconCls: 'arrow_right',
                        //height: 22,
                        iconMask: true,
                        handler: function() {
                            var panel = this.up('SafeStartLeftContainer');
                            panel.toggleMenu();
                        }
                    }]
                }]
            }]
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
        this.down('SafeStartMainToolbar').setBtnTitle(SafeStartApp.companyModel.get('title') + ': ' + 'Users');
        this.usersStore.getProxy().setExtraParam('companyId', this.companyId);
        this.usersStore.loadData();
    }
});