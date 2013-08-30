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
            reviewCard: {
                activate: 'onActivateReviewCard'
            },
            navMain: {
                leafitemtap: 'onSelectAction'
            },
            addButton: {
                tap: 'addAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompanyPage SafeStartNestedListVehicles',
            infoPanel: 'SafeStartCompanyPage panel[name=info-container]',
            vehicleInspectionPanel: 'SafeStartCompanyPage SafeStartVehicleInspection',
            addButton: 'SafeStartCompanyPage SafeStartCompanyToolbar > button[action=add-vehicle]',
            manageChecklistPanel: 'SafeStartCompanyPage > panel[name=info-container] > panel[name=vehicle-manage]',
            reviewCard: 'SafeStartCompanyPage SafeStartVehicleInspection formpanel[name=checklist-card-review]'
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
        }

    }
});