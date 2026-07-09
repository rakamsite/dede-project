import { Modal } from 'flowbite';
export default function AlertConfirm (message , options){
    const Alert_confirm = $("div#Alert_confirm");
    const Alert_text = Alert_confirm.find("p#prompt_text");
    const Alert_button_accept = Alert_confirm.find("button#prompt_button_accept");
    const Alert_button_denide = Alert_confirm.find("button#prompt_button_denide");
    Alert_text.text(message);
    Alert_button_accept.text(options.accept);
    console.log(typeof options.denide)
    if (typeof options.denide === "string"){
        Alert_button_denide.removeClass("dev-hidden").text(options.denide);
    }else{
        Alert_button_denide.addClass("dev-hidden");
    }
    const modalOptions = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'dev-bg-gray-900/50 dark:dev-bg-gray-900/80 dev-fixed dev-inset-0 dev-z-20',
        closable: true,
    };
    const modal = new Modal(Alert_confirm[0], modalOptions);
    modal.show();
    return new Promise(function (resolve, reject){
        Alert_button_accept.on("click tap" , function (){
            modal.hide();
            resolve(true);
        });
        Alert_button_denide.on("click tap" , function (){
            modal.hide();
            reject(true);
        });
    });
}