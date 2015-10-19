mlmsystem.grid.Client = function (config) {
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
                            action: config.action || 'mgr/client/sort',
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
        id: 'mlmsystem-grid-client',
        url: mlmsystem.config.connector_url,
        baseParams: {
            action: 'mgr/client/getlist'
        },
        save_action: 'mgr/client/updatefromgrid',
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
        stateId: 'mlmsystem-grid-client-state'

    });
    mlmsystem.grid.Client.superclass.constructor.call(this, config);
    this.getStore().sortInfo = {
        field: 'id',
        direction: 'DESC'
    };
};
Ext.extend(mlmsystem.grid.Client, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = mlmsystem.config.client_grid_fields;

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
            },'-'/*, {
                text: '<i class="fa fa-toggle-on green"></i> ' + _('mlmsystem_action_active'),
                cls: 'mlmsystem-cogs',
                handler: this.inactiveDisabled,
                scope: this
            }, {
                text: '<i class="fa fa-toggle-off red"></i> ' + _('mlmsystem_action_inactive'),
                cls: 'mlmsystem-cogs',
                handler: this.activeDisabled,
                scope: this
            }*/]
        });
        tbar.push({
            text: '<i class="fa fa-refresh"></i>',
            handler: this.updateClient,
            scope: this
        });

        tbar.push('->');
        tbar.push({
            xtype: 'mlmsystem-combo-status',
            width: 210,
            custm: true,
            clear: true,
            addall: true,
            class: config.class,
            value: 0,
            listeners: {
                select: {
                    fn: this._filterByCombo,
                    scope: this
                }
            }
        });

        tbar.push({
            xtype: 'mlmsystem-combo-client-leader',
            width: 210,
            custm: true,
            clear: true,
            addall: true,
            class: config.class,
            value: '-',
            listeners: {
                select: {
                    fn: this._filterByCombo,
                    scope: this
                }
            }
        });

        if (1 != MODx.config.mlmsystem_client_field_search_disable) {
            tbar.push({
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
            });
        }

        return tbar;
    },

    getColumns: function (config) {
        var columns = [this.exp, this.sm];
        var add = {
            id: {
                width: 15,
                sortable: true
            },
            username: {
                width: 50,
                sortable: true,
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.userLink(value, record['data']['id'])
                }
            },
            email: {
                width: 40,
                sortable: true,
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.userLink(value, record['data']['id'])
                }
            },
            status: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'mlmsystem-combo-status',
                    custm: true,
                    clear: true,
                    class: config.class,
                    allowBlank: false
                },
                renderer: function (value, metaData, record) {
                    return mlmsystem.utils.renderReplace(value, record['json']['status_name'],record['json']['status_color'])
                }
            },
            balance: {
                width: 15,
                sortable: true,
                renderer: function(value, metaData, record){
                    return mlmsystem.utils.renderMoney(value);
                }
            },
            incoming: {
                width: 15,
                sortable: true,
                renderer: function(value, metaData, record){
                    return mlmsystem.utils.renderMoney(value);
                }
            },
            outcoming: {
                width: 15,
                sortable: true,
                renderer: function(value, metaData, record){
                    return mlmsystem.utils.renderMoney(value);
                }
            },
            createdon: {
                width: 25,
                sortable: true,
                renderer: mlmsystem.utils.formatDate
            },
            updatedon: {
                width: 25,
                sortable: true,
                renderer: mlmsystem.utils.formatDate
            },
            actions: {
                width: 30,
                sortable: false,
                renderer: mlmsystem.utils.renderActions,
                id: 'actions'
            }
        };

        for (var i = 0; i < mlmsystem.config.client_grid_fields.length; i++) {
            var field = mlmsystem.config.client_grid_fields[i];
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
                action: 'mgr/client/multiple',
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

    updateClient: function (btn, e) {
        this.setAction('updateclients', 'false', 0);
    },

    create: function (btn, e) {
        var record = {
            active: 1,
            status: mlmsystem.config.client_status[1] || 0
        };
        var w = MODx.load({
            xtype: 'mlmsystem-client-window-create',
            record: record,
            class: this.config.class,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    update: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }

    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
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
        Ext.getCmp('mlmsystem-grid-client').refresh();
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
Ext.reg('mlmsystem-grid-client', mlmsystem.grid.Client);
