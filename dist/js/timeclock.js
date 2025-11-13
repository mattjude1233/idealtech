function update_timeclock_buttons(data) {
    // Helper to safely retrieve non-empty values
    const safeVal = val => (val !== undefined && val !== null && val !== '') ? val : '';

    // Destructure and sanitize incoming data
    const punchIn = safeVal(data.punch_in);
    const punchOut = safeVal(data.punch_out);
    const breakStart = safeVal(data.break_start);
    const breakEnd = safeVal(data.break_end);
    const late = safeVal(data.late);
    const totalBreak = safeVal(data.total_break);
    const overBreak = safeVal(data.overbreak);
    const lunchStart = safeVal(data.lunch_start);
    const lunchEnd = safeVal(data.lunch_end);
    const totalLunch = safeVal(data.total_lunch);
    const overLunch = safeVal(data.overlunch);
    const breakCount = safeVal(data.break_count);

    // CSS classes based on values
    const lateClass = late ? 'text-danger' : 'text-green';
    const overBreakClass = overBreak ? 'text-danger' : 'text-green';
    const overBreakInfoClass = overBreak ? 'danger' : 'info';
    const overLunchClass = overLunch ? 'text-danger' : 'text-green';
    const overLunchInfoClass = overLunch ? 'danger' : 'info';

    // Build the timing details HTML parts
    let timedetails = [];
    if (late) {
        timedetails.push(`
            <div class="col-md-4 my-auto">
                <div style="border: 1px solid #cccc; padding: 5px 0; border-radius: 7px;">
                    <div class="text-info font-14 text-bold">Late</div>
                    <div class="text-danger font-14">${late}</div>
                </div>
            </div>
        `);
    }
    if (totalBreak) {
        timedetails.push(`
            <div class="col-md-4 my-auto">
                <div style="border: 1px solid #cccc; padding: 5px 0; border-radius: 7px;">
                    <div class="text-${overBreakInfoClass} font-14 text-bold">Total Break</div>
                    <div class="${overBreakClass} font-14">${totalBreak}</div>
                </div>
            </div>
        `);
    }
    if (totalLunch) {
        timedetails.push(`
            <div class="col-md-4 my-auto">
                <div style="border: 1px solid #cccc; padding: 5px 0; border-radius: 7px;">
                    <div class="text-${overLunchInfoClass} font-14 text-bold">Total Lunch</div>
                    <div class="${overLunchClass} font-14">${totalLunch}</div>
                </div>
            </div>
        `);
    }
    if (timedetails.length) {
        $('#timeclock-details').html(`
            <div class="row m-t-15">
                ${timedetails.join('')}
            </div>
        `);
    }

    // Build punch buttons HTML
    let punchButtons = [];
    if (punchIn && !punchOut) { // Only punchIn filled
        punchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block ${lateClass} font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Punch In</small> ${punchIn}
                </a>
            </div>
        `);
        if ((breakStart && !breakEnd) || (lunchStart && !lunchEnd)) {
            punchButtons.push(`
                <div class="col-md-6 my-auto">
                    <a href="javascript:;" class="btn btn-secondary btn-block disabled">Punch OUT</a>
                </div>
            `);
        } else {
            punchButtons.push(`
                <div class="col-md-6 my-auto">
                    <a href="javascript:;" class="btn btn-info btn-block timeclock_punch-btn">Punch OUT</a>
                </div>
            `);
        }
    } else if (punchIn && punchOut) { // Both filled
        punchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block ${lateClass} font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Punch IN</small> ${punchIn}
                </a>
            </div>
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block text-primary font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Punch Out</small> ${punchOut}
                </a>
            </div>
        `);
    } else { // Default button when neither is filled
        punchButtons.push(`
            <div class="col-md-12">
                <a href="javascript:;" class="btn btn-success btn-block timeclock_punch-btn">Punch IN</a>
            </div>
        `);
    }

    // Build break buttons HTML
    let breakButtons = [];
    if (!breakStart && (!lunchStart || lunchEnd) && punchIn) {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-success btn-block timeclock_break-btn" data-break="start">Start ${breakCount} Break</a>
            </div>
        `);
    } else if ((!breakStart && lunchStart) || !punchIn) {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-secondary btn-block disabled">Start ${breakCount} Break</a>
            </div>
        `);
    } else {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block text-green font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Break Start</small> ${breakStart}
                </a>
            </div>
        `);
    }
    if (!breakStart && !breakEnd) {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-secondary btn-block disabled">End ${breakCount} Break</a>
            </div>
        `);
    } else if (breakStart && !breakEnd) {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-${overBreakInfoClass} btn-block timeclock_break-btn" data-break="end">End ${breakCount} Break</a>
            </div>
        `);
    } else if (breakStart && breakEnd) {
        breakButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block text-primary font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Break End</small> ${breakEnd}
                </a>
            </div>
        `);
    }

    // Build lunch buttons HTML
    let lunchButtons = [];
    if (!lunchStart && (!breakStart || breakEnd) && punchIn) {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-success btn-block timeclock_lunch-btn" data-lunch="start">Start Lunch</a>
            </div>
        `);
    } else if ((!lunchStart && breakStart) || !punchIn) {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-secondary btn-block disabled">Start Lunch</a>
            </div>
        `);
    } else {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block text-green font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Lunch Start</small> ${lunchStart}
                </a>
            </div>
        `);
    }
    if (!lunchStart && !lunchEnd) {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-secondary btn-block disabled">Return Lunch</a>
            </div>
        `);
    } else if (lunchStart && !lunchEnd) {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-${overLunchInfoClass} btn-block timeclock_lunch-btn" data-lunch="end">Return Lunch</a>
            </div>
        `);
    } else if (lunchStart && lunchEnd) {
        lunchButtons.push(`
            <div class="col-md-6 my-auto">
                <a href="javascript:;" class="btn btn-default btn-block text-primary font-20 lh-24 disabled">
                    <small style="display: block; font-size: 12px; line-height: 12px; font-weight: 600; color: #111;">Lunch End</small> ${lunchEnd}
                </a>
            </div>
        `);
    }

    // Update DOM elements
    $('#timeclock_punch').html(punchButtons.join(''));
    $('#timeclock_break').html(breakButtons.join(''));
    $('#timeclock_lunch').html(lunchButtons.join(''));
}


function open_timeclock(data) {
    const modalHTML = `
                <div class="modal fade" id="timeclock-modal" tabindex = "-1">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="timeclock-current_time"></h3>
                                <h4 class="timeclock-current_date"></h4>

                                <div id="timeclock-details"></div>
                            </div>
                            <div class="modal-body">
                                <div class="row m-b-10" id="timeclock_punch">
                                    <div class="col-md-12">
                                        <a href="javascript:;" class="btn btn-success btn-block timeclock_punch-btn">Punch IN</a>
                                    </div>
                                </div>

                                <div class="row m-b-10" id="timeclock_lunch">
                                    <div class="col-md-6">
                                        <a href="javascript:;" class="btn btn-secondary btn-block disabled timeclock_lunch-btn">Start Lunch</a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:;" class="btn btn-secondary btn-block disabled timeclock_lunch-btn">Return Lunch</a>
                                    </div>
                                </div>

                                <div class="row m-b-10" id="timeclock_break">
                                    <div class="col-md-6">
                                        <a href="javascript:;" class="btn btn-secondary btn-block disabled">Start Break</a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:;" class="btn btn-secondary btn-block disabled">End Break</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;


    // Check if the modal not exists, append modalHTML to body
    if (!$('#timeclock-modal').length) {
        $('body').append(modalHTML);
        $('#timeclock-modal').modal('show');

        // if modal is hidden, remove it from DOM
        $('#timeclock-modal').on('hidden.bs.modal', function () {
            $(this).remove();
        });
    }

    update_timeclock_buttons(data);
}

function get_timeclock() {
    $.ajax({
        url: base_url + 'timeclock/get_timeclock',
        type: 'POST',
        dataType: 'json',
        success: function (data) {

            // modal handling
            if ($('#timeclock-modal').length) {
                update_timeclock_buttons(data);
            } else {
                open_timeclock(data);
            }

            // set initial server values
            $('.timeclock-current_time').text(data.current_time);
            $('.timeclock-current_date').text(data.current_date);

            // convert server time string to a Date object
            // expected format: "HH:MM:SS AM/PM"
            let serverDateTime = new Date(`${data.current_date} ${data.current_time}`);

            // live update based on server time
            setInterval(function () {
                serverDateTime = new Date(serverDateTime.getTime() + 1000);

                let hours = serverDateTime.getHours();
                let minutes = String(serverDateTime.getMinutes()).padStart(2, '0');
                let seconds = String(serverDateTime.getSeconds()).padStart(2, '0');
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                hours = String(hours).padStart(2, '0');

                let formattedTime = `${hours}:${minutes}:${seconds} ${ampm}`;
                $('.timeclock-current_time').text(formattedTime);
            }, 1000);
        },
        error: function () {
            alert('Error fetching time clock data.');
        }
    });
}


$(document).ready(function () {
    $(document).on('click', '#timeclock_open-btn', function (e) {
        e.preventDefault();

        get_timeclock();
    });

    $(document).on('click', '.timeclock_break-btn', function (e) {
        e.preventDefault();

        let breakType = $(this).data('break');

        $.alert({
            title: `${breakType === 'start' ? 'Start Break' : 'End Break'} `,
            content: `Are you sure you want to ${breakType === 'start' ? 'start' : 'end'} the break?`,
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.ajax({
                            url: base_url + 'timeclock/break',
                            type: 'POST',
                            dataType: 'json',
                            success: function (data) {
                                if (data.status === 'success') {
                                    get_timeclock();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            },
                            error: function () {
                                alert('Error processing break.');
                            }
                        });
                    }
                },
                cancel: {
                    text: 'No'
                }
            }
        });
    });


    $(document).on('click', '.timeclock_lunch-btn', function (e) {
        e.preventDefault();

        let lunchType = $(this).data('lunch');

        $.alert({
            title: `${lunchType === 'start' ? 'Start Lunch' : 'End Lunch'} `,
            content: `Are you sure you want to ${lunchType === 'start' ? 'start' : 'end'} the lunch?`,
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.ajax({
                            url: base_url + 'timeclock/break/lunch',
                            type: 'POST',
                            dataType: 'json',
                            success: function (data) {
                                if (data.status === 'success') {
                                    get_timeclock();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            },
                            error: function () {
                                alert('Error processing lunch.');
                            }
                        });
                    }
                },
                cancel: {
                    text: 'No'
                }
            }
        });
    });



    $(document).on('click', '.timeclock_punch-btn', function (e) {
        e.preventDefault();

        let retryUrl = base_url + 'timeclock/punch'; // Default URL
        
        // Check if this is a punch out button (has "Punch OUT" text)
        let buttonText = $(this).text().trim();
        let isPunchOut = buttonText === 'Punch OUT';

        const sendPunch = (url) => {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status === 'success') {
                        get_timeclock();
                    } else if (data.status === 'work_hours_error') {
                        $.alert({
                            title: 'Warning!',
                            content: data.message,
                            type: 'red',
                            buttons: {
                                retry: {
                                    text: 'Punch OUT',
                                    btnClass: 'btn-red',
                                    action: function () {
                                        // Change URL and retry
                                        sendPunch(base_url + 'timeclock/punch/force_punch');
                                    }
                                },
                                cancel: {
                                    text: 'Cancel'
                                }
                            }
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error processing punch in.');
                }
            });
        };

        // Show confirmation dialog for Punch OUT
        if (isPunchOut) {
            $.alert({
                title: 'Punch OUT Confirmation',
                content: 'Are you sure you want to punch out? This will end your work shift.',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes, Punch OUT',
                        btnClass: 'btn-blue',
                        action: function () {
                            sendPunch(retryUrl);
                        }
                    },
                    cancel: {
                        text: 'Cancel'
                    }
                }
            });
        } else {
            // For Punch IN, proceed directly without confirmation
            sendPunch(retryUrl);
        }
    });
});