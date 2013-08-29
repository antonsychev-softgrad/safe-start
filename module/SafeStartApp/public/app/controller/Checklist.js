Ext.define('SafeStartApp.controller.Checklist', {
    extend: 'Ext.app.Controller',

    config: {
        control: {
            'SafeStartVehicleInspection formpanel button[action=prev]': {
                tap: 'onPrevBtnTap'
            },
            'SafeStartVehicleInspection formpanel button[action=next]': {
                tap: 'onNextBtnTap'
            },
            'SafeStartVehicleInspection formpanel[name=checklist-card-choise-additional] checkboxfield': {
                change: 'onSelectAdditional'
            },
            reviewCard: {
                activate: 'onActivateReviewCard'
            }
        },

        refs: {
            navMain: 'SafeStartChecklistPage SafeStartNestedListVehicles',
            checklistPage: 'SafeStartChecklistPage',
            checklistPanel: 'SafeStartVehicleInspection',
            reviewCard: 'SafeStartChecklistPage panel[name=user-checklist] formpanel[name=checklist-card-review]'
        }
    },

    onNextBtnTap: function (btn) {
        var checklistPanel = this.getChecklistPanel(),
            activeCard = btn.up('formpanel'),
            includedCards = this.getIncludedChecklistCards(),
            nextIndex = 0,
            index = includedCards.indexOf(activeCard);

        if (index !== -1) {
            nextIndex = index + 1;
        }
        if (includedCards[nextIndex]) {
            checklistPanel.setActiveItem(includedCards[nextIndex]);
        } else {
            console.log('submitAction');
        }
    },

    onPrevBtnTap: function (btn) {
        var checklistPanel = this.getChecklistPanel(),
            activeCard = btn.up('formpanel'),
            includedCards = this.getIncludedChecklistCards(),
            prevIndex = 0,
            index = includedCards.indexOf(activeCard);

        if (index !== -1) {
            prevIndex = index - 1;
        }
        if (includedCards[prevIndex]) {
            checklistPanel.setActiveItem(includedCards[prevIndex]);
        }
    },

    onSelectAdditional: function (checkbox, state) {
        this.getChecklistPanel()
            .down('formpanel{config.groupId === ' + checkbox.config.checklistGroupId + '}')
            .isIncluded = state ? true : false;
        console.log('included');
    },

    getIncludedChecklistCards: function () {
        var query = [
            'formpanel[name=checklist-card]',
            'formpanel[name=checklist-card-choise-additional]',
            'formpanel[name=checklist-card-additional][isIncluded]',
            'formpanel[name=checklist-card-review]'
        ].join(', ');
        return this.getChecklistPanel().query(query);
    },

    onActivateReviewCard: function () {
        console.log('REVIEW');
    },

    onActivateChecklistPanel: function () {
    }

});