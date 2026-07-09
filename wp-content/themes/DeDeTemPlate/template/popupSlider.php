<?php

$popup_data = get_option('DeDePopUp');
$popUpDiv = '';
$popup_button = '';
if (empty($popup_data)) {
    return;
}
$popup_length = count($popup_data);
for ($i = 1; $i <= $popup_length; $i++) {
    foreach ($popup_data['dede_main_popup_' . $i] as $item) {
        $popUpDiv .= '<div id="Button' . $i . 'Popup" class="absolute z-50 container left-1/2 -translate-x-1/2 md:rounded-lg dede_popup_div popupDiv hidden overflow-y-auto"><button class="absolute top-2 right-0-2"><svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.5" cy="16.5" r="16.5" fill="#2F2483"></circle><rect x="23.782" y="7.66553" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(45 23.782 7.66553)" fill="white"></rect><rect x="25.9026" y="23.7817" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(135 25.9026 23.7817)" fill="white"></rect></svg></button><div class="px-3 py-2 bg-[#2F2483] h-full grid grid-cols-2 md:grid-cols-3 justify-content-center overflow-y-auto"><div class="col-span-2 flex flex-col items-center text-center font-bold text-white pt-5 md:justify-center space-y-1"><p class="text-[18px] font-bold md:text-3xl">' . esc_html($item["dede_title_popup_$i"]) . '</p><p class="md:text-xl">' . wpautop($item["dede_description_popup_$i"]) . '</p></div><div class=" h-auto w-fit rounded-lg hidden md:flex justify-center self-center "><img class="object-fill aspect-square w-1/2 rounded-lg" src="' . esc_url($item["dede_popup_image_url_$i"]) . '" alt="' . esc_html($item["dede_title_popup_$i"]) . '"></div></div></div>';
        $popup_button .= '<div class="flex justify-center overflow-x-hidden"><svg class="absolute -mt-[45px] hidden rotate-180 popupArrow" width="50" height="50" viewBox="0 0 115 115" fill="#2F2483" xmlns="http://www.w3.org/2000/svg">
    <rect y="57.2756" width="50" height="50" transform="rotate(-45 0 57.2756)" fill="#2F2483"/>
</svg>
<button id="Button' . $i . '" type="button"  class="w-fit md:w-full rounded-full md:rounded-[20px] bg-[#2F2483] text-center text-white flex items-center justify-center p-3 md:p-5 text-lg hover:bg-[#E3000F] focus:bg-[#E3000F] popupButton"><img src="' . esc_url($item["dede_button_icon_$i"]) . '" class="filter-white object-fill md:ml-3 md:w-7 md:h-7 h-7 h-12 w-auto" alt="' . esc_html($item["dede_button_text_$i"]) . '" /><p class="hidden md:block">' . esc_html($item["dede_button_text_$i"]) . '</p></button></div>';
    }
}
?>
<div class="w-full popupUnderSlider">
    <?php echo $popUpDiv; ?>
    <div class="container mx-auto grid grid-cols-4 gap-5 py-5">
        <?php echo $popup_button; ?>
    </div>
</div>
