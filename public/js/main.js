var currentCustomer = [], currentCall = [];
var currentCallId , currentCustomerId;

$(function () {

    var contentEl = $('#content');

    routie('/customer/add', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#customerAddLink').addClass('active');


        contentEl.empty();
        $.tmpl(customerAddFormTemplate, {}).appendTo(contentEl);

    });

    routie('/customer/update/:id', function (customerId) {

        $('#navbar').find('li.active').removeClass('active');
        $('#callUpdateLink').addClass('active');

        contentEl.empty();

        if (!currentCustomer[customerId]) {
            routie.navigate('/customer/list');
            return;
        }

        $.tmpl(customerAddFormTemplate, currentCustomer[customerId]).appendTo(contentEl);
        currentCustomerId = customerId;

        $('#status').val(currentCustomer[customerId].status);

        $('#addContent').attr('id', 'updateCustomer');
    });

    routie('/customer/list', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#customersListLink').addClass('active');

        contentEl.empty();

        $.tmpl(customerListTemplate, {}).appendTo(contentEl);

        //init jqGrid
        $("#customers").jqGrid({
            url: '/api/customers',
            datatype: "json",
            colNames: ['Customer Nr', 'First Name', 'Last Name', 'Phone', 'Status', 'Edit'],
            colModel: [
                {name: 'id', index: 'id', width: 75},
                {name: 'first_name', index: 'first_name', width: 100},
                {name: 'last_name', index: 'last_name', width: 100},
                {name: 'phone', index: 'phone', width: 120, align: "right"},
                {name: 'status', index: 'status', width: 80, align: "right"},
                {name: 'id', index: 'edit', width: 55, formatter: editCustomer}
            ],
            autowidth: true,
            shrinkToFit: false,
            rowNum: 2,
            rowList: [2, 10, 20, 30],
            viewrecords: true,
            pager: "#customers_table_pager",
            caption: "Customers list",
            subGrid: true,
            subGridRowExpanded: function (subgrid_id, row_id) {
                var subgrid_table_id;
                subgrid_table_id = subgrid_id + "_t";
                jQuery("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table>");
                jQuery("#" + subgrid_table_id).jqGrid({
                    url: "/api/calls?customer_id=" + row_id,
                    datatype: "json",
                    colNames: ['Call Nr', 'Customer', 'Subject', 'Content', 'Edit'],
                    colModel: [
                        {name: 'id', index: 'id', width: 55},
                        {name: 'customer_id', index: 'customer_id', width: 200, formatter: showCustomer},
                        {name: 'subject', index: 'subject', width: 100},
                        {name: 'content', index: 'content', width: 300, align: "right"},
                        {name: 'id', index: 'edit', width: 55, formatter: editCall}
                    ],
                    height: '100%',
                    rowNum: 10
                });
            }
        });
    });

    routie('/call/add', function () {

        $('#navbar').find('li.active').removeClass('active');
        $('#callAddLink').addClass('active');

        contentEl.empty();
        $.tmpl(callAddFormTemplate, {}).appendTo(contentEl);


        $('#addCall').find('button[type="submit"]').attr('disabled', 'disabled');

        $.get('/api/customers', function (resp) {
            $('#customer_id').empty();
            if (resp.rows) {
                for (var i in resp.rows) {
                    if (!resp.rows.hasOwnProperty(i)) {
                        continue;
                    }

                    var user = resp.rows[i];
                    $('#customer_id').append('<option value="' + user.id + '">' + user.first_name + ' ' + user.last_name + '</option>');

                }
                $('#addCall').find('button[type="submit"]').attr('disabled', null);
            } else {
                $('#customer_id').append('<option>No users found...</option>');
            }
        });
    });

    routie('/call/update/:id', function (callId) {

        $('#navbar').find('li.active').removeClass('active');
        $('#callUpdateLink').addClass('active');

        if (!currentCall[callId]) {
            routie.navigate('/call/list');
            return;
        }

        contentEl.empty();

        $.tmpl(callAddFormTemplate, currentCall[callId]).appendTo(contentEl);
        currentCallId = callId;

        $('#addCall').find('button[type="submit"]').attr('disabled', 'disabled');

        $.get('/api/customers', function (resp) {

            $('#customer_id').empty();

            if (resp.rows) {
                for (var i in resp.rows) {
                    if (!resp.rows.hasOwnProperty(i)) {
                        continue;
                    }

                    var selected = '';
                    var user = resp.rows[i];

                    if (currentCall[callId].customer_id == user.id) {
                        selected = ' selected="selected" ';
                    }
                    $('#customer_id').append('<option value="' + user.id + '" ' + selected + '>' + user.first_name + ' ' + user.last_name + '</option>');

                }
                $('#addCall').find('button[type="submit"]').attr('disabled', null);
            } else {
                $('#customer_id').append('<option>No users found...</option>');
            }

            $('#addCall').attr('id', 'updateCall');
        });
    });

    routie('/call/list', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#callsListLink').addClass('active');

        contentEl.empty();

        $.tmpl(callListTemplate, {}).appendTo(contentEl);

        //init jqGrid
        $("#calls").jqGrid({
            url: '/api/calls',
            datatype: "json",
            colNames: ['Call Nr', 'Customer', 'Subject', 'Content', 'Edit'],
            colModel: [
                {name: 'id', index: 'id', width: 55},
                {name: 'customer_id', index: 'customer_id', width: 200, formatter: showCustomer},
                {name: 'subject', index: 'subject', width: 100},
                {name: 'content', index: 'content', width: 300, align: "right"},
                {name: 'id', index: 'edit', width: 55, formatter: editCall}
            ],
            rowNum: 10,

            autowidth: true,
            shrinkToFit: false,
            rowList: [10, 20, 30],
            viewrecords: true,
            pager: "#calls_table_pager",
            caption: "Calls list"
        });
    });

    contentEl.on('submit', '#addContent', function (e) {
        e.preventDefault();

        $('.has-error').removeClass('has-error');

        $(e.target).find('button[type="submit"]').attr('disabled', 'disabled');

        $.ajax('/api/customers', {
            type: 'POST',
            datatype: 'json',
            data: $(e.target).serialize(),
            complete: function () {
                $(e.target).find('button[type="submit"]').attr('disabled', null);
            },
            success: function (response) {
                routie.navigate('/customer/list');
            },
            error: function (result) {
                if (result.responseJSON.validationMessages) {
                    var messagesObject = result.responseJSON.validationMessages.customer;
                    for (var i in result.responseJSON.validationMessages.customer) {
                        if (!messagesObject.hasOwnProperty(i)) {
                            continue;
                        }

                        $('#' + i).parent('.form-group').addClass('has-error');

                    }
                } else {
                    alert('Error happen: ' + result.responseJSON.title)
                }
            }
        });

        return false;
    });

    contentEl.on('submit', '#addCall', function (e) {
        e.preventDefault();

        $('.has-error').removeClass('has-error');

        $(e.target).find('button[type="submit"]').attr('disabled', 'disabled');

        $.ajax('/api/calls', {
            type: 'POST',
            datatype: 'json',
            data: $(e.target).serialize(),
            complete: function () {
                $(e.target).find('button[type="submit"]').attr('disabled', null);
            },
            success: function (response) {
                routie.navigate('/call/list');
            },
            error: function (result) {

                if (result.responseJSON.validationMessages) {
                    var messagesObject = result.responseJSON.validationMessages.call;
                    for (var i in result.responseJSON.validationMessages.call) {
                        if (!messagesObject.hasOwnProperty(i)) {
                            continue;
                        }

                        $('#' + i).parent('.form-group').addClass('has-error');
                    }
                } else {
                    alert('Error happen: ' + result.responseJSON.title)
                }
            }
        });

        return false;
    });

    contentEl.on('submit', '#updateCustomer', function (e) {
        e.preventDefault();

        $('.has-error').removeClass('has-error');

        $(e.target).find('button[type="submit"]').attr('disabled', 'disabled');

        $.ajax('/api/customers/' + currentCustomerId, {
            type: 'PUT',
            datatype: 'json',
            data: $(e.target).serialize(),
            complete: function () {
                $(e.target).find('button[type="submit"]').attr('disabled', null);
            },
            success: function (response) {
                routie.navigate('/customer/list');
            },
            error: function (result) {

                if (result.responseJSON.validationMessages) {
                    var messagesObject = result.responseJSON.validationMessages.customer;
                    for (var i in result.responseJSON.validationMessages.customer) {
                        if (!messagesObject.hasOwnProperty(i)) {
                            continue;
                        }

                        $('#' + i).parent('.form-group').addClass('has-error');
                    }
                } else {
                    alert('Error happen: ' + result.responseJSON.title)
                }
            }
        });

        return false;
    });

    contentEl.on('submit', '#updateCall', function (e) {
        e.preventDefault();

        $('.has-error').removeClass('has-error');

        $(e.target).find('button[type="submit"]').attr('disabled', 'disabled');

        $.ajax('/api/calls/' + currentCallId, {
            type: 'PUT',
            datatype: 'json',
            data: $(e.target).serialize(),
            complete: function () {
                $(e.target).find('button[type="submit"]').attr('disabled', null);
            },
            success: function (response) {
                routie.navigate('/call/list');
            },
            error: function (result) {

                if (result.responseJSON.validationMessages) {
                    var messagesObject = result.responseJSON.validationMessages.call;
                    for (var i in result.responseJSON.validationMessages.call) {
                        if (!messagesObject.hasOwnProperty(i)) {
                            continue;
                        }

                        $('#' + i).parent('.form-group').addClass('has-error');
                    }
                } else {
                    alert('Error happen: ' + result.responseJSON.title)
                }
            }
        });

        return false;
    });

    routie.reload();

});


function showCustomer(cellvalue, options, rowObject) {
    return rowObject.customer.first_name + ' ' + rowObject.customer.last_name;
}

function editCustomer(cellvalue, options, rowObject) {
    currentCustomer[cellvalue] = rowObject;
    return '<a href="#/customer/update/' + cellvalue + '" >Edit</a>';
}

function editCall(cellvalue, options, rowObject) {
    currentCall[cellvalue] = rowObject;
    return '<a href="#/call/update/' + cellvalue + '">Edit</a>';
}

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};