Ext.define('SafeStartApp.view.pages.panel.VehicleInspections', {
    extend: 'Ext.navigation.View',

    alias: 'widget.SafeStartVehicleInspectionsPanel',

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    requires: [
        'SafeStartApp.store.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails'
    ],

    config: {
        navigationBar: {
            items: [{
                name: 'edit-inspection',
                align: 'right',
                hidden: true,
                text: 'Edit',
                handler: function (button) {
                    var vehicleInspectionsPanel = button.up('SafeStartVehicleInspectionsPanel');
                    var vehicleInspectionDetails = vehicleInspectionsPanel.down('SafeStartVehicleInspectionDetails');

                    var checkListId = vehicleInspectionDetails.checkListId; 
                    var vehicleId = vehicleInspectionDetails.vehicleId;
                    vehicleInspectionsPanel.fireEvent('editInspection', vehicleId, checkListId, vehicleInspectionDetails.inspectionRecord);
                }
            }]
        },
        listeners: {
            push: function (view, item) {
                this.changeButtons(item);
            },
            pop: function (view, item) {
                this.changeButtons(view);
            },
            back: function () {
                this.hideButtons();
            }
        }
    },

    initialize: function () {
        this.callParent();
        this.inspectionsStore = Ext.create('SafeStartApp.store.VehicleInspections');
        this.inspectionsStore.getProxy().setUrl('/api/vehicle/1/getinspections');
        this.add(this.getListPanel());
    },

    getListPanel: function () {
        var self = this;
        return [{
            xtype: 'list',
            name: 'vehicle-inspections',
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
        this.removeChecklistDetails();
    },

    onSelectInspectionAction: function(list, index, node, record) {
        this.loadChecklistDetails(record);
    },

    loadChecklistDetails: function (record) {
        if (this.inspectionView) {
            this.inspectionView.destroy();
        }
        this.inspectionView = Ext.create('SafeStartApp.view.pages.panel.VehicleInspectionDetails', {
            title: 'Inspection'
        });
        this.hideButtons();
        this.push(this.inspectionView);
        this.inspectionView.loadChecklist(this.vehicle, record);
    },

    removeChecklistDetails: function () {
        var detailsView = this.down('SafeStartVehicleInspectionDetails');
        if (detailsView) {
            this.pop(detailsView);
            this.hideButtons();
        }
    },

    onEditInspectionAction: function () {
    },

    changeButtons: function (view) {
        switch (view.xtype) {
            case 'SafeStartVehicleInspectionsPanel':
                break;
            case 'SafeStartVehicleInspectionDetails':
                this.down('button[name=edit-inspection]').show(true);
                break;
        }
    },

    hideButtons: function () {
        this.down('button[name=edit-inspection]').hide(true);
    }

});