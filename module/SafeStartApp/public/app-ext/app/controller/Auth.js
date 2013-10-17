Ext.define('SafeStartExt.controller.Auth', {
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
            'viewport': {
                userLoggedIn: this.userLoggedIn,
                userLoggedOut: this.userLoggedOut
            }
        });
    },

    userLoggedIn: function () {
        this.getMainPanel().getLayout().setActiveItem(this.getVehiclesPanel());
        var bottomNavPanel = this.getBottomNavPanel();
        bottomNavPanel.down('button[cls~=sfa-bottomnav-button-auth]').hide();
        bottomNavPanel.down('button[cls~=sfa-bottomnav-button-vehicles]').show();
    }, 

    userLoggedOut: function () {
        this.getMainPanel().getLayout().setActiveItem(this.getAuthPanel());
        var bottomNavPanel = this.getBottomNavPanel();
        bottomNavPanel.down('button[cls~=sfa-bottomnav-button-vehicles]').hide();
        bottomNavPanel.down('button[cls~=sfa-bottomnav-button-auth]').show();
    }

});
