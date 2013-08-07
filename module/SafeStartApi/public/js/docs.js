$(document).ready(function(){
    jsonParser.init({
        url: 'docs.json'
    });
});

var JSONParser = function(){
    var opt = {
        url: '',
        targetWrap: '#json-wrap',
        $targetWrap: null,
        specialKey: 'properties',
        specialValue: ['object'],
        tabLevel: 4, // TODO: resolve bug!!!

        tpl: {
            title:          '<h1><b><%= label %>:</b> <%= text %></h1>',
            subTitle:       '<h2><b><%= label %>:</b> <%= text %></h2>',
            description:    '<p><b><%= label %>:</b> <%=  text %></p>',
            text:           '<div><b><%= label %>:</b> <%= text %></div>',

            tabWrap:         '<div id="tab-wrap-<%= tabId %>">',
            tab:        '<ul  id="tab-<%= tabId %>" class="nav nav-tabs">',
            tabItem:            '<li><a href="#tab-item-<%= tabId %>-<%= itemId %>" data-toggle="tab"><%= text %></a></li>',
            tabWrapContent: '<div id="tab-content-<%= tabId %>" class="tab-content"></div>',
            tabItemContent:     '<div id="tab-item-<%= tabId %>-<%= itemId %>" class="tab-pane"><%= text %></div>',

            tabTree:            '<ul id="tab-<%= tabId %>"  class="accordion">',
            tabItemTree:            '<li id="tab-item-<%= tabId %>-<%= itemId %>"  class="accordion-group">' +
                                        '<div class="accordion-heading">' +
                                            '<a href="#tab-item-collapse-<%= tabId %>-<%= itemId %>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2"><%= text %></a>' +
                                        '</div>' +
                                    '</li>',
            tabWrapContentTree: '<div id="tab-item-collapse-<%= tabId %>-<%= itemId %>" class="accordion-body collapse">' +
                                    '<div class="accordion-inner">' +
                                     // content
                                    '</div>' +
                                '</div>'
        },
        tabId: 0
    };

    this.init = function(settings){
        if (typeof settings === 'object') {
            $.extend(opt, settings)
        }
        opt.$targetWrap = $(opt.targetWrap);

        this.render( this.getJSON(opt.url) );
    };
    this.getJSON = function(url){
        url  = '/schemas/' + url;
        var JSON;
        $.ajax({
            dataType: "json",
            url: url,
            async: false, // synchronous loading of data
            data: {},
            success: function(data){
                JSON = data
            },
            error: function(){
                JSON = false
            }
        });
        return JSON;
    };

    // !!! recursive call from renderTab() and renderTree()
    this.render = function(json, $wrap){
        if ($('#tab-wrap-' + opt.tabId).length) { // проверка на существование первого врапера
            opt.tabId++;
            $wrap.append( _.template(opt.tpl.tabWrap, {tabId: opt.tabId}) ); // во врапер с атрибута вставляем div  c котррым быдем работаь
        } else {
            opt.$targetWrap.append( _.template(opt.tpl.tabWrap, {tabId: opt.tabId}) ); // первый уровень вложенности создаётся только один раз
        }
        var $wrap = $('#tab-wrap-' + opt.tabId);

        this.renderText(json, $wrap); // render text

        // выбор способа рендеринга в зависимости от указанного уровн вложенности горизонтальных табов
        if ( $wrap.parents('[id^="tab-wrap-"]').length < opt.tabLevel ) {
            this.renderTab(json, $wrap, opt.tabId);
        } else {
            this.renderTree(json, $wrap, opt.tabId);
        }
    };
    this.renderText = function(json, $wrap){
        // редер текстовых полей. если находит ключ $ref - тогда пытаемся загрузить синхронно по адресу данные JSON
        // в зависимости от ключа можно использовать отдельные tpl, по умолчанию просто работает tpl.text
        var self = this;
        for (var key in json) {
            if (typeof json[key] === "string" || typeof json[key] === "number" ) {
                switch (key) {
                    case 'name': $wrap.append(_.template(opt.tpl.title, {label: key, text: json[key]}) );
                        break;
                    case 'description': $wrap.append(_.template(opt.tpl.description, {label: key, text: json[key]}) );
                        break;
                    case '$ref':
                        var obj = this.getJSON(json[key]);
                        if ( !obj ) {
                            $wrap.append(_.template(opt.tpl.text, {label: key, text: 'ERROR LOAD: ' + json[key]}) );
                        } else {
                            this.render( this.getJSON( json[key]), $wrap );
                        }
                        break;
                    default :
                        var check = true;
                        for (var i = 0;  i < opt.specialValue.length; i++) {
                            if ( opt.specialValue[i] === json[key] ) {
                                check = false;
                            }
                        }
                        if (check) $wrap.append(_.template(opt.tpl.text, {label: key, text: json[key]}) );
                        break;
                }
            }
        }
    };
    this.renderTab = function(json, $wrap, tabId){ // рендеринг табов
        var self = this;
        var itemId = 0;
        for (var key in json) {
            if (typeof json[key] === "object" ) {

                // если указан специальный ключ, тогда таб для него не создаётся, а запускается рендеринг его содержимого
                if (key === opt.specialKey) {
                    self.render(json[key], $wrap, tabId );
                    return;
                }

                // добавляем врамер UL для списка табов
                if ( !$wrap.find('#tab-' + tabId).length) {
                    $wrap.append( _.template(opt.tpl.tab, {tabId: tabId}) )
                }
                // вставка пункта таба LI
                $('#tab-' + tabId).append(_.template(opt.tpl.tabItem, {tabId: tabId, itemId: itemId, text: key}) );
                //вставка врапера для контента таба, лежит рядом с UL
                if ( !$wrap.find('#tab-content-' + tabId).length) {
                    $wrap.append( _.template(opt.tpl.tabWrapContent, {tabId: tabId}) )
                }
                $('#tab-content-' + tabId).append(_.template(opt.tpl.tabItemContent, {tabId: tabId, itemId: itemId, text: ''}) );
                // рекурсивный вызов рендеринга сонтента таба
                self.render(json[key], $('#tab-item-' + tabId + '-' + itemId) );

                itemId++;
            }
        }
    };
    this.renderTree = function(json, $wrap, tabId){
        var self = this;
        var itemId = 0;
        for (var key in json) {
            if (typeof json[key] === "object" ) {
                // если указан специальный ключ, тогда таб для него не создаётся, а запускается рендеринг его содержимого
                if (key === opt.specialKey) {
                    self.render(json[key], $wrap, tabId );
                    return;
                }
                // добавляем врамер UL для списка табов
                if ( !$wrap.find('#tab-' + tabId).length) {
                    $wrap.append( _.template(opt.tpl.tabTree, {tabId: tabId}) )
                }
                // вставка пункта таба LI
                $('#tab-' + tabId).append(_.template(opt.tpl.tabItemTree, {tabId: tabId, itemId: itemId, text: key}) );
                // добавление врапера для контента. лежит внутри таба LI
                $('#tab-item-' + tabId + '-' + itemId).append( _.template( opt.tpl.tabWrapContentTree, {tabId: tabId, itemId: itemId} ) );
                // рекурсивный вызов рендеринга сонтента таба
                self.render(json[key], $('#tab-item-collapse-' + tabId + '-' + itemId) );

                itemId++;
            }
        }
    };


};

var jsonParser = new JSONParser();
