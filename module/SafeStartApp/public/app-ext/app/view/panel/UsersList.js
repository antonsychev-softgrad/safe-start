Ext.define('SafeStartExt.view.panel.UsersList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.Users'
    ],
    xtype: 'SafeStartExtPanelUsersList',
    cls: 'sfa-left-coll',
    layout: 'fit',
    ui: 'light-left',
    minWidth: 250,
    border: 0,
    title: 'Users',

    initComponent: function() {
        var store = SafeStartExt.store.Users.create();
        Ext.apply(this, {
            tbar: {
                xtype: 'toolbar',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: [{
                    text: 'Add user',
                    cls: 'sfa-add-button',
                    handler: function() {
                        this.up('SafeStartExtPanelUsersList').fireEvent('addUserAction');
                    }
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        cls: 'search',
                        flex: 1,
                        margin: '0 5 0 5',
                        height: 22,
                        listeners: {
                            change: function(textfield, value) {
                                store.clearFilter();
                                if (value) {
                                    store.filter('firstName', value);
                                }
                            }
                        }
                    }, {
                        xtype: 'button',
                        iconCls: 'sfa-icon-refresh',
                        scale: 'medium',
                        handler: function() {
                            this.up('toolbar').down('textfield').setValue('');
                            store.load();
                        }
                    }]
                }]
            },
            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class=sfa-vehicle-item>',
                    '{firstName}',
                    '</div>',
                    '</tpl>'
                ),
                store: store,
                listeners: {
                    itemclick: this.onUserClick,
                    scope: this
                }
            }]
        });
        this.callParent();
    },

    onUserClick: function(dataview, record) {
        this.fireEvent('changeUserAction', record);
    },

    getListStore: function() {
        return this.down('dataview').getStore();
    }

});