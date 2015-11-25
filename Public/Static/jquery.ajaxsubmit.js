(function($) {

    /**
     *  jQuery ajaxSubmit
     *  Craig Blanchette (isometriks.com)
     *  craig.blanchette@gmail.com
     *
     *  $('form').ajaxSubmit(function(Object data, String textStatus, jqXHR jqXHR){ ... });
     */
    $.fn.ajaxSubmit = function(success, options) {

        var default_options = {
            url: this.attr('action') || location.href,
            success: success,
            type: this.attr('method') || 'POST'
        };

        var options = $.extend(default_options, options || {});

        if (!!this.attr('enctype') && this.attr('enctype').toLowerCase() === 'multipart/form-data') {

            var formData = new FormData();

            var $files = this.find('input[type="file"][name]');
            $files.each(function() {
                if ('files' in this && this.files.length > 0) {
                    // ToDo: Support Multiple on any input? 
                    // Just need a loop here..
                    formData.append(this.name, this.files[0]);
                }
            });

            var $noFiles = this.find(':not(input[type="file"])');
            $.each($noFiles.serializeArray(), function(i, pair) {
                formData.append(pair.name, pair.value);
            });

            options.data = formData;
            options.method = 'POST';
            options.contentType = false;
            options.processData = false;

        } else {
            options.data = this.serializeArray();
        }

        $.ajax(options);
    }

})(jQuery); 