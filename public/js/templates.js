var customerAddFormTemplate = '<form id="addContent" role="form">' +

    '<div class="form-group">' +
    '<label for="first_name">First Name:</label>' +
    '<input class="form-control" type="text" value="" name="first_name" id="first_name">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="last_name">Last Name:</label>' +
    '<input class="form-control" type="text" value="" name="last_name" id="last_name">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="phone">Phone:</label>' +
    '<input class="form-control" type="tel" value="" name="phone" id="phone">' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="status">Status:</label>' +
    '<select class="form-control" name="status" id="status">' +
    '<option value="active">Active</option>' +
    '<option value="blocked">Blocked</option>' +
    '</select>' +
    '</div>' +

    '<button class="btn btn-default" type="submit">Add new content</button>' +


    '</form>';


var customerListTemplate = '<table id="customers"></table>';


var callAddFormTemplate = '<form id="addCall" role="form">' +

    '<div class="form-group">' +
    '<label for="status">User:</label>' +
    '<select class="form-control" name="status" id="status">' +
    '<option value="">Pre loading users ...</option>' +
    '</select>' +
    '</div>' +

    '<div class="form-group">' +
    '<label for="subject">Subject:</label>' +
    '<input class="form-control" type="text" value="" name="subject" id="subject">' +
    '</div>' +


    '<div class="form-group">' +
    '<label for="content">Content:</label>' +
    '<textarea class="form-control" name="content" id="content"></textarea>' +
    '</div>' +


    '<button class="btn btn-default" type="submit">Add new call</button>' +


    '</form>';