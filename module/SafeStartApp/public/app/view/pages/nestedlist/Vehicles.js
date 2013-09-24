Ext.define('SafeStartApp.view.pages.nestedlist.Vehicles', {
    extend: 'Ext.dataview.NestedList',
    alias: 'widget.SafeStartNestedListVehicles',
    xtype: 'SafeStartNestedListVehicles',
    mixins: ['SafeStartApp.store.mixins.FilterByField'],
    name: 'vehicles',
    config: {
        minWidth: 150,
        maxWidth: 300,
        showAnimation: {
            type: 'slide',
            direction: 'right',
            duration: 100
        },
        hideAnimation: {
            type: 'slide',
            direction: 'left',
            duration: 200
        },
        filterValue: '',
        filterField: 'text',
        title: 'Vehicles',
        displayField: 'text',
        cls: 'sfa-left-container',
        flex: 1,
        getTitleTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        getItemTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        listeners: {
            activeitemchange: function (nestedlist) {
                var toolbar = this.down('toolbar');
                if (toolbar) {
                    toolbar.hide();
                }
            },
            back: function () {
                if(this._backButton._hidden) {
                    this.down('toolbar').show();
                }
            }
        }
    },

    syncStores: function () {
        this.setFilterValue('');
        this.updateNestedListStore();
    },

    updateNestedListStore: function () {
        var store = this.getStore();
        var vehiclesStore = this.vehiclesStore;
        var filter = this.getFilterValue();
        var records = [];

        if (! filter) {
            records = [];
            var nodes = vehiclesStore.getRoot().childNodes;
            for (var i = 0, len = nodes.length; i < len; i++) {
                records.push(this._parseNode(nodes[i]));
            }
            store.fillNode(store.getRoot(), Ext.clone(records));
            return;
        }
    },

    _parseNode: function (node) {
        var childNodes = node.childNodes;
        var data = Ext.clone(node.getData());
        delete data.internalId;
        delete data.parentId;
        if (childNodes.length) {
            data.expanded = true;
        }
        data.data = [];
        for (var i = 0, len = childNodes.length; i < len; i++) {
            data.data.push(this._parseNode(childNodes[i]));
        }
        return data;
    },

    getVehiclesStore: function () {
        return this.vehiclesStore;
    },

    tapOnNode: function (node) {
        this.fireEvent('itemtap', this, this.getActiveItem(), 0, null, node);
        this.on({
            activeitemchange: function () {
                this.getActiveItem().select(node);
            },
            single: true
        });
    },

    initialize: function() {
        var me = this;
        this.filters = {};
        this.vehiclesStore = this.config.vehiclesStore;

        this.vehiclesStore.on('load', function (store, records) {
            me.updateNestedListStore();
        });

        this.setItems([{
            xtype: 'toolbar',
            docked: 'top',
            items: [{
                xtype: 'searchfield',
                placeHolder: 'Search...',
                listeners: {
                    clearicontap: function() {
                        this.getStore().clearFilter();
                    },
                    keyup: function(field, e) {
                        this.filterVehiclesByName(field.getValue());
                    },
                    scope: this
                }
            }, {
                xtype: 'spacer'
            }, {
                xtype: 'button',
                name: 'reload',
                ui: 'action',
                action: 'refresh',
                iconCls: 'refresh',
                cls: 'sfa-search-reload',
                handler: function() {
                    var nestedlist = this.up('nestedlist');
                    this.up('toolbar').down('searchfield').setValue('');
                    nestedlist.filterVehiclesByName('');
                    nestedlist.vehiclesStore.loadData();
                }
            }]
        }]);
    }
});