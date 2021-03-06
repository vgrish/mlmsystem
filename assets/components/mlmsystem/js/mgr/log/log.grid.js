mlmsystem.grid.Log = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description}</p>'),
        renderer: function(v, p, record) {
            return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
        }
    });

    this.dd = function(grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function(dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: mlmsystem.config.connector_url,
                        params: {
                            action: config.action || 'mgr/log/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function(r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function(r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        id: 'mlmsystem-grid-log',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/log/getlist',
            class: config.class || '',
            identifier: config.identifier || ''
        },
        save_action: 'mgr/log/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: this.exp,
        /*ddGroup: 'dd',
        enableDragDrop: true,*/

        autoHeight: true,
        paging: true,
        pageSize: 10,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        cls: 'mlmsystem-grid',
        bodyCssClass: 'grid-with-buttons',
        stateful: true,
        stateId: 'mlmsystem-grid-log-state'

    });
    mlmsystem.grid.Log.superclass.constructor.call(this, config);
 /*   this.getStore().sortInfo = {
        field: 'id',
        direction: 'DESC'
    };*/
};
Ext.extend(mlmsystem.grid.Log, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = mlmsystem.config.log_grid_fields;

        return fields;
    },

    getTopBarComponent: function(config) {
        var component = ['menu', 'update', 'left', 'object_class' ,'type_change', 'search'];
        if (!!config.compact) {
            component = ['menu', 'update', 'left', 'type_change', 'spacer'];
        }

        return component;
    },

    getTopBar: function(config) {
        var tbar = [];
        var add = {
            menu: {
                text: '<i class="fa fa-cogs"></i> ',
                menu: [{
                    text: '<i class="fa fa-trash-o red"></i> ' + _('mlmsystem_action_remove'),
                    cls: 'mlmsystem-cogs',
                    handler: this.remove,
                    scope: this
                }]
            },
            update: {
                 text: '<i class="fa fa-refresh"></i>',
                 handler: this._updateRow,
                 scope: this
            },
            left: '->',
            object_class: {
                xtype: 'mlmsystem-combo-object-class',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: 0,
                target: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },
            type_change: {
                xtype: 'mlmsystem-combo-type-change',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: 0,
                class: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },
            mode_change: {
                xtype: 'mlmsystem-combo-mode-change',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: 0,
                class: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },
            search: {
                xtype: 'mlmsystem-field-search',
                width: 210,
                listeners: {
                    search: {
                        fn: function (field) {
                            this._doSearch(field);
                        },
                        scope: this
                    },
                    clear: {
                        fn: function (field) {
                            field.setValue('');
                            this._clearSearch();
                        },
                        scope: this
                    }
                }
            },
            spacer: {
                xtype: 'spacer',
                style: 'width:1px;'
            }
        };

        var cmp = this.getTopBarComponent(config);
        for (var i = 0; i < cmp.length; i++) {
            var item = cmp[i];
            if (add[item]) {
                tbar.push(add[item]);
            }
        }

        return tbar;
    },

    getColumns: function(config) {
        var columns = [this.exp, this.sm];
        var add = {
            id: {
                width: 5,
                sortable: true
            },
            object: {
                width: 20,
                sortable: true,
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.objectLink(_('mlmsystem_class_' + value), record['data']['identifier'], value);
                }
            },
            name: {
                width: 20,
                sortable: true
            },
            username_action: {
                width: 20,
                sortable: true,
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.userLink(value, record['data']['user'])
                }
            },
            target: {
                width: 15,
                sortable: true
            },
            value: {
                width: 15,
                sortable: true
            },

            timestamp: {
                width: 25,
                sortable: true,
                renderer: mlmsystem.utils.formatDate

            },
            actions: {
                width: 15,
                sortable: false,
                renderer: mlmsystem.utils.renderActions,
                id: 'actions'
            }
        };
        for (var field in add) {
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('mlmsystem_header_' + field),
                    tooltip: _('mlmsystem_tooltip_' + field),
                    dataIndex: field
                });
                if (!!config.compact && (field == 'object')) {
                    add[field]['hidden'] = true;
                }
                columns.push(add[field]);
            }
        }

        return columns;
    },

    getListeners: function(config) {
        return {
            render: {
                fn: this.dd,
                scope: this
            },
            afterrender: function(grid) {
                var params = mlmsystem.utils.Hash.get();
                if (!!params['logs']) {
                    this.update(grid, Ext.EventObject, {data: {id: params['logs']}});
                }
            }
        };
    },

    getMenu: function(grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = mlmsystem.utils.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },

    onClick: function(e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    setAction: function(method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/log/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function() {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function(response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    remove: function() {
        Ext.MessageBox.confirm(
            _('mlmsystem_action_remove'),
            _('mlmsystem_confirm_remove'),
            function(val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    update: function(btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/log/get',
                id: id,
                process: true,
                aliases: Ext.util.JSON.encode(['ActionUser', 'ActionUserProfile', 'ActionClient', 'Type'])
            },
            listeners: {
                success: {
                    fn: function(r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'mlmsystem-log-window-view',
                            record: record,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
                                },
                                afterrender: function() {
                                    mlmsystem.utils.Hash.add('logs', r.object['id']);
                                },
                                hide: function() {
                                    mlmsystem.utils.Hash.remove('logs');
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
                    },
                    scope: this
                }
            }
        });
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

    _doSearch: function(tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function() {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _updateRow: function(response) {
        this.refresh();
    },

    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('mlmsystem-grid-log', mlmsystem.grid.Log);
