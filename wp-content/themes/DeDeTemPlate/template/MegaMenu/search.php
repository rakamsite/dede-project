<div id="search-container"
     class="fixed top-0 z-50 p-4 overflow-x-hidden overflow-y-auto w-full h-full items-center flex flex-col gap-5 hidden ">
    <div id="search-box" class="w-full">
        <div class="container relative w-full mx-auto">
            <div class="w-full text-[50px] font-[700] text-white flex items-center pb-1">
                <p class="w-full text-center ">جست و جو</p>
                <button class="open_search_box rounded-full border border-white justify-self-end w-fit h-fit block">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="search_form_main_page_dede">
                <label for="search_input"></label>
                <input id="search_input" name="search_input"
                       class="rounded-lg w-full border-[1px] border-black p-5 text-lg font-bold placeholder:text-gray-700 "
                       placeholder="جست و جو ..."/>
                <button class="absolute left-4 top-[85px]" type="submit" id="search_button">
                    <svg aria-hidden="true" width="48" height="48"
                         class="searching animate-spin text-white fill-blue-600 hidden"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                              fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                              fill="currentFill"/>
                    </svg>
                    <svg width="47" height="48" viewBox="0 0 47 48" fill="none" class="searchbutton"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.5938 43.7494L31.5018 32.4214C34.1673 29.1533 35.4964 24.9616 35.2128 20.7182C34.9292 16.4748 33.0546 12.5064 29.979 9.63863C26.9035 6.77081 22.8637 5.22436 18.7001 5.32098C14.5365 5.4176 10.5697 7.14984 7.62481 10.1574C4.67995 13.1649 2.98379 17.2161 2.88919 21.4683C2.79459 25.7205 4.30882 29.8462 7.11688 32.9872C9.92495 36.1282 13.8106 38.0427 17.9656 38.3323C22.1206 38.622 26.225 37.2645 29.425 34.5424L40.517 45.8704L42.5938 43.7494ZM5.87503 21.8704C5.87503 19.2003 6.6503 16.5902 8.10279 14.3702C9.55529 12.1501 11.6198 10.4198 14.0352 9.39798C16.4506 8.3762 19.1084 8.10885 21.6726 8.62975C24.2368 9.15065 26.5922 10.4364 28.4408 12.3244C30.2895 14.2124 31.5485 16.6179 32.0585 19.2366C32.5686 21.8554 32.3068 24.5698 31.3063 27.0366C30.3058 29.5034 28.6115 31.6118 26.4377 33.0952C24.2639 34.5786 21.7082 35.3704 19.0938 35.3704C15.5891 35.3664 12.2292 33.9428 9.75101 31.4119C7.27285 28.881 5.87892 25.4496 5.87503 21.8704Z"
                              fill="#BCBCBC"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <div id="main_container_result"
         class="container overflow-y-auto relative mx-auto pb-5 bg-white rounded-lg shadow-lg hidden">
        <table class="container mx-auto border-separate border-spacing-y-4">
            <thead class="w-full hidden md:table-header-group" id="searchHeaderTable">
            <tr class=" text-[#525252] w-full bg-[#F2F2F2] rounded-lg mb-4 text-center">
                <th scope="col" class="p-4 hidden md:block">تصویر</th>
                <th scope="col">نام کالا</th>
                <th scope="col"> کد کالا</th>
                <th scope="col">فی (ریال)</th>
                <th scope="col">واحد اصلی</th>
                <th scope="col">موجودی</th>
                <th scope="col">مشاهده</th>
            </tr>
            </thead>
            <tbody class="space-y-4 w-full pb-5" id="result_container">
            </tbody>
        </table>
        <div class="flex items-center justify-center w-full h-full absolute bg-gray-900/50 bottom-1/2 translate-y-1/2 z-[80] rounded-lg hidden loading_search_box">
            <svg aria-hidden="true" width="80" height="80" class="animate-spin text-white fill-gray-600"
                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentFill"/>
            </svg>
        </div>

        <nav aria-label="Page navigation product list and price" class="container mx-auto pagination-search-list-and-price" style="list-style: none">
            <ul class="!list-none flex items-center justify-center gap-2 w-full -space-x-px h-8 text-sm">
                <li>
                    <button id="firstPagination"
                            class="flex items-center justify-center px-1 md:px-3 h-8 gap-1 leading-tight bg-white border border-gray-300 rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m6 17l5-5l-5-5m7 10l5-5l-5-5"/>
                        </svg>
                        <span>اولین</span>
                    </button>
                </li>
                <li id="prevPage">
                    <button class="flex items-center justify-center px-3 h-8 ms-0 leading-tight bg-white border border-gray-300 rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg"  class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m9 18l6-6l-6-6"/>
                        </svg>
                        <span>قبل</span>
                    </button>
                </li>
                <li id="pointer">
                    <button class="flex items-center justify-center px-1 md:px-3 h-8 leading-tight text-[#2F2483] bg-white border border-[#2F2483] rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white">
                        ...
                    </button>
                </li>
                <li id="nextPage">
                    <button class="flex items-center justify-center px-1 md:px-3 h-8 ms-0 leading-tight bg-white border border-gray-300 rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white">
                        <span>بعد</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m15 18l-6-6l6-6"/>
                        </svg>
                    </button>
                </li>
                <li>
                    <button id="lastPagination"
                            class="flex items-center justify-center px-1 md:px-3 h-8 gap-1 leading-tight bg-white border border-gray-300 rounded-lg hover:bg-[#2F2483] hover:text-white focus:bg-[#2F2483] focus:text-white">
                        <span>آخرین</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m11 17l-5-5l5-5m7 10l-5-5l5-5"/>
                        </svg>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</div>