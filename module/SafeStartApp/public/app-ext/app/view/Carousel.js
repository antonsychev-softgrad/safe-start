Ext.define('SafeStartExt.view.Carousel', {
    extend: 'Ext.container.Container',
    requires: ['SafeStartExt.view.CarouselTb'],
    alias: 'widget.carousel',
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    defaults: {
        flex: 1
    },
    style: {
        background: 'url(http://3.bp.blogspot.com/-kanvyoXSOSs/Tsi0W496bzI/AAAAAAAAAG8/-Bq53wJqaqM/s320/carbonfibre.png)'
    },
    initComponent: function() {
        var me = this;

        me.addEvents('carouselchange');

        me.callParent(arguments);
    },
    onDocMouseup: function() {
        var me = this;
        me.drag = false;
        var children = me.items.items;
        var parentLeft = me.ownerCt.el.getLeft();
        var rule = 1000000;
        var target;
        Ext.each(children, function(div, l) {
            l = Math.abs(div.el.getLeft() - parentLeft);
            if (l < rule) {
                rule = l;
                target = div;
            }
        });
        me.showChild(target);
    },
    onMousedown: function(e) {
        e.stopEvent(); // prevents selecting the underlying text and whatnot
        var me = this;
        me.drag = true;
        me.startX = e.getX();
        var par = me.el.first();
        par.on({
            mousemove: function(e) {
                e.stopEvent(); // prevents selecting the underlying text and whatnot
                if (me.drag) {
                    var rate = 1;
                    if (par.getLeft() > me.ownerCt.el.getLeft() || par.getRight() < me.ownerCt.el.getRight()) {
                        rate = 2;
                    }
                    par.move('l', (me.startX - e.getX()) / rate, false);
                    me.startX = e.getX();
                }
            }
        });
    },
    syncSizeToOwner: function() {
        var me = this;
        if (me.ownerCt) {
            me.setSize(me.ownerCt.el.getWidth() * me.items.items.length, me.ownerCt.el.getHeight());
        }
    },
    showChild: function(item) {
        var me = this,
            left = item.el.getLeft() - me.el.getLeft();
        me.el.first().move('l', left, true);
        me.currentItem = item;
        me.fireEvent('carouselchange', me, item);
    },
    nextChild: function() {
        var me = this;
        var next = me.currentItem.nextSibling();
        me.showChild(next || me.items.items[0]);
    },
    previousChild: function() {
        var me = this;
        var next = me.currentItem.previousSibling();
        me.showChild(next || me.items.items[me.items.items.length - 1]);
    },
    onRender: function() {
        var me = this;

        me.currentItem = me.items.items[0];

        if (me.ownerCt) {
            me.relayEvents(me.ownerCt, ['resize'], 'owner');
            me.on({
                ownerresize: me.syncSizeToOwner
            });
        }

        me.mon(Ext.getBody(), 'mouseup', me.onDocMouseup, me);
        me.mon(Ext.fly(me.el.dom), 'mousedown', me.onMousedown, me);


        me.callParent(arguments);
    }
});

