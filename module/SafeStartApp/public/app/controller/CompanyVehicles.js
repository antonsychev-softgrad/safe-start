Ext.define('SafeStartApp.controller.CompanyVehicles', {
    extend: 'SafeStartApp.controller.DefaultVehicles',

    config: {
        control: {
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=prev]': {
                tap: 'onPrevBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=next]': {
                tap: 'onNextBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-choise-additional] checkboxfield': {
                change: 'onSelectAdditional'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection formpanel button[action=submit]': {
                tap: 'onReviewSubmitBtnTap'
            },
            'SafeStartCompanyPage SafeStartVehicleInspection sheet[cls=sfa-messagebox-confirm] button[action=confirm]': {
                tap: 'onReviewConfirmBtnTap'
            },
            reviewCard: {
                activate: 'onActivateReviewCard'
            },
            navMain: {
                itemtap: 'onSelectAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompanyPage SafeStartNestedListVehicles',
            infoPanel: 'SafeStartCompanyPage panel[name=info-container]',
            vehicleInspectionPanel: 'SafeStartCompanyPage SafeStartVehicleInspection',
            vehicleUsersPanel: 'SafeStartCompanyPage SafeStartVehicleUsersPanel',
            vehicleAlertsPanel: 'SafeStartCompanyPage SafeStartVehicleAlertsPanel',
            vehicleInspectionsPanel: 'SafeStartCompanyPage SafeStartVehicleInspectionsPanel',
            addButton: 'SafeStartCompanyPage SafeStartCompanyToolbar > button[action=add-vehicle]',
            manageChecklistPanel: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage]',
            reviewCard: 'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-review]'
        }
    },

    onSelectAction: function () {
        this.selectedRecord = this.getNavMain().getActiveItem().getStore().getNode();
        this.selectedNodeId = arguments[4].get('id');
        switch (arguments[4].get('action')) {
            case 'info':
                this.getInfoPanel().setActiveItem(0);
                this.showUpdateForm();
                break;
            case 'fill-checklist':
                this.loadChecklist(arguments[4].parentNode.get('id'));
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionPanel());
                break;
            case 'inspections':
                this.getInfoPanel().setActiveItem(this.getVehicleInspectionsPanel());
               // this.showAlerts();
                break;
            case 'alerts':
                this.getInfoPanel().setActiveItem(this.getVehicleAlertsPanel());
             //   this.showAlerts();
                break;
            case 'update-checklist':
                this.getInfoPanel().setActiveItem(3);
                this.showUpdateCheckList();
                break;
            case 'users':
                this.loadUsers(arguments[4].parentNode.get('id'));
                this.getInfoPanel().setActiveItem(this.getVehicleUsersPanel());
                break;
        }
    },

    showUpdateCheckList: function () {
        var self = this;
        if (!this.vehicleChecklistStore) {
            this.vehicleChecklistStore = Ext.create('SafeStartApp.store.VehicleChecklist');
            this.vehicleChecklistStore.getProxy().setExtraParam('vehicleId', this.selectedNodeId);
        } else {
            this.vehicleChecklistStore.getProxy().setExtraParam('vehicleId', this.selectedNodeId);
            this.vehicleChecklistStore.loadData();
        }
        if (!this.checkListTree) {
            this.checkListTree = new SafeStartApp.view.components.UpdateChecklist({checkListStore: this.vehicleChecklistStore});
            this.getInfoPanel().getActiveItem().add(this.checkListTree);
        }
        this.vehicleChecklistStore.addListener('data-load-success', function () {
            if (self.vehicleChecklistStore.getRoot()) self.checkListTree.getTreeList().goToNode(self.vehicleChecklistStore.getRoot());
        }, this);
    },

    loadUsers: function (id) {
        var self = this;
        SafeStartApp.AJAX('vehicle/' + id + '/users', {}, function (result) {
            self.getVehicleUsersPanel().buildList(result, id);
        });
    },

    updateUsersAction: function(value, id, obj) {
        SafeStartApp.AJAX('vehicle/' + id + '/update-users', {value: value}, function (result) {

        });
    }

});