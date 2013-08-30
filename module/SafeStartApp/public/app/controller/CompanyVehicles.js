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
        }
    }
});