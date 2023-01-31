$(document).ready(function() {

    var productData = $('#productListInactive').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'productListInactive' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 5],
            "orderable": false,
        }, ],
        "pageLength": 10,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(5)').addClass('text-center')
        },
    });


    $(document).on('change', '#categoryid', function() {
        var categoryid = $('#categoryid').val();
        var btn_action = 'getCategoryBrand';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { categoryid: categoryid, btn_action: btn_action },
            success: function(data) {
                $('#brandid').html(data);
            }
        });
    });

    $(document).on('submit', '#productForm', function(event) {
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var formData = $(this).serialize();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
                $('#productForm')[0].reset();
                $('#productModal').modal('hide');
                $('#action').attr('disabled', false);
                productData.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function() {
        var pid = $(this).attr("id");
        var btn_action = 'viewProduct';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { pid: pid, btn_action: btn_action },
            success: function(data) {
                $('#productViewModal').modal('show');
                $('#productDetails').html(data);
            }
        })
    });

    $(document).on('click', '.update', function() {
        var pid = $(this).attr("id");
        var btn_action = 'getProductDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { pid: pid, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                $('#productModal').modal('show');
                $('#categoryid').val(data.categoryid);
                $('#brandid').html(data.brand_select_box);
                $('#brandid').val(data.brandid);
                $('#pname').val(data.pname);
                $('#pmodel').val(data.model);
                $('#description').val(data.description);
                $('#quantity').val(data.quantity);
                $('#base_price').val(data.base_price);
                $('#supplierid').val(data.supplier);
                $('.modal-title').html("<i class='fa fa-edit'></i> Editar Producto");
                $('#pid').val(pid);
                $('#action').val("Editar");
                $('#btn_action').val("updateProduct");
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var pid = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'deleteProduct';
        if (confirm("¿Está seguro de que desea eliminar este producto?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { pid: pid, status: status, btn_action: btn_action },
                success: function(data) {
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">' + data + '</div>');
                    productData.ajax.reload();
                }
            });
        } else {
            return false;
        }
    });
});