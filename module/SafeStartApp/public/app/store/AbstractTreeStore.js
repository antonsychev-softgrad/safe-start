
Ext.define('SafeStartApp.store.AbstractTreeStore', {
    extend: 'Ext.data.TreeStore',
    mixins: ['Ext.mixin.Observable'],

    config: {
        autoLoad: false
    },

    loadData: function() {
        this.load({
            callback: function(records, operation, success) {
                if (operation.getError()) {
                    SafeStartApp.showFailureInfoMsg(Ext.getClass(this).getName() +': '+operation.getError().statusText);
                    this.fireEvent('data-load-failure', this);
                } else {
                    this.fireEvent('data-load-success', this);
                }
            },
            scope: this
        });
    }
});
