<?php
?>

<html>
<head>
    <title>RSS Feed Grabber</title>
    <style>
        #printer_container {
            display: none;
        }

        @media print {
            #content {
                display: none;
            }

            #printer_container {
                display: block;
                background-color: white;
                height: 100%;
                width: 100%;
                position: fixed;
                top: 0;
                left: 0;
                margin: 0;
                padding: 15px;
                font-size: 14px;
                line-height: 18px;
            }
        }
    </style>
</head>
<body>
<div id="content">
    <div id="input_data">
        <form id="rss_form">
            <label>
                Please enter URI:
                <input name="rss_uri" id="rss_uri" value="http://feeds.reuters.com/reuters/entertainment.rss"/>
            </label>
            <button>Process URL</button>
        </form>
    </div>
    <div id="rss_container">

    </div>
</div>
<div id="printer_container">

</div>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script lang="JavaScript">
    $(document).ready(function () {

        $('#rss_form').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent($('#rss_uri').val()),
                dataType: 'json',
                success: function (data) {
                    var container = $('#rss_container');
                    container.empty();

                    if (data.responseData.feed && data.responseData.feed.entries) {
                        $.each(data.responseData.feed.entries, function (i, e) {

                            $('<ul>' +
                                '<li>Title: ' + e.title + '</li>' +
                                '<li>Date: ' + e.publishedDate + '</li>' +
                                '<li>Description: ' + e.contentSnippet + '</li>' +
                                '</ul>').click(function (ev) {
                                ev.stopPropagation();

                                if (confirm('Print this article')) {
                                    $('#printer_container').empty();
                                    
                                    $(this).clone().appendTo($('#printer_container'));
                                    window.print();
                                }

                            }).appendTo(container);
                        });
                    }
                }
            });
            return false;
        });
    });

</script>
</body>
</html>