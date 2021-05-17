var productDataList = [];
const SNACK_BAR_DUR_MSEC = 3000;

const GET_REST_CALL = "GET";
const POST_REST_CALL = "POST";
const JSON_DATATYPE = "json";
const GET_ALL_PRODUCTS_URL = "http://localhost/warehouseshippingdept/api/get.php";
const ADD_PRODUCT_URL = "http://localhost/warehouseshippingdept/api/add.php";
const DELETE_PRODUCT_URL ="http://localhost/warehouseshippingdept/api/delete.php";
const UPDATE_PRODUCT_URL ="http://localhost/warehouseshippingdept/api/update.php";


$(document).ready( function(){
    $('body').bootstrapMaterialDesign();

    $(".progress").show();

    $.ajax({  
        type: GET_REST_CALL, 
        dataType: "json", 
        url: GET_ALL_PRODUCTS_URL,  
        data: "",  
        success: response => {
            setProductList(response.data)
            $(".progress").hide();   
        },
        error: error => {
            showErrorSnackbar(error.responseJSON.message);
            $(".progress").hide();   
        }
    });  
    
    $("#add_product_submit_button").click(event => {
        event.preventDefault();

        const notValid = !$("#add_product_form")[0].checkValidity();
        if(notValid) {
            event.stopPropagation();
            showErrorSnackbar("Make sure all product fields are properly filled.");
            return;
        }

        $(".progress").show();
        $.ajax({
            type: POST_REST_CALL,
            dataType: JSON_DATATYPE, 
            url: ADD_PRODUCT_URL,
            data: $('#add_product_form').serialize(),
            success: response => {
                showSuccessSnackbar("Product Created");
 
                addNewProduct(response.data);

                $(".progress").hide(); 
                $('#add_product_form')[0].reset();  
            },
            error: error => {
                showErrorSnackbar(error.responseJSON.message);
                $(".progress").hide();   
            }
        });
    });

    function setProductList(products){
        productDataList = products;
        $("#product_table > tbody").empty();

        $.each(productDataList, function(key, value) {
            const productID = value.id;
            const productName = value.name;
            const productWeightKG = `${value.weight_kg} kg`;
            const productDimensionsCM = `${value.length_cm}cm x 
                ${value.width_cm}cm x 
                ${value.height_cm}cm`;

            const editButtonID = `edit_button_for_id_${productID}`;
            const editButton = `<button type="button" class="btn btn-warning" id="${editButtonID}">
                    <span class="material-icons">edit</span>
                </button>`;

            const deleteButtonID = `delete_button_for_id_${productID}`;
            const deleteButton = `<button type="button" class="btn btn-danger" id="${deleteButtonID}">
                    <span class="material-icons">delete</span>
                </button>`;

            const rowMarkup = `<tr>
                    <td>${productName}</td>
                    <td>${productWeightKG}</td>
                    <td>${productDimensionsCM}</td>
                    <td>${editButton} ${deleteButton}</td>
                </tr>`;

            $("#product_table > tbody").append(rowMarkup);

            $(`#${editButtonID}`).on('click', () => openEditProductModal (productID));
            $(`#${deleteButtonID}`).on('click', () => deleteProduct (productID));
        });
    }

    function deleteProduct(productID) {
        $(".progress").show();
        $.ajax({
            type: GET_REST_CALL,
            dataType: JSON_DATATYPE, 
            url: DELETE_PRODUCT_URL,
            data: {product_id: productID},
            success: response => {
                showSuccessSnackbar("Product Deleted");
                deleteProductID(response.data);
                $(".progress").hide();
            },
            error: error => {
                showErrorSnackbar(error.responseJSON.message);
                $(".progress").hide();   
            }
        });
    }

    function updateProductInList(updatedProduct){
        if(updatedProduct === null) {
            return;
        }
        
        const updatedProductIndex = productDataList.findIndex(product => product.id == updatedProduct.id);
        if(updatedProductIndex === -1) {
            return;
        }
        
        productDataList.splice(updatedProductIndex, 1);
        addNewProduct(updatedProduct);

        setProductList(productDataList);
    }

    function addNewProduct(newProduct){
        var index = -1;
        
        for(let i = 0; i < productDataList.length; i++) {
            if(productDataList[i].name.toLowerCase() > newProduct.name.toLowerCase()) {
                index = i;
                break;
            }
        }

        var needToPutAtEnd = productDataList.length > 0 && index == -1;
        if(needToPutAtEnd) {
            productDataList.push(newProduct);
        }
        else {
            productDataList.splice(index, 0, newProduct);
        }

        setProductList(productDataList);
    }

    function deleteProductID(productID){
        const deleteIndex = productDataList.findIndex(product => product.id == productID);
        if(deleteIndex > -1) {
            productDataList.splice(deleteIndex, 1);
        }

        setProductList(productDataList);
    }

    function openEditProductModal(productID) {
        const editProductIndex = productDataList.findIndex(product => product.id == productID);
        if(editProductIndex == -1) {
            return;
        }

        const editProduct = productDataList[editProductIndex];
        $('#edit_product_modal').modal('show');
        $('#edit_product_modal').find('.modal-title').text("Edit Product");

        $('#edit_product_id').val(editProduct.id);
        $('#edit_product_name').val(editProduct.name);
        $('#edit_product_weight').val(editProduct.weight_kg);
        $('#edit_product_length').val(editProduct.length_cm);
        $('#edit_product_width').val(editProduct.width_cm);
        $('#edit_product_height').val(editProduct.height_cm);

        $("#edit_product_save_button").click(event => {
            event.preventDefault();

            const notValid = !$("#edit_product_form")[0].checkValidity();
            if(notValid) {
                event.stopPropagation();
                showErrorSnackbar("Make sure all product fields are properly filled.");
                return;
            }

            $(".progress").show();
            $.ajax({
                type: POST_REST_CALL,
                dataType: JSON_DATATYPE, 
                url: UPDATE_PRODUCT_URL,
                data: $('#edit_product_form').serialize(),
                success: response => {
                    showSuccessSnackbar("Product Updated");

                    updateProductInList(response.data);

                    $(".progress").hide(); 
                    $('#edit_product_form')[0].reset();
                    closeEditProductModal();
                
                },
                error: error => {
                    console.log(error);
                    showErrorSnackbar(error.responseJSON.message);
                    $(".progress").hide();   
                }
            });
        });
    }

    function closeEditProductModal(){
        $("#edit_product_save_button").off('click');
        $('#edit_product_modal').modal('hide');
    }

    function showSuccessSnackbar(message){
        var options = {
            content: `SUCCESS: ${message}`,
            timeout: SNACK_BAR_DUR_MSEC
        }

        $.snackbar(options);
    }

    function showErrorSnackbar(message){
        var options = {
            content: `ERROR: ${message}`,
            timeout: SNACK_BAR_DUR_MSEC
        }

        $.snackbar(options);
    }

    function showSnackbar(message){
        var options = {
            content: `${message}`,
            timeout: SNACK_BAR_DUR_MSEC
        }

        $.snackbar(options);
    }
});