Ext.define('SafeStartExt.view.panel.Inspections', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.toolbar.Paging',
        'SafeStartExt.store.Inspections',
        'SafeStartExt.view.panel.InspectionInfo'
    ],
    xtype: 'SafeStartExtPanelInspections',
    cls:'sfa-previous-inspection',
    // layout: 'fit',
    border: 0,
    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    listeners: {
        activate: function () {
            if (this.params && this.params.checklistHash) {
                var hashes = [];
                var store = this.getListStore();
                if (store.getCount()) {
                    var record = this.getListStore().findRecord('hash', this.params.checklistHash);
                    this.getListStore().each(function (rec) {
                        hashes.push({title: rec.get('title'), hash: rec.get('hash')});
                    });
                    if (record) {
                        this.down('dataview').fireEvent('itemclick', this.down('dataview'), record, {}, {});
                        delete this.params.checklistHash;
                    }
                    return;
                }
                store.on('load', function (records) {
                    var record = this.getListStore().findRecord('hash', this.params.checklistHash);
                    this.getListStore().each(function (rec) {
                        hashes.push({title: rec.get('title'), hash: rec.get('hash')});
                    });
                    if (record) {
                        this.down('dataview').fireEvent('itemclick', this.down('dataview'), record, {}, {});
                        delete this.params.checklistHash;
                    }
                }, this, {single: true, priority: 1000});
            }  
        }
    },

    initComponent: function () {
        var store = SafeStartExt.store.Inspections.create({
            vehicleId: this.vehicle.get('id')
        });
        Ext.apply(this, {
            items: [{
                xtype: 'panel',
                title: 'Previous Inspections',
                ui: 'light-left',
                flex: 1,
                border: 0,
                maxWidth: 250,
                cls: 'sfa-previous-inspections-left-coll',
                overflowY: 'auto',
                items: [{
                    xtype: 'dataview',
                    itemSelector: 'div.sfa-vehicle-item',
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                        '<div class=sfa-vehicle-item>',
                        '{title}',
                        '</div>',
                        '</tpl>'
                    ),
                    store: store,
                    listeners: {
                        select: function () {
                            this.down('SafeStartExtPanelInspectionInfo').down('toolbar').show();
                        },
                        deselect: function () {
                            this.down('SafeStartExtPanelInspectionInfo').down('toolbar').hide();
                        },
                        scope: this
                    }
                }]
            }, {
                xtype: 'SafeStartExtPanelInspectionInfo',
                tbar: {
                    xtype: 'toolbar',
                    border: 0,
                    style: {
                        border: 0
                    },
                    hidden: true,
                    items: [{
                        text: 'Print',
                        handler: function (btn) {
                            var panel = btn.up('SafeStartExtPanelInspections').down('dataview');
                            if (panel.inspection) {
                                this.fireEvent('printInspectionAction', panel.inspection.get('id'));
                            }
                        },
                        scope: this
                    }, {
                        text: 'Edit',
                        handler: function (btn) {
                            var panel = btn.up('SafeStartExtPanelInspections').down('dataview');
                            if (panel.inspection) {
                                this.fireEvent('editInspectionAction', panel.inspection.get('id'));
                            }
                        },
                        scope: this
                    }, {
                        text: 'Delete',
                        handler: function (btn) {
                            var panel = btn.up('SafeStartExtPanelInspections').down('dataview');
                            if (panel.inspection) {
                                this.fireEvent('deleteInspectionAction', panel.inspection.get('id'));
                            }
                        },
                        scope: this
                    }]
                },
                vehicle: this.vehicle,
                flex: 2
            }]
        });
        this.callParent();
    },

    getListStore: function () {
        return this.down('dataview').getStore();
    }
});
