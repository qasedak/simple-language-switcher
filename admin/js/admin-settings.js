jQuery(document).ready(function($) {
    let originalValues = {};

    // Edit functionality
    $(document).on('click', '.edit-string', function() {
        const row = $(this).closest('tr');
        const rowId = row.data('row-id');
        
        originalValues[rowId] = {
            identifier: row.find('.identifier-input').val(),
            value: row.find('.value-input').val()
        };

        row.find('input').prop('readonly', false);
        row.find('.edit-string, .remove-string').hide();
        row.find('.save-string, .cancel-edit').show();
    });

    // Cancel editing
    $(document).on('click', '.cancel-edit', function() {
        const row = $(this).closest('tr');
        const rowId = row.data('row-id');
        
        if (originalValues[rowId]) {
            row.find('.identifier-input').val(originalValues[rowId].identifier);
            row.find('.value-input').val(originalValues[rowId].value);
            delete originalValues[rowId];
        }

        row.find('input').prop('readonly', true);
        row.find('.save-string, .cancel-edit').hide();
        row.find('.edit-string, .remove-string').show();
    });

    // Save changes
    $(document).on('click', '.save-string', function() {
        const row = $(this).closest('tr');
        const rowId = row.data('row-id');
        
        const newIdentifier = row.find('.identifier-input').val();
        row.find('code').text('[SLS-' + newIdentifier + ']');

        row.find('input').prop('readonly', true);
        row.find('.save-string, .cancel-edit').hide();
        row.find('.edit-string, .remove-string').show();
        
        delete originalValues[rowId];
    });

    // Remove string
    $(document).on('click', '.remove-string', function() {
        $(this).closest('tr').remove();
    });
});