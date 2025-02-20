function tabActiveInactive(tabButtonId) {
    $('.tab-pane').removeClass('show active');
    $('.nav-link').removeClass('active');
    var contentId = $('#' + tabButtonId).attr('data-bs-target').substring(1);
    $('#' + contentId).addClass('show active');
    $('#' + tabButtonId).addClass('active');
}

function formValidation() {
    $("#dataFrom").find('.error').remove();
    let isValid = true;
    $("#dataFrom").find(':input[required]').each(function () {
        if (!this.checkValidity()) {
            $(this).after('<div class="error" style="color: red; font-size: 12px;">This field is required</div>');
            isValid = false;
        }
    });

    return isValid;
}



function resetValidation() {
    $('#dataFrom input').val('');
    $('#dataFrom select').val('');
    $('#dataFrom textarea').val('');
    $('#dataFrom').find('.error').remove();

    $('#search input').val('');
    $('#search select').val('');
    $('#search textarea').val('');
    $('#search').find('.error').remove();

    $('#imagePreview').attr('src', '');
    $('#imagePreview').hide();
    $('#removeImage').hide();
    $('#item_image').val('');

    loadTable(1,'list');
}

function loadTable(pageNo=1,type='list', sortOrder = "", column = "") {
    var page_limit = $('#records_per_page').val();
    var url = $("#url").val();
    var formElem = document.getElementById('search');
    var formData = new FormData(formElem);

    formData.append('type', type);
    formData.append('sort_order', sortOrder);
    formData.append('sort_column', column);
    formData.append('page', pageNo);
    if (page_limit > 0) {
        formData.append('records_per_page', page_limit);
    }
    $.ajax({
        url: url + '.php',
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        async: false,
        success: function (data) {
            var response = JSON.parse(data);
            $("#table_record").html(response.html);

            Pagination(pageNo, response.total_pages, page_limit);
        }
    });
}


function Pagination(currentPage, totalPages, recordsPerPage) {
    let paginationHTML = `<nav aria-label="Page navigation">
                            <ul class="pagination">`;

    // Previous button
    paginationHTML += `<li class="page-item btn btn-secondary ${currentPage <= 1 ? 'disabled' : ''}">
                        <span class="page-link" onclick="loadTable(${currentPage - 1})">Previous</span>
                      </li>`;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `<li class="page-item btn btn-secondary ${i === currentPage ? 'active' : ''}">
                             <a class="page-link text-white" href="javascript:void(0);" onclick="loadTable(${i})">${i}</a>
                           </li>`;
    }

    // Next button
    paginationHTML += `<li class="page-item btn btn-secondary ${currentPage >= totalPages ? 'disabled' : ''}">
                        <span class="page-link" onclick="loadTable(${currentPage + 1})">Next</span>
                      </li>`;

    paginationHTML += `</ul>
                      </nav>`;

    $('#pagination').html(paginationHTML);
}



function addUpdate(type) {
    if (!formValidation()) {
        return;
    }
    var url = $("#url").val();
    var formElem = document.getElementById('dataFrom');
    var formData = new FormData(formElem);

    formData.append('type', type);
    $.ajax({
        type: 'POST',
        url: url + '.php',
        data: formData,
        processData: false,
        contentType: false,
        async: false,
        dataType: "json",
        success: function (result) {
            if (result.success == 'false' || result.success == false) {
                if (typeof result.message === 'object') {
                    $.each(result.message, function (key, value) {
                        $('#' + key + '-error').html(value).show();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message
                    });
                }
            } else {
                resetValidation()
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.message
                });
                loadTable(1,'list');
                tabActiveInactive('pills-home-tab');
                $('#imagePreview').attr('src', '');
                $('#imagePreview').hide();
                $('#removeImage').hide();
                $('#item_image').val('');
            }
        },
        error: function (xhr, status, error) {
            console.log("Error:", error);
        }
    });
}



function deleteImageSrc() {
    $('#imagePreview').attr('src', '');
    $('#imagePreview').removeAttr('src');
    $('#removeImage').css('display', 'none');

}

function getFromData(id) {
    var url = $("#url").val();
    $.ajax({
        type: 'POST',
        url: url + '.php',
        data: {
            type: 'get_record',
            id: id
        },
        dataType: "json",
        success: function (result) {
            $.each(result.data, function (name, value) {
                $('#dataFrom').find('[name="' + name + '"]').each(function () {
                    if ($(this).is('input') || $(this).is('textarea')) {
                        if (name == 'password') {
                            $(this).val(''); // Don't populate the password field
                            $(this).prop('required', false); // Remove the required attribute
                            $(this).attr('required', false); // Remove the required attribute
                        } else {
                            $(this).val(value);
                        }
                    }
                    else if ($(this).is('select')) {
                        if (name == 'state') {
                            var state_id = value;
                            $(this).val(value);
                            cityList(state_id);
                        }
                        if (name == 'city') {
                            setTimeout(() => {
                                $(this).val(value).change();
                            }, 20);
                        }
                    }
                    else if ($(this).is('input[type="checkbox"]') || $(this).is('input[type="radio"]')) {
                        if ($(this).val() == value) {
                            $(this).prop('checked', true);
                        }
                    }
                });
                if (name == 'path') {
                    $('#imagePreview').attr('src', value.slice(8));
                    $('#removeImage').show();
                    $('#imagePreview').show();
                }
            });
            $('#id').val(id);
            tabActiveInactive('pills-profile-tab');
            $('#password').removeAttr('required'); // Remove the required attribute
        },

        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('Error ');
        }

    });
}

function deleteRecords(delId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this record!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = $("#url").val();
            $.ajax({
                url: url + '.php',
                type: "POST",
                data: {
                    type: "delete_record",
                    id: delId
                },
                dataType: "json",
                success: function (result) {
                    if (result.success == true || result.success == "true") {
                        loadTable(1,'list')
                        tabActiveInactive('pills-home-tab');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message
                        })
                    }
                }
            });
        }
    })
}



function stateList() {
    var url = $("#url").val();
    $.ajax({
        url: url + '.php',
        type: 'POST',
        data: {
            type: 'state_list'
        },
        success: function (data) {
            $('#state').html('<option value="">Select</option>' + data);
        }
    });
};

function getStateCity() {
    var state_id = $("#state").val();
    cityList(state_id)
}

function cityList(state_id) {
    var url = $("#url").val();
    $.ajax({
        url: url + '.php',
        type: 'POST',
        data: {
            type: 'city_list',
            state_id: state_id
        },
        success: function (data) {
            $('#city').html('<option value="">Select</option>' + data);
        }


    });
};

