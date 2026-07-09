jQuery(document).ready(function ($) {
    let body = $('body');
    const search_container = $('div#search-container');
    let searching = $("svg.searching");
    let searchbutton = $("svg.searchbutton");
    let product_list = $("tbody#result_container");
    let main_container_result = $("#main_container_result");
    let pointer = $("#pointer");
    let nextPage = $('#nextPage');
    let prevPage = $('#prevPage');
    let firstPagination = $("#firstPagination");
    let lastPagination = $("#lastPagination");
    let pageSearched = 1;
    let search_query;
    let pagination;
    if ($(".pagination-search-list-and-price").length > 0){
        pagination = $(".pagination-search-list-and-price");
    }
    const search_options = {
        backdrop: 'dynamic',
        backdropClasses: 'bg-[#383838] bg-opacity-50 fixed inset-0 z-30',
        closable: true,
        onShow:function (){
          body.addClass('!overflow-hidden');
        },
        onHide:function (){
            main_container_result.addClass("hidden");
            loading_search_box.addClass('hidden');
            body.removeClass('!overflow-hidden');
        }
    };
    const searchBox = new Modal(search_container[0], search_options)
    let loading_search_box = $("div.loading_search_box");

    body.on('click tap', 'button.open_search_box', function () {
        searchBox.toggle();
    });

    function pointerElementGenerator(counter, starter) {
        pointer.nextUntil(nextPage).remove();
        pointer.prevUntil(prevPage).remove();

        if (window.screen.width <= "640") {
            maxCountable = 3;
            firstMove = 2;
            firstMoveStart = 1;
        } else {
            maxCountable = 10;
            firstMove = 6;
            firstMoveStart = 5;
        }
        if (counter < maxCountable) {
            pointer.addClass('hidden');
            for (let i = 1; i <= counter; i++) {
                pointer.before(`
                    <li>
                        <button value="${i}" class="flex items-center justify-center px-2 md:px-3 h-8 leading-tight text-[#2F2483] bg-white border border-[#2F2483] rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white pagination_button">
                            ${i}
                        </button>
                    </li>
                `);
            }
        }
        if (counter > maxCountable) {
            if (starter > firstMove) {
                starter = starter - firstMoveStart;
            }else{
                starter = 1;
            }
            for (let i = starter, b = 1; i <= counter - 1 && b <= maxCountable-1; i++ , b++) {
                if (b === maxCountable) {
                    break;
                }
                pointer.before(`
                    <li>
                        <button value="${i}" class="flex items-center justify-center px-2 md:px-3 h-8 leading-tight text-[#2F2483] bg-white border border-[#2F2483] rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white pagination_button">
                            ${i}
                        </button>
                    </li>
                `);
            }
            pointer.after(`
                 <li>
                     <button value="${counter}" class="flex items-center justify-center px-2 md:px-3 h-8 leading-tight text-[#2F2483] bg-white border border-[#2F2483] rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white pagination_button">
                         ${counter}
                     </button>
                 </li>
            `);
            lastPagination.val(counter);
        }
    }

    function getFormResult(formDate) {
        loading_search_box.removeClass('hidden');
        $("input#search_input").blur();
        if ('virtualKeyboard' in navigator) {
            navigator.virtualKeyboard.hide();
        }
        $.ajax({
            url: ajax_admin.ajax_url,
            type: "post",
            data: formDate,
            success: function (res) {
                if (res.success === true) {
                    let html = res.data.html;
                    let pages = res.data.pages
                    if (html === ""){
                        alert('نتیجه ای برای کلمه جستوجو شده یافت نشد.');
                    }
                    product_list.html(html);
                    if (pages > 1 ){
                        pagination.removeClass('hidden');
                        pointerElementGenerator(pages, pageSearched);
                    }else{
                        pagination.addClass('hidden');
                    }
                    $(`button[value=${pageSearched}]`).focus();
                } else {
                    alert(res.data)
                }
                document.getElementById('searchHeaderTable').scrollIntoView();
            },
            error: function (error) {
                console.log(error);
            },
            complete:function (){
                main_container_result.removeClass("hidden");
                loading_search_box.addClass('hidden');
            }
        });
    }

    function searchAction () {
        pageSearched =1;
        search_query = $("input#search_input").val();
        if (search_query ===''){
            searching.addClass('hidden');
            searchbutton.removeClass('hidden');

            return;
        }
        if (search_query.length !== 0) {
            let formData = {
                action: 'product_list_and_price',
                searchProduct: true,
                search_query
            }
            getFormResult(formData);
            searching.addClass('hidden');
            searchbutton.removeClass('hidden');
        } else {
            alert("لطفا کلید واژه برای جست و جو وارد کنید.")
        }
    }

    $("input#search_input").on("input" ,function (){
        searching.removeClass('hidden');
        searchbutton.addClass('hidden');
        let search = setTimeout(searchAction, 1000);
        $(this).on("input", function () {
            clearTimeout(search);
        });
    });

    $("form#search_form_main_page_dede").on("submit", (e)=>{
        e.preventDefault();
        searchAction()
    });

    body.on('click tap', '.pagination_button', function () {
        search_query = $("input#search_input").val();
        let page = $(this).val();
        pageSearched = page;
        let formData = {
            action: 'product_list_and_price',
            searchProduct: true,
            pageSearched: page
        }
        if (search_query.length !== 0) {
            formData.search_query = search_query;
        }
        getFormResult(formData, page);
    });

    firstPagination.on("click tap", function () {
        search_query = $("input#search_input").val();
        pageSearched = 1;
        let formData = {
            action: 'product_list_and_price',
            searchProduct: true,
            pageSearched: pageSearched
        }
        if (search_query.length !== 0) {
            formData.search_query = search_query;
        }
        getFormResult(formData, pageSearched);

    });

    lastPagination.on("click tap", function () {
        search_query = $("input#search_input").val();
        pageSearched = $(this).val();
        let formData = {
            action: 'product_list_and_price',
            searchProduct: true,
            pageSearched: pageSearched
        }
        if (search_query.length !== 0) {
            formData.search_query = search_query;
        }
        getFormResult(formData, pageSearched);
    });

    prevPage.on('click tap', function () {
        search_query = $("input#search_input").val();
        if (pageSearched === 1) {
            return;
        }
        pageSearched--;
        let formData = {
            action: 'product_list_and_price',
            searchProduct: true,
            pageSearched: pageSearched
        }
        if (search_query.length !== 0) {
            formData.search_query = search_query;
        }
        getFormResult(formData, pageSearched);
    });

    nextPage.on('click tap', function () {
        search_query = $("input#search_input").val();

        if (pageSearched === lastPagination.val()) {
            return;
        }
        pageSearched++;
        let formData = {
            action: 'product_list_and_price',
            searchProduct: true,
            pageSearched: pageSearched
        }
        if (search_query.length !== 0) {
            formData.search_query = search_query;
        }
        getFormResult(formData, pageSearched);
    })
});