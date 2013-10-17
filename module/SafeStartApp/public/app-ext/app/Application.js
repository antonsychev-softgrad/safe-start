Ext.define('SafeStartExt.Application', {
    name: 'SafeStartExt',

    extend: 'Ext.app.Application',

    controllers: [
        'Main',
        'Auth',
        'Contact',
        'Vehicles'
    ],

    launch: function () {
    }
});
