mlmsystem.grid.TypeСhange = function (config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description}</p>'),
        renderer: function (v, p, record) {
            return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
        }
    });

    this.dd = function (grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function (dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: mlmsystem.config.connector_url,
                        params: {
                            action: config.action || 'mgr/change/type/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function (r) {
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
        id: 'mlmsystem-grid-type-change',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/change/type/getlist',
            class: config.class || ''
        },
        save_action: 'mgr/change/type/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: this.exp,
        ddGroup: 'dd',
        enableDragDrop: true,

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
        stateId: 'mlmsystem-grid-type-change-state'

    });
    mlmsystem.grid.TypeСhange.superclass.constructor.call(this, config);
    this.getStore().sortInfo = {
        field: 'rank',
        direction: 'ASC'
    };
};
Ext.extend(mlmsystem.grid.TypeСhange, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = ['id', 'name', 'class', 'description', 'field', 'mode', 'mode_name', 'rank', 'active', 'editable', 'actions'];

        return fields;
    },

    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="fa fa-cogs"></i> ', // + _('mlmsystem_actions'),
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('mlmsystem_action_create'),
                cls: 'mlmsystem-cogs',
                handler: this.create,
                scope: this
            }, {
                text: '<i class="fa fa-trash-o red"></i> ' + _('mlmsystem_action_remove'),
                cls: 'mlmsystem-cogs',
                handler: this.remove,
                scope: this
            }, '-', {
                text: '<i class="fa fa-toggle-on green"></i> ' + _('mlmsystem_action_active'),
                cls: 'mlmsystem-cogs',
                handler: this.active,
                scope: this
            }, {
                text: '<i class="fa fa-toggle-off red"></i> ' + _('mlmsystem_action_inactive'),
                cls: 'mlmsystem-cogs',
                handler: this.inactive,
                scope: this
            }]
        });
        tbar.push('->');
        tbar.push({
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
        });

        return tbar;
    },

    getColumns: function (config) {
        var columns = [this.exp, this.sm];
        var add = {
            id: {
                width: 15,
                sortable: true
            },
            name: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'textfield'
                }
            },
            field: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'mlmsystem-combo-object-field',
                    custm: true,
                    clear: true,
                    class: config.class,
                    allowBlank: false
                }
            },
            mode: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'mlmsystem-combo-mode-change',
                    custm: true,
                    clear: true,
                    class: config.class,
                    allowBlank: false
                },
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.renderReplace(value, record['json']['mode_name'])
                }
            },
            actions: {
                width: 25,
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
                columns.push(add[field]);
            }
        }

        return columns;
    },

    getListeners: function (config) {
        return {
            render: {
                fn: this.dd,
                scope: this
            }
        };
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = mlmsystem.utils.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
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

    setAction: function (method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/change/type/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    active: function (btn, e) {
        this.setAction('setproperty', 'active', 1);
    },

    inactive: function (btn, e) {
        this.setAction('setproperty', 'active', 0);
    },

    remove: function () {
        Ext.MessageBox.confirm(
            _('mlmsystem_action_remove'),
            _('mlmsystem_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    update: function (btn, e, row) {
        var record = typeof(row) != 'undefined' ? row.data : this.menu.record;
        MODx.Ajax.request({
            url: mlmsystem.config.connector_url,
            params: {
                action: 'mgr/change/type/get',
                id: record.id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'mlmsystem-type-change-window-create',
                            title: _('mlmsystem_action_update'),
                            action: 'mgr/change/type/update',
                            record: record,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
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

    create: function (btn, e) {
        var record = {
            active: 1,
            class: this.config.class
        };

        w = MODx.load({
            xtype: 'mlmsystem-type-change-window-create',
            record: record,
            listeners: {
                success: {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _updateRow: function (response) {
        this.refresh();
    },

    _getSelectedIds: function () {
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
Ext.reg('mlmsystem-grid-type-change', mlmsystem.grid.TypeСhange);
