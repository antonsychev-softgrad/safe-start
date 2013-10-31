Ext.define('SafeStartExt.controller.Company', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'SafeStartExtComponentCompany SafeStartExtPanelVehicleList',
        ref: 'vehicleListView'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtPanelVehicleTabs',
        ref: 'vehicleTabsView'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtContainerTopNav',
        ref: 'vehicleTopNav'
    }, {
        selector: 'SafeStartExtComponentCompany',
        ref: 'companyPage'
    }, {
        selector: 'SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'SafeStartExtPanelInspectionInfo',
        ref: 'inspectionInfoPanel'
    }, {
        selector: 'SafeStartExtComponentCompany SafeStartExtFormVehicle',
        ref: 'vehicleForm'
    }],

    needUpdate: false,

    init: function () {
        this.control({
            'SafeStartExtMain': {
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtPanelVehicleList': {
                changeVehicleAction: this.changeVehicleAction,
                addVehicleAction: this.addVehicle
            },
            'SafeStartExtComponentCompany': {
                activate: this.refreshPage,
                afterrender: this.refreshPage
            },
            'SafeStartExtFormVehicle': {
                afterrender: this.fillForm,
                updateVehicleAction: this.updateVehicle,
                deleteVehicleAction: this.deleteVehicle
            },
            'SafeStartExtPanelInspections': {
                afterrender: this.loadInspections
                // setInspectionInfo: this.setInspectionInfo
            },
            'SafeStartExtPanelInspections dataview': {
                itemclick: this.setInspectionInfo
            }
        });
    },

    addVehicle: function () {
        var vehicle = SafeStartExt.model.MenuVehicle.create({});
        vehicle.pages().add([{
            action: 'info',
            text: 'Current Information'
        }]);
        this.deselectVehicle();
        this.changeVehicleAction(vehicle);
    },

    updateVehicle: function (vehicle, data) {
        var me = this;
        data.companyId = this.company.get('id');
        Ext.applyIf(data, {
            enabled: false
        });


        SafeStartExt.Ajax.request({
            url: 'vehicle/' + vehicle.get('id') + '/update',
            data: data,
            success: function (res) {
                if (res.done) {
                    me.reloadVehicles(res.vehicleId);
                }
            }
        });
    },

    deleteVehicle: function (vehicle) {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + vehicle.get('id') + '/delete',
            success: function (res) {
                if (res.done) {
                    me.reloadVehicles();
                }
            }
        });
    },

    reloadVehicles: function (vehicleId) {
        var me = this, 
            store = this.getVehicleListView().getListStore();

        store.load({
            callback: function (records) {
                var record;
                if (vehicleId) {
                    record = this.findRecord('id', vehicleId);
                }
                if (record) {
                    me.selectVehicle(record);
                } else {
                    me.deselectVehicle();
                }
            }
        });
    },

    selectVehicle: function (vehicle) {
        this.getVehicleListView().getList().select(vehicle);
        this.changeVehicleAction(vehicle);
    },

    deselectVehicle: function () {
        if (this.vehicle) {
            this.getVehicleListView().getList().deselect(this.vehicle);
        }
        this.getCompanyPage().unsetVehicle();
    },

    changeCompanyAction: function (company) {
        this.company = company;
        this.needUpdate = true;

        if (this.getMainPanel().getLayout().getActiveItem() === this.getCompanyPage()) {
            this.refreshPage();
        }
    },

    fillForm: function (form) {
        form.loadRecord(this.vehicle);
    },

    loadInspections: function (view) {
        view.getListStore().load();
    },

    setInspectionInfo: function (view, inspection) {
        var me = this;
        SafeStartExt.Ajax.request({
            url: 'vehicle/' + inspection.get('id') + '/getchecklistdata',
            success: function (data) {
                me.getInspectionInfoPanel().setInspectionInfo(inspection, data);
            }
        });
    },

    changeVehicleAction: function (vehicle) {
        this.vehicle = vehicle;
        this.getCompanyPage().setVehicle(vehicle);
    },

    refreshPage: function () {
        if (this.needUpdate) {
            this.needUpdate = false;
            this.getCompanyPage().unsetVehicle();

            var store = this.getVehicleListView().getListStore();
            this.getVehicleTopNav().setCompanyName(this.company.get('title'));
            store.getProxy().setExtraParam('companyId', this.company.get('id'));
            store.load();
        }
    }
});
