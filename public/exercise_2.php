<?php
?>

<html>
<head>
    <title>RSS Feed Grabber</title>
</head>
<body>
<div id="content">
    <div id="input_data">
        <form id="rss_form">
            <label>
                Please enter URI:
                <input name="rss_uri" id="rss_uri" value=""/>
            </label>
            <button>Process URL</button>
        </form>
    </div>
    <div id="result">

    </div>
</div>
<script lang="JavaScript">
    $(document).ready(function () {
        $('#rss_form').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent($('#rss_uri').val()),
                dataType: 'json',
                success: function (data) {
                    if (data.responseData.feed && data.responseData.feed.entries) {
                        $.each(data.responseData.feed.entries, function (i, e) {
                            console.log("------------------------");
                            console.log("title      : " + e.title);
                            console.log("author     : " + e.author);
                            console.log("description: " + e.description);
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