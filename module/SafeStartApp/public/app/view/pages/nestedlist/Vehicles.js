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
            type: 'pop'
        },
        hideAnimation: {
            type: 'pop',
            out: 'true'
        },
        toolbar: {
        },
        filterValue: '',
        filterField: 'text',
        title: 'Vehicles',
        displayField: 'text',
        flex: 1,
        getTitleTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        getItemTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        listeners: {
            activeitemchange: function (nestedlist) {
                Ext.each(this.down('toolbar[name=first-level]'), function (toolbar) {
                    toolbar.hide();
                });
            },
            back: function () {
                if (this._backButton._hidden) {
                    Ext.each(this.down('toolbar[name=first-level]'), function (toolbar) {
                        toolbar.show();
                    });
                }
                this._activeNode = this.getStore().getRoot();
            },
            itemtap: function (nestedlist, list, index, target, record) {
                this._activeNode = record;
                if (record.get('depth') == 1) {
                    this.fireEvent('selectVehicle', record, false);
                } else if (record.get('depth') == 2) {
                    this.fireEvent('selectAction', record, false);
                }
            }
        }
    },

    updateNestedListStore: function () {
        var store = this.getStore(),
            vehiclesStore = this.vehiclesStore,
            filter = this.getFilterValue(),
            records = [],
            filteredRecords = [];

        this._activeNode = this.getStore().getRoot();
        records = [];
        store.removeAll();
        var nodes = vehiclesStore.getRoot().childNodes;
        for (var i = 0, len = nodes.length; i < len; i++) {
            records.push(function parseNode(node) {
                var childNodes = node.childNodes;
                var data = Ext.clone(node.getData());
                delete data.internalId;
                delete data.parentId;
                if (childNodes.length) {
                    data.expanded = true;
                }
                data.data = [];
                for (var i = 0, len = childNodes.length; i < len; i++) {
                    data.data.push(parseNode(childNodes[i]));
                }
                return data;
            }(nodes[i]));
        }
        this.goToNode(store.getRoot());

        if (filter) {
            Ext.each(records, function (record) {
                if (RegExp(filter, 'i').test(record.text)) {
                    filteredRecords.push(record);
                }
            });
        } else {
            filteredRecords = records;
        }

        store.getRoot().appendChild(filteredRecords);
    },

    getVehiclesStore: function () {
        return this.vehiclesStore;
    },

    tapOnActionNode: function (action, vehicleId, silent) {
        var activeNode = this._activeNode,
            actionNode = null;

        if (!activeNode) {
            return false;
        }

        if (activeNode.get('depth') === 0 && vehicleId) {
            vehicleNode = activeNode.findChild('id', vehicleId);
            this.on({
                activeitemchange: function () {
                    this.tapOnActionNode(action, vehicleId, silent);
                },
                single: true
            });
            this._activeNode = vehicleNode;
            this.goToNode(vehicleNode);
            this.getActiveItem().select(vehicleNode);
            return;
        }

        if (activeNode.get('depth') === 1) {
            vehicleNode = activeNode;
        }

        if (activeNode.get('depth') === 2) {
            vehicleNode = activeNode.parentNode;
        }

        actionNode = vehicleNode.findChild('action', action);
        if (this.getActiveItem().getStore().indexOf(actionNode) === -1) {
            this.on({
                activeitemchange: function () {
                    this.goToLeaf(actionNode);
                    this.getActiveItem().select(actionNode);
                    this._activeNode = actionNode;
                    this.fireEvent('selectAction', actionNode, silent);
                },
                single: true
            });
            return;
        }
        this.goToLeaf(actionNode);
        this.getActiveItem().select(actionNode);
        this._activeNode = actionNode;
        this.fireEvent('selectAction', actionNode, silent);
    },

    initialize: function () {
        var me = this;
        this.filters = {};
        this.vehiclesStore = this.config.vehiclesStore;

        this.vehiclesStore.on('beforeload', function (store, records) {
            this.setMasked({
                xtype: 'loadmask',
                message: 'Loading...'
            });
        }, this);


        this.vehiclesStore.on('load', function (store, records) {
            this.updateNestedListStore();
        }, this);

        this.vehiclesStore.on('load', function (store, records) {
            this.setMasked(false);

            Ext.each(this.down('toolbar'), function (toolbar) {
                toolbar.show();
            });
        }, this, {order: 'after'});


        this.callParent();


        this.add([
            {
                xtype: 'toolbar',
                name: 'first-level',
                docked: 'top',
                ui: '',
                items: [
                    {
                        xtype: 'searchfield',
                        flex: 1,
                        placeHolder: 'Search...',
                        listeners: {
                            clearicontap: function () {
                                this.setFilterValue('');
                                this.updateNestedListStore();
                            },
                            keyup: function (field, e) {
                                this.setFilterValue(field.getValue());
                                this.updateNestedListStore();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'button',
                        name: 'reload',
                        ui: 'action',
                        action: 'refresh',
                        iconCls: 'refresh',
                        cls: 'sfa-search-reload',
                        handler: function () {
                            this.down('searchfield').setValue('');
                            this.setFilterValue('');
                            this.getVehiclesStore().loadData();
                        },
                        scope: this
                    }
                ]
            }
        ]);

        this.add([
            {
                xtype: 'toolbar',
                name: 'first-level-bottom',
                docked: 'bottom',
                ui: '',
                items: [
                    {
                        xtype: 'spacer'
                    },
                    {
                        xtype: 'button',
                        cls: 'sfa-add-button',
                        name: 'print-action-lists',
                        ui: 'action',
                        iconCls: 'organize',
                        action: 'print-action-lists',
                        text: 'Print Action List',
                        handler: function () {
                            window.open('/api/vehicle/0/print-action-list', '_blank');
                        },
                        scope: this
                    },
                    {
                        xtype: 'spacer'
                    }
                ]
            }
        ]);

    }
});