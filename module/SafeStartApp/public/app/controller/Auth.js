Ext.define('SafeStartApp.controller.Auth', {
    extend: 'Ext.app.Controller',

    config: {
        control: {
            loginButton: {
                tap: 'doLogin'
            }
        },

        refs: {
            loginButton: 'button[action=login]'
        }

    },

    doLogin: function() {

    }

});
