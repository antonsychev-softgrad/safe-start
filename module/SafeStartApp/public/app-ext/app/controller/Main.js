Ext.define('SafeStartExt.controller.Main', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport > SafeStartExtBottomNav',
        ref: 'bottomNavPanel'
    }, {
        selector: 'viewport > SafeStartExtMain',
        ref: 'mainPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentAuth',
        ref: 'authPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentVehicles',
        ref: 'vehiclesPanel'
    }, {
        selector: 'viewport > SafeStartExtMain > SafeStartExtComponentContact',
        ref: 'contactPanel'
    }],

    init: function () {
        this.control({
            'SafeStartExtBottomNav': {
                showAuth: this.showAuth,
                showVehicles: this.showVehicles,
                showContact: this.showContact
            }
        });   
    },

    showAuth: function () {
        this.getMainPanel().getLayout().setActiveItem(this.getAuthPanel());
    },

    showVehicles: function () {
        this.getMainPanel().getLayout().setActiveItem(this.getVehiclesPanel());
    },

    showContact: function () {
        this.getMainPanel().getLayout().setActiveItem(this.getContactPanel());
    }

});
