function form_error(elem, atyp = 'alert-danger', ahdr = 'Form submission failed!', abdy = 'Something went wrong in submitting the form.') {
    $(elem).html('');

    var h = `<div class="alert ${atyp} alert-dismissible alertbox__main">`;
    h += `<button type="button" class="close" data-hidden="alert" aria-hidden="true">&times;</button>`;
    h += `<h5><i class="icon fas ` + (atyp == 'alert-danger' ? 'fa-ban' : 'fa-check') + ` alertbox__icon"></i> <span class="alertbox__heading">${ahdr}</span></h5>`;
    h += `<span class="alertbox__text">${abdy}</span>`;
    h += `</div>`;

    $(elem).html(h);
}

function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

function validateTime() {
    var timeStart = $('#time_started').val();
    var timeEnd = $('#time_ended').val();

    var currentDate = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format

    var timeStartDate = new Date(currentDate + ' ' + timeStart);
    var timeEndDate = new Date(currentDate + ' ' + timeEnd);

    // If start time is greater than end time, clear end time value
    if (timeStartDate > timeEndDate) {
        $('#time_ended').val('');
    }
}

function page_loader_show() {
    if ($('#custom-loader').length === 0) {
        $('head').append(`
            <style id="loader-style">
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                #custom-loader-spinner {
                    border: 8px solid #f3f3f3;
                    border-top: 8px solid #570d1a;
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    animation: spin 1s linear infinite;
                    margin: 0 auto;
                }
            </style>
        `);

        $('body').append(`
            <div id="custom-loader" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:9999;display:flex;justify-content:center;align-items:center;">
                <div id="custom-loader-spinner"></div>
            </div>
        `);
    } else {
        $('#custom-loader').show();
    }
}

function page_loader_hide() {
    $('#custom-loader').remove();
    $('#loader-style').remove();
}

function setSelect2Value(selector, value) {
    const $select = $(selector);
    const values = Array.isArray(value) ? value : [value]; // support single or array

    let current = $select.val() || [];

    values.forEach(function (val) {
        if (val && !$select.find(`option[value="${val}"]`).length) {
            $select.append(new Option(val, val));
        }
        if (!current.includes(val)) {
            current.push(val);
        }
    });

    $select.val(current).trigger('change');
}

function dateFormat(dateString) {
    if (!dateString) return '';
    return new Date(dateString);
}

$(document).on('mouseover mouseout', '[class*="bg--days-"]', function (event) {
    var classes = $(this).attr('class').split(/\s+/);
    var hoveredClass = '';

    classes.forEach(function (className) {
        if (className.startsWith('bg--days-')) {
            hoveredClass = className;
            if (event.type === 'mouseover') {
                $('.' + className).addClass('active');
                $('[class*="bg--days-"]').not('.' + className).addClass('bg--days-off');
            } else if (event.type === 'mouseout') {
                $('.' + className).removeClass('active');
                $('[class*="bg--days-"]').not('.' + className).removeClass('bg--days-off');
            }
        }
    });
});


$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('input', '.number_only', function (event) {
        // Prevent non-numeric input
        let input = $(this).val();
        input = input.replace(/[^0-9.]/g, '')  // Remove non-numeric characters
            .replace(/(\..*)\./g, '$1')  // Allow only one decimal point
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');  // Add commas for thousands
        $(this).val(input);
    });

    $(document).on('paste', '.number_only', function (event) {
        event.preventDefault();  // Prevent pasting non-numeric input
    });

    $('.timepicker').clockpicker({
        placement: 'bottom',
        twelvehour: true,
        donetext: 'Done',
        default: 'now'
    }).on('change', function () {
        var disTime = this.value;

        var currentDate = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format

        var time = new Date(currentDate + ' ' + disTime);

        var formattedTime = time.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        $(this).val(formattedTime);
        validateTime();
    });

    $('.timepicker').inputmask("h:s t", {
        placeholder: "HH:MM AM/PM",
        alias: "datetime",
        hourFormat: "12",
        insertMode: false,
        casing: "upper"
    }).on('keyup', function () {
        // close clockpicker on input keyup
        $(this).clockpicker('hide');
    });

    $(document).on('select2:open', function (e) {
        const $sel = $(e.target);
        const inst = $sel.data('select2');

        // wait for the dropdown DOM to be ready
        setTimeout(() => {
            const $search = inst.dropdown.$search;
            if (!$search?.length) return;

            // always focus
            $search[0].focus();

            // if tags=true, prefill with the selected option text
            if (inst.options.get('tags')) {
                const txt = $sel.find('option:selected').text().trim();
                if (txt) {
                    $search.val(txt).trigger('input');
                }
            }
        }, 0);
    });

    $('.datetimepicker').datetimepicker({
        format: 'F d, Y h:i A',
        formatTime: 'h:i A',
        step: 30,
    });
});

// ChartJS


let employeeTimeChart = null;

function renderEmployeeTimeChart(canvasId, dataInMinutes) {
    const canvas = document.getElementById(canvasId);

    // Set fixed size for consistent rendering
    canvas.width = 190;
    canvas.height = 190;
    const ctx = canvas.getContext('2d');

    // Destroy existing chart if any
    if (employeeTimeChart) {
        employeeTimeChart.destroy();
        employeeTimeChart = null;
    }

    // Sum total minutes
    const totalMinutes = dataInMinutes.reduce((sum, val) => sum + val, 0);

    // Plugin for center text
    const centerTextPlugin = {
        beforeDraw(chart) {
            const { width, height } = chart.chart;
            const ctx = chart.chart.ctx;
            ctx.restore();

            const fontSize = (height / 170).toFixed(2);
            ctx.font = `${fontSize}em sans-serif`;
            ctx.textBaseline = "middle";
            ctx.fillStyle = '#000';

            const totalH = Math.floor(totalMinutes / 60);
            const totalM = totalMinutes % 60;
            const value = `${totalH}h ${totalM}m`;
            const valueX = Math.round((width - ctx.measureText(value).width) / 2);
            const valueY = height / 2;
            ctx.fillText(value, valueX, valueY);
            ctx.save();
        }
    };

    // Filter out 0-value segments
    const rawLabels = ['Working Time', 'Break Time', 'Loss Time Incident'];
    const rawColors = [
        'rgba(0, 150, 62, 0.6)',
        'rgba(230, 140, 0, 0.6)',
        'rgba(200, 0, 0, 0.6)'
    ];

    const filteredData = [];
    const filteredLabels = [];
    const filteredColors = [];

    dataInMinutes.forEach((val, idx) => {
        if (val > 0) {
            filteredData.push(val);
            filteredLabels.push(rawLabels[idx]);
            filteredColors.push(rawColors[idx]);
        }
    });

    // Create chart
    employeeTimeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: filteredLabels,
            datasets: [{
                data: filteredData,
                backgroundColor: filteredColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            legend: { display: false },
            title: { display: false },
            tooltips: {
                enabled: true,
                callbacks: {
                    label(tooltipItem, data) {
                        const label = data.labels[tooltipItem.index];
                        const value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        const h = Math.floor(value / 60);
                        const m = value % 60;
                        return `${label}: ${h}h ${m}m`;
                    }
                }
            },
            plugins: {
                datalabels: {
                    color: '#000',
                    formatter(value) {
                        const h = Math.floor(value / 60);
                        const m = value % 60;
                        return `${h}.${m < 10 ? '0' + m : m}`;
                    },
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    align: 'center',
                    anchor: 'center'
                }
            },
            cutoutPercentage: 60
        },
        plugins: [centerTextPlugin, ChartDataLabels]
    });
}

function calcHours(fromStr, toStr, lunchHourStart = 12, lunchPeriod = 'am') {
    const from = new Date(fromStr);
    const to = new Date(toStr);
    if (isNaN(from) || isNaN(to) || to <= from) return '0.00';

    const diffHours = (to - from) / 36e5;

    // Convert lunch start hour to 24h format
    let lunchStartHour;
    if (lunchPeriod.toLowerCase() === 'pm') {
        lunchStartHour = lunchHourStart === 12 ? 12 : (lunchHourStart % 12) + 12; // PM hours
    } else { // 'am'
        lunchStartHour = lunchHourStart % 12; // AM hours
    }

    // Start checking from the first possible lunch occurrence before or on 'from'
    let checkDay = new Date(from.getFullYear(), from.getMonth(), from.getDate(), lunchStartHour, 0, 0);

    // If lunch time is before 'from' on the same day, move to next day
    if (checkDay < from) checkDay.setDate(checkDay.getDate() + 1);

    let count = 0;
    while (checkDay < to) {
        const lunchEnd = new Date(checkDay.getTime() + 1 * 60 * 60 * 1000); // +1 hr
        if (lunchEnd > from && checkDay < to) count++;
        checkDay.setDate(checkDay.getDate() + 1);
    }

    const adjusted = Math.max(0, diffHours - (count * 1));
    return adjusted.toFixed(2);
}

$(document).ready(function () {
    var currentUrl = window.location.href;

    $('.main-sidebar nav ul.nav li a').each(function () {
        if (this.href === currentUrl) {
            $(this).addClass('active');
            $(this).closest('li').addClass('menu-open'); // for treeview
            $(this).parents('ul.nav-treeview').prev('a').addClass('active');
        }
    });

    $(document).on('click', '.toggle_add_password_visibility', function () {
        var input = $(this).parents('.input-group').find('input');
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $(document).on('click', '.generate_add_password', function (e) {
        e.preventDefault();

        var input = $(this).parents('.input-group').find('input');
        var icon = $(this).find('i');

        // Generate stronger random password (8-11 characters, at least 2 symbols)
        var length = Math.floor(Math.random() * 4) + 8; // Random length between 8 and 11
        var lowercase = 'abcdefghijklmnopqrstuvwxyz';
        var uppercase = lowercase.toUpperCase();
        var symbols = '!@#$%&*+-_';
        var numbers = '0123456789';
        var allChars = lowercase + uppercase + numbers + symbols;
        var passwordArray = [];

        // Ensure at least 2 symbols
        for (var i = 0; i < 2; i++) {
            passwordArray.push(symbols.charAt(Math.floor(Math.random() * symbols.length)));
        }

        // Fill remaining characters
        for (var i = passwordArray.length; i < length; i++) {
            passwordArray.push(allChars.charAt(Math.floor(Math.random() * allChars.length)));
        }

        // Shuffle characters
        for (var i = passwordArray.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = passwordArray[i];
            passwordArray[i] = passwordArray[j];
            passwordArray[j] = temp;
        }

        // Set password and show it
        var newPassword = passwordArray.join('');
        input.val(newPassword).attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    });
});