// JavaScript Document.
M.block_learning_plan = {};
M.block_learning_plan.init = function(Y, param1, param2) {
// Load Message on Message Body.
// End Load Message.
    var lp_id = Y.one('#id_l_id');
    var lp_id_value = lp_id.get('value');
    if (param1 == '6' && param2 != '1') {
        var u_id = Y.one('#id_u_id');
        var id_u_id_value = u_id.get('value');
        // If page refresh  users & thier training will be selected on the base of learning plan.
        // Users change.
        Y.io('ajax_bridge.php?id=' + lp_id_value, {
            on: {
                start: function(id, args) {
                },
                complete: function(id, e) {
                    var u_id = Y.one('#id_u_id');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    for (i = 0; i < test.length - 1; i++) {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    u_id.set('innerHTML', asd);
                    // Trainig Change.
                    id_u_id_value = u_id.get('value');
                    Y.io('ajax_bridge.php?id=' + lp_id_value + '&u_id=' + id_u_id_value, {
                        on: {
                            start: function(id, args) {
                            },
                            complete: function(id, e) {
                                var t_id = Y.one('#id_t_id');
                                var json = e.responseText;
                                console.log(json);
                                var test = json.split("^");
                                var asd = "";
                                for (i = 0; i < test.length - 1; i++) {
                                    var sep = test[i].split("~");
                                    asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                                }
                                t_id.set('innerHTML', asd);
                            }
                        }
                    });
                }
            }
        });
// Learning path change event.
        lp_id.on('change', function() {
            lp_id_value = lp_id.get('value');
            Y.io('ajax_bridge.php?id=' + lp_id_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var u_id = Y.one('#id_u_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        u_id.set('innerHTML', asd);
                        // Training Change.
                        id_u_id_value = u_id.get('value');
                        Y.io('ajax_bridge.php?id=' + lp_id_value + '&u_id=' + id_u_id_value, {
                            on: {
                                start: function(id, args) {
                                },
                                complete: function(id, e) {
                                    var t_id = Y.one('#id_t_id');
                                    var json = e.responseText;
                                    console.log(json);
                                    var test = json.split("^");
                                    var asd = "";
                                    for (i = 0; i < test.length - 1; i++) {
                                        var sep = test[i].split("~");
                                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                                    }
                                    t_id.set('innerHTML', asd);
                                }
                            }
                        });
                    }
                }
            });
        });

        u_id.on('change', function() {
            // Training Change.
            id_u_id_value = u_id.get('value');
            lp_id_value = lp_id.get('value');
            Y.io('ajax_bridge.php?id=' + lp_id_value + '&u_id=' + id_u_id_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_t_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });
        });
    } else if (param1 == '7') {
        // Change training on the base of lp_id.
        lp_id_value = lp_id.get('value');
        Y.io('ajax_bridge.php?id=' + lp_id_value + '&t=1', {
            on: {
                start: function(id, args) {
                },
                complete: function(id, e) {
                    var t_id = Y.one('#id_t_id');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    for (i = 0; i < test.length - 1; i++) {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    t_id.set('innerHTML', asd);
                }
            }
        });
        // On change learning plan.
        lp_id.on('change', function() {
            lp_id_value = lp_id.get('value');
            Y.io('ajax_bridge.php?id=' + lp_id_value + '&t=1', {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_t_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });
        });
        // Show user button click.
        var showuser = Y.one('#btnajax');
        showuser.on('click', function() {
            var lp_id = Y.one("#id_l_id").get('value');
            var t_id = Y.one("#id_t_id").get('value');
            var status = Y.one("#id_status").get('value');
            var userlist = Y.one("#statuslist");
            // alert(showuser_empty_field.get('id'));
            Y.io('ajax_bridge.php?id=' + lp_id + '&t_id=' + t_id + '&status=' + status, {
                on: {
                    start: function(id, args) {
                        //userlist.set('innerHTML','<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                    },
                    complete: function(id, e) {
                        var json = e.responseText;
                        console.log(json);
                        userlist.set('innerHTML', json);
                    }
                }
            });
        });
    } else if (param1 == '4')
    {
        var training_type = Y.one('#id_training_type');
        training_type_value = training_type.get('value');
        lp_id_value = lp_id.get('value');
        Y.io('ajax_bridge.php?id=' + lp_id_value + '&hidetraining=1&t_type_id=' + training_type_value, {
            on: {
                start: function(id, args) {
                },
                complete: function(id, e) {
                    var t_id = Y.one('#id_t_id');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    for (i = 0; i < test.length - 1; i++) {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    t_id.set('innerHTML', asd);
                }
            }
        });

        // On change learning plan event.
        lp_id.on('change', function() {
            lp_id_value = lp_id.get('value');

            training_type_value = training_type.get('value');

            Y.io('ajax_bridge.php?id=' + lp_id_value + '&hidetraining=1&t_type_id=' + training_type_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_t_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });
        });

        // On change training type.
        training_type.on('change', function() {
            lp_id_value = lp_id.get('value');
            training_type_value = training_type.get('value');
            Y.io('ajax_bridge.php?id=' + lp_id_value + '&hidetraining=1&t_type_id=' + training_type_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_t_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });
        });
    } else if (param1 == '5') {
        var id_g_id = Y.one('#id_g_id');
        var id_g_selection = Y.one('#id_g_selection');
        id_g_selection_value = id_g_selection.get('value');



        lp_id_value = lp_id.get('value');
        Y.io('ajax_bridge.php?id=' + lp_id_value + '&hideusers=1', {
            on: {
                start: function(id, args) {
                },
                complete: function(id, e) {
                    var u_id = Y.one('#id_u_id');
                    var json = e.responseText;
                    console.log(json);
                    var test = json.split("^");
                    var asd = "";
                    for (i = 0; i < test.length - 1; i++) {
                        var sep = test[i].split("~");
                        asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                    }
                    u_id.set('innerHTML', asd);
                }
            }
        });
        // On Group change.
        id_g_id.on('change', function() {
            lp_id_value = lp_id.get('value');

            id_g_selection_value = id_g_selection.get('value');
            if (id_g_selection_value == 2) {
                id_g_id_value = id_g_id.get('value');
            }
            else {
                id_g_id_value = 0;
            }
            Y.io('ajax_bridge.php?id=' + lp_id_value + '&hideusers=1&g_id=' + id_g_id_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var u_id = Y.one('#id_u_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';

                        }
                        u_id.set('innerHTML', asd);
                    }
                }
            });
        });


        // On Change selection.
        id_g_selection.on('change', function() {
            id_g_selection_value = id_g_selection.get('value');
            lp_id_value = lp_id.get('value');
            if (id_g_selection_value == 2) {
                id_g_id_value = id_g_id.get('value');
            }
            else {
                id_g_id_value = 0;
            }
            Y.io('ajax_bridge.php?id=' + lp_id_value + '&hideusers=1&g_id=' + id_g_id_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var u_id = Y.one('#id_u_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';

                        }
                        u_id.set('innerHTML', asd);
                    }
                }
            });
        });
        // On change learning plan event.
        lp_id.on('change', function() {
            lp_id_value = lp_id.get('value');
            id_g_selection_value = id_g_selection.get('value');
            if (id_g_selection_value == 2) {
                id_g_id_value = id_g_id.get('value');
            }
            else {
                id_g_id_value = 0;
            }

            Y.io('ajax_bridge.php?id=' + lp_id_value + '&hideusers=1&g_id=' + id_g_id_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var u_id = Y.one('#id_u_id');
                        var json = e.responseText;
                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';

                        }
                        u_id.set('innerHTML', asd);
                    }
                }
            });
        });
    }
}