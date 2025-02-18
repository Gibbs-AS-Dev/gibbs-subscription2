<div class="gift_all_bookings">
    <div class="search-box">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="search-email" placeholder="Søk med epost eller kode" onkeyup="searchGiftCard()">
    </div>

    <div id="noDataMessage" style="display: none;">
        Ingen kjøpte gavekort funnet
    </div>

    <table id="giftCardTable" class="display gift-card-table" style="display:none;">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Kunde</th>
                <th>Kjøpt beløp</th>
                <th>Saldo</th>
                <th>Kjøpt dato</th>
                <th>Utløps dato</th>
                <th>Handlinger</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child::before {
        font-family: "Font Awesome 5 Free";
        content: "\f067"; /* Plus icon */
        font-weight: 900;
        margin-right: 10px;
        color: #008474;
    }

    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td:first-child::before {
        content: "\f068"; /* Minus icon */
    }
    #giftCardTable_paginate a {
        font-family: Source Sans Pro;
        font-weight: bold;
        border-radius: 5px;
        padding: 2px 12px;
        margin: 0px 1px;
        color: #008474 !important;
        text-decoration: none;
    }
    #giftCardTable_paginate, #giftCardTable_info {
        margin-top: 30px;
    }
    
    #giftCardTable_paginate a.active, #giftCardTable_paginate a.current, #giftCardTable_paginate a:hover {
        border: 1px solid #008474 !important;
        background-color: #585858;
        background: linear-gradient(to bottom, #008474 0%, #008474 100%) !important;
        color: #fff !important;
    }
    #giftCardTable_paginate .disabled{
        display: none;
    }
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Initialize DataTable
    let giftCardTable = $('#giftCardTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[4, "desc"]], // Default sorting by purchased date (newest first)
        "searching": false, // Disable default search box
        "lengthMenu": [10, 25, 50, 100], // Options for "Show entries" dropdown
        "pageLength": 10, // Default number of entries to show
        "ajax": {
            "url": "<?php echo admin_url('admin-ajax.php'); ?>",
            "type": "POST",
            "data": function(d) {
                d.action = "fetch_giftcard_data"; // Custom AJAX action
                d.searchData = $('#search-email').val(); // Include search email in AJAX data
            },
            "dataSrc": function(json) {
                if (json.data.length > 0) {
                    $(".dataTables_length").show();
                    $('#giftCardTable_wrapper').show();
                    $('#giftCardTable').show();
                    $('#noDataMessage').hide();
                } else {
                    $(".dataTables_length").hide();
                    $('#giftCardTable_wrapper').hide();
                    $('#giftCardTable').hide();
                    $('#noDataMessage').show();
                }
                return json.data;
            }
        },
        "columns": [
            { "data": "code", "render": function(data) { return `<span class="gift-card-code">${data}</span>`; }},
            { "data": "purchased_by" },
            { "data": "purchased_amount" },
            { "data": "remaining_saldo" },
            { "data": "purchased_date" },
            { "data": "expire_date" },
            { 
                "data": "actions", 
                "defaultContent": "<button class='btn'>No Actions</button>",
                "render": function(data, type, row) {
                    const pdfButton = `<button class="btn btn-pdf" onclick="downloadPDF('${row.code}')">Last ned PDF</button>`;
                    if (row.show_actions) {
                        const actionButton = row.is_active
                            ? `<button class="btn btn-deactivate" onclick="deactivateGiftCard('${row.id}')">Deaktiver</button>`
                            : `<button class="btn btn-activate" onclick="activateGiftCard('${row.id}')">Aktiver</button>`;
                        return `${pdfButton} ${actionButton}`;
                    } else {
                        return pdfButton;
                    }
                }
            }
        ],
        "language": {
            "processing": "Laster inn...",
            "lengthMenu": "Vis _MENU_ oppføringer per side",
            "zeroRecords": "Ingen gavekort er kjøpt enda 😊",
            "info": "_TOTAL_ Gavekort",
            "infoEmpty": "Ingen oppføringer tilgjengelig",
            "infoFiltered": "",
            "paginate": {
                "next": "Neste",
                "previous": "Forrige"
            },
            "search": "Søk:"
        }
    });

    // Reload DataTable on search input change
    window.searchGiftCard = function() {
        giftCardTable.ajax.reload();
    };
});

// Function to download PDF
function downloadPDF(giftcode) {
    const form = jQuery("<form>", {
        action: '<?php echo admin_url("admin-ajax.php"); ?>',
        method: "POST"
    });
    form.append(jQuery("<input>", { type: "hidden", name: "action", value: "downloadGiftPDF" }));
    form.append(jQuery("<input>", { type: "hidden", name: "giftcode", value: giftcode }));
    jQuery("body").append(form);
    form.submit();
    form.remove(); // Clean up
}

// Function to deactivate a gift card
function deactivateGiftCard(postId) {
    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
        action: 'deactivate_giftcard',
        post_id: postId
    }, function(response) {
        jQuery('#giftCardTable').DataTable().ajax.reload(null, false);
    });
}

// Function to activate a gift card
function activateGiftCard(postId) {
    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
        action: 'activate_giftcard',
        post_id: postId
    }, function(response) {
        jQuery('#giftCardTable').DataTable().ajax.reload(null, false);
    });
}
// Apply custom styling to pagination buttons after the table is initialized
giftCardTable.on('draw', function() {
    // Remove inline styles set by DataTables
    $('.dataTables_paginate .paginate_button').removeAttr('style');

    // Apply custom styles
    $('.dataTables_paginate .paginate_button').css({
        'display': 'inline-block',
        'padding': '8px 16px',
        'margin': '0 5px',
        'font-size': '14px',
        'color': '#008474',
        'background-color': 'transparent',
        'border-radius': '20px',
        'font-weight': 'bold',
        'cursor': 'pointer',
        'text-align': 'center'
    });

    // Active button style
    $('.dataTables_paginate .paginate_button.current').css({
        'background-color': '#008474',
        'color': '#fff',
        'pointer-events': 'none'
    });

    // Hover effect
    $('.dataTables_paginate .paginate_button').hover(
        function() {
            $(this).css({
                'background-color': '#006b5c',
                'color': '#fff'
            });
        },
        function() {
            if (!$(this).hasClass('current')) {
                $(this).css({
                    'background-color': 'transparent',
                    'color': '#008474'
                });
            }
        }
    );
});
</script>
