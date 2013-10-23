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
    }],

    needUpdate: false,

    init: function () {
        this.control({
            'SafeStartExtMain': {
                changeCompanyAction: this.changeCompanyAction
            },
            'SafeStartExtPanelVehicleList': {
                changeVehicleAction: this.changeVehicleAction
            },
            'SafeStartExtComponentCompany': {
                activate: this.refreshPage,
                afterrender: this.refreshPage
            },
            'SafeStartExtPanelVehicleInfo': {
                afterrender: this.setVehicleInfo
            }
        });
    },

    changeCompanyAction: function (company) {
        this.company = company;
        this.needUpdate = true;

        if (this.getMainPanel().getLayout().getActiveItem() === this.getCompanyPage()) {
            this.refreshPage();
        }
    },

    setVehicleInfo: function (vehicleInfoPanel) {
        vehicleInfoPanel.setVehicleInfo(this.vehicle);
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
