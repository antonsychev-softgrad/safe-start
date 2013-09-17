Ext.define('SafeStartApp.view.pages.panel.VehicleInspections', {
    extend: 'Ext.navigation.View',

    alias: 'widget.SafeStartVehicleInspectionsPanel',

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    requires: [
        'SafeStartApp.store.VehicleInspections',
        'SafeStartApp.view.pages.panel.VehicleInspectionDetails'
    ],

    initialize: function () {
        this.callParent();
        this.inspectionsStore = Ext.create('SafeStartApp.store.VehicleInspections');
        this.inspectionsStore.getProxy().setUrl('/api/vehicle/1/getinspections');
        this.add(this.getListPanel());
    },

    getListPanel: function () {
        var self = this;
        return {
            xtype: 'list',
            name: 'vehicle-inspections',
            title: 'Vehicle Inspections',
            emptyText: 'No Inspections',
            itemTpl: [
                '{title}'
                // '<span>{user.firstName} {user.lastName} at {title}</span>'
            ].join(''),
            cls: 'sfa-inspections',
            store: this.inspectionsStore,
            listeners: {
                itemtap: function(list, index, node, record) {
                   self.onSelectInspectionAction(list, index, node, record);
                }
            }
        };
    },

    loadList: function (vehicle, checklists) {
        this.vehicleId = vehicle.get('id');
        this.vehicle = vehicle;
        this.inspectionsStore.getProxy().setUrl('/api/vehicle/' + this.vehicleId + '/getinspections');
        this.inspectionsStore.load();
    },

    onSelectInspectionAction: function(list, index, node, record) {
        if (this.inspectionView) {
            this.inspectionView.destroy();
        }
        this.inspectionView = Ext.create('SafeStartApp.view.pages.panel.VehicleInspectionDetails');
        this.inspectionView.loadChecklist(this.vehicle, record.get('checkListId'));
        this.push(this.inspectionView);
    }
});