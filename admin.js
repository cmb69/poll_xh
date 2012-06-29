(function($) {

    Poll = {

        add: function() {
            var tpl = $('#poll_template').clone().removeAttr('id').show();
            $('#poll_options').append(tpl);
        },

        select: function(elt) {
            $('div.poll_option').removeClass('poll_selected');
            $(elt).parents('div.poll_option').addClass('poll_selected')
        },

        remove: function() {
            var sel = $('div.poll_selected')
            sel.remove();
        },

        down: function() {
            var sel = $('div.poll_selected');
            if (sel.next('div.poll_option').length > 0) {
                sel.insertAfter(sel.next())
            }
        },

        up: function() {
            var sel = $('div.poll_selected');
            if (sel.prev('div.poll_option').length > 0) {
                sel.insertBefore(sel.prev())
            }
        },

    }

})(jQuery)
