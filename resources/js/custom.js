const choiceOption = {
    searchEnabled: true,
    searchChoices: true,
    shouldSort: false,
    searchPlaceholderValue: "Tìm kiếm...",
    removeItemButton: true,
    searchResultlimit: 1000,
    noResultsText: "Không có kết quả",
    noChoicesText: "Không có lựa chọn",
    placeholder: true,
    loadingText: "Đang tìm thông tin...",
    itemSelectText: "Chọn",
    fuseOptions: {
        keys: ["label"],
        threshold: 0.2,
        ignoreLocations: true,
        minMatchCharLength: 1,
        includeScore: true,
    },
};
const choiceOptions = {
    searchEnabled: true,
    searchChoices: true,
    shouldSort: false,
    searchPlaceholderValue: "Tìm kiếm...",
    removeItemButton: true,
    searchResultlimit: 1000,
    noResultsText: "Không có kết quả",
    noChoicesText: "Không có lựa chọn",
    placeholder: true,
    loadingText: "Đang tìm thông tin...",
    itemSelectText: "Chọn",
    fuseOptions: {
        keys: ["label"],
        threshold: 0.2,
        ignoreLocations: true,
        minMatchCharLength: 1,
        includeScore: true,
    },
};
const phoneRegex = /^0\d{1,3}[\s.-]?\d{3,4}[\s.-]?\d{3,4}$/;
const flatPickerOptions = {
    dateFormat: "d-m-Y",
    disableMobile: true,
    locale: "vn",
    shorthandCurrentMonth: true,
};

const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

// Hàm Xử Lý delay
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}
// Hàm sử lý format Tiền tệ
const formatBalance = (event) => {
    let balance = event.target.value.replace(/[^\d.-]/g, "");
    let index = balance.indexOf(".", balance.indexOf(".") + 1);
    if (index !== -1) {
        balance = balance.substring(0, index);
    }
    if (balance === "") {
        event.target.value = balance;
    } else {
        balance = formatNumber(balance);
        event.target.value = balance;
    }
};
const handleInputBalance = (event) => {
    delayedBalanceHandler(event);
};
// hàm sử lý tiền tệ kèm thêm đơn vị"tiền VN"
function priceToText(price) {
    price = price || 0;
    let priceText = '';
    let priceUnit = '';

    if (price >= 1000000000) {
        priceText = formatNumber(price / 1000000000, 0);
        priceUnit = 'Tỷ';
    }
    else if (price >= 1000000) {
        priceText = formatNumber(price / 1000000, 0);
        priceUnit = 'Triệu'
    }
    else if (price >= 1000) {
        priceText = formatNumber(price / 1000, 0);
        priceUnit = 'Nghìn'
    } else {
        priceText = formatNumber(price, 0);
    }
    priceUnit = priceUnit === '' ? '' : priceUnit;
    return `${priceText} ${priceUnit}`;
}

/**
 * @param {string} -Nhận vào 1 chuỗi API
 */
// format API. Hàm có tác dụng format lại API khi truyền quá nhiều param
function formatAPI(api) {
    // Đầu tiên tìm dấu ? đầu tiên và sau đó thay thế các dấu ? khác bằng &
    let foundFirstQuestionMark = false;
    let modifiedApi = api.replace(/\?(?=\w+=)/g, (match, offset) => {
        if (!foundFirstQuestionMark) {
            foundFirstQuestionMark = true;
            return match;
        }
        return '&';
    });
    return modifiedApi;
}

// Sử dụng debounce để tạo độ trễ cho sự kiện oninput
const delayedBalanceHandler = debounce(formatBalance, 1000);

/**
 * Format a number string into a custom format with grouping and decimal separators.
 *
 * @param {string} numberString - The number string to be formatted.
 * @param {number} [max=0] - The maximum number of decimal places to display. Default is 0.
 * @param {string} [groupSeparator=','] - The character used to separate groups of digits. Default is ','.
 * @param {string} [decimalSeparator='.'] - The character used to separate the integer and decimal parts. Default is '.'.
 * @returns {string} - The formatted number string.
 * @throws {Error} - If the input number string is not a valid number.
 * @example
 * formatNumber('1234567.89', 2, '.', ',') => '1,234,567.89'
 * formatNumber('9876543.21', 0, ',', '.') => '9,876,543'
 */
function formatNumber(numberString, max = 0, groupSeparator = ',', decimalSeparator = '.') {
    const number = parseFloat(numberString);
    if (isNaN(number)) {
        throw new Error("Invalid number string");
    }
    const options = {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
        useGrouping: true,
    };
    const formattedNumber = number.toLocaleString("en-US", options);

    const customFormattedNumber = formattedNumber
        .replace(/,/g, groupSeparator)
        .replace(/\./g, decimalSeparator);

    return customFormattedNumber;
};

const removeCommas = (numberString) => {
    const cleanedString = numberString.replace(/,/g, "");
    const number = parseInt(cleanedString, 10);
    return number;
};

const showErrorMD = (content, time = 4500) => {
    let modal = document.getElementById("modalErrorNotification");
    let myModal = new bootstrap.Modal(modal, {
        keyboard: false,
        backdrop: false,
    });
    let element = modal.getElementsByClassName("error-message")[0];
    element.innerHTML = content;
    myModal.show();
    setTimeout(() => {
        element.innerHTML = "";
        myModal.hide();
    }, time);
};

const changeSizeText = (text, maxLength = 100) => {
    if (text.length > maxLength) {
        text = text.substring(0, maxLength) + "...";
    }
    return text;
};

/**
 * Changes the validation message for an input element.
 *
 * @param {HTMLElement} element - The input element to change the message for.
 * @param {boolean} [error=false] - If true, the message will be an error message. If false, the message will be a success message.
 * @param {string} [message=""] - The message to display.
 * @param {Array<string>} [listClass=["p-2", "small"]] - Additional classes to add to the error message element.
 * @returns {void}
 */
function changeValidateMessage(
    element,
    error = false,
    message = "",
    listClass = ["p-2", "small"]
) {
    const isChoicesSelect = element.tagName.toLowerCase() === "select" && element.classList.contains("choices__input");
    if (isChoicesSelect) {
        element = element.parentNode.parentNode;
    }
    let parentNode = element.parentNode;

    let classError = "show-error";
    let errorElement = parentNode.querySelector(`.${classError}`);

    if (error) {
        if (errorElement) {
            errorElement.innerHTML = message;
        } else {
            errorElement = document.createElement("div");
            errorElement.classList.add(classError, "text-danger", ...listClass);
            errorElement.innerHTML = message;
            parentNode.appendChild(errorElement);
        }
    } else if (errorElement) {
        parentNode.removeChild(errorElement);
    }
    changeClassValidate(element, error, isChoicesSelect);
}

/**
 * Changes the class of an element based on validation error status.
 *
 * @param {HTMLElement} element - The HTML element to change the class of.
 * @param {boolean} [error=false] - Indicates if the element is in an error state. Default is false.
 * @param {boolean} [isChoiceJs=false] - Indicates if the element is a Choice.js element. Default is false.
 *
 * @returns {void}
 */
function changeClassValidate(element, error = false, isChoiceJs = false) {
    const choiceJsClasses = ["custom-validate", error ? "invalid" : "valid"];
    const formClasses = [error ? "form-error" : "form-success"];
    if (isChoiceJs) {
        element.classList.remove("valid", "invalid");
    } else {
        element.classList.remove("form-error", "form-success");
    }
    if (isChoiceJs) {
        element.classList.add(...choiceJsClasses);
    } else {
        element.classList.add(...formClasses);
    }
}
//  hàm validation cho ô inputs
const validateNotEmpty = (element, length = null) => {
    let value = element.value.trim();
    let valid = true;
    if (value === "") {
        changeValidateMessage(
            element,
            true,
            `${element.placeholder} không dược để trống!`
        );
        valid = false;
    } else if (length) {
        valid = validateLength(element, length);
    }
    if (valid) {
        changeValidateMessage(element, false);
    }
    return valid;
};

const validateLength = (element, length) => {
    let value = element.value.trim();
    if (length.min || length.max) {
        if (value.length < length.min) {
            changeValidateMessage(
                element,
                true,
                `${element.placeholder} phải có ít nhất ${length["min"]} ký tự!`
            );
            return false;
        }
        if (value.length >= length.max) {
            changeValidateMessage(
                element,
                true,
                `${element.placeholder} không được nhiều hơn ${length["max"]} ký tự!`
            );
            return false;
        }
    }
    return true;
};
// hàm validation cho selection
const validateSelectOption = (element, text) => {
    if (element.value.trim() == "") {
        changeValidateMessage(element, true, `Vui lòng chọn ${text}!`);
        return false;
    } else {
        changeValidateMessage(element, false,);
        return true;
    }
};

/**
 * Removes all validation classes from the form elements.
 *
 * @param {HTMLElement} element - The form element from which to remove the validation classes.
 * @returns {void}
 */
function removeAllValidationClasses(element) {
    let elementsWithErrors = element.querySelectorAll(
        ".invalid, .valid, .form-success, .form-error"
    );
    elementsWithErrors.forEach(function (ele) {
        ele.classList.remove("invalid", "valid", "form-success", "form-error");
    });
    let errorMessages = element.querySelectorAll(".show-error");
    errorMessages.forEach((ele) => ele.remove());
}

const BuildPagination = (data, element, functionName) => {
    let html = `<div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                ${data.from} đến ${data.to
        }<span class="text-body-tertiary"> Trong </span> ${data.total}
                </p>
                <a class="btn-link" href="javascript:" title="Tất cả" ${data.all ? "hidden" : ""
        } onclick="${functionName}('${data.url + data.params + 1 + "&show_all=true"
        }')">
                    Tất cả<span class="fas fa-angle-right ms-1"></span>
                </a>
                <a class="btn-link" href="javascript:" title="Thu gọn" ${data.all ? "" : "hidden"
        } onclick="${functionName}('${data.url + data.params + 1}')">
                    Thu gọn<span class="fas fa-angle-left ms-1"></span>
                </a>
            </div>
                <nav class="col-auto d-flex">
                    <ul class="mb-0 pagination justify-content-end">
                        <li class="page-item ${data.currentPage <= 1 ? "disabled" : ""
        }">
                            <a class="page-link ${data.currentPage <= 1 ? "disabled" : ""
        }" ${data.currentPage <= 1 ? 'disabled=""' : ""
        } href="javascript:" title="Trang trước" onclick="${functionName}('${data.url + data.params + (data.currentPage - 1)
        }')">
                                <span class="fas fa-chevron-left"></span>
                            </a>
                        </li>`;
    html += getPage(data);
    html += `
                <li class="page-item ${data.currentPage >= data.totalPages ? "disabled" : ""
        }">
                    <a class= "page-link ${data.currentPage >= data.totalPages ? "disabled" : ""
        }"  href="javascript:" title="Trang sau"  onclick="${functionName}('${data.url + data.params + (data.currentPage + 1)
        }')" >
                    <span class= "fas fa-chevron-right"></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    `;
    element.innerHTML = html;

    function getPage(data) {
        let html = '';
        let start = (+data.currentPage - 3);
        let max = (+data.currentPage + 3);

        if (start > 1) {
            html += `<li class="page-item disabled"><a class="page-link" disabled="" title="" type="button" href="javascript:">...</a>`;
        }

        for (let index = start; index <= max; index++) {
            if (index > 0 && index <= +data.totalPages) {
                if (index == +data.currentPage) {
                    html += `<li class="page-item active"><a class="page-link" title="Trang ${index}" href="javascript:" type="button">${index}</a></li>`;
                } else {
                    html += `<li class="page-item"><a class="page-link" type="button" title="Trang ${index}" href="javascript:" onclick="${functionName}('${data.url + data.params + index
                        }')">${index}</a></li>`;
                }
            }
        }
        if (max < +data.totalPages) {
            html += `<li class="page-item disabled"><a class="page-link" disabled="" title="" type="button" href="javascript:">...</a></li>`;
        }
        return html;
    }
};

function clearAllClassValidate(element) {
    // Lấy ra tất cả các phần tử trong biểu mẫu có class là "form-errors" hoặc "invalid" ...
    let elementsWithErrors = element.querySelectorAll('.form-error, .invalid, .valid, .form-success');
    // Lặp qua từng phần tử và loại bỏ lớp "form-errors" và "invalid" ...
    elementsWithErrors.forEach(function (ele) {
        ele.classList.remove('form-error');
        ele.classList.remove('invalid');
        ele.classList.remove('valid');
        ele.classList.remove('form-success');
    });
    let elementShowError = element.querySelectorAll('.show-error');
    elementShowError.forEach(ele => {
        ele.remove();
    })
}
const showMessage = (element, message, delay = 4500) => {
    element.innerHTML = message;
    element.style.display = "block";
    setTimeout(() => {
        element.innerHTML = "";
        element.style.display = "none";
    }, delay);
};
const showMessageMD = (content, url = '', time = 4500) => {
    let modal = document.getElementById("modalSuccessNotification");
    let myModal = new bootstrap.Modal(modal, {
        keyboard: false,
        backdrop: false,
    });
    let element = modal.getElementsByClassName("success-message")[0];
    let link = modal.querySelector(".open-link");
    if (url != '') {
        link.href = url;
        link.hidden = false;
    }
    element.innerHTML = content;
    setTimeout(() => {
        myModal.show();
        setTimeout(() => {
            myModal.hide();
            element.innerHTML = "";
            link.hidden = true;
            link.href = 'javascript:';
        }, time);
    }, 400)
};

const confirmDelete = (url, title, getData) => {
    let modal = document.getElementById("modalConfirmDelete");
    let btnConfirmDelete = modal.getElementsByClassName("btn-confirm")[0];
    let myModal = new bootstrap.Modal(modal, {
        keyboard: false,
    });
    let element = modal.getElementsByClassName("confirm-message")[0];
    element.innerHTML = `Chắc chắn xoá ${title} này!`;
    myModal.show();
    btnConfirmDelete.addEventListener("click", () => {
        btnLoading(btnConfirmDelete, true);
        axios
            .delete(url)
            .then((res) => {
                if (res.data.status == 200) {
                    myModal.hide();
                    getData();
                    showMessageMD(res.data.successMessage);
                }
                myModal.hide();
                btnLoading(btnConfirmDelete, false);
            })
            .catch((error) => {
                console.log(error);
                btnLoading(btnConfirmDelete, false);
            });
    });
};


// hàm hiện loading và vo hiệu hoá nút
const btnLoading = (btn, isLoad = true, text = "") => {
    if (isLoad) {
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" style="--phoenix-spinner-width: 0.8rem;--phoenix-spinner-height: 0.8rem" role="status" aria-hidden="true"></span> ${text || btn.title}`;
    } else {
        btn.disabled = false;
        if (text) {
            btn.innerHTML = `${text}`;
        } else {
            btn.innerHTML = `${btn.title}`;
        }
    }
};


/**
 * Mảng định dạng thời gian cho việc hiển thị các khoảng thời gian theo các định dạng khác nhau.
 * Mỗi phần tử trong mảng bao gồm 3 giá trị: giới hạn thời gian, định dạng hiển thị cho khoảng thời gian đó,
 * và đơn vị thời gian tương ứng.
 */
const time_formats = [
    [60, "giây", 1], // 60
    [120, "1 phút trước", "1 phút sau"], // 60*2
    [3600, "Phút", 60], // 60*60, 60
    [7200, "1 giờ trước", "1 giờ sau"], // 60*60*2
    [86400, "giờ", 3600], // 60*60*24, 60*60
    [172800, "hôm qua", "Ngày mai"], // 60*60*24*2
    [604800, "ngày", 86400], // 60*60*24*7, 60*60*24
    [1209600, "tuần trước", "Tuần sau"], // 60*60*24*7*4*2
    [2419200, "tuần", 604800], // 60*60*24*7*4, 60*60*24*7
    [4838400, "tháng trước", "Tháng sau"], // 60*60*24*7*4*2
    [29030400, "tháng", 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
    [58060800, "năm trước", "năm sau"], // 60*60*24*7*4*12*2
    [2903040000, "năm", 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
    // [5806080000, 'thế kỷ trước', 'thế kỷ tiếp theo'], // 60*60*24*7*4*12*100*2
    // [58060800000, 'thế kỷ', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
];

function time_ago(time) {
    if (validateDate(time)) {
        return "Chưa đăng nhập";
    } else {
        let rs = "";
        time = getTime(time);
        let seconds = (+new Date() - time) / 1000,
            token = "trước",
            list_choice = 1;

        if (seconds == 0) {
            rs = "Vừa xong";
        } else if (seconds < 0) {
            seconds = Math.abs(seconds);
            token = "sau";
            list_choice = 2;
            rs = getResultOfTimeAgo(seconds, list_choice, token);
        } else {
            rs = getResultOfTimeAgo(seconds, list_choice, token);
        }
        return rs;
    }
}

const day_between = (date1, date2 = "", format = true) => {
    if (validateDate(date1)) {
        return "";
    }
    if (date2 === "") {
        date2 = new Date();
        date2.setHours(0, 0, 0, 0);
        date2 = +date2;
    }
    date1 = getDate1(date1);
    let onceDay = 60 * 60 * 24;
    let seconds = (date2 - date1) / 1000,
        token = " Ngày";
    let rs = "";
    if (format) {
        if (seconds < 0) {
            seconds = Math.abs(seconds);
            token = " Ngày sau";
        }
        if (seconds == 0) {
            return "Hôm nay";
        }
        if (seconds > 0) {
            token = " Ngày trước";
        }
        rs = Math.floor(seconds / onceDay) + token;
    } else {
        rs = Math.floor(seconds / onceDay);
    }
    return rs;
};
function getResultOfTimeAgo(seconds, list_choice, token) {
    let i = 0,
        format,
        rs = "";
    while ((format = time_formats[i++])) {
        if (seconds < format[0]) {
            if (typeof format[2] == "string") {
                rs = format[list_choice];
                break;
            } else {
                rs = Math.floor(seconds / format[2]) + " " + format[1] + " " + token;
                break;
            }
        }
    }
    if (!rs) {
        format = time_formats[time_formats.length - 1];
        rs = Math.floor(seconds / format[2]) + " " + format[1] + " " + token;
    }
    return rs;
}

function getTime(time) {
    switch (typeof time) {
        case "number":
            break;
        case "string":
            time = +new Date(time);
            break;
        case "object":
            if (time.constructor === Date) time = time.getTime();
            break;
        default:
            time = +new Date();
    }
    return time;
}

function validateDate(date1) {
    return (
        typeof date1 === "string" &&
        (date1 === "0000-00-00 00:00:00" || date1 === "0000-00-00")
    );
}
function getDate1(date1) {
    switch (typeof date1) {
        case "number":
            break;
        case "string":
            date1 = new Date(date1);
            date1.setHours(0, 0, 0, 0);
            date1 = +date1;
            break;
        case "object":
            if (date1.constructor === Date) date1 = date1.getTime();
            break;
        default:
            date1 = new Date();
            date1.setHours(0, 0, 0, 0);
            date1 = +date1;
    }
    return date1;
}

const dateFormat = (date) => {
    let currentDate = new Date(date);

    let day = currentDate.getDate().toString().padStart(2, "0"); // Add leading zero if needed
    let month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Month is zero-based
    let year = currentDate.getFullYear();

    return `${day}-${month}-${year}`;
};

// 
/**hàm có tác dụng tạo mã theo thời gian thường dùng cho số phiếu
 * @param codeDefault nhận vào  mã bắt đầu
 * @param dom nhận vào dom cần trả về
 */
const codeGeneration = (codeDefault = "MBT-PN", dom = '') => {
    // Lấy ngày giờ hiện tại
    const currentDate = new Date();

    // Lấy các thành phần của ngày giờ
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');
    const hours = String(currentDate.getHours()).padStart(2, '0');
    const minutes = String(currentDate.getMinutes()).padStart(2, '0');
    const seconds = String(currentDate.getSeconds()).padStart(2, '0');
    // Kết hợp các thành phần thành chuỗi định dạng mong muốn
    let code = `${codeDefault}-${year + month + day + hours + minutes + seconds}`;
    if (dom) {
        document.querySelector(dom).value = code;
    }
    return code;
}

const dateTimeFormat = (date, format = "d-m-Y") => {
    let currentDate;
    if (date == "0000-00-00 00:00:00" || date == "0000-00-00" || date == "") {
        return "";
    }
    if (typeof date === "string" || typeof date === "number") {
        currentDate = new Date(date);
    } else {
        return "";
    }

    let seconds = currentDate.getSeconds().toString().padStart(2, "0"); // Add leading zero if needed
    let minutes = currentDate.getMinutes().toString().padStart(2, "0"); // Add leading zero if needed
    let hours = currentDate.getHours().toString().padStart(2, "0"); // Add leading zero if needed
    let day = currentDate.getDate().toString().padStart(2, "0"); // Add leading zero if needed
    let month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Month is zero-based
    let year = currentDate.getFullYear();
    let result = format.replace("i", minutes);
    result = result.replace("s", seconds);
    result = result.replace("H", hours);
    result = result.replace("d", day);
    result = result.replace("m", month);
    result = result.replace("Y", year);
    return result;
};

const displayTime = (time, format = "H:i") => {
    const timeParts = time.split(":");
    if (timeParts.length !== 3) {
        return "Invalid time format";
    }
    const hours = timeParts[0];
    const minutes = timeParts[1];
    const seconds = timeParts[2];

    let result = format.replace("i", minutes);
    result = result.replace("s", seconds);
    result = result.replace("H", hours);
    return result;
};
/**
 * Lấy các giá trị của checkbox dựa trên tên và trạng thái checked.
 *
 * @param {string} name - Tên của các checkbox cần lấy giá trị.
 * @param {boolean} [checked=true] - Nếu true, chỉ lấy các checkbox được chọn. Nếu false, lấy tất cả các checkbox.
 * @returns {Array<string>} - Mảng các giá trị của các checkbox.
 */
function getValueCheckBox(name, checked = true) {
    let selector = `input[type="checkbox"][name="${name}"]`;
    if (checked) {
        selector += ":checked";
    }
    let checkboxes = document.querySelectorAll(selector);
    let values = Array.from(checkboxes).map(function (checkbox) {
        return checkbox.value;
    });
    return values;
}

class URLHelper {
    constructor(url = window.location.href) {
        this.url = new URL(url);
    }
    setUrl(url) {
        this.url = new URL(url);
    }
    getQueryString() {
        return this.url.search;
    }
    getLastPathSegment() {
        const parts = this.url.pathname.split('/');
        return parts.pop();
    }

    getParams() {
        // Lấy URL hiện tại từ window.location
        const params = new URLSearchParams(window.location.search);
        const paramList = {};
        for (const [key, value] of params.entries()) {
            paramList[key] = value;
        }
        return paramList;
    }

    /**Hàm có tác dụng xóa param trên url
     * @param key Nhận vào key của param đó
     */
    removeParam(key) {
        // Lấy URL hiện tại
        var url = window.location.href;

        // Tạo một URL không có tham số cần xóa
        var urlWithoutParam = url.replace(new RegExp('[?&]' + key + '=[^&#]*(#.*)?$'), '$1').replace(new RegExp('([?&])' + key + '=[^&]*&'), '$1');

        // Nếu URL đã thay đổi, cập nhật URL mới
        if (urlWithoutParam !== url) {
            window.history.replaceState({ path: urlWithoutParam }, '', urlWithoutParam);
        }
    }

    // hàm có tác dụng thêm param vào url và chuyển hướng đến trang đích
    /** 
     * @param {object} paramas nhận vào object chứa các param muốn truyền
     * ví dụ: { key1: value1, key2: value2 }
     * @param {string} [url=""] nhận vào chuỗi url
    */
    static addParamsToURL(params, url = "") {
        let currentURL;

        try {
            if (url) {
                // Xử lý URL tương đối bằng cách kết hợp với `window.location.origin`
                currentURL = new URL(url, window.location.origin);
            } else {
                // Nếu không có URL nào được cung cấp, sử dụng URL hiện tại
                currentURL = new URL(window.location.href);
            }
        } catch (error) {
            console.error("Invalid URL:", url);
            return;
        }

        // Thêm các tham số vào URL
        for (const key in params) {
            currentURL.searchParams.set(key, params[key]);
        }

        if (url) {
            // Chuyển hướng đến URL mới nếu có URL được cung cấp
            window.location.href = currentURL.href;
        } else {
            // Cập nhật URL hiện tại mà không tải lại trang
            window.history.pushState({}, '', currentURL.href);
        }
    }


}

// làm việc với form
class FormCustom {

    // nhận vào form
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.choice = {};
        this.modal = "";
        this.newModal = "";
        this.loading = "";
    }

    /**
     * khởi tạo choice
     * @param choiceObject là mộ mảng chứa tên hoặc id, class của 1 thẻ
     */
    startChoice(choiceObject) {
        // Kiểm tra xem choiceObject có phải là một mảng và không rỗng
        if (choiceObject.length > 0) {
            choiceObject.forEach(item => {
                // item có thể là selector hoặc phần tử DOM, đảm bảo đây là một phần tử hợp lệ
                const element = document.querySelector(item);

                if (element) {
                    // Khởi tạo Choices.js cho phần tử
                    const choiceInstance = new Choices(element, choiceOption);
                    this.choice[item] = choiceInstance;
                    // Lưu trữ thông tin về phần tử và đối tượng Choices.js
                }
            });
        }
    }

    /**Lắng nghe sự change và gọi API của 1 thẻ và đổ dữ liệu ra 1 thẻ khác
     * @param selectChange ID, Class của thẻ được chọn
     * @param selectEeceive ID, Class của thẻ được nhận
     * @param api api nhận lấy ra dữ liệu
     * @param key nhận vào param của api,
     * @param label là phần ky khi api trả về nhận vào value của thẻ option
     * @param value là phần ky khi api trả về nhận vào đoạn text của thẻ option
     */
    eventListenerChange(selectChange, selectEeceive, api, key, label, value) {
        // Lấy phần tử DOM cho selectChange
        let domSelectChange = document.querySelector(selectChange);
        // Tìm đối tượng Choices.js cho selectChange và selectEeceive
        let choiceSelectEeceive = this.choice[selectEeceive];
        // Kiểm tra nếu đối tượng Choices.js tồn taij
        if (choiceSelectEeceive) {
            // Thêm sự kiện change cho selectChange
            domSelectChange.addEventListener("change", (e) => {
                // Hiển thị trạng thái loading
                choiceSelectEeceive._handleLoadingState(true);
                let id = e.target.value;
                // Gửi yêu cầu GET đến API
                axios.get(`${api}?${key}=${id}`)
                    .then(res => {
                        if (res.data.status === 200) {
                            let data = res.data.data.data;
                            let dataChoice = data.map(item => ({
                                lable: item[label],
                                value: item[value]
                            }))
                            // trả dữ liệu về thẻ sleect
                            choiceSelectEeceive.setChoices(dataChoice, "value", "lable", true);
                            choiceSelectEeceive._handleLoadingState(false);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showErrorMD("Vui lòng gọi IT");
                        return;
                    })
            })
        } else {
            console.error("Vui lòng khởi tạo choice cho " + selectChange + " và " + selectEeceive);
            showErrorMD("Vui lòng gọi IT");
        }
    }

    /**Thực hiện reset form */
    reset() {
        // Lấy tất cả các phần tử trong form
        const elements = this.form.elements;
        for (let item of elements) {
            if (item.hasAttribute("data-choice")) {
                // Giả sử đối tượng Choices.js được lưu trong thuộc tính custom của element
                const choiceInstance = item.choices; // Hoặc item._choices nếu bạn lưu đối tượng Choices.js theo cách này

                if (choiceInstance) {
                    choiceInstance.removeActiveItems(); // Xóa các lựa chọn hiện tại
                }
            } else {
                if (item.tagName === "INPUT" || item.tagName === "SELECT" || item.tagName === "TEXTAREA") {
                    item.value = ""; // Đặt lại giá trị của các phần tử input, select, textarea
                }
            }
        }
        clearAllClassValidate(this.form);
    }

    /**
     * @param validations  nhận vào bản thiết kế các trường cần kiểm tra bao gồm
     * const validations = [
            {name: "tên thẻ", condition: value => điều kiện, message: "thông báo người dùng"},
        }
    */
    validate(validations) {
        const dom = {};
        const data = {};

        // Lấy ra form và kiểm tra xem form có tồn tại không
        const form = this.form;
        if (!form) {
            console.error(`Form with id ${this.form} not found.`);
            return false; // Trả về false nếu không tìm thấy form
        }

        const selects = form.querySelectorAll("select");
        const inputs = form.querySelectorAll("input");
        const textareas = form.querySelectorAll("textarea");

        // Thu thập tất cả các thẻ select, input, textarea vào đối tượng dom
        const collectElements = (elements) => {
            elements.forEach(item => {
                let name = item.getAttribute("name");
                if (name) {
                    dom[name] = item;
                }
            });
        };

        collectElements(selects);
        collectElements(inputs);
        collectElements(textareas);

        // Kiểm tra dựa theo bảng thiết kế
        for (let { name, condition, message } of validations) {
            let field = dom[name];
            if (field) {
                // Kiểm tra điều kiện cơ bản
                if (!condition(field.value)) {
                    changeValidateMessage(field, true, message, ["p-2", "small"]);
                    return false; // Trả về false để chỉ ra rằng kiểm tra không thành công
                }

                // Kiểm tra nếu có thuộc tính min-custom
                if (field.hasAttribute('min-custom')) {
                    let minValue = parseFloat(field.getAttribute('min-custom'));
                    if (parseFloat(field.value) < minValue) {
                        changeValidateMessage(field, true, `Số lượng phải lớn hơn hoặc bằng ${minValue}`, ["p-2", "small"]);
                        return false;
                    }
                }

                // Kiểm tra nếu có thuộc tính max-custom
                if (field.hasAttribute('max-custom')) {
                    let maxValue = parseFloat(field.getAttribute('max-custom'));
                    if (parseFloat(field.value) > maxValue) {
                        changeValidateMessage(field, true, `Số lượng phải nhỏ hơn hoặc bằng ${maxValue}`, ["p-2", "small"]);
                        return false;
                    }
                }

                // Nếu không có lỗi nào, xóa thông báo lỗi
                changeValidateMessage(field, false, '', []);
            }
        }

        // Nếu tất cả các kiểm tra đều thành công, thu thập dữ liệu từ các trường
        for (let key in dom) {
            let item = dom[key];
            data[key] = item.value;
        }
        console.log(data); return;
        return data;
    }

    /**thực hiện khởi tạo modal
     * @param modal nhận vào 1 id, class modal
     * @param show nhận vào 1 id class của 1 button lắng nghe sự kiện mở modal
     * @param loading nhận vào 1 id class của 1 loadding
     */
    startModal(show = "", modal, loading) {
        const openModal = () => {
            this.modal = document.querySelector(modal);
            this.newModal = new bootstrap.Modal(this.modal, {});
            this.newModal.show();

            this.loading = document.querySelector(loading);
            this.loading.style.display = "none";

            this.form.style.display = "flex";

            // Đảm bảo closeModal được gọi sau khi modal được khởi tạo
            this.modal.addEventListener('hidden.bs.modal', e => {
                this.reset();
                this.newModal.hide();
            });
        };

        if (show) {
            const element = document.querySelector(show);

            if (element) {  // Kiểm tra nếu phần tử tồn tại
                element.addEventListener("click", openModal);
            }
        } else {
            openModal();
        }

    }



    /**Hàm có tác dụng gửi thông tin form
     * @param api Nhận vào ip thực hiện việc gửi dữ liệu
     * @param method nhận vào method thực hiện gửi dữ liệu
     * @param data nhận vào mảng thực hiện gửi dữ liệu
     */
    async submitForm(api, method, validations, modal = true) {
        try {
            let data = this.validate(validations);
            if (data !== false) {
                const res = await axios[method](api, data);

                // Kiểm tra trạng thái trả về
                if (res.data.status === 403 || res.data.status === 500) {
                    showErrorMD(res.data.error);
                    return false;
                }

                // Hiển thị thông điệp thành công và thực hiện các thao tác cần thiết
                showMessageMD(res.data.success);

                if (modal) {
                    // Ẩn form và đóng modal
                    this.form.style.display = 'none';
                    this.newModal.hide();
                }

                // Trả về đối tượng kết quả
                return { status: true, data: res.data.data };
            }

        } catch (err) {
            // Xử lý lỗi
            showErrorMD("Vui lòng gọi IT");
            console.error(err);

            return { status: false, error: err };
        }
    }

}

// đổ ra dữ liệu
class LayoutCustom {
    constructor(html) {
        this.html = html
    }

    /**Hàm có tác dụng gọi api
     * @param API thực hiện gọi api
     * @param type phân biệt giữa lọc và lấy dữ liệu
     */

    getUI(api, total = true, type = "get") {
        return axios.get(api)
            .then(res => {
                // Kiểm tra trạng thái trả về
                if (res.data.status === 404 || res.data.status === 500) {
                    if (type !== "get") {
                        showErrorMD(res.data.error || "Đã xảy ra lỗi");
                    }
                    return false;
                }

                // Cập nhật tổng số sản phẩm nếu cần
                if (total) {
                    document.getElementById("total").innerText = `${res.data.data.total} sản phẩm`;
                }

                // Trả về dữ liệu
                return res.data.data;
            })
            .catch(err => {
                showErrorMD("Vui lòng gọi IT");
                console.error(err);
                return false; // Trả về false trong trường hợp có lỗi
            });
    }


    insertHTMLInTable(data, form, stauts = 0, template, colspan = 14, pagination = true) {
        let html = "";
        if (stauts === 0) {
            html += `
                <tr class="loading-data">
                    <td class="text-center" colspan="${colspan}">
                        <span class="text-danger">Không có dữ liệu</span>
                    </td>
                </tr>
            `;
        }
        else {

            html += template;
            // Pagination
            if (pagination) {
                let paginations = document.querySelector(pagination);
                BuildPagination(data, paginations, "getUI");
            }
        };

        document.querySelector(form).innerHTML = html;
    }
}


// Hàm thêm thanh cuộn nổi cho bảng
function createFixedScrollbar(containerSelector) {
    const tableContainer = typeof containerSelector === 'string'
        ? document.querySelector(containerSelector)
        : containerSelector;

    if (!tableContainer) {
        console.error('Container not found');
        return;
    }

    // Create the div container for scrollbar
    const scrollbarContainer = document.createElement('div');
    scrollbarContainer.className = 'scrollbar-x';
    document.body.appendChild(scrollbarContainer);

    const scrollbarThumb = document.createElement('div');
    scrollbarThumb.className = 'scrollbar-x-thumb';
    scrollbarContainer.appendChild(scrollbarThumb);

    let hideTimeout;

    function updateScrollbar() {
        const containerWidth = tableContainer.offsetWidth;
        const contentWidth = tableContainer.scrollWidth;
        const scrollRatioX = containerWidth / contentWidth;
        const thumbWidth = containerWidth * scrollRatioX;

        scrollbarContainer.style.width = `${containerWidth}px`;
        scrollbarContainer.style.left = `${tableContainer.getBoundingClientRect().left}px`;
        scrollbarThumb.style.width = `${thumbWidth}px`;
        scrollbarThumb.style.left = `${tableContainer.scrollLeft * scrollRatioX}px`;

        if (contentWidth > containerWidth) {
            scrollbarContainer.classList.add('visible');
        } else {
            scrollbarContainer.classList.remove('visible');
        }
    }

    tableContainer.addEventListener('scroll', updateScrollbar);
    window.addEventListener('resize', updateScrollbar);

    scrollbarContainer.addEventListener('mousedown', (e) => {
        const startX = e.clientX;
        const startLeft = scrollbarThumb.offsetLeft;

        const onMouseMove = (e) => {
            const deltaX = e.clientX - startX;
            tableContainer.scrollLeft = startLeft + deltaX * (tableContainer.scrollWidth / tableContainer.offsetWidth);
        };

        const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });

    tableContainer.addEventListener('mouseover', () => {
        clearTimeout(hideTimeout);
        updateScrollbar();
        scrollbarContainer.classList.add('visible');
    });

    tableContainer.addEventListener('mouseout', () => {
        hideTimeout = setTimeout(() => {
            scrollbarContainer.classList.remove('visible');
        }, 700); // Delay to hide
    });

    updateScrollbar(); // Initial update
}