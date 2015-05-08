function EditUserCntrl() {

    init();

    function init()
    {
        $('#e_user_name').editable({
            type: 'select',
            toggle: 'click',
            url: './ccny/scidiv/cores/ctrl/changeUser.php',
            title: 'Select a new user:',
            pk: getRecordID,
            params: function(params){
                params.timestamp = getTimestamp();
                return params;  
            },
            disabled: false,
            sourceCache: false,
            ajaxOptions: {
                dataType: 'json'
            },
            source: './ccny/scidiv/cores/ctrl/getUsers.php',
            sourceOptions: {data: {rec_id: getRecordID}, type: 'post'},
            sourceError: 'Users could not be loaded',
            success: function (response, newValue) {

                if (response.hasOwnProperty('error'))
                {
                    if (response.error == 1)
                        return response.message;
                    else
                    {
                        clearUI();
                        $('#calendar').fullCalendar('refetchEvents');
                        notifySuccess("User changed.");
                    }
                }
                else
                    return "Invalid server response";

            }
        });

        $('#edit_user_btn').click(function (e)
        {
            e.stopPropagation();
            $('#e_user_name').editable('toggle');

        });

    }
    ;

    function getTimestamp()
    {
        return $('input#s_timestamp').val();
    };
    
    function getRecordID()
    {
        return $('input#s_rec_id').val();
    }
    ;

    function destroy()
    {
        $('#edit_user_btn').off();
    }

    return{
        destroy: destroy
    }

}
;

module_handler.addModule('EDIT_USER', new EditUserCntrl());