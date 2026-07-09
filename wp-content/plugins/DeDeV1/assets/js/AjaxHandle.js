import AlertConfirm from "./AlrertConfirm";
export default function AjaxHandle(formDate = '') {
    return new Promise(function (Resolve, Reject) {
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formDate,
            success: function (res) {
                let response = res.data;
                let status = res.success;
                if (status) {
                    Resolve(response.data);
                    AlertConfirm(response.message , response.options);
                } else {
                    alert(response.message)
                    Reject(response.message);
                }
            }
        });
    });
}
