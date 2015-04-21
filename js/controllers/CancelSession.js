function CancelSessionCntrl()
{
    init();

    function init()
    {
        $('#cancel_btn').click(function () {
            showCancelConfirm();
        });
        $('#cancel_back_btn').click(function () {
            hideCancelConfirm();
        });
        $('#cancel_conf_btn').click(function ()
        {
            var record_id = getRecordID();

            var data = {'id': record_id};

            $.ajax({
                type: 'POST',
                url: './ccny/scidiv/cores/ctrl/cancelEvent.php',
                dataType: 'json',
                data: data,
                cache: false,
                success: function (data)
                {
                    if (data.hasOwnProperty('error'))
                    {
                        if (data.error == 1)
                        {
                            alert(data.message);

                        }
                        else
                        {

                            clearUI();
                            deleteEvent(record_id);
                            notifySuccess("Event cancelled.");

                        }
                    }
                    else
                    {
                        notifyError('Error: Invalid reponse from the server. Operation failed');

                    }

                },
                error: function ()
                {
                    notifyError('Cancel operation failed. Please check your connection and try again');

                }
            });
        });

    }
    ;

    function getRecordID()
    {
        return $('input#s_rec_id').val();
    }
    ;

    function showCancelConfirm()
    {
        $('#session_info_fields').css({'opacity': 0.5});
        $('#s_info_cancel_panel').show();
        $('#s_info_cntr_panel').hide();
    }
    ;

    function hideCancelConfirm()
    {
        $('#s_info_cancel_panel').hide();
        $('#s_info_cntr_panel').show();
        $('#session_info_fields').css({'opacity': 1.0});
    }
    ;

    function destroy()
    {
        $('#cancel_btn').off();
        $('#cancel_back_btn').off();
        $('#cancel_conf_btn').off();
    }

    return{
        destroy: destroy
    }

}
;

module_handler.addModule('CANCEL_SESSION', new CancelSessionCntrl());