$(function () {

    var contentEl = $('#content');
    routie('/customer/add', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#customerAddLink').addClass('active');


        contentEl.empty();
        $.tmpl(customerAddFormTemplate, {}).appendTo(contentEl);
    });

    routie('/customer/list', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#customersListLink').addClass('active');

        contentEl.empty();

        $.tmpl(customerListTemplate, {}).appendTo(contentEl);

        //init jqGrid
        jQuery("#customers").jqGrid({
            url: '/api/customers',
            datatype: "json",
            colNames: ['Customer Nr', 'First Name', 'Last Name', 'Phone', 'Status'],
            colModel: [
                {name: 'id', index: 'id', width: 55},
                {name: 'first_name', index: 'first_name', width: 90},
                {name: 'last_name', index: 'last_name', width: 100},
                {name: 'phone', index: 'phone', width: 80, align: "right"},
                {name: 'status', index: 'status', width: 80, align: "right"}
            ],
            rowNum: 10,
            rowList: [10, 20, 30],
            viewrecords: true,
            caption: "Customers list"
        });
    });

    routie('/call/add', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#callAddLink').addClass('active');

        contentEl.empty();
        $.tmpl(callAddFormTemplate, {}).appendTo(contentEl);
    });

    routie('/call/list', function () {
        $('#navbar').find('li.active').removeClass('active');
        $('#callsListLink').addClass('active');

        contentEl.empty();
    });

});