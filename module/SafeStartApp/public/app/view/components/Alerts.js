Ext.define('SafeStartApp.view.components.Alerts', {
    extend: 'Ext.List',
    xtype: 'SafeStartAppAlerstList',

    config: {
        title: 'Alerts',
        cls: 'x-contacts',
        variableHeights: true,

        store: 'Contacts',
        itemTpl: [
            '<div class="headshot" style="background-image:url(resources/images/headshots/{headshot});"></div>',
            '{firstName} {lastName}',
            '<span>{title}</span>'
        ].join('')
    }
});