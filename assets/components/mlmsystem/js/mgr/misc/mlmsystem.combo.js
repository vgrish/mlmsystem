Ext.namespace('mlmsystem.combo');

mlmsystem.combo.Browser = function(config) {
    config = config || {};

    if (config.length != 0 && typeof config.openTo !== "undefined") {
        if (!/^\//.test(config.openTo)) {
            config.openTo = '/' + config.openTo;
        }
        if (!/$\//.test(config.openTo)) {
            var tmp = config.openTo.split('/')
            delete tmp[tmp.length - 1];
            tmp = tmp.join('/');
            config.openTo = tmp.substr(1)
        }
    }

    Ext.applyIf(config, {
        width: 300,
        triggerAction: 'all'
    });
    mlmsystem.combo.Browser.superclass.constructor.call(this, config);
    this.config = config;
};
Ext.extend(mlmsystem.combo.Browser, Ext.form.TriggerField, {
    browser: null

    ,
    onTriggerClick: function(btn) {
        if (this.disabled) {
            return false;
        }

        //if (this.browser === null) {
        this.browser = MODx.load({
            xtype: 'modx-browser',
            id: Ext.id(),
            multiple: true,
            source: this.config.source || MODx.config.default_media_source,
            rootVisible: this.config.rootVisible || false,
            allowedFileTypes: this.config.allowedFileTypes || '',
            wctx: this.config.wctx || 'web',
            openTo: this.config.openTo || '',
            rootId: this.config.rootId || '/',
            hideSourceCombo: this.config.hideSourceCombo || false,
            hideFiles: this.config.hideFiles || true,
            listeners: {
                'select': {
                    fn: function(data) {
                        this.setValue(data.fullRelativeUrl);
                        this.fireEvent('select', data);
                    },
                    scope: this
                }
            }
        });
        //}
        this.browser.win.buttons[0].on('disable', function(e) {
            this.enable()
        });
        this.browser.win.tree.on('click', function(n, e) {
            path = this.getPath(n);
            this.setValue(path);
        }, this);
        this.browser.win.tree.on('dblclick', function(n, e) {
            path = this.getPath(n);
            this.setValue(path);
            this.browser.hide()
        }, this);
        this.browser.show(btn);
        return true;
    },
    onDestroy: function() {
        mlmsystem.combo.Browser.superclass.onDestroy.call(this);
    },
    getPath: function(n) {
        if (n.id == '/') {
            return '';
        }
        data = n.attributes;
        path = data.path + '/';

        return path;
    }
});
Ext.reg('mlmsystem-combo-browser', mlmsystem.combo.Browser);


mlmsystem.combo.Search = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear
    });
    mlmsystem.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function() {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function() {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(mlmsystem.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function() {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger ' + this.searchBtnCls
            }, {
                tag: 'div',
                cls: 'x-form-trigger ' + this.clearBtnCls
            }]
        };
    },

    _triggerSearch: function() {
        this.fireEvent('search', this);
    },

    _triggerClear: function() {
        this.fireEvent('clear', this);
    }

});
Ext.reg('mlmsystem-field-search', mlmsystem.combo.Search);


mlmsystem.combo.Client = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'client',
        hiddenName: config.name || 'client',
        displayField: 'username',
        valueField: 'id',
        editable: true,
        fields: ['username', 'id', 'fullname'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/client/getlist',
            combo: true,
            client: config.client || 0,
            addno: config.addno || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{username}</b><br/>{fullname}</span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-client',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Client.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Client, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-client', mlmsystem.combo.Client);


mlmsystem.combo.Chunk = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-chunk-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-chunk-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'chunk',
        hiddenName: config.name || 'chunk',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name', 'description'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/chunk/getlist',
            mode: 'chunks',
            combo: config.combo || true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '<tpl if="description"><br><small>{description}</small></tpl>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-chunk',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Chunk.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Chunk, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-chunk', mlmsystem.combo.Chunk);


mlmsystem.combo.Snippet = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-snippet-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-snippet-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'snippet',
        hiddenName: config.name || 'snippet',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name', 'description'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/snippet/getlist',
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '<tpl if="description"><br><small>{description}</small></tpl>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-snippet',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Snippet.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Snippet, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-snippet', mlmsystem.combo.Snippet);


mlmsystem.combo.Context = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-context-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-context-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'context',
        hiddenName: config.name || 'context',
        displayField: 'name',
        valueField: 'key',
        editable: true,
        fields: ['name', 'key'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: MODx.config.connector_url,
        baseParams: {
            action: 'context/getlist',
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({key})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-context',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Context.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Context, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-context', mlmsystem.combo.Context);


mlmsystem.combo.Status = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-status-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-status-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'status',
        hiddenName: config.name || 'status',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/status/getlist',
            combo: true,
            addall: config.addall || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-status',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Status.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Status, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-status', mlmsystem.combo.Status);


mlmsystem.combo.ClientLeader = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-leader-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-leader-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'leader',
        hiddenName: config.name || 'leader',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['name', 'value'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/leader/getlist',
            combo: true,
            addall: config.addall || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({value})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-client-leader',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ClientLeader.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ClientLeader, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-client-leader', mlmsystem.combo.ClientLeader);


mlmsystem.combo.ClientLevel = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-level-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-client-level-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'level',
        hiddenName: config.name || 'level',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['name', 'value'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/level/getlist',
            combo: true,
            addall: config.addall || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({value})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-client-level',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ClientLevel.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ClientLevel, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-client-level', mlmsystem.combo.ClientLevel);


mlmsystem.combo.TypeChangeBalance = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear?62:31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-balance-change-type-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-balance-change-type-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'change_balance_type',
        hiddenName: config.name || 'change_balance_type',
        allowBlank: config.allowBlank || false,
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['value','name'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/balance/type/getlist',
            combo: true,
            addall: config.addall || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '{name}',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-balance-change-type',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.TypeChangeBalance.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.TypeChangeBalance, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-type-change-balance', mlmsystem.combo.TypeChangeBalance);


mlmsystem.combo.Event = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-event-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-event-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'event',
        hiddenName: config.name || 'event',
        displayField: 'name',
        valueField: 'name',
        editable: true,
        fields: ['name', 'groupname'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/event/getlist',
            combo: true,
            addall: config.addall || 0,
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({groupname})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-event',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.Event.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.Event, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-event', mlmsystem.combo.Event);


mlmsystem.combo.ProfitGroup = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-profit-group-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-profit-group-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'group',
        hiddenName: config.name || 'group',
        displayField: 'name',
        valueField: 'name',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select_group'),
        hideMode: 'offsets',
        allowBlank: true,
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/group/getlist',
            identifier: config.identifier,
            class: config.class,
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-profit-group',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ProfitGroup.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ProfitGroup, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-profit-group', mlmsystem.combo.ProfitGroup);


mlmsystem.combo.ObjectField = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-object-field-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-object-field-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'field',
        hiddenName: config.name || 'field',
        displayField: 'name',
        valueField: 'name',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 15,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        allowBlank: true,
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/object/field/getlist',
            class: config.class,
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-object-field',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ObjectField.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ObjectField, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-object-field', mlmsystem.combo.ObjectField);


mlmsystem.combo.ObjectClass = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-object-class-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-object-class-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'class',
        hiddenName: config.name || 'class',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['name', 'value'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/misc/object/class/getlist',
            combo: true,
            addall: config.addall || 0,
            target: config.target || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({value})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-object-class',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ObjectClass.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ObjectClass, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-object-class', mlmsystem.combo.ObjectClass);


mlmsystem.combo.ModeСhange = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-mode-change-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-mode-change-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'mode',
        hiddenName: config.name || 'mode',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        allowBlank: true,
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/change/mode/getlist',
            addall: config.addall || 0,
            class: config.class,
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-mode-change',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.ModeСhange.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.ModeСhange, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-mode-change', mlmsystem.combo.ModeСhange);


mlmsystem.combo.TypeСhange = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-type-change-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-mlmsystem-type-change-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'type',
        hiddenName: config.name || 'type',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name', 'description'],
        pageSize: 10,
        emptyText: _('mlmsystem_combo_select'),
        hideMode: 'offsets',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/change/type/getlist',
            combo: true,
            addall: config.addall || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '<tpl if="description"><br><small>{description}</small></tpl>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-mlmsystem-type-change',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    mlmsystem.combo.TypeСhange.superclass.constructor.call(this, config);

};
Ext.extend(mlmsystem.combo.TypeСhange, MODx.combo.ComboBox);
Ext.reg('mlmsystem-combo-type-change', mlmsystem.combo.TypeСhange);