Ext.define('SafeStartApp.controller.UserVehicles', {
    extend: 'SafeStartApp.controller.DefaultVehicles',

    config: {
        control: {
            'SafeStartVehiclesPage SafeStartVehicleInspection formpanel button[action=prev]': {
                tap: 'onPrevBtnTap'
            },
            'SafeStartVehiclesPage SafeStartVehicleInspection formpanel button[action=next]': {
                tap: 'onNextBtnTap'
            },
            'SafeStartVehiclesPage SafeStartVehicleInspection formpanel[name=checklist-card-choise-additional] checkboxfield': {
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
            navMain: 'SafeStartVehiclesPage SafeStartNestedListVehicles',
            infoPanel: 'SafeStartVehiclesPage panel[name=info-container]',
            vehicleInspectionPanel: 'SafeStartVehiclesPage SafeStartVehicleInspection',
            addButton: 'SafeStartVehiclesToolbar > button[action=add-vehicle]',
            manageChecklistPanel: 'SafeStartVehiclesPage > panel[name=info-container] > panel[name=vehicle-manage]',
            reviewCard: 'SafeStartVehiclesPage SafeStartVehicleInspection formpanel[name=checklist-card-review]'
        }
    },

    saveAction: function () {
        if (!this.vehicleModel) this.vehicleModel = Ext.create('SafeStartApp.model.Vehicle');
        if (this.validateFormByModel(this.vehicleModel, this.currentForm)) {
            var self = this;
            var formValues = this.currentForm.getValues();
            formValues.companyId = SafeStartApp.userModel.get('companyId');
            SafeStartApp.AJAX('vehicle/' + this.currentForm.getValues().id + '/update', formValues, function (result) {
                if (result.vehicleId) {
                    self._reloadStore(result.vehicleId);
                    self.currentForm.down('button[name=delete-data]').show();
                    self.currentForm.down('button[name=reset-data]').hide();
                }
            });
        }
    },
});