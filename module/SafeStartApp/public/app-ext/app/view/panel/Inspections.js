Ext.define('SafeStartExt.view.panel.Inspections', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.toolbar.Paging',
        'SafeStartExt.store.Inspections',
        'SafeStartExt.view.panel.InspectionInfo',
        'Ext.ux.GMapPanel'
    ],
    xtype: 'SafeStartExtPanelInspections',
    cls:'sfa-previous-inspection',
    border: 0,
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    configData: {

    },

    listeners: {
        activate: function () {
            if (this.configData && this.configData.checklistHash) {
                var hashes = [];
                var store = this.getListStore();
                if (store.getCount()) {
                    var record = this.getListStore().findRecord('hash', this.configData.checklistHash);
                    this.getListStore().each(function (rec) {
                        hashes.push({title: rec.get('title'), hash: rec.get('hash')});
                    });
                    if (record) {
                        this.down('dataview').fireEvent('itemclick', this.down('dataview'), record, {}, {});
                        delete this.configData.checklistHash;
                    }
                    return;
                }
                store.on('load', function (records) {
                    var record = this.getListStore().findRecord('hash', this.configData.checklistHash);
                    this.getListStore().each(function (rec) {
                        hashes.push({title: rec.get('title'), hash: rec.get('hash')});
                    });
                    if (record) {
                        this.down('dataview').fireEvent('itemclick', this.down('dataview'), record, {}, {});
                        delete this.configData.checklistHash;
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
                        select: function (dataview, record) {
                            var latlon = record.get('gps').split(';'),
                                lat = NaN, 
                                lon = NaN;

                            this.down('SafeStartExtPanelInspectionInfo').down('container[name=toolbar]').show();

                            if (latlon.length) {
                                lat = parseFloat(latlon[0], 10);
                                lon = parseFloat(latlon[1], 10);
                            }
                            var button = this.down('SafeStartExtPanelInspectionInfo').down('button[action=open-map]');
                            if (isNaN(lat) || isNaN(lon)) {
                                button.hide();
                            } else {
                                button.lat = lat;
                                button.lon = lon;
                                button.show();
                            }
                        },
                        deselect: function () {
                            this.down('SafeStartExtPanelInspectionInfo').down('container[name=toolbar]').hide();
                        },
                        scope: this
                    }
                }]
            }, {
                xtype: 'SafeStartExtPanelInspectionInfo',
                tbar: {
                    xtype: 'container',
                    name: 'toolbar',
                    border: 0,
                    style: {
                        border: 0
                    },
                    hidden: true,
                    width: '100%',
                    defaults: {
                        margin: '5'
                    },
                    items: [{
                        xtype: 'button',
                        text: 'Open Map',
                        action: 'open-map',
                        ui: 'blue',
                        scale: 'medium',
                        hidden: true,
                        handler: function (btn) {
                            this.openMap(btn.lat, btn.lon);
                        },
                        scope: this
                    }, {
                        xtype: 'button',
                        text: 'Print',
                        ui: 'blue',
                        scale: 'medium',
                        handler: function (btn) {
                            var panel = btn.up('SafeStartExtPanelInspections').down('dataview');
                            if (panel.inspection) {
                                this.fireEvent('printInspectionAction', panel.inspection.get('id'));
                            }
                        },
                        scope: this
                    }, {
                        xtype: 'button',
                        text: 'Edit',
                        ui: 'blue',
                        scale: 'medium',
                        handler: function (btn) {
                            var panel = btn.up('SafeStartExtPanelInspections').down('dataview');
                            if (panel.inspection) {
                                this.fireEvent('editInspectionAction', panel.inspection.get('id'));
                            }
                        },
                        scope: this
                    }, {
                        xtype: 'button',
                        text: 'Delete',
                        //cls:'sfa-red-button',
                        ui: 'red',
                        scale: 'medium',
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
    },

    openMap: function (lat, lon) {
        var panel;
        if (typeof google === 'undefined') {
            Ext.Msg.alert(
                "Error", 
                "The maps is currently unreachable"
            );
            return;
        }
        panel = Ext.create('Ext.window.Window', {
            cls: 'sfa-vehicle-inspection-details-map', 
            width: 800,
            height: 600,
            layout: 'fit'
        });
        var position = new google.maps.LatLng(lat, lon);
        var map = panel.down('map');
        if (map) {
            map.marker.setPosition(position);
            map.getMap().setCenter(position);
        } else {
            panel.setLoading(true);
            panel.add({
                xtype: 'gmappanel',
                // gmaptype: google.GMapType.G_NORMAL_MAP,
                // GMapType: 
                mapOptions: {
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                },
                // gmaptype: google.maps.MapTypeId.ROADMAP,
                center: position,
                markers: [{
                    lat: lat,
                    lng: lon,
                    marker: {title: "TEST"}
                }]
                // listeners: {
                    // maprender: function (mapCmp) {
                    //     panel.setLoading(false);
                    //     mapCmp.marker = new google.maps.Marker({
                    //         position: position,
                    //         title: 'Vehicle Inspection',
                    //         map: mapCmp.getMap()
                    //     });
                    // }
                // }
            });
        }
        this.add(panel);
        panel.show();
    }
});
