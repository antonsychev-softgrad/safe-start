
Ext.define('SafeStartApp.store.AbstractTreeStore', {
    extend: 'Ext.data.Store',
    mixins: ['Ext.mixin.Observable'],

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
