$(document).ready(function ()
{

    $('#session_info_d').dialog(
            {
                autoOpen: false,
                resizable: false,
                width: 300,
                height: 'auto',
                minHeight: 0,
                modal: false,
                close: function ()
                {
                    module_handler.removeModule('CANCEL_SESSION');
                    module_handler.removeModule('EDIT_NOTE');
                    module_handler.removeModule('EDIT_USER');
                    $('#session_info').empty();
                },
            }
    );
    
    showCalendarView();

    setUpLoginPannel();

    loadServiceSelector();

    showCalendar();

    $("#corecal_login").button().click(function ()
    {
        processLogIn();
        return false;
    });

    $("#register_btn").click(function ()
    {
        $("#signup_dialog").dialog("open");
        return false;
    });

    $("#req_access_btn").button().click(function ()
    {
        requestServiceAccess();
    });

    $('.login_txt_box').keypress(function (event)
    {
        if (event.which == 13) {
            processLogIn();
            event.preventDefault();
        }

    });

    $("#corecal_logout").button().click(function ()
    {
        processLogOut();
    });

    $("#facility_select").change(function ()
    {
        //alert("Facility changed");
        $('#equipment_select option').remove();
        $('#service_select option').remove();
        $('#calendar').fullCalendar('removeEvents');

        //Rest user role when facility is changed
        resetRolePanel();
        getResourceList();

    }
    );

    $("#equipment_select").change(function ()
    {
        //alert("Changing equipment");
        $('#service_select option').remove();
        getServiceList();
    }
    );

    $("#service_select").change(function ()
    {
        //update user's role whenever service is changed
        updateUserPermissions();
    }
    );

    $('body').fadeIn("fast");

});

function showCalendar(display_state)
{

    var selectable = false;
    var curr_hour = new Date().getHours();


    if (display_state == 1)
        selectable = {
            month: true,
            agendaWeek: true,
            agendaDay: true
        };
    else
        selectable = false;

    var options = {
        theme: true,
        firstDay: 1,
        defaultView: 'agendaWeek',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        slotMinutes: 15,
        firstHour: curr_hour,
        allDaySlot: false,
        selectable: selectable,
        selectHelper: false,
        lazyFetching: false,
        select: handleEventSelection,
        eventClick: handleEventClick,
        eventResize: handleEventResize,
        eventDrop: handleEventDrop,
        viewDisplay: handleViewDisplay,
        eventRender: renderEvent,
        events: getEvents
    };

    $('#calendar').fullCalendar(options);
}

function refreshCalendar()
{
    $('#calendar').fullCalendar('refetchEvents');
}

function deleteEvent(event_id)
{
    $('#calendar').fullCalendar('removeEvents', event_id);
}

function updateEvent(event_data)
{

}

function getFacilityList()
{

    $.ajax({
        url: './ccny/scidiv/cores/ctrl/getFacilities.php',
        type: 'POST',
        dataType: 'json',
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {

                    $facilities = data.data;

                    if ($facilities.length > 0)
                        $('#facility_select').append($('<option value = ""></option>').html("Select one..."));

                    $.each($facilities, function (item, value)
                    {
                        $('#facility_select').append($('<option value =' + value.id + '></option>').html(value.label));
                    });
                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        }
    });

}

function getFacilityStatistics()
{
    var selected = $("#facility_select option:selected");
    var number = selected.val();

    $.ajax({
        url: './php/getFacilityStats.php',
        type: 'POST',
        dataType: 'json',
        data: {
            facility_id: number
        },
        success: function (data)
        {
            //alert(data);
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {

                    var stats = data.data.usage;

                    chart_options.series[0].data = stats.values;
                    chart_options.xAxis.categories = stats.categories;

                    if (chart != null)
                        chart.destroy();

                    chart = new Highcharts.Chart(chart_options);

                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        }
    });


}


function getResourceList()
{
    var selected = $("#facility_select option:selected");
    var number = selected.val();

    $.ajax({
        url: './ccny/scidiv/cores/ctrl/getResources.php',
        type: 'POST',
        dataType: 'json',
        data: {
            facility_id: number
        },
        success: function (data)
        {
            //alert(data);
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {

                    $resources = data.data;

                    if ($resources.length > 0)
                        $('#equipment_select').append($('<option value = ""></option>').html("Select one..."));

                    $.each($resources, function (item, value)
                    {
                        $('#equipment_select').append($('<option value =' + value.id + '></option>').html(value.label));
                    });



                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        }
    });

}






function getServiceList()
{

    var selected = $("#equipment_select option:selected");
    var number = selected.val();

    //alert("Getting services " + number);

    $.ajax({
        url: './ccny/scidiv/cores/ctrl/getServices.php',
        type: 'POST',
        dataType: 'json',
        data: {
            rid: number
        },
        success: function (data)
        {
            //alert(data);
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {

                    $services = data.data;

                    $.each($services, function (item, value)
                    {
                        $('#service_select').append($('<option value =' + value.id + '></option>').html(value.label));
                    });

                    //Trigger the service change function to refresh data for newly loaded services
                    $('#service_select').trigger('change');

                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        }
    });

}

function renderEvent(event, element)
{
    element.attr("title", event.description);
}


function getEvents(start, end, callback)
{

    var selected = $("#equipment_select option:selected");
    var eq_id = selected.val();

    $.ajax({
        url: './ccny/scidiv/cores/ctrl/getEvents.php',
        type: 'POST',
        dataType: 'json',
        data: {
            // our hypothetical feed requires UNIX timestamps
            start: Math.round(start.getTime() / 1000),
            end: Math.round(end.getTime() / 1000),
            eq_id: eq_id
        },
        success: function (data)
        {
            //alert(data);
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {
                    var events = data.data;
                    callback(events);
                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        }
    });

}


//===BEGIN: These functions handle event creation===
function handleEventSelection(start, end, allDay)
{

    $('#calendar').fullCalendar('unselect');

    createNewEvent(start, end, allDay);

}

function createNewEvent(s_time, e_time, allDay)
{

    var selected = $("#service_select option:selected");
    var serv_id = selected.val();

    //pass the data to the server. Time should be a unix timestamp
    var event_data = {};
    event_data.start = Math.round(s_time.getTime() / 1000);
    event_data.end = Math.round(e_time.getTime() / 1000);
    event_data.service = serv_id;

    if (allDay)
        event_data.allday = 1;
    else
        event_data.allday = 0;

    if (serv_id == null)
        notifyError("Service type not selected.");
    else
        $.ajax({
            type: "POST",
            url: "./ccny/scidiv/cores/ctrl/newEvent.php",
            data: event_data,
            dataType: "json",
            cache: false,
            success: newEventCreatedHandler,
            error: newEventCreateErrorHandler
        });
}

function newEventCreatedHandler(data)
{

    //alert(data);

    if (data.hasOwnProperty('error'))
    {
        if (data.error == 1)
        {
            notifyError(data.message);
        }
        else
        {
            notifySuccess("Event added successfully");
        }
    }
    else
    {
        notifyError("Invalid reponse from the server. Operation failed");
    }
    
    $('#calendar').fullCalendar('refetchEvents');
}

function newEventCreateErrorHandler(jqXHR, textStatus, errorThrown)
{
    notifyError(errorThrown);
}
//===END:

//===BEGIN: Handle event click

function handleEventClick(calEvent, jsEvent, view)
{
    
    
    var data = {"id": calEvent.id,"timestamp":calEvent.timestamp};

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/getEventDetails.php",
        data: data,
        dataType: "html",
        cache: false,
        success: function (data) {
            resetDialogContent();
            $("#session_info").append(data);
            showEventDetails(jsEvent);
        },
        error: function () {
            notifyError("Could not load event information.");
        }
    });

}
//===END:

function reloadEventDetails(data)
{
    resetDialogContent();

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/getEventDetails.php",
        data: data,
        dataType: "html",
        cache: false,
        success: function (data) {
            $("#session_info").append(data);
        },
        error: function () {
            notifyError("Could not load event information.");
        }
    });
}

function showEventDetails(jsEvent)
{

    var d_w = $('#session_info_d').dialog("option", "width");

    var d_x = jsEvent.clientX - d_w / 2;
    var d_y = jsEvent.clientY - 20;
    var pos = [d_x, d_y];

    $('#session_info_d').dialog("option", "position", pos);
    $('#session_info_d').dialog('open');

}

function resetDialogContent()
{
    $("#session_info").empty();
}

//===BEGIN: Handle event click

function handleEventResize(event, dayDelta, minuteDelta, revertFunc)
{


    var event_data = {};
    event_data.record_id = event.id;
    event_data.dayDelta = dayDelta;
    event_data.minuteDelta = minuteDelta;
    event_data.timestamp = event.timestamp;

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/resizeEvent.php",
        data: event_data,
        dataType: "json",
        cache: false,
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                    revertFunc();
                }
                else
                {
                    //alert("Event is resized done");
                    notifySuccess("Event resized.");
                }
            }
            else
            {
                notifyError("Invalid reponse from the server. Operation failed");
                revertFunc();
            }
            
            $('#calendar').fullCalendar('refetchEvents');

        },
        error: function ()
        {
            notifyError("Error updating event information");
            revertFunc();
        }
    });


}
//===END:

//===BEGIN: Handle event click

function handleEventDrop(event, dayDelta, minuteDelta, allDay, revertFunc)
{

    //alert("Moving the event");

    var event_data = {};
    event_data.record_id = event.id;
    event_data.timestamp = event.timestamp;
    event_data.dayDelta = dayDelta;
    event_data.minuteDelta = minuteDelta;

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/moveEvent.php",
        data: event_data,
        dataType: "json",
        cache: false,
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                    revertFunc();
                }
                else
                {
                    notifySuccess("Event moved.");
                }
            }
            else
            {
                notifyError("Error: Invalid reponse from the server. Operation failed");
                revertFunc();
            }
            
            $('#calendar').fullCalendar('refetchEvents');

        },
        error: function ()
        {
            notifyError("Error updating event information");
            revertFunc();
        }
    });

}
//===END:


//===BEGIN: Handle event click
function handleViewDisplay(view)
{
    clearUI();
}
//===END:


function clearUI()
{
    if ($("#session_info_d").dialog("isOpen"))
        $("#session_info_d").dialog('close');

    //$('#user_options_window').dialog('close');
}

//---BEGIN Functions that change viewport settings



function showCalendarView()
{
    clearUI();
    $('#calendar_view').show();
}

function setUpLoginPannel()
{

    checkLogin();

}

function processLogIn()
{
    //alert("Trying to log in");

    var login_data = {};
    login_data.user = $('#username_txt').val();
    login_data.pass = $('#password_txt').val();

    //alert(login_data.user);

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/loginProcessor.php",
        data: login_data,
        dataType: "json",
        cache: false,
        success: loginHandler,
        error: function ()
        {
            notifyError("Error logging in. Please try again later");

        }
    });


}

function checkLogin()
{
    //alert("Checking login");

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/loginChecker.php",
        dataType: "json",
        cache: false,
        success: checkLoginHandler,
        error: function ()
        {
            //notifyError("Error logging in. Please try again later");

        }
    });


}

function checkLoginHandler(data, status, settings)
{

    var logged_in = false;


    if (data.hasOwnProperty('error'))
    {
        if (data.error == 1)
        {
            notifyError(data.message);

        }
        else
        {


            if (data.hasOwnProperty('data'))
            {

                if (data.data != null)
                {

                    logged_in = true;

                    $('#user_login').text(data.data.name);
                    $('#user_last_log').text(data.data.last_active);
                    $('#user_pi').text(data.data.pi);
                    $('#user_type').text(data.data.type);

                }
                else
                {
                    logged_in = false;
                }

            }
            else
            {
                logged_in = false;
            }
        }
    }
    else
    {
        notifyError("Error: Invalid reponse from the server. Operation failed");
        logged_in = false;
    }

    if (logged_in)
    {
        $('#log_in_panel').hide();
        $('#logged_in_panel').fadeIn('fast');
        $('#dashboard_role_panel').slideDown('fast');

    }
    else
    {
        $('#log_in_panel').fadeIn("fast");
        $('#logged_in_panel').hide();
        $('#dashboard_role_panel').hide();
    }


}



function loginHandler(data, status, settings)
{

    if (data.hasOwnProperty('error'))
    {
        if (data.error == 1)
        {
            notifyError(data.message);
            resetPassword();
        }
        else
        {
            $('#log_in_panel').hide();
            $('#logged_in_panel').fadeIn("fast");
            $('#dashboard_role_panel').slideDown('fast');


            $('#user_login').text(data.data.name);
            $('#user_last_log').text(data.data.last_active);
            $('#user_pi').text(data.data.pi);
            $('#user_type').text(data.data.type);

            updateUserPermissions();

            resetLogInPanel();
        }
    }
    else
    {
        notifyError("Error: Invalid reponse from the server. Operation failed");
    }

}

function processLogOut()
{

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/logoutProcessor.php",
        dataType: "json",
        cache: false,
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);

                }
                else
                {
                    $('#log_in_panel').fadeIn("fast");
                    $('#logged_in_panel').hide();
                    $('#dashboard_role_panel').hide();

                    updateUserPermissions();
                    clearUI();
                    resetLogInPanel();
                }
            }
            else
            {
                notifyError("Error: Invalid reponse from the server. Operation failed");

            }

        },
        error: function ()
        {
            notifyError("Error logging out. Please check your connection and try again");

        }
    });
}


function resetLogInPanel()
{
    $('#username_txt').val('');
    resetPassword();
}

function resetPassword()
{
    $('#password_txt').val('');
}

function updateUserPermissions()
{

    resetUI();


    var selected = $("#service_select option:selected");
    var serv_id = selected.val();

    var event_data = {};
    event_data.serv_id = serv_id;

    if (serv_id != null)
    {
        $.ajax({
            type: "POST",
            url: "./ccny/scidiv/cores/ctrl/getUserParams.php",
            data: event_data,
            dataType: "json",
            cache: false,
            success: function (data)
            {

                if (data.hasOwnProperty('error'))
                {
                    if (data.error == 1)
                    {
                        setUserRole("N/A", "black");
                        resetCalendar(0);
                    }
                    else
                    {

                        var can_add = 0;
                        var show_req_btn = 0;
                        var show_txt = 0;
                        var txt = '';

                        if (data.data.hasOwnProperty('can_add'))
                            can_add = data.data.can_add;

                        if (data.data.hasOwnProperty('show_req_btn'))
                            show_req_btn = data.data.show_req_btn;

                        if (data.data.hasOwnProperty('show_txt'))
                            show_txt = data.data.show_txt;

                        if (data.data.hasOwnProperty('txt'))
                            txt = data.data.txt;

                        if (can_add)
                            resetCalendar(1);
                        else
                            resetCalendar(0);

                        if (show_req_btn)
                            showRequestAccessButton();

                        if (show_txt)
                            showPermission(txt);

                    }
                }
                else
                {
                    setUserRole("N/A", "black");
                    resetCalendar(0);
                }

            },
            error: function ()
            {
                setUserRole("N/A", "black");
                resetCalendar(0);
            }
        });
    }
    else
    {
        setUserRole("N/A", "black");
        resetCalendar(0);
    }
}

function showRequestAccessButton()
{
    $('#req_access_cont').show();
}

function resetUI()
{
    resetRolePanel();
}

function resetRolePanel()
{
    $('#user_role').text('');
    $('#role_txt_cont').hide();
    $('#req_access_cont').hide();
}

function getRecordID()
{
    return $('#e_record_id').attr('rec_id');
}

function notifyError(msg_text)
{
    var n = noty({
        text: msg_text,
        type: 'error',
        layout: 'center',
        maxVisible: 2,
        closeWith: ['button'],
        timeout: 2000,
        killer: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 200
        }
    });


}

function notifySuccess(txt)
{
    var n = noty({
        text: txt,
        type: 'success',
        layout: 'bottomRight',
        maxVisible: 1,
        closeWith: ['button'],
        killer: true,
        timeout: 2000,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 200
        }
    });

}

function showConfirmMsg(header_txt, body_txt)
{
    var n = noty({
        text: body_txt,
        type: 'information',
        layout: 'center',
        maxVisible: 2,
        closeWith: ['button'],
        killer: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 200
        }
    });
}

function showPermission(txt)
{
    $('#role_txt_cont').show();
    $('#user_role').fadeOut().text(txt).fadeIn('slow');
}

function setUserRole(user_role_string, color_str)
{
    $('#user_role').fadeOut().text(user_role_string).fadeIn('slow');
    $("#user_role").css("color", color_str);
}

function requestServiceAccess()
{

    var selected_service = $("#service_select option:selected");
    var service_id = selected_service.val();

    $.ajax({
        type: "POST",
        url: "./ccny/scidiv/cores/ctrl/requestAccess.php",
        dataType: "json",
        data: {
            id: service_id
        },
        cache: false,
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error)
                {
                    notifyError(data.message);
                }
                else
                {
                    updateUserPermissions();
                    showConfirmMsg("Request submitted.", "You will be notified by e-mail when your request is approved by facility administrator.");
                }
            }
            else
            {
                notifyError("Error: Invalid reponse from the server. Operation failed");
            }

        },
        error: function ()
        {
            notifyError("Error requesting access. Please try again later");

        }
    });

}

function resetCalendar(sel_enabled)
{

    var current_date = $('#calendar').fullCalendar('getDate');
    var current_view = $('#calendar').fullCalendar('getView');

    $('#calendar').fullCalendar('destroy');

    showCalendar(sel_enabled);

    $('#calendar').fullCalendar('changeView', current_view.name);
    $('#calendar').fullCalendar('gotoDate', current_date);

}

/*
 Function used to get attribute RID from a hidden div.
 If set, it will indicate if an instrument/resource should be preloaded
 This allows for creation of links for inidividual intruments/resources
 
 URL Format calview.php?RID=<FACILITY_NAME>
 */
function getSelectedResource()
{
    return $('#sel_res_id').attr('RID');
}


/*
 Function will check if getSelectedResource returns a string value
 
 If yes, corresponding menus will be populated with correct values
 If no, only the list of available facilities will be loaded
 */
function loadServiceSelector()
{

    var sel_resource = getSelectedResource();

    if (typeof sel_resource === 'string')
        preloadServiceSelector(sel_resource);
    else
        getFacilityList();

}


/*
 Function will call the server and preload service selector with corrent values
 */

function preloadServiceSelector(sel_resource)
{

    $.ajax({
        url: './ccny/scidiv/cores/ctrl/getServiceSelectorContent.php',
        type: 'POST',
        dataType: 'json',
        data: {
            rid: sel_resource
        },
        success: function (data)
        {
            if (data.hasOwnProperty('error'))
            {
                if (data.error == 1)
                {
                    notifyError(data.message);
                }
                else
                {

                    //Array holding facilities to show
                    var jQ_fac_arr = null;

                    //Array holding resources to show
                    var jQ_res_arr = null;

                    //Array holding services to show
                    var jQ_ser_arr = null;

                    //Default facility to select
                    var sel_fac_id = null;

                    //Default resource to select
                    var sel_res_id = null;

                    //Check if data property was recieved from the server
                    if (data.hasOwnProperty('data'))
                    {
                        /*
                         
                         Expected data format:
                         
                         data.fac_array - array that holds facilities to load.
                         data.res_array - array that holds resources to load.
                         data.ser_array - array that holds services to load.
                         
                         Each element of the array will have 'id' and 'label' defined for each option.
                         See loading loops below for usage details.
                         
                         data.fac_id - id of the facility that is loaded.
                         data.res_id - id of the service that is loaded.
                         
                         */


                        //Check each value that should be defined in data
                        if (data.data.hasOwnProperty('fac_array'))
                            jQ_fac_arr = data.data.fac_array;

                        if (data.data.hasOwnProperty('res_array'))
                            jQ_res_arr = data.data.res_array;

                        if (data.data.hasOwnProperty('ser_array'))
                            jQ_ser_arr = data.data.ser_array;


                        if (data.data.hasOwnProperty('fac_id'))
                            sel_fac_id = data.data.fac_id;

                        if (data.data.hasOwnProperty('res_id'))
                            sel_res_id = data.data.res_id;

                        var selected = "";

                        //Load facilities

                        if (jQ_fac_arr.length > 0)
                            $('#facility_select').append($('<option value = ""></option>').html("Select one..."));

                        $.each(jQ_fac_arr, function (item, value)
                        {
                            selected = "";

                            if (sel_fac_id != null)
                                if (value.id == sel_fac_id)
                                    selected = " selected";

                            $('#facility_select').append($('<option value =' + value.id + selected + '></option>').html(value.label));
                        });

                        //Load resources

                        if (jQ_res_arr.length > 1)
                            $('#equipment_select').append($('<option value = ""></option>').html("Select one..."));

                        $.each(jQ_res_arr, function (item, value)
                        {
                            selected = "";

                            if (sel_res_id != null)
                                if (value.id == sel_res_id)
                                    selected = " selected";

                            $('#equipment_select').append($('<option value =' + value.id + selected + '></option>').html(value.label));
                        });

                        //Load services

                        $.each(jQ_ser_arr, function (item, value)
                        {
                            $('#service_select').append($('<option value =' + value.id + '></option>').html(value.label));
                        });

                        //Trigger the service change function to refresh data for newly loaded services
                        $('#service_select').trigger('change');
                    }
                    else
                    {
                        //If data retured by the server is bad (no 'data' defined), load facility list as normal
                        getFacilityList();
                    }
                }
            }
            else
            {
                notifyError("Error fetching data: Invalid reponse from the server.");
            }
        },
        error: function ()
        {
            notifyError("Error loadnig data. Please try again later.");
        }
    });

}
