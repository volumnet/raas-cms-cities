<script>
jQuery(document).ready(function($) {
    $('.select-city-modal').appendTo('body');
    $('.select-city-modal-item').on('click', () => {
        $(document).trigger('raas.changecity');
    });
});
</script>