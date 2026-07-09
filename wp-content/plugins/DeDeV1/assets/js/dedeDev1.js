import AjaxHandle from "./AjaxHandle";
import AlertConfirm from "./AlrertConfirm";
jQuery(document).ready(function ($) {
    const Exel = $("#exel_importer");
    const PDF = $("form#PDF_exporter");
    const email = $("form#woocommerce_Email");
    const upload_exel = $('input#dede_exel_file_for_upload');
    let selet_header_email_img = $('#selet_header_email_img')
    let importer_log_div = $('#importer_log_div');
    let importer_log = $('#importer_log');
    let select_meta_data = $("#select_meta_data");
    let type_of_exel = $("#type_of_exel");
    let post_meta_data = {};
    let waiting_for_response = $("#waiting_for_response")

    type_of_exel.on('change' , function (){
        let val = $(this).val();
        if (val === "variable"){
            post_meta_data = {
                "product":"variable",
                "sku": "Mother SKU",
                "term_name": "دسته",
                "post_title": "عنوان فارسی محصول",
                "menu_order": "موقعیت",
                "attribute_name_1": "P1 Title",
                "attribute_value_1": "P1 Values",
                "attribute_default_1": "P1 Default",
                "attribute_name_2": "P2 Title",
                "attribute_value_2": "P2 Values",
                "attribute_default_2": "P2 Default",
                "post_excerpt": "Short Mother Description (MS-word Link)",
                "post_content": "Long Mother Description (MS-word Link)",
                "images": "images (TXT link)",
                "yoast-google-preview-title-metabox": "ستون عنوان SEO",
                "focus-keyword-input-metabox": "ستون کلمه کلیدی",
                "yoast-google-preview-description-metabox": "ستون توضیحات متا",
                "_shy_product_inventory_reduction_factor": "ضریب کاهنده",
            }
        }else if (val === "variation"){
            post_meta_data = {
                "product":"variation",
                "ajax_code":"AJAX-Code",
                "sku": "SKU",
                "mother_sku": "SKU Mother",
                "main_unit": "واحد اصلی",
                "_shy_product_inventory_reduction_factor": "حداقل و ضریب مقدار خرید",
                "sub_unit_name_1": "واحد فرعی 1",
                "sub_unit_value_1": "مقدار واحد اصلی در واحد فرعی 1",
                "sub_unit_name_2": "واحد فرعی 2",
                "sub_unit_value_2": "مقدار واحد اصلی در واحد فرعی 2",
                "attribute_name_1": "P1 Title",
                "attribute_value_1": "P1 Value",
                "attribute_name_2": "P2 Title",
                "attribute_value_2": "P2 Value",
                "visible": "قابل مشاهده بودن",
                "on_back_order": "مجاز به پیشفروش",
                "updated": "آپدیت",
                "content": "Short Description (HTML)",
                "image": "Image",
                "_shy_product_guid":"GUID"
                

            }
        }
    });

    upload_exel.on('click tap', function (e) {
        e.preventDefault();
        let mediaUploader = wp.media({
            title: 'انتخاب یا آپلود exel',
            button: {
                text: 'انتخاب'
            },
            multiple: false
        });

        mediaUploader.on('close', function () {
            let attachment = mediaUploader.state().get('selection').first().toJSON();
            upload_exel.val(attachment.id)
        });
        mediaUploader.open();
    });

    selet_header_email_img.on('click tap', function (e) {
        e.preventDefault();
        let mediaUploader = wp.media({
            title: 'انتخاب یا آپلود تصویر',
            button: {
                text: 'انتخاب'
            },
            multiple: false
        });

        mediaUploader.on('close', function () {
            let attachment = mediaUploader.state().get('selection').first().toJSON();
            $("#image_id").val(attachment.id);
            $("#prev_header_image").prop('src' , attachment.url)
        });
        mediaUploader.open();
    });

    function createSelector(key) {
        let selectors = '<select name="type_of_row_metadata[]" class="dev-w-full">';
        for (let selector in post_meta_data) {
            if (key === post_meta_data[selector]){
                selectors += `<option value="${selector}" selected>${post_meta_data[selector]}</option>`;
            }else {
                selectors += `<option value="${selector}">${post_meta_data[selector]}</option>`;
            }
        }
        selectors +='</select>';
        return selectors;
    }

    Exel.on('submit', function (e) {
        waiting_for_response.removeClass("dev-hidden")
        e.preventDefault();
        let formDate = new FormData(this);
        AjaxHandle(formDate).then((res) => {
            if (res === "exists"){
                importer_log_div.removeClass("dev-hidden");
                let logger = setInterval(function() {
                    checkForChanges('assets/js/import-log.txt');
                }, 5000);
                return;
            }
            select_meta_data.removeClass('dev-hidden').html(`                
                <input type="hidden" name="action" value="dede_dev_exel_extractor">
                <input type="hidden" name="dede_dev_request_type" value="import">
                <button class="dev-w-full dev-bg-[#0058BF] dev-p-4 dev-text-white dev-ring-none dev-border-none lg:dev-col-span-2 dev-rounded-t-lg">وارد کردن</button>
            `);
            for (let key in res) {
                select_meta_data.append(`<div class="dev-flex dev-basis-full md:dev-basis-1/2 dev-px-5">
                    <label class="dev-w-full">
                        <p>${key}</p>
                    </label>
                    ${createSelector(key)}
                </div>`);
            }
            waiting_for_response.addClass("dev-hidden")
        });
    });

    select_meta_data.on('submit' ,  function (e){
        e.preventDefault();
        let confirm_message="از صحیح بودن اطلاعات اطمینان حاصل کنید. به علت طولانی بودن پروسه و اجرا شدن در پس زمینه سرور ، قابل توقف نمیباشد.";
        AlertConfirm( confirm_message , {"accept": "تایید میکنم", "denide":"دوباره برسی میکنم"}).then((res)=>{

            $(this).addClass("dev-hidden");
            importer_log_div.removeClass("dev-hidden");
            let formData = new FormData(this);
            formData.append('dede_exel_file_for_upload', upload_exel.val())
            formData.append('type_of_exel', type_of_exel.val());
            importer_log.val('');
            AjaxHandle(formData).then(function (res){
                clearInterval(logger);
            });
            let logger = setInterval(function() {
                checkForChanges('assets/js/import-log.txt');
            }, 5000);

        }).catch(()=>{
            return false;
        })
    });

    async function checkForChanges(filePath) {
        try {
            let response = await $.ajax({
                url: ajax_admin.plugin_path + filePath,
                dataType: "text"
            });
            importer_log.html(response);
            if (importer_log.length) {
                importer_log.scrollTop(importer_log[0].scrollHeight - importer_log.height());
            }
        } catch (error) {
            console.error("Error fetching file content:", error);
        }
    }

    PDF.on("submit" , function (e){
        let linkUrl = $("#PDF_link_url").val();
        waiting_for_response.removeClass("dev-hidden")
        e.preventDefault();
        let formData = new FormData(this);
        AjaxHandle(formData).then(function (res){
            window.open(linkUrl, '_self');
            waiting_for_response.addClass("dev-hidden")
        });
    });

    email.on('submit' , function (e){
        e.preventDefault();
        waiting_for_response.removeClass("dev-hidden")
        let formData = new FormData(this);
        let content = tinyMCE.activeEditor.getContent();
        formData.append("description", content);
        AjaxHandle(formData).then(function (res){
            console.log(res);
            waiting_for_response.addClass("dev-hidden")
        });
    });
});