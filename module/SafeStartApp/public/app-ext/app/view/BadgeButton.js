Ext.define('SafeStartExt.view.BadgeButton',{
    extend: 'Ext.button.Button',
    alias: 'widget.badgebutton',

    badgeText: '',
    renderTpl : [
    '<span id="{id}-btnWrap" class="{baseCls}-wrap',
        '<tpl if="splitCls"> {splitCls}</tpl>',
        '{childElCls}" unselectable="on">',

        '<span id="{id}-btnBadge" class="{baseCls}-badge hide-badge">',
            '{badgeText}',
        '</span>',

        '<span id="{id}-btnEl" class="{baseCls}-button">',
            '<span id="{id}-btnInnerEl" class="{baseCls}-inner {innerCls}',
                '{childElCls}" unselectable="on">',
                '{text}',
            '</span>',
            '<span role="img" id="{id}-btnIconEl" class="{baseCls}-icon-el {iconCls}',
                '{childElCls} {glyphCls}" unselectable="on" style="',
                '<tpl if="iconUrl">background-image:url({iconUrl});</tpl>',
                '<tpl if="glyph && glyphFontFamily">font-family:{glyphFontFamily};</tpl>">',
                '<tpl if="glyph">&#{glyph};</tpl><tpl if="iconCls || iconUrl">&#160;</tpl>',
            '</span>',
        '</span>',
    '</span>',
    '<tpl if="closable">',
        '<span id="{id}-closeEl" class="{baseCls}-close-btn" title="{closeText}" tabIndex="0"></span>',
    '</tpl>'
    ],
    
    childEls: [
        'btnEl', 'btnWrap', 'btnInnerEl', 'btnIconEl', 'btnBadge'
    ],

    constructor: function( config ){
        this.callParent(arguments);
    },

    setBadgeText:function(text) {
        this.badgeText = text;
        if (this.rendered) {
            if( Ext.isEmpty( text)){
                text = undefined;
                this.btnBadge.addCls('hide-badge');
            } else {
                this.btnBadge.removeCls('hide-badge');
            }
            this.btnBadge.update(text);
        }
        return this;
    }
});
