var customerAddFormTemplate = '<form id="addContent" role="form">' +
    '<input type="hidden" name="customer[id]" value="${id}"/>' +
    '<div class="form-group">' +
    '<label for="first_name">First Name:</label>' +
    '<input class="form-control" required type="text" value="${first_name}" name="customer[first_name]" id="first_name">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="last_name">Last Name:</label>' +
    '<input class="form-control" required type="text" value="${last_name}" name="customer[last_name]" id="last_name">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="address">Address:</label>' +
    '<input class="form-control" required type="text" value="${address}" name="customer[address]" id="address">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="phone">Phone:</label>' +
    '<input class="form-control" required type="tel" value="${phone}" name="customer[phone]" id="phone">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="status">Status:</label>' +
    '<select class="form-control" required name="customer[status]" id="status">' +
    '<option value="active">Active</option>' +
    '<option value="blocked">Blocked</option>' +
    '</select>' +
    '</div>' +

    '<button class="btn btn-default" type="submit">Save</button>' +


    '</form>';


var customerListTemplate = '<div class="row">' +
    '<div class="col-lg-12 col-sm-12">' +
    '<table id="customers"></table><div id="customers_table_pager"></div>' +
    '</div>' +
    '</div>';


var callAddFormTemplate = '<form id="addCall" role="form">' +

    '<div class="form-group">' +
    '<label for="customer_id">User:</label>' +
    '<select class="form-control" name="call[customer_id]" id="customer_id">' +
    '<option value="">Pre loading users ...</option>' +
    '</select>' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="subject">Subject:</label>' +
    '<input class="form-control" type="text" value="${subject}" name="call[subject]" id="subject">' +
    '</div>' +


    '<div class="form-group">' +
    '<label for="content">Content:</label>' +
    '<textarea class="form-control" name="call[content]" id="content">${content}</textarea>' +
    '</div>' +


    '<button class="btn btn-default" type="submit">Save</button>' +


    '</form>';


var callListTemplate = '<div class="row">' +
    '<div class="col-lg-12 col-sm-12">' +
    '<table id="calls"></table><div id="calls_table_pager"></div>' +
    '</div>' +
    '</div>';