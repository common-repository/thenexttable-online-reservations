function popupwindow(form) {
    var settings = WPVARS.settings;
    var w = 800, h = 800;
    var left = Number((screen.width / 2) - (w / 2));
    var tops = Number((screen.height / 2) - (h / 2));

    var url = settings.text_general_baseurl + "/index.php/reservering/" + settings.number_general_restaurantid + "/start?" + jQuery(form).serialize();
    window.open(url, 'popup', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + tops + ', left=' + left);
}

jQuery(document).ready(function () {
    jQuery(".tnt-widget .datum").each(function(i, v) {
        jQuery(v).datepicker({
            dateFormat: 'd-m-Y',
            minDate: 0,
            maxDate: "+12M"
        }).on('change', function (e) {
            var datum = jQuery(e.target);
            var datumVeld = datum.parent().find('.datumVeld');
            datumVeld.val(jQuery.datepicker.formatDate('dd-mm-yy', datum.datepicker('getDate')));
        });
    });
});

