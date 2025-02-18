<?php
$get_column_definitions = Access_management_v2::get_column_definitions();
$columns = $get_column_definitions["default"]; // Default columns, can be updated based on provider selection.

$message = get_transient('flash_message');
?>
<div class="access_all_data">

    <?php if ($message) {
        $type = get_transient('flash_type') ?? 'info';
        ?>
        <div class="alert alert-<?php echo $type; ?>" role="alert">
            <?php echo $message; ?>
        </div>     
    <?php 
        delete_transient('flash_message'); 
        delete_transient('flash_type');
    } ?>

    <div class="flex-container">
        <div class="filter-container">
            <div class="search-box-data">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="search-access" placeholder="Søk" onkeyup="searchAccessCard()">
            </div>
            <div class="filter-box" style="display: none;">
                <select id="provider-select" onchange="searchAccessCard()">
                    <option value="default">Velg</option>
                    <option value="locky">locky</option>
                    <option value="shelly">shelly</option>
                    <option value="unloc">unloc</option>
                    <option value="igloohome">igloohome</option>
                </select>
            </div>
        </div>
        <a href="?addEditAccessCode=true"><button id="create-access-code" class="btn btn-success">Opprett integrasjon</button></a>
    </div>

    <div id="loader-access" class="loader-access-services" style="display: none;"><div class="lds-ring-access"><div></div><div></div><div></div><div></div></div></div>

    <div id="noDataMessage" style="display: none;">
        Ingen treff
    </div>
    
    <table id="AccessCardTable" class="display gift-card-table" style="display: none;">
        <thead>
            <tr>
                <?php foreach($columns as $column) { ?>
                    <th><?php echo $column;?></th>
                <?php } ?>
            </tr>
        </thead>
    </table>
</div>
<style>
    #AccessCardTable_paginate a {
        font-family: Source Sans Pro;
        font-weight: bold;
        border-radius: 5px;
        padding: 2px 12px;
        margin: 0px 1px;
        color: #008474 !important;
        text-decoration: none;
        border: none;
    }
    #AccessCardTable_paginate {
        margin-top: 30px;
    }
    
    #AccessCardTable_paginate a.active, #AccessCardTable_paginate a.current, #AccessCardTable_paginate a:hover, #AccessCardTable_paginate .active a {
        border: 1px solid #008474 !important;
        background-color: #585858;
        background: linear-gradient(to bottom, #008474 0%, #008474 100%) !important;
        color: #fff !important;
    }
    #AccessCardTable_paginate .disabled{
        display: none !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">   
function editAccessCode(id) {
    window.location.href = "?addEditAccessCode=true&edit="+id;
}
function deleteAccessCode(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "delete_accesscard_data",
                    id: id
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.data.message, 'success');
                        jQuery('#AccessCardTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error!', response.data.message || 'Failed to delete access card.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Unable to delete the access card.', 'error');
                }
            });
        }
    });
}
jQuery(document).ready(function ($) {
    // Fetch columns based on provider selection
    function getColumnDefinitions(provider) {
        let columns = <?php echo json_encode($get_column_definitions); ?>;
        return columns[provider] || columns["default"];
    }
    

    // Function to update table headers
    function updateTableHeaders(provider) {
        let columns = getColumnDefinitions(provider);
        let headerHtml = '';
        Object.values(columns).forEach((column) => {
            headerHtml += `<th>${column}</th>`;
        });
        $('#AccessCardTable thead').html(`<tr>${headerHtml}</tr>`);
    }

    // Function to initialize the DataTable
    function initializeDataTable(provider) {
        let columns = getColumnDefinitions(provider);
        let columnDefs = Object.keys(columns).map((dbColumn, index) => {
            if (dbColumn === "actions") {
                return {
                    data: dbColumn,
                    orderable: false, // Disable ordering for action column
                    defaultContent: "<button class='btn'>No Actions</button>",
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-pdf" onclick="editAccessCode('${row.id}')">Rediger</button>
                            <button type="button" class="btn btn-deactivate" onclick="deleteAccessCode('${row.id}')">Slett</button>`;
                    },
                    responsivePriority: 1, 
                    width: "120px",
                };
            } else if (dbColumn === "id") {
                return {
                    data: dbColumn,
                    visible: false,
                    searchable: false,
                };
            }else if (dbColumn === "sms_content") {
                    // Handle newline characters in sms_content
                    return {
                        data: dbColumn,
                        className: dbColumn,
                        render: function (data, type, row) {
                            if (type === "display" && data) {
                                // Replace \n with actual line breaks for display
                                return data.replace(/\\n/g, "<br>");
                            }
                            return data;
                        }
                    };
            }

            return { 
                data: dbColumn,
                minwWidth: "120px",
                className: dbColumn,
                responsivePriority: index < 4 ? 2 : 99, // First 4 columns have high priority for visibility
            };
        });
        jQuery("#loader-access").show();

        // Initialize DataTable
        return $('#AccessCardTable').DataTable({
            processing: false,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            searching: false,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            ajax: {
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: function (d) {
                    d.action = "fetch_accesscard_data";
                    d.provider = provider;
                    d.searchData = $('#search-access').val();
                },
                beforeSend: function () {
                    jQuery("#loader-access").show();
                    $(".dataTables_length").hide();
                    $('#AccessCardTable_wrapper').hide();
                    $('#AccessCardTable').hide();
                },
                dataSrc: function (json) {
                    
                    
                    if (json.data.length > 0) {
                        $(".dataTables_length").show();
                        $('#AccessCardTable_wrapper').show();
                        $('#AccessCardTable').show();
                        $('#noDataMessage').hide();
                    } else {
                        $(".dataTables_length").hide();
                        $('#AccessCardTable_wrapper').hide();
                        $('#AccessCardTable').hide();
                        $('#noDataMessage').show();
                    }
                    return json.data || [];
                },
                complete: function () {
                    jQuery("#loader-access").hide();
                    let table = $('#AccessCardTable').DataTable();
                    if (table.rows().count() > 0) {
                        table.columns().visible(true); // Make sure columns are visible after load
                        table.column(0).visible(false);
                    }else{
                        table.columns().visible(false);
                    }
                }
            },
            columns: columnDefs,
            language: {
                processing: "Laster inn...",
                lengthMenu: "Vis _MENU_ oppføringer per side",
                zeroRecords: "Ingen treff enda 😊",
                info: "_TOTAL_ treff",
                infoEmpty: "Ingen oppføringer tilgjengelig",
                infoFiltered: "",
                paginate: {
                    next: "Neste",
                    previous: "Forrige",
                },
                search: "Søk:",
            },
            drawCallback: function () {
                stylePagination();
            },
        });
    }

    // Function to style pagination buttons
    function stylePagination() {
        $('.dataTables_paginate .paginate_button').removeAttr('style').css({
            display: 'inline-block',
            padding: '8px 2px',
            margin: '0 5px',
            fontSize: '14px',
            color: '#008474',
            backgroundColor: 'transparent',
            borderRadius: '5px',
            fontWeight: 'bold',
            cursor: 'pointer',
            textAlign: 'center',
        });

        $('.dataTables_paginate .paginate_button.current').css({
            backgroundColor: '#008474',
            color: '#fff',
            pointerEvents: 'none',
        });

        $('.dataTables_paginate .paginate_button').hover(
            function () {
                $(this).css({ backgroundColor: '#006b5c', color: '#fff' });
            },
            function () {
                if (!$(this).hasClass('current')) {
                    $(this).css({ backgroundColor: 'transparent', color: '#008474' });
                }
            }
        );
    }

    // Initialize with default provider
    let currentProvider = 'default';
    updateTableHeaders(currentProvider);
    let AccessCardTable = initializeDataTable(currentProvider);
    
    let $i_inc = 0;

    // Handle provider change
    $('#provider-select').change(function () {
        if($i_inc == 0){
            AccessCardTable.destroy();
            $('#AccessCardTable').find("tbody").remove();
            currentProvider = $(this).val(); // Get the selected provider

            // Update headers
            updateTableHeaders(currentProvider);


            // Destroy existing DataTable instance
        

            // Reinitialize DataTable with new provider's columns
            AccessCardTable = initializeDataTable(currentProvider);

            AccessCardTable.columns().visible(false);
        }
        $i_inc++;   
    });

    // Reload DataTable on search input change
    window.searchAccessCard = function () {
        AccessCardTable.ajax.reload();
    };

    // Style pagination on initial load
    stylePagination();
});


</script>
