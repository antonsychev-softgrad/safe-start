Ext.define('SafeStartApp.view.pages.panel.VehicleInspections', {
    extend: 'Ext.navigation.View',

    alias: 'widget.SafeStartVehicleInspectionsPanel',

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    requires: [
        'SafeStartApp.store.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails'
    ],

    config: {cls:'sfa-container-padding',
        navigationBar: {cls:'sfa-inspection-toolbar',
            items: [{
                name: 'edit-inspection',
                align: 'right',
                text: 'Edit',
                hidden: true,
                handler: function (button) {
                    var vehicleInspectionsPanel = button.up('SafeStartVehicleInspectionsPanel'),
                        vehicleInspectionDetails = vehicleInspectionsPanel.down('SafeStartVehicleInspectionDetails'),
                        checkListId = vehicleInspectionDetails.checkListId,
                        vehicleId = vehicleInspectionDetails.vehicleId;

                    vehicleInspectionsPanel.fireEvent('editInspection', vehicleId, checkListId, vehicleInspectionDetails.inspectionRecord);
                }
            }, {
                name: 'delete-inspection',
                align: 'right',
                text: 'Delete',
                hidden: true,
                handler: function (button) {
                    var vehicleInspectionsPanel = button.up('SafeStartVehicleInspectionsPanel'),
                        vehicleInspectionDetails = vehicleInspectionsPanel.down('SafeStartVehicleInspectionDetails'),
                        checkListId = vehicleInspectionDetails.checkListId,
                        vehicleId = vehicleInspectionDetails.vehicleId;

                    Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this inspection?", function(){
                        vehicleInspectionsPanel.fireEvent('deleteInspection', vehicleId, checkListId);
                    });
                }
            }]
        },
        listeners: {
            push: function (view, item) {
                this.hideButtons();
                this.changeButtons(item);
            },
            pop: function (view, item) {
            },
            back: function (view, item, item2) {
                var innerItems = this.getInnerItems();
                this.hideButtons();
                this.changeButtons(innerItems[innerItems.length-2]);
            },
            openMap: function (lat, lon) {
                this.onOpenMapAction(lat, lon);
            },
            hide: function () {
                this.hideButtons();
                var innerItems = this.getInnerItems();
                for (var i = 1, len = innerItems.length; i < len; i++) {
                    this.remove(innerItems[i]);
                }
            }
        }
    },

    initialize: function () {
        this.callParent();
        this.inspectionsStore = Ext.create('SafeStartApp.store.VehicleInspections');
        this.add(this.getListPanel());
    },

    getListPanel: function () {
        var self = this;
        return [{
            xtype: 'list',
            name: 'vehicle-inspections',
            plugins: [{
                xclass: 'Ext.plugin.ListPaging',
                autoPaging: true,
                noMoreRecordsText: ''
            }],
            disableSelection: true,
            title: 'Vehicle Inspections',
            emptyText: 'No Inspections',
            itemTpl: '{title}',
            cls: 'sfa-inspections',
            store: this.inspectionsStore,
            listeners: {
                itemtap: function(list, index, node, record) {
                   self.onSelectInspectionAction(list, index, node, record);
                }
            }
        }];
    },

    loadList: function (vehicle, checklists) {
        this.vehicleId = vehicle.get('id');
        this.vehicle = vehicle;
        this.inspectionsStore.getProxy().setUrl('/api/vehicle/' + this.vehicleId + '/getinspections');
        this.inspectionsStore.loadData();
        this.hideButtons();
        var innerItems = this.getInnerItems();
        for (var i = 1, len = innerItems.length; i < len; i++) {
            this.remove(innerItems[i]);
        }
    },

    onSelectInspectionAction: function(list, index, node, record) {
        this.loadChecklistDetails(record);
    },

    onOpenMapAction: function  (lat, lon) {
        var panel;
        if (! google) {
            Ext.Msg.alert(
                "Error", 
                "The maps is currently unreachable"
            );
            return;
        }
        panel = Ext.create('Ext.Panel', {
            cls: 'sfa-vehicle-inspection-details-map', 
            layout: 'fit'
        });
        var position = new google.maps.LatLng(lat, lon);
        var map = panel.down('map');
        if (map) {
            map.marker.setPosition(position);
            map.getMap().setCenter(position);
        } else {
            panel.add({
                xtype: 'map',
                mapOptions: {
                    center: position
                },
                listeners: {
                    maprender: function (mapCmp) {
                        mapCmp.marker = new google.maps.Marker({
                            position: position,
                            title: 'Vehicle Inspection',
                            map: mapCmp.getMap()
                        });
                    }
                }
            });
        }
        this.push(panel);
    },

    loadChecklistDetails: function (record) {
        this.inspectionView = Ext.create('SafeStartApp.view.pages.panel.VehicleInspectionDetails', {
            title: 'Inspection'
        });
        this.push(this.inspectionView);
        this.inspectionView.loadChecklist(this.vehicle, record);
        var me = this;
            me.changeButtons(me.inspectionView);
    },

    changeButtons: function (view) {
        switch (view.xtype) {
            case 'SafeStartVehicleInspectionsPanel':
                break;
            case 'SafeStartVehicleInspectionDetails':
                this.down('button[name=edit-inspection]').show(true);
                this.down('button[name=delete-inspection]').show(true);
                break;
            case 'SafeStartVehicleInspectionMapPanel':
                break;
        }
    },

    hideButtons: function () {
        this.down('button[name=edit-inspection]').hide();
        this.down('button[name=delete-inspection]').hide();
    }

});